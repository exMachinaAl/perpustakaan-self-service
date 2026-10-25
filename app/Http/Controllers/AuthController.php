<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\AnalyticsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman register
     */
    public function showRegister()
    {
        $prodi = Prodi::orderBy('nama', 'asc')->get();

        return view('auth.register', compact('prodi'));
    }

    /**
     * Proses register user baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:50|unique:users,nim',
            'prodi_id' => 'required|exists:prodi,id',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:10|confirmed',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',

            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',

            'prodi_id.required' => 'Program studi wajib dipilih.',
            'prodi_id.exists' => 'Program studi tidak valid.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 10 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'nim' => $request->nim,
            'prodi_id' => $request->prodi_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $this->simpanActivityLog(
            $user,
            'Registrasi User',
            'User baru berhasil melakukan registrasi akun.',
            [
                'user_id' => $user->id,
                'nama' => $user->nama,
                'nim' => $user->nim,
                'email' => $user->email,
                'prodi_id' => $user->prodi_id,
                'role' => $user->role,
                'status' => $user->status,
            ],
            $request
        );

        $this->simpanAnalyticsLog(
            $user,
            'Auth',
            'Registrasi User',
            [
                'status' => 'success',
                'email' => $user->email,
            ],
            $request
        );

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil. Silakan login.');
    }

    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['status'] = 'aktif';
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            $this->simpanActivityLog(
                $user,
                'Login',
                'User berhasil login ke sistem.',
                [
                    'email' => $user->email,
                    'role' => $user->role,
                    'remember_me' => $remember,
                ],
                $request
            );

            $this->simpanAnalyticsLog(
                $user,
                'Auth',
                'Login Berhasil',
                [
                    'status' => 'success',
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                $request
            );

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'user') {
                return redirect()->route('user.dashboard');
            }

            $this->simpanActivityLog(
                $user,
                'Login Gagal',
                'Role akun tidak dikenali.',
                [
                    'email' => $user->email,
                    'role' => $user->role,
                    'alasan' => 'Role tidak dikenali',
                ],
                $request
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Role akun tidak dikenali.',
                ]);
        }

        $this->simpanActivityLog(
            null,
            'Login Gagal',
            'Percobaan login gagal karena email atau password salah.',
            [
                'email' => $request->email,
                'alasan' => 'Email atau password salah',
            ],
            $request
        );

        $this->simpanAnalyticsLog(
            null,
            'Auth',
            'Login Gagal',
            [
                'status' => 'failed',
                'email' => $request->email,
            ],
            $request
        );

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->onlyInput('email');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        $this->simpanActivityLog(
            $user,
            'Logout',
            'User keluar dari sistem.',
            [
                'email' => $user->email ?? null,
                'role' => $user->role ?? null,
            ],
            $request
        );

        $this->simpanAnalyticsLog(
            $user,
            'Auth',
            'Logout',
            [
                'status' => 'success',
                'email' => $user->email ?? null,
                'role' => $user->role ?? null,
            ],
            $request
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Simpan activity log ke MongoDB
     */
    private function simpanActivityLog($user, string $aktivitas, string $keterangan, array $data, Request $request): void
    {
        try {
            ActivityLog::create([
                'user_id' => $user->id ?? null,
                'nama_user' => $user->nama ?? 'Guest',
                'role' => $user->role ?? 'guest',
                'aktivitas' => $aktivitas,
                'keterangan' => $keterangan,
                'data' => array_merge($data, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan ActivityLog Auth: ' . $e->getMessage());
        }
    }

    /**
     * Simpan analytics log ke MongoDB
     */
    private function simpanAnalyticsLog($user, string $halaman, string $aksi, array $data, Request $request): void
    {
        try {
            AnalyticsLog::create([
                'user_id' => $user->id ?? null,
                'role' => $user->role ?? 'guest',
                'halaman' => $halaman,
                'aksi' => $aksi,
                'data' => $data,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan AnalyticsLog Auth: ' . $e->getMessage());
        }
    }
}