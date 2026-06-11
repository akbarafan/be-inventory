<?php

return [

    'spreadsheet_id' => env('GOOGLE_SHEETS_ID'),

    'credentials' => storage_path('app/' . env('GOOGLE_SHEETS_CREDENTIALS')),

    'sheets' => [
        'barang' => env('GOOGLE_SHEETS_SHEET_BARANG', 'Barang'),
        'logs' => env('GOOGLE_SHEETS_SHEET_LOGS', 'ScanLogs'),
    ]

];
