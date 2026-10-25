<?php
return [
    // Always off on install. Admin must enable explicitly for an attended pilot.
    'enabled' => env('KIOSK_PILOT_CHECKOUT', false),
    'loan_days' => 7, // In sync with existing LoanWorkflow return/fine calculation.
    'max_active_loans' => 3,
];
