<?php

namespace App\Providers;

use App\Models\Buku;
use App\Models\User;
use App\Services\BookInventory;
use App\Services\MemberIdentity;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::created(function (User $user): void {
            if ($user->role === 'user') {
                app(MemberIdentity::class)->ensure($user);
            }
        });

        Buku::created(function (Buku $book): void {
            app(BookInventory::class)->ensureCopies($book);
        });
    }
}
