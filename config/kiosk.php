<?php
return [
    // Address opened by STUDENT PHONES, not 'localhost' unless testing on the same PC.
    // Example: https://perpus.example.sch.id (trusted TLS and reachable from the phones).
    'phone_base_url' => env('KIOSK_PHONE_BASE_URL', env('APP_URL', 'http://localhost')),
];
