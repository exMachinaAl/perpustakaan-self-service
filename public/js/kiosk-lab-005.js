'use strict';
(() => {
const cfg=window.kiosk005, el=id=>document.getElementById(id);
const loaderSources=[cfg.jsLocal,'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js','https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'];
let libraryLoading=null;
function tryScript(src){return new Promise((resolve,reject)=>{
 const script=document.createElement('script'); let ended=false;
 const timer=setTimeout(()=>finish(false),6500);
 function finish(ok){if(ended)return;ended=true;clearTimeout(timer);if(!ok)script.remove();ok?resolve():reject(new Error('Tidak tersedia: '+src));}
 script.onload=()=>finish(!!window.Html5Qrcode);
 script.onerror=()=>finish(false);script.src=src;script.async=true;document.head.appendChild(script);
});}
async function qrLibrary(){
 if(window.Html5Qrcode)return window.Html5Qrcode;
 if(!libraryLoading)libraryLoading=(async()=>{
  for(const src of loaderSources){try{await tryScript(src);if(window.Html5Qrcode)return window.Html5Qrcode;}catch(e){}}
  throw new Error('Decoder QR tidak berhasil dimuat. Pastikan internet aktif atau pasang html5-qrcode.min.js di public/vendor/. Anda masih dapat memakai scanner USB dan simulasi.');
 })();
 try{return await libraryLoading;}catch(e){libraryLoading=null;throw e;}
}
let member=null,book=null,completed=false,generation=0,cam=null,cameraBusy=false;
let lastLocal='',lastLocalAt=0,lastKiosk=0,lastRFID=0,seededRFID=false,seededKiosk=false,knownLocalIds=new Set();
const state=(title,desc,cls='ready')=>{el('stage').className=cls;el('stage-title').textContent=title;el('stage-description').textContent=desc;};
function reset(){generation++;member=null;book=null;completed=false;el('member-name').textContent='BELUM ADA';el('book-name').textContent='BELUM ADA';el('result-state').textContent='MODE DRY RUN — TIDAK MENGUBAH STOK';state('SIAP UNTUK SCAN','Scan QR anggota / buku, atau tap kartu RFID');}
function receive(result,channel){
 if(!result || !result.kind)return;
 if(completed)reset();
 if(result.outcome!=='identified'){state('SCAN DITOLAK',result.outcome||'QR tidak dikenal','error');return;}
 if(result.kind==='member'){
  if(member&&member.id===result.member_id)return;
  member={id:result.member_id,label:result.label,channel};el('member-name').textContent=result.label+' ('+channel+')';
 }
 if(result.kind==='book'){
  if(book&&book.copy===result.copy_id)return;
  book={copy:result.copy_id,label:result.label};el('book-name').textContent=result.label;
 }
 if(member&&book){completed=true;state('DUA IDENTITAS TERBACA','Simulasi selesai. Tidak ada pinjaman dibuat.','ok');
  el('result-state').textContent='DRY RUN BERHASIL — STOK TIDAK BERUBAH';const version=++generation;
  setTimeout(()=>{if(completed && generation===version)reset();},9000);
 }else if(member)state('ANGGOTA DIKENALI','Silakan scan QR eksemplar buku.');
 else if(book)state('BUKU DIKENALI','Silakan scan QR anggota atau tap RFID.');
}
async function postJson(url,payload){
 const res=await fetch(url,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':cfg.csrf,'Accept':'application/json'},body:JSON.stringify(payload)});
 let data={};try{data=await res.json();}catch(e){}
 if(!res.ok)throw new Error(data.message||('HTTP '+res.status+' — periksa login admin, CSRF, dan jaringan'));
 return data;
}
async function inspect(raw,channel){
 const text=String(raw||'').trim();if(!text)return;
 const now=Date.now();if(text===lastLocal && now-lastLocalAt<2400)return;
 lastLocal=text;lastLocalAt=now;
 try{const json=await postJson(cfg.inspectUrl,{payload:text,channel});
  if(json.event_id)knownLocalIds.add(Number(json.event_id));receive(json.result,channel);
 }catch(e){state('SCAN GAGAL',e.message,'error');}
}
async function simulate(kind){
 const key=kind==='member'?'member_id':'copy_id';const val=el(kind==='member'?'sim-member':'sim-book').value;
 if(!val){state('PILIH DATA','Pilih '+(kind==='member'?'anggota aktif':'eksemplar terdaftar')+' terlebih dahulu.','error');return;}
 try{const r=await postJson(cfg.simulateUrl,{kind,[key]:Number(val)});
  if(r.event_id)knownLocalIds.add(Number(r.event_id));receive(r.result,'debug');
 }catch(e){state('SIMULASI GAGAL',e.message,'error');}
}
function diag(){
 const secure=window.isSecureContext,media=!!(navigator.mediaDevices&&navigator.mediaDevices.getUserMedia);
 const address=location.origin;const text=[];
 if(!secure){text.push('KAMERA LIVE DIBLOKIR: halaman bukan secure context.');
  text.push('PC kiosk: buka http://localhost:8000/admin/kiosk/hub di komputer yang menjalankan Laravel.');
  text.push('HP / LAN: gunakan HTTPS dengan sertifikat tepercaya (bukan HTTP IP LAN).');
 }else if(!media){text.push('Secure context terdeteksi, tetapi getUserMedia tidak tersedia. Periksa browser / permission policy.');}
 else{text.push('Konteks aman & API kamera tersedia. Klik Mulai Kamera, lalu izinkan akses browser.');}
 text.push('Decoder QR: '+(window.Html5Qrcode?'sudah dimuat':'akan dimuat saat diperlukan (local atau CDN).'));
 el('camera-diagnostics').textContent=text.join(' ');
 el('camera-diagnostics').className='alert '+(secure&&media?'good':'warn');
 el('camera-note').textContent=secure&&media?'Izin kamera belum diuji. Klik Mulai kamera.':'Live camera perlu localhost/HTTPS. Upload foto QR atau gunakan scanner USB/simulasi.';
 el('origin-help').textContent='Alamat halaman: '+address+' | SecureContext: '+secure+' | mediaDevices: '+media;
 el('cam-start').disabled=!(secure&&media);
}
async function stopCamera(){const q=cam;if(!q)return;cam=null;
 try{await q.stop();}catch(e){}
 try{await q.clear();}catch(e){}
}
async function startCamera(){
 if(cameraBusy||cam)return;
 cameraBusy=true;
 try{
  if(!window.isSecureContext||!navigator.mediaDevices?.getUserMedia)throw new Error('Buka halaman dari localhost (PC yang sama) atau HTTPS tepercaya. HTTP IP LAN tidak diizinkan untuk live camera.');
  const Cls=await qrLibrary();
  const scanner=new Cls('reader');cam=scanner;
  const callback=text=>inspect(text,'webcam');
  try{await scanner.start({facingMode:'environment'},{fps:8,qrbox:{width:220,height:220}},callback,()=>{});}
  catch(err){
   // Some laptop webcams do not advertise an 'environment' camera.
   const cameras=await Cls.getCameras();if(!cameras.length)throw err;
   await scanner.start(cameras[0].id,{fps:8,qrbox:{width:220,height:220}},callback,()=>{});
  }
  el('camera-note').textContent='Kamera aktif. Arahkan QR anggota atau QR buku ke bingkai.';
 }catch(e){await stopCamera();el('camera-note').textContent='Gagal: '+e.message+' | Pilih Upload Foto QR bila kamera tetap tidak bisa dipakai.';}
 finally{cameraBusy=false;}
}
async function scanPhoto(file,input){
 if(!file)return;
 try{
  const Cls=await qrLibrary();await stopCamera();
  const scanner=new Cls('reader');let text;
  try{text=await scanner.scanFile(file,true);}
  finally{try{await scanner.clear();}catch(e){}}
  el('camera-note').textContent='QR terbaca dari gambar. Sedang memverifikasi ke Laravel...';
  await inspect(text,'webcam');
 }catch(e){el('camera-note').textContent='Gambar gagal dibaca: '+e.message+' (coba foto lebih dekat, terang, dan tidak miring).';}
 finally{input.value='';}
}
function eventRow(e){const row=document.createElement('div');row.textContent='#'+e.id+' • '+e.channel+' • '+e.kind+' • '+(e.label||e.outcome);
 el('events').prepend(row);while(el('events').children.length>20)el('events').lastElementChild.remove();}
async function poll(){
 try{const chosen=el('reader-select').value,url=cfg.eventUrl+'?after_id='+lastKiosk+(chosen?'&reader_id='+encodeURIComponent(chosen):'');
 const res=await fetch(url,{credentials:'same-origin'});
 if(res.ok){const data=await res.json();for(const e of data.events||[]){const id=Number(e.id);lastKiosk=Math.max(lastKiosk,id);eventRow(e);
 if(!seededKiosk||knownLocalIds.has(id))continue;
 receive(e,e.channel);
 }seededKiosk=true;}
 }catch(e){}
 try{const selected=el('reader-select').value;if(!selected)return;
 const r=await fetch(cfg.rfidUrl+'?reader_id='+encodeURIComponent(selected),{credentials:'same-origin'});
 if(!r.ok)return;const data=await r.json(),rows=data.events||[];
 if(!seededRFID){lastRFID=Math.max(0,...rows.map(x=>Number(x.id)));seededRFID=true;return;}
 for(const e of rows){if(Number(e.id)<=lastRFID)continue;lastRFID=Math.max(lastRFID,Number(e.id));
  if(e.outcome==='identified'&&e.member_id)receive({kind:'member',outcome:'identified',member_id:e.member_id,label:(e.member_name||'Anggota').split(' ')[0]},'rfid');
 }
 }catch(e){}
}
el('reset').addEventListener('click',reset);
el('diag-recheck').addEventListener('click',diag);
el('sim-member-btn').addEventListener('click',()=>simulate('member'));
el('sim-book-btn').addEventListener('click',()=>simulate('book'));
el('cam-start').addEventListener('click',startCamera);
el('cam-stop').addEventListener('click',stopCamera);
el('qr-photo').addEventListener('change',e=>scanPhoto(e.target.files?.[0],e.target));
el('wedge').addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();const text=e.target.value.trim();e.target.value='';inspect(text,'usb');}});
el('book-select').addEventListener('change',()=>{el('copy-form').action=cfg.copyBase+'/'+encodeURIComponent(el('book-select').value)+'/copies';});
el('copy-form').addEventListener('submit',e=>{if(!el('book-select').value)e.preventDefault();});
el('reader-select').addEventListener('change',()=>{seededRFID=false;lastRFID=0;});
setInterval(poll,3000);poll();
setInterval(async()=>{try{const data=await postJson(cfg.pairUrl,{});el('pair-qr').innerHTML=data.svg;el('pair-link').textContent=data.pair_url;}catch(e){}},90000);
diag();
})();
