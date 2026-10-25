<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\AnalyticsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function edit()
    {
        $this->simpanAnalyticsLog('Profil User', 'User membuka halaman profil', [
            'user_id' => Auth::id(),
        ]);

        return view('user.profil.index');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'nama' => 'required|string|max:255',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:10|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'foto_profile.image' => 'File harus berupa gambar.',
            'foto_profile.mimes' => 'Foto harus berformat jpg, jpeg, png, atau webp.',
            'foto_profile.max' => 'Ukuran foto maksimal 2MB.',
            'current_password.required_with' => 'Password lama wajib diisi jika ingin mengubah password.',
            'new_password.min' => 'Password baru minimal 10 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sama.',
        ]);

        $dataLama = [
            'nama' => $user->nama,
            'foto_profile' => $user->foto_profile,
        ];

        $passwordDiubah = false;
        $fotoDiubah = false;

        $user->nama = $request->nama;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                $this->simpanActivityLog('Gagal Update Profil', 'User gagal mengubah password karena password lama salah', [
                    'user_id_target' => $user->id,
                    'email' => $user->email,
                    'alasan' => 'Password lama tidak sesuai',
                ]);

                return back()
                    ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                    ->withInput();
            }

            $user->password = Hash::make($request->new_password);
            $passwordDiubah = true;
        }

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }

            $path = $request->file('foto_profile')->store('profile', 'public');
            $user->foto_profile = $path;
            $fotoDiubah = true;
        }

        $user->save();

        $this->simpanActivityLog('Update Profil', 'User memperbarui profil', [
            'user_id_target' => $user->id,
            'email' => $user->email,
            'data_lama' => $dataLama,
            'data_baru' => [
                'nama' => $user->nama,
                'foto_profile' => $user->foto_profile,
                'password_diubah' => $passwordDiubah,
                'foto_diubah' => $fotoDiubah,
            ],
        ]);

        return back()->with('success', 'Profile berhasil diperbarui.');
    }

    public function destroyPhoto()
    {
        $user = User::findOrFail(Auth::id());

        $fotoLama = $user->foto_profile;

        if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        $user->foto_profile = null;
        $user->save();

        $this->simpanActivityLog('Hapus Foto Profil', 'User menghapus foto profil', [
            'user_id_target' => $user->id,
            'email' => $user->email,
            'foto_lama' => $fotoLama,
        ]);

        return back()->with('success', 'Foto profile berhasil dihapus.');
    }

    private function simpanActivityLog(string $aktivitas, string $keterangan, array $data = []): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'nama_user' => Auth::user()->nama ?? Auth::user()->name ?? 'User',
                'role' => Auth::user()->role ?? 'user',
                'aktivitas' => $aktivitas,
                'keterangan' => $keterangan,
                'data' => array_merge($data, [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan ActivityLog Profile: ' . $e->getMessage());
        }
    }

    private function simpanAnalyticsLog(string $halaman, string $aksi, array $data = []): void
    {
        try {
            AnalyticsLog::create([
                'user_id' => Auth::id(),
                'role' => Auth::user()->role ?? 'user',
                'halaman' => $halaman,
                'aksi' => $aksi,
                'data' => $data,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan AnalyticsLog Profile: ' . $e->getMessage());
        }
    }
}