<?php

namespace App\Http\Controllers;

use App\Jobs\GoogleSpreadsheet;
use Exception;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GoogleSheetController extends Controller
{
    public function showForm()
    {
        return view('form');
    }

    public function updateSheet(Request $request)
    {
        $spreadsheetUrl = $request->input('spreadsheet_url');
        $fromDate = $request->input('start_date');
        $toDate = $request->input('end_date'); 
        
        $spreadsheetId = $this->extractSpreadsheetId($spreadsheetUrl);

        if (empty($spreadsheetId)) {
            return redirect()->route('show-form')->with('error', 'URL is invalid');
        }
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('google_credentials/credentials.json'));
        $client->setAccessType('offline');


        if (file_exists(storage_path('google_credentials/access_token.json'))) {
            $accessToken = json_decode(file_get_contents(storage_path('google_credentials/access_token.json')), true);
            $client->setAccessToken($accessToken);
        } else {
            return redirect()->away($client->createAuthUrl());
        }

        GoogleSpreadsheet::dispatch($spreadsheetId,$fromDate,$toDate);
        return redirect()->route('show-form')->with('success', 'The spreadsheet will be updated shortly.');

    }

    public function oauthCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('google_credentials/credentials.json'));
        $client->setRedirectUri('http://127.0.0.1:8000/oauth-callback');
        $client->setAccessType('offline');

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
            $client->setAccessToken($token);

            file_put_contents(storage_path('google_credentials/access_token.json'), json_encode($token));

            return redirect()->route('show-form')->with('success', 'Google sign in sign in sucessfully Please try again.');
        }

        return response()->json([
            'error' => 'Authorization failed.'
        ], 401);
    }


    private function extractSpreadsheetId($url)
    {
        try {
            $path = parse_url($url, PHP_URL_PATH);
            $segments = explode('/', $path);
            return $segments[count($segments) - 2];
        } catch (Exception $e) {
            return "";
        }
    }
}