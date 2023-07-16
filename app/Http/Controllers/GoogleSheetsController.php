<?php

namespace App\Http\Controllers;

use App\Models\Insert_gsheet;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Sheets;


class GoogleSheetsController extends Controller
{
     public function auth()
    {
        $client = new Google_Client();
        $client->setAuthConfig(config('services.google_sheets.client_secret'));
        $client->setRedirectUri(config('services.google_sheets.redirect'));
        $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        return redirect()->to($client->createAuthUrl());
    }

    public function callback(Request $request)
    {
        $client = new Google_Client();
        $client->setAuthConfig(config('services.google_sheets.client_secret'));
        $client->setRedirectUri(config('services.google_sheets.redirect'));
        $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        if ($request->query('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->query('code'));
            $client->setAccessToken($token);

            // Save the access token to use it later for API requests
            // You can store it in the session, database, or any other storage mechanism
            session(['google_sheets_access_token' => $token]);

            return redirect()->route('google-sheets.data');
        }

        return redirect()->route('google-sheets.auth');
    }

    public function getData()
    {
        $client = new Google_Client();
        $client->setAuthConfig(config('services.google_sheets.client_secret'));
        $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        // Set the access token retrieved from the callback
        // $client->setAccessToken(session('google_sheets_access_token'));

        $service = new Google_Service_Sheets($client);
        $spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $range = 'Sheet1!A2:D';

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        // Process the retrieved data as per your requirements
        foreach ($values as $row) {
        $columnName = $row[0]; // Assuming the first column is the unique identifier
        $updatedValue = $row[1]; // Assuming the second column is the updated value

        $existingRecords = Insert_gsheet::all();

        // Find the corresponding record in the existing records
        $record = $existingRecords->where('cust_name', $columnName)->first();
        $existingColumnNames = $existingRecords->pluck('cust_name')->toArray();

        if ($record) {
            // Update the record in the database
            $record->status = $updatedValue;
            $record->save();
        }elseif (!in_array($columnName, $existingColumnNames)) {
            // Insert a new record into the database
                Insert_gsheet::create([
                'cust_name' => $columnName,
                'status' => $updatedValue,
                // Add other columns as necessary
            ]);
        }
    }

    return response()->json(['message' => 'Database updated successfully']);
    }
}
