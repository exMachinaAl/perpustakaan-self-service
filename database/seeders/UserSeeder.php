<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder {
    public function run(): void {
        $email=env('BOOTSTRAP_ADMIN_EMAIL'); $password=env('BOOTSTRAP_ADMIN_PASSWORD');
        if (!$email || !$password || strlen($password)<12) {
            $this->command?->warn('Akun admin tidak dibuat. Set BOOTSTRAP_ADMIN_EMAIL dan BOOTSTRAP_ADMIN_PASSWORD (min 12 karakter) untuk provisioning satu kali.');
            return;
        }
        User::firstOrCreate(['email'=>$email],[
            'nama'=>'Administrator Perpustakaan','nim'=>'ADMIN-'.strtoupper(bin2hex(random_bytes(5))),
            'password'=>Hash::make($password),'role'=>'admin','status'=>'aktif','prodi_id'=>null,
        ]);
    }
}
