<?php
namespace App\Console\Commands;
use App\Models\ReaderDevice;
use Illuminate\Console\Command;
class RegisterReaderDevice extends Command {
 protected $signature='perpus:register-reader {serial : ID unik perangkat} {name : Nama reader}';
 protected $description='Provision reader dengan secret acak; secret hanya ditampilkan satu kali';
 public function handle():int {
  $serial=$this->argument('serial');$name=$this->argument('name');
  if(!preg_match('/\A[A-Za-z0-9._-]{1,64}\z/D',$serial)){$this->error('Serial invalid');return 1;}
  if(ReaderDevice::where('serial',$serial)->exists()){$this->error('Serial sudah terdaftar; buat serial baru atau rotasi secret secara manual.');return 1;}
  $secret=bin2hex(random_bytes(32));
  ReaderDevice::create(['serial'=>$serial,'name'=>$name,'secret'=>$secret,'active'=>true]);
  $this->line('READER_ID='.$serial);
  $this->line('READER_SECRET='.$secret);
  $this->warn('Simpan secret secara aman. Jangan unggah tangkapan layar/perintah ini ke repositori.');
  return 0;
 }
}
