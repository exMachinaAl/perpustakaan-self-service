<?php

return [
    'loan_days' => (int) env('LIBRARY_LOAN_DAYS', 7),
    'max_active_loans' => (int) env('LIBRARY_MAX_ACTIVE_LOANS', 3),
    'fine_per_day' => (int) env('LIBRARY_FINE_PER_DAY', 2000),
    'kiosk_session_seconds' => (int) env('LIBRARY_KIOSK_SESSION_SECONDS', 90),
];
