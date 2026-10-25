'use strict';
(()=>{
 const cfg=window.KIOSK_DEVICE, el=id=>document.getElementById(id);
 let reader=null,video=null,stream=null,detector=null,busy=false,complete=false,last='',lastTime=0;
 const message=(s)=>{el('scan-note').textContent=s;};
 const draw=(res)=>{
  const state=res.state||'ready'; el('stage').className='stage '+(state==='rejected'?'error':state==='validated'?'success':'');
  const titles={ready:'READY — SCAN QR',waiting_member:'SCAN QR ANGGOTA',waiting_book:'SCAN QR BUKU',validated:'QR TERVALIDASI',rejected:'SCAN DITOLAK'};
  el('state-title').textContent=titles[state]||state.toUpperCase();
  el('message').textContent=res.message||'';
  el('member').textContent=res.member||'—'; el('book').textContent=res.book||'—';
  complete=state==='validated'; message(res.message||state);
 };
 async function api(url,body){
  const r=await fetch(url,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':cfg.csrf,'Accept':'application/json'},body:JSON.stringify(body)});
  if(r.status===401){location.href='/kiosk/setup';throw Error('Perangkat harus diaktifkan ulang');}
  let j;try{j=await r.json()}catch(e){throw Error('Respons bukan JSON, HTTP '+r.status)}
  if(!r.ok)throw Error(j.message||('HTTP '+r.status));return j;
 }
 async function scan(value,channel){
  const s=String(value||'').trim();if(!s||busy||complete)return;
  if(s===last&&Date.now()-lastTime<2500)return;
  last=s;lastTime=Date.now();busy=true;
  try{draw(await api(cfg.scan,{payload:s,channel}));}catch(e){message('Server: '+e.message);}finally{busy=false;}
 }
 async function reset(){try{draw(await api(cfg.reset,{}));last='';complete=false;}catch(e){message('Reset: '+e.message)}}
 el('reset').onclick=reset;
 el('wedge').onkeydown=e=>{if(e.key==='Enter'){e.preventDefault();const s=e.target.value;e.target.value='';scan(s,'usb');}};
 const libSources=[cfg.js,'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js'];
 async function loadLibrary(){if(window.Html5Qrcode)return;for(const src of libSources){try{await new Promise((yes,no)=>{const script=document.createElement('script');script.src=src;script.onload=()=>window.Html5Qrcode?yes():no(Error('Library invalid'));script.onerror=no;document.head.appendChild(script);});return;}catch(e){}}throw Error('Decoder QR tidak tersedia; gunakan browser pendukung BarcodeDetector atau scanner USB.');}
 async function stop(){if(reader){try{await reader.stop()}catch(e){}try{await reader.clear()}catch(e){}reader=null;}
  if(stream){stream.getTracks().forEach(t=>t.stop());stream=null;}if(video){video.remove();video=null;}el('reader').replaceChildren();}
 async function nativeLoop(){if(!stream||!video||!detector)return;try{if(!busy&&video.readyState>=2){const found=await detector.detect(video);if(found?.length)await scan(found[0].rawValue,'webcam');}}catch(e){message('Decoder: '+e.message)}if(stream)setTimeout(nativeLoop,350);}
 async function start(){await stop();if(!window.isSecureContext||!navigator.mediaDevices?.getUserMedia){message('Akses kamera harus melalui HTTPS tepercaya.');return;}
  try{if('BarcodeDetector'in window){const formats=await BarcodeDetector.getSupportedFormats();if(formats.includes('qr_code')){detector=new BarcodeDetector({formats:['qr_code']});stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}},audio:false});video=document.createElement('video');video.autoplay=true;video.muted=true;video.playsInline=true;video.style.width='100%';el('reader').appendChild(video);video.srcObject=stream;await video.play();nativeLoop();message('Kamera aktif (decoder browser).');return;}}
  await loadLibrary();reader=new Html5Qrcode('reader');await reader.start({facingMode:'environment'},{fps:8,qrbox:220},s=>scan(s,'webcam'),()=>{});message('Kamera aktif.');}catch(e){await stop();message('Kamera gagal: '+e.message);}}
 el('camera-start').onclick=start;el('camera-stop').onclick=stop;
 el('photo').onchange=async e=>{const f=e.target.files?.[0];e.target.value='';if(!f)return;try{
  if('BarcodeDetector'in window){const formats=await BarcodeDetector.getSupportedFormats();if(formats.includes('qr_code')){const b=await createImageBitmap(f);try{const codes=await(new BarcodeDetector({formats:['qr_code']})).detect(b);if(!codes.length)throw Error('QR tidak terdeteksi');await scan(codes[0].rawValue,'photo');return;}finally{b.close();}}}
  await stop();await loadLibrary();const temp=new Html5Qrcode('reader');try{await scan(await temp.scanFile(f,true),'photo');}finally{try{await temp.clear()}catch(e){}}}catch(e){message('Foto gagal: '+e.message)}};
 fetch(cfg.status,{credentials:'same-origin',headers:{Accept:'application/json'}}).then(r=>r.status===401?(location.href='/kiosk/setup',null):r.json()).then(v=>{if(v)draw(v)}).catch(e=>message(e.message));
 setInterval(()=>{if(!busy)fetch(cfg.status,{credentials:'same-origin',headers:{Accept:'application/json'}}).then(r=>r.ok?r.json():null).then(v=>{if(v&&!complete)draw(v)}).catch(()=>{});},5000);
})();
