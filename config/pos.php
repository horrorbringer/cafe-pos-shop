<?php

return [
    'tax_rate' => env('POS_TAX_RATE', 0.10),
    'currency' => env('POS_CURRENCY', 'USD'),
    'cash_denominations' => [1, 2, 5, 10, 20, 50, 100],
    'receipt' => [
        'header' => env('POS_RECEIPT_HEADER', 'POS Cafe'),
        'footer' => env('POS_RECEIPT_FOOTER', 'Thank you!'),
    ],
];
