<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class EnsureAccountActive {
    public function handle(Request $request, Closure $next) {
        if (!$request->user() || $request->user()->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email'=>'Akun tidak aktif atau diblokir.']);
        }
        return $next($request);
    }
}
