<?php
namespace App\Http\Controllers;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
class ForgotPasswordController extends Controller {
    public function showForgotForm() { return view('auth.lupa-password'); }
    public function checkEmail(Request $request) {
        $request->validate(['email'=>'required|email|max:255']);
        // Jangan bocorkan apakah alamat terdaftar; mailer harus disetel oleh operator.
        try { Password::sendResetLink($request->only('email')); }
        catch (\Throwable $exception) { Log::error('Gagal mengirim email reset password', ['error'=>$exception->getMessage()]); }
        return back()->with('success','Jika alamat terdaftar, tautan reset password telah dikirim melalui email.');
    }
    public function showResetForm(Request $request, string $token) {
        $email = (string)$request->query('email','');
        return view('auth.reset-password', ['email'=>$email,'token'=>$token]);
    }
    public function resetPassword(Request $request) {
        $data = $request->validate([
            'token'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|string|min:10|confirmed',
        ]);
        $status = Password::reset($data,function(User $user,string $password) {
            $user->forceFill(['password'=>Hash::make($password),'remember_token'=>Str::random(60)])->save();
            event(new PasswordReset($user));
            try { ActivityLog::create(['user_id'=>$user->id,'nama_user'=>$user->nama,'role'=>$user->role,'aktivitas'=>'Reset Password','keterangan'=>'Password diubah via token email','data'=>[],'waktu'=>now()]); }
            catch (\Throwable $e) { Log::warning('Gagal merekam log reset password',['error'=>$e->getMessage()]); }
        });
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success','Password telah diubah. Silakan login.')
            : back()->withErrors(['email'=>'Token tidak valid atau telah kedaluwarsa.'])->onlyInput('email');
    }
}
