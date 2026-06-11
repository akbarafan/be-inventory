<?php
namespace App\Services;

use App\Models\Barang;

class GoogleSheetsService
{
    public function appendBarang(Barang $barang): void
    {
        // Implement Google Sheets API integration here
        // Requires GOOGLE_SHEETS_SPREADSHEET_ID and credentials in .env
        if (!config('google-sheets.spreadsheet_id')) {
            \Log::info('Google Sheets not configured, skipping sync.');
            return;
        }
        // TODO: implement actual sheets sync
    }
}
