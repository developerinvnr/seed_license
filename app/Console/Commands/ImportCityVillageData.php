<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportCityVillageData extends Command
{
    protected $signature = 'import:city_village';
    protected $description = 'Import city and village data from the API and save to city_village table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // API details
        $apiUrl = 'https://core.vnrin.in/api/city_village_by_state';
        $apiKey = '8X2Rk0bDB4BoVqcShyOmtcJ7y6PKtXuX';

        // Get all states from the 'state' table
        $states = DB::table('state')->get();

        $this->info('Starting to import city/village data...');

        foreach ($states as $state) {
            $this->info("Fetching data for state_id: {$state->id}");

            try {
                // Send POST request to the API
                $response = Http::withHeaders([
                    'api-key' => $apiKey,
                ])->timeout(120)->post($apiUrl, [
                    'state_id' => $state->id,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Check if 'list' key exists in the response
                    if (isset($data['list'])) {
                        $cityVillages = $data['list'];

                        DB::transaction(function () use ($cityVillages) {
                            foreach (array_chunk($cityVillages, 1000) as $chunk) {
                                foreach ($chunk as $cityVillage) {
                                    DB::table('city_village')->updateOrInsert(
                                        ['city_village_code' => $cityVillage['city_village_code']],
                                        [
                                            'state_id'          => $cityVillage['state_id'],
                                            'city_village_name' => $cityVillage['city_village_name'],
                                            'is_active'         => $cityVillage['is_active'],
                                            'created_at'        => now(),
                                            'updated_at'        => now(),
                                        ]
                                    );
                                }
                            }
                        });

                        $this->info("Successfully imported data for state_id: {$state->id}");
                    } else {
                        Log::error("Invalid response format for state_id: {$state->id}");
                    }
                } else {
                    Log::error("Failed to fetch city/village data for state_id: {$state->id}");
                }
            } catch (\Exception $e) {
                Log::error("Error fetching city/village data for state_id: {$state->id}. Error: " . $e->getMessage());
            }

            // Optional delay to avoid hitting rate limits
            sleep(1);
        }

        $this->info('City/Village data import completed successfully!');
    }
}
