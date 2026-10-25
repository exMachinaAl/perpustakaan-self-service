<?php
return [
    // DEFAULT: uji QR asli, bukan simulasi klik. Validasi berjalan di Laravel.
    // Aktifkan live secara sadar HANYA untuk pilot dengan petugas hadir.
    'live_loans' => env('KIOSK_ENABLE_LIVE_LOANS', false),
    'staff_pilot' => env('KIOSK_STAFF_PILOT', false),
    'session_seconds' => 45,
    'max_active_loans' => 3,
    'loan_days' => 7,
];
