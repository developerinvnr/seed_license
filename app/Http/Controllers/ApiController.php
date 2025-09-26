<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CoreApi;


class ApiController extends Controller
{
    public function index()
    {
        $apiData = CoreApi::all();
        return view('api_data.index', compact('apiData'));
    }

    public function sync(Request $request)
    {
        $apiUrl = 'https://core.vnrin.in/api/project/apis';
        $apiKey = '8X2Rk0bDB4BoVqcShyOmtcJ7y6PKtXuX';

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
            ])->get($apiUrl);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'Failed to fetch data from API.']);
            }

            $data = $response->json();

            if (!isset($data['api_list'])) {
                return response()->json(['success' => false, 'message' => 'Invalid API response format.']);
            }

            foreach ($data['api_list'] as $item) {
                if (!isset($item['id'])) continue;

                CoreApi::updateOrCreate(
                    ['api_id' => $item['id']],
                    [
                        'api_name'      => $item['api_name'] ?? '',
                        'api_end_point' => $item['api_end_point'] ?? '',
                        'description'   => $item['description'] ?? '',
                        'parameters'    => json_encode($item['parameters'] ?? []),
                        'table_name'    => $item['table_name'] ?? '',
                    ]
                );
            }

            $apiData = CoreApi::all();

            return response()->json([
                'success' => true,
                'message' => 'Data synced successfully!',
                'apiData' => $apiData
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

}
