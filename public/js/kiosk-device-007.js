'use strict';
(()=>{
 const cfg=window.KIOSK_DEVICE, el=id=>document.getElementById(id);
 let reader=null,video=null,stream=null,detector=null,busy=false,complete=false,last='',lastTime=0,requestId=null;
 const formats=['qr_code','ean_13','ean_8','code_128','code_39','upc_a','upc_e'];
 const message=s=>{el('scan-note').textContent=s;};
 const uuid=()=>crypto.randomUUID?crypto.randomUUID():'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,c=>{const r=Math.random()*16|0,v=c==='x'?r:(r&3|8);return v.toString(16)});
 const draw=res=>{
  const state=res.state||'ready';
  el('stage').className='stage '+(state==='rejected'?'error':(state==='ready_checkout'||state==='borrowed')?'success':'');
  const titles={ready:'SIAP — SCAN',waiting_member:'SCAN KARTU ANGGOTA',waiting_pin:'MASUKKAN PIN',waiting_book:'SCAN BUKU',ready_checkout:'SIAP DIPINJAM',borrowed:'PEMINJAMAN BERHASIL',rejected:'SCAN DITOLAK'};
  el('state-title').textContent=titles[state]||state.toUpperCase();
  el('message').textContent=res.message||'';el('member').textContent=res.member||'—';el('book').textContent=res.book||'—';
  el('pin-panel').hidden=!res.needs_pin;el('checkout').hidden=!res.can_checkout;
  complete=state==='borrowed';message(res.message||state);
  if(res.needs_pin)setTimeout(()=>el('member-pin').focus(),50);
 };
 async function api(url,body){
  const r=await fetch(url,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':cfg.csrf,'Accept':'application/json'},body:JSON.stringify(body)});
  if(r.status===401){location.href='/kiosk/setup';throw Error('Perangkat harus diaktifkan ulang');}
  let j;try{j=await r.json()}catch(e){throw Error('Respons bukan JSON, HTTP '+r.status)}
  if(!r.ok)throw Error(j.message||Object.values(j.errors||{})[0]?.[0]||('HTTP '+r.status));return j;
 }
 async function scan(value,channel){
  const s=String(value||'').trim();if(!s||busy||complete)return;if(s===last&&Date.now()-lastTime<2500)return;
  last=s;lastTime=Date.now();busy=true;try{draw(await api(cfg.scan,{payload:s,channel}));}catch(e){message('Server: '+e.message);}finally{busy=false;}
 }
 async function reset(){try{draw(await api(cfg.reset,{}));last='';complete=false;requestId=null;el('member-pin').value='';}catch(e){message('Reset: '+e.message)}}
 el('reset').onclick=reset;
 el('pin-submit').onclick=async()=>{const pin=el('member-pin').value;if(!/^\d{6}$/.test(pin)){message('PIN harus 6 angka.');return;}try{draw(await api(cfg.authenticate,{pin}));el('member-pin').value='';}catch(e){message(e.message)}};
 el('member-pin').onkeydown=e=>{if(e.key==='Enter'){e.preventDefault();el('pin-submit').click();}};
 el('checkout').onclick=async()=>{if(busy)return;busy=true;requestId=requestId||uuid();try{draw(await api(cfg.checkout,{request_id:requestId}));}catch(e){message(e.message)}finally{busy=false;}};
 el('wedge').onkeydown=e=>{if(e.key==='Enter'){e.preventDefault();const s=e.target.value;e.target.value='';scan(s,'usb');}};
 const libSources=[cfg.js];
 async function loadLibrary(){if(window.Html5Qrcode)return;for(const src of libSources){try{await new Promise((yes,no)=>{const script=document.createElement('script');script.src=src;script.onload=()=>window.Html5Qrcode?yes():no(Error('Library invalid'));script.onerror=no;document.head.appendChild(script);});return;}catch(e){}}throw Error('Decoder tidak tersedia; gunakan scanner USB.');}
 async function stop(){if(reader){try{await reader.stop()}catch(e){}try{await reader.clear()}catch(e){}reader=null;}if(stream){stream.getTracks().forEach(t=>t.stop());stream=null;}if(video){video.remove();video=null;}el('reader').replaceChildren();}
 async function nativeLoop(){if(!stream||!video||!detector)return;try{if(!busy&&video.readyState>=2){const found=await detector.detect(video);if(found?.length)await scan(found[0].rawValue,'webcam');}}catch(e){message('Decoder: '+e.message)}if(stream)setTimeout(nativeLoop,350);}
 async function start(){await stop();if(!window.isSecureContext||!navigator.mediaDevices?.getUserMedia){message('Kamera memerlukan HTTPS tepercaya.');return;}try{if('BarcodeDetector'in window){const supported=await BarcodeDetector.getSupportedFormats(),usable=formats.filter(f=>supported.includes(f));if(usable.length){detector=new BarcodeDetector({formats:usable});stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}},audio:false});video=document.createElement('video');video.autoplay=true;video.muted=true;video.playsInline=true;video.style.width='100%';el('reader').appendChild(video);video.srcObject=stream;await video.play();nativeLoop();message('Kamera aktif.');return;}}await loadLibrary();reader=new Html5Qrcode('reader');await reader.start({facingMode:'environment'},{fps:8,qrbox:220,formatsToSupport:window.Html5QrcodeSupportedFormats?formats.map(f=>window.Html5QrcodeSupportedFormats[f.toUpperCase()]).filter(Number.isInteger):undefined},s=>scan(s,'webcam'),()=>{});message('Kamera aktif.');}catch(e){await stop();message('Kamera gagal: '+e.message);}}
 el('camera-start').onclick=start;el('camera-stop').onclick=stop;
 el('photo').onchange=async e=>{const f=e.target.files?.[0];e.target.value='';if(!f)return;try{await stop();await loadLibrary();const temp=new Html5Qrcode('reader');try{await scan(await temp.scanFile(f,true),'photo');}finally{try{await temp.clear()}catch(e){}}}catch(e){message('Foto gagal: '+e.message)}};
 fetch(cfg.status,{credentials:'same-origin',headers:{Accept:'application/json'}}).then(r=>r.status===401?(location.href='/kiosk/setup',null):r.json()).then(v=>{if(v)draw(v)}).catch(e=>message(e.message));
 setInterval(()=>{if(!busy)fetch(cfg.status,{credentials:'same-origin',headers:{Accept:'application/json'}}).then(r=>r.ok?r.json():null).then(v=>{if(v)draw(v)}).catch(()=>{});},5000);
})();
