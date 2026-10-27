'use strict';
(()=>{
const cfg=window.kiosk006, by=id=>document.getElementById(id);
let camera=null, video=null, stream=null, detector=null, processing=false, prev='', prevAt=0;
let completed=false;const seen=new Set();
const detail=by('kiosk006-note');
const setNote=(s,kind='')=>{if(detail){detail.textContent=s;detail.className='alert '+(kind==='bad'?'warn':kind==='good'?'good':'');}};
const stage=(title,message,cls='ready')=>{by('stage').className=cls;by('stage-title').textContent=title;by('stage-description').textContent=message;};
const update=r=>{
 by('member-name').textContent=r.member||'BELUM ADA';by('book-name').textContent=r.book||'BELUM ADA';
 const state=r.state||'ready';
 if(['validated','borrowed','complete'].includes(state))completed=true;
 const m={ready:'SIAP SCAN',waiting_book:'SCAN BUKU',waiting_member:'SCAN SISWA',validated:'QR ASLI VALID',borrowed:'PEMINJAMAN BERHASIL',rejected:'QR DITOLAK',complete:'SELESAI'};
 stage(m[state]||state,r.message||'',state==='rejected'?'error':['validated','borrowed','complete'].includes(state)?'ok':'ready');
 by('result-state').textContent=r.live_enabled?'MODE PILOT LIVE: STOK DAPAT BERUBAH':'MODE VALIDASI QR ASLI: BELUM MEMINJAM';
 setNote(r.message||state,state==='rejected'?'bad':'good');
 if(['validated','borrowed'].includes(state))setTimeout(()=>reset(false),7000);
};
async function post(url, data){const r=await fetch(url,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':cfg.csrf,'Accept':'application/json'},body:JSON.stringify(data)});
 const t=await r.text();let j={};try{j=JSON.parse(t);}catch(e){}
 if(!r.ok)throw Error(j.message||('HTTP '+r.status+'; periksa login, CSRF dan error Laravel'));return j;
}
async function scan(payload,channel){const raw=String(payload||'').trim();if(!raw||processing||completed||seen.has(raw))return;
 const now=Date.now();if(raw===prev&&now-prevAt<2600)return;
 prev=raw;prevAt=now;processing=true;
 try{const r=await post(cfg.scan,{payload:raw,channel});if(r.state!=='rejected')seen.add(raw);update(r);}catch(e){setNote('GAGAL SERVER: '+e.message,'bad');stage('SCAN ERROR',e.message,'error');}
 finally{processing=false;}
}
async function reset(manual=true){try{const r=await post(cfg.reset,{});completed=false;seen.clear();prev='';update(r);if(manual)setNote('Sesi baru siap. Silakan scan QR asli.');}catch(e){setNote('Reset gagal: '+e.message,'bad');}}
async function status(){try{const r=await fetch(cfg.status,{credentials:'same-origin',headers:{Accept:'application/json'}});if(r.ok)update(await r.json());}catch(e){setNote('Tidak dapat membaca status server.','bad');}}
const candidates=[cfg.localJs];
async function loadLib(){if(window.Html5Qrcode)return;
 for(const src of candidates){if(!src)continue;try{await new Promise((ok,no)=>{const el=document.createElement('script'),timer=setTimeout(()=>no(Error('Timeout decoder QR')),5500);el.onload=()=>{clearTimeout(timer);window.Html5Qrcode?ok():no(Error('Decoder invalid'));};el.onerror=()=>{clearTimeout(timer);no(Error('Decoder tidak tersedia'));};el.src=src;document.head.appendChild(el)});return;}catch(e){}}
 throw Error('Decoder QR tidak tersedia. Hubungkan ke internet untuk CDN atau gunakan browser BarcodeDetector / scanner USB.');
}
let busy=false;
async function nativeLoop(){if(!stream||!detector||!video)return;try{if(!busy&&video.readyState>=2){busy=true;const codes=await detector.detect(video);if(codes?.[0]?.rawValue)await scan(codes[0].rawValue,'webcam');}}catch(e){setNote('Decoder native: '+e.message,'bad');}finally{busy=false;if(stream)setTimeout(nativeLoop,300);}}
async function stopCam(){if(camera){try{await camera.stop()}catch(e){}try{await camera.clear()}catch(e){}camera=null;}
 if(stream){stream.getTracks().forEach(t=>t.stop());stream=null;}if(video){video.remove();video=null;}by('reader').replaceChildren();}
async function startCam(){await stopCam();if(!window.isSecureContext||!navigator.mediaDevices?.getUserMedia){setNote('Kamera memerlukan https:// tepercaya atau http://localhost pada PC yang sama.','bad');return;}
 try{
  if('BarcodeDetector' in window){try{const formats=await window.BarcodeDetector.getSupportedFormats();if(formats.includes('qr_code')){detector=new window.BarcodeDetector({formats:['qr_code']});stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}},audio:false});video=document.createElement('video');video.autoplay=true;video.muted=true;video.playsInline=true;video.style.cssText='width:100%;height:auto;max-height:330px';by('reader').appendChild(video);video.srcObject=stream;await video.play();setNote('Kamera aktif (decoder bawaan browser). Arahkan QR siswa atau QR buku.','good');nativeLoop();return;}}catch(e){await stopCam();}}
  await loadLib();camera=new window.Html5Qrcode('reader');
  const onRead=value=>scan(value,'webcam');
  try{await camera.start({facingMode:'environment'},{fps:8,qrbox:{width:220,height:220}},onRead,()=>{});}
  catch(e){const cams=await window.Html5Qrcode.getCameras();if(!cams.length)throw e;await camera.start(cams[0].id,{fps:8},onRead,()=>{});}
  setNote('Kamera aktif (html5-qrcode).','good');
 }catch(e){await stopCam();setNote('Kamera gagal: '+e.message,'bad');}
}
async function photo(file){if(!file)return;
 try{if('BarcodeDetector'in window){const supported=await window.BarcodeDetector.getSupportedFormats();if(supported.includes('qr_code')){const b=await createImageBitmap(file);try{const codes=await(new window.BarcodeDetector({formats:['qr_code']})).detect(b);if(!codes?.length)throw Error('Tidak ada QR pada foto');await scan(codes[0].rawValue,'photo');return;}finally{b.close?.();}}}
 await loadLib();await stopCam();const tmp=new window.Html5Qrcode('reader');try{await scan(await tmp.scanFile(file,true),'photo');}finally{try{await tmp.clear()}catch(e){}}
 }catch(e){setNote('Foto tidak dapat dibaca: '+e.message,'bad');}
}
const secure=window.isSecureContext&&!!navigator.mediaDevices?.getUserMedia;
by('camera-diagnostics').textContent=secure?'Konteks aman: API kamera tersedia. Izin tetap perlu diberikan browser.':'Kamera live diblokir pada HTTP LAN. Gunakan localhost di PC atau HTTPS tepercaya di HP.';
by('cam-start').disabled=!secure;
by('cam-start').onclick=startCam;by('cam-stop').onclick=stopCam;
by('diag-recheck').onclick=()=>location.reload();
by('reset').onclick=()=>reset(true);
by('qr-photo').onchange=e=>{photo(e.target.files?.[0]);e.target.value='';};
by('wedge').onkeydown=e=>{if(e.key==='Enter'){e.preventDefault();const s=e.target.value;e.target.value='';scan(s,'usb');}};
// Legacy demo buttons deliberately excluded from REAL QR session.
for(const id of ['sim-member-btn','sim-book-btn']){const x=by(id);if(x){x.disabled=true;x.title='Mode simulasi ditinggalkan, gunakan scan QR asli';}}
const gen=by('lab-qr-generate');if(gen)gen.onclick=async()=>{const member_id=Number(by('lab-qr-member').value);
 if(!member_id){by('lab-qr-note').textContent='Pilih akun siswa aktif terlebih dahulu.';return;}
 try{const r=await post(cfg.memberPass,{member_id});by('lab-qr-generated').innerHTML=r.svg;by('lab-qr-generated').classList.remove('hide');by('lab-qr-note').textContent='QR siswa '+r.member_label+' siap. Berlaku '+r.expires_seconds+' detik dan sekali pakai.';}catch(e){by('lab-qr-note').textContent=e.message;}
};
by('lab-health-btn').onclick=async()=>{try{const r=await fetch(cfg.health,{credentials:'same-origin'});if(!r.ok)throw Error('HTTP '+r.status);const s=(await r.json()).stats;
 const a=Number(by('snapshot-loans').textContent),b=Number(by('snapshot-stock').textContent);
 by('lab-health-result').textContent='Pinjaman: '+s.loans+' (awal '+a+'). Stok: '+s.stock_total+' (awal '+b+'). '+(cfg.live?'Perubahan dapat terjadi pada mode PILOT LIVE.':'Validasi tidak mengubah stok.');}catch(e){by('lab-health-result').textContent=e.message;}}
// Keep pairing QR refreshed; its phone route is a separate experimental channel.
async function pair(){try{const r=await post(cfg.pair,{});by('pair-qr').innerHTML=r.svg;by('pair-link').textContent=r.pair_url;}catch(e){}}
setInterval(pair,90000);
by('js-check').textContent='PATCH 006 aktif — semua scan QR asli diproses di Laravel; sesi tidak disimpan di JavaScript.';
setNote(secure?'Kamera tersedia. Klik Mulai kamera untuk scan sungguhan.':'Gunakan HTTPS/localhost untuk kamera, atau scanner USB.','good');
status();
})();
