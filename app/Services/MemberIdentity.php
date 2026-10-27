<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class MemberIdentity
{
    public function ensure(User $user): object
    {
        $existing = DB::table('library_member_credentials')->where('user_id', $user->id)->first();
        if ($existing) {
            return $existing;
        }

        $payload = 'LIBCARD1:'.bin2hex(random_bytes(32));
        DB::table('library_member_credentials')->insert([
            'user_id' => $user->id,
            'card_hash' => hash('sha256', strtolower($payload)),
            'card_encrypted' => Crypt::encryptString($payload),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('library_member_credentials')->where('user_id', $user->id)->first();
    }

    public function payload(User $user): string
    {
        return Crypt::decryptString($this->ensure($user)->card_encrypted);
    }

    public function reissue(User $user): string
    {
        $this->ensure($user);
        $payload = 'LIBCARD1:'.bin2hex(random_bytes(32));
        DB::table('library_member_credentials')->where('user_id', $user->id)->update([
            'card_hash' => hash('sha256', strtolower($payload)),
            'card_encrypted' => Crypt::encryptString($payload),
            'revoked_at' => null,
            'updated_at' => now(),
        ]);

        return $payload;
    }

    public function setPin(User $user, string $pin): void
    {
        $this->ensure($user);
        DB::table('library_member_credentials')->where('user_id', $user->id)->update([
            'pin_hash' => Hash::make($pin),
            'updated_at' => now(),
        ]);
    }

    public function verifyPin(int $userId, string $pin): bool
    {
        $credential = DB::table('library_member_credentials')
            ->where('user_id', $userId)->whereNull('revoked_at')->first();

        return $credential && $credential->pin_hash && Hash::check($pin, $credential->pin_hash);
    }
}
