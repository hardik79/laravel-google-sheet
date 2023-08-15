<?php

namespace App\Jobs;

use Exception;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_Request;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Illuminate\Support\Facades\Log;

class GoogleSpreadsheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $spreadsheetId, $fromDate, $toDate;

    public function __construct($spreadsheetId, $fromDate, $toDate)
    {
        $this->spreadsheetId = $spreadsheetId;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function handle()
    {
        Log::info("Spreadsheet Updates start");

        $client = new Google_Client();

        $client->setApplicationName('Google Sheets API');

        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);

        $client->setAuthConfig(storage_path('google_credentials/credentials.json'));

        $client->setAccessType('offline');
        $accessToken = json_decode(file_get_contents(storage_path('google_credentials/access_token.json')), true);

        $client->setAccessToken($accessToken);
        $service = new Google_Service_Sheets($client);

        // $range = 'Sheet1';

        // $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);

        // $values = $response->getValues();

        $statsData = $this->getStatsFromlookerStudio($this->fromDate, $this->toDate);

        if (count($statsData) > 0) {
            $updatedRows = [];
            // $updatedRows[] = ['domain', 'date', 'initial_searches', 'feed_searches', 'monetized_searches', 'clicks', 'revenue'];
            foreach ($statsData as $key => $value) {
                $updatedRows[$value['domain']][] = [$value['domain'], $value['date'], $value['initial_searches'], $value['feed_searches'], $value['monetized_searches'], $value['clicks'], $value['revenue']];
            }

            foreach ($updatedRows as $domain => $rows) {
                $outputRows = [];
                $outputRows[] = ['domain', 'date', 'initial_searches', 'feed_searches', 'monetized_searches', 'clicks', 'revenue'];
                foreach ($rows as $key => $row) {
                    $outputRows[] = $row;
                }

                $body = new Google_Service_Sheets_ValueRange([
                    'values' => $outputRows
                ]);
                $params = [
                    'valueInputOption' => 'RAW'
                ];

                try {
                    $range = $domain;
                    // Create a new sheet with the domain name as the title
                    $requests = [
                        new Google_Service_Sheets_Request([
                            'addSheet' => [
                                'properties' => [
                                    'title' => $domain,
                                ],
                            ],
                        ]),
                    ];
                    $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                        'requests' => $requests,
                    ]);
                    $service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);

                    // Update the sheet with the data
                    $result = $service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);
                    Log::info("Spreadsheet Updated Successfully");
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

    private function getStatsFromlookerStudio($fromDate, $toDate)
    {
        try {
            $client = new Client();

            $response = $client->get('https://www.trackingapis.com/trafficSources/feeds/stats', [
                'headers' => [
                    'ApiUsername' => 'digitalFuture_api',
                    'ApiPassword' => '6d83e6f9a92bc9279ed8e9ad32c5810811ad0f11',
                ],
                'query' => [
                    'from' => $fromDate,
                    'to' => $toDate,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] == 'OK' || count($data['output']) > 0) {
                return $data['output'];
            } else {
                return [];
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
