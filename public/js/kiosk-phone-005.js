'use strict';
(() => {
const cfg=window.kioskPhone005;let cam=null, busy=false,loader=null;
const el=id=>document.getElementById(id), message=text=>el('result').textContent=text;
const sources=cfg.allowCdn?[cfg.jsLocal,'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js','https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js']:[cfg.jsLocal];
async function getLib(){
 if(window.Html5Qrcode)return window.Html5Qrcode;
 if(!loader)loader=(async()=>{
  for(const src of sources){try{await new Promise((resolve,reject)=>{
   const script=document.createElement('script');let settled=false;
   const timer=setTimeout(()=>finish(false),6500);
   function finish(ok){if(settled)return;settled=true;clearTimeout(timer);if(!ok)script.remove();ok?resolve():reject(new Error('Load gagal'));}
   script.onload=()=>finish(!!window.Html5Qrcode);script.onerror=()=>finish(false);
   script.src=src;document.head.appendChild(script);
  });if(window.Html5Qrcode)return window.Html5Qrcode;}catch(e){}}
  throw new Error('Decoder QR gagal dimuat. Hubungkan internet atau pasang html5-qrcode.min.js lokal.');
 })();
 try{return await loader;}catch(e){loader=null;throw e;}
}
function diag(){const secure=window.isSecureContext,camAvailable=!!navigator.mediaDevices?.getUserMedia;
 el('go').disabled=!(secure&&camAvailable);
 el('camera-advice').textContent=secure&&camAvailable?'Kamera tersedia secara teknis. Klik Mulai scan buku dan izinkan kamera.':
 'Live camera terblokir karena alamat HTTP LAN/browser. Pilih foto QR dari galeri atau buka situs melalui HTTPS tepercaya.';
}
async function stop(){if(!cam)return;const scanner=cam;cam=null;try{await scanner.stop();}catch(e){}try{await scanner.clear();}catch(e){}}
async function send(payload){
 if(busy)return;busy=true;
 try{const r=await fetch(cfg.url,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':cfg.csrf,'Accept':'application/json'},body:JSON.stringify({payload})});
  let j={};try{j=await r.json();}catch(e){}
  if(!r.ok)throw new Error(j.message||'QR tidak valid / sesi kedaluwarsa (HTTP '+r.status+')');
  message('Buku dikenali: '+j.result.label+' — DRY RUN. Belum ada peminjaman.');await stop();
 }catch(e){message('Gagal: '+e.message);busy=false;}
}
el('go').onclick=async()=>{if(cam)return;try{
 if(!window.isSecureContext||!navigator.mediaDevices?.getUserMedia)throw new Error('Gunakan HTTPS tepercaya / localhost.');
 const Cls=await getLib();cam=new Cls('reader');
 try{await cam.start({facingMode:'environment'},{fps:8,qrbox:220},send,()=>{});}
 catch(e){const cameras=await Cls.getCameras();if(!cameras.length)throw e;await cam.start(cameras[0].id,{fps:8,qrbox:220},send,()=>{});}
 message('Kamera aktif: arahkan ke QR stiker buku.');
 }catch(e){await stop();message('Kamera gagal: '+e.message+' — coba foto/galeri.');}
};
el('photo').addEventListener('change',async e=>{const file=e.target.files?.[0];if(!file)return;
 try{const Cls=await getLib();await stop();const reader=new Cls('reader');let result;
  try{result=await reader.scanFile(file,true);}finally{try{await reader.clear();}catch(e){}}
  await send(result);
 }catch(error){message('Foto QR gagal dibaca: '+error.message);}
 finally{e.target.value='';}
});
diag();
})();
