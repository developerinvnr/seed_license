<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Schema;
// use Illuminate\Database\Schema\Blueprint;

// class CoreController extends Controller
// {
//     public function syncApis()
//     {
//         $apiList = DB::table('core_api_list')->get();
//         if ($apiList->isEmpty()) {
//             return response()->json(['status' => 'error', 'message' => 'No APIs found to sync.']);
//         }

//         $syncedData = [];

//         foreach ($apiList as $api) {
//             $result = $this->syncApiData($api->api_end_point, "core_" . $api->table_name);
//             if ($result['status'] === 'success') {
//                 $syncedData[] = $result['message'];
//             }
//         }

//         if (empty($syncedData)) {
//             return response()->json(['status' => 'error', 'message' => 'No data was synced.']);
//         }

//         return response()->json(['status' => 'success', 'message' => 'All APIs synced successfully.', 'synced_data' => $syncedData]);
//     }

//     public function coreApis()
//     {
//         if (Schema::hasTable('core_api_list')) {
//             $apiList = DB::table('core_api_list')->get();
//             return view('core_apis', ['api_list' => $apiList]);
//         } else {
            
//             return view('core_apis', ['api_list' => collect(), 'error' => 'API table does not exist.']);
//         }
//     }

//     public function fetchApis()
//     {
//         $url = 'https://core.vnrin.in/api/project/apis';
//         $response = Http::withHeaders([
//             'api-key' => '8X2Rk0bDB4BoVqcShyOmtcJ7y6PKtXuX',
//             'Content-Type' => 'application/json',
//         ])->get($url);

//         $apiData = $response->json();

//         if (!empty($apiData['api_list'])) {
//             foreach ($apiData['api_list'] as &$api) {
//                 $api['api_id'] = $api['id'];
//                 unset($api['id']);
//             }

//             $this->syncApiList($apiData['api_list']);

//             return response()->json(['status' => 'success', 'message' => 'API list updated']);
//         }

//         return response()->json(['status' => 'error', 'message' => 'No API data found']);
//     }

//     private function syncApiList(array $apiList)
//     {
//         $columns = array_keys($apiList[0]);
//         $this->createOrUpdateTable('core_api_list', $columns);

//         foreach ($apiList as $api) {
//             DB::table('core_api_list')->updateOrInsert(
//                 ['api_id' => $api['api_id']],
//                 $api
//             );
//         }
//     }

//     public function syncSingleApi(Request $request)
//     {
//         $apiEndPoint = $request->input('api_end_point');
//         $tableName = $request->filled('table_name') ? 'core_' . $request->input('table_name') : null;
//         $params = $request->input('params', []);

//         if (!$apiEndPoint || !$tableName) {
//             return response()->json(['status' => 'error', 'message' => 'Invalid API data.']);
//         }

//         if (!empty($params)) {
//             $apiEndPoint .= '?' . http_build_query($params);
//         }

//         return response()->json($this->syncApiData($apiEndPoint, $tableName));
//     }

//     private function syncApiData($apiEndPoint, $tableName)
//     {
//         $url = "https://core.vnrin.in/api/{$apiEndPoint}";
//         $response = Http::withHeaders([
//             'api-key' => '8X2Rk0bDB4BoVqcShyOmtcJ7y6PKtXuX',
//             'Content-Type' => 'application/json',
//         ])->get($url);

//         $data = $response->json();

//         if (!empty($data['list']) && is_array($data['list'])) {
//             $apiIds = [];

//             foreach ($data['list'] as &$row) {
//                 $row['api_id'] = $row['api_id'] ?? $row['id'];
//                 unset($row['id']);
//                 $apiIds[] = $row['api_id'];
//             }

//             $columns = array_keys($data['list'][0]);
//             $this->createOrUpdateTable($tableName, $columns);

//             foreach ($data['list'] as $row) {
//                 DB::table($tableName)->updateOrInsert(['api_id' => $row['api_id']], $row);
//             }

//             DB::table($tableName)->whereNotIn('api_id', $apiIds)->delete();

//             return ['status' => 'success', 'message' => "Data synced successfully for $apiEndPoint."];
//         }

//         return ['status' => 'error', 'message' => "No data found for $apiEndPoint."];
//     }

//     private function createOrUpdateTable($tableName, $columns)
//     {
//         if (!Schema::hasTable($tableName)) {
//             Schema::create($tableName, function (Blueprint $table) use ($columns) {
//                 $table->increments('id');
//                 $table->integer('api_id')->unique();
//                 foreach ($columns as $column) {
//                     if (!in_array($column, ['id', 'api_id'])) {
//                         $table->text($column)->nullable();
//                     }
//                 }
//             });
//         } else {
//             foreach ($columns as $column) {
//                 if (!Schema::hasColumn($tableName, $column) && $column !== 'id') {
//                     Schema::table($tableName, function (Blueprint $table) use ($column) {
//                         $table->text($column)->nullable();
//                     });
//                 }
//             }

//             if (!Schema::hasColumn($tableName, 'api_id')) {
//                 Schema::table($tableName, function (Blueprint $table) {
//                     $table->integer('api_id')->nullable();
//                 });
//             }
//         }
//     }

//     public function getApiData(Request $request)
//     {
//         $table = $request->input('table_name');

//         if (!$table || !Schema::hasTable('core_' . $table)) {
//             return response()->json(['status' => 'error', 'message' => 'Table not found.']);
//         }

//         $data = DB::table('core_' . $table)->get();

//         return response()->json(['status' => 'success', 'data' => $data]);
//     }

//     public function emptyTable(Request $request)
//     {
//         $table = 'core_' . $request->input('table_name');

//         if (Schema::hasTable($table)) {
//             DB::table($table)->truncate();
//             return response()->json(['status' => 'success', 'message' => 'Table data emptied successfully.']);
//         }

//         return response()->json(['status' => 'error', 'message' => 'Table not found.']);
//     }

//     public function dropTable(Request $request)
//     {
//         $table = 'core_' . $request->input('table_name');

//         if (Schema::hasTable($table)) {
//             Schema::drop($table);
//             DB::table('core_api_list')->where('table_name', $request->input('table_name'))->delete();
//             return response()->json(['status' => 'success', 'message' => 'API table and entry deleted successfully.']);
//         }

//         return response()->json(['status' => 'error', 'message' => 'Table not found.']);
//     }
// }


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CoreController extends Controller
{
    public function syncApis()
    {
        $apiList = DB::table('core_api_list')->get();
        if ($apiList->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No APIs found to sync.']);
        }

        $syncedData = [];

        foreach ($apiList as $api) {
            // Pass true for full sync (delete non-matching records)
            $result = $this->syncApiData($api->api_end_point, "core_" . $api->table_name, true);
            if ($result['status'] === 'success') {
                $syncedData[] = $result['message'];
            }
        }

        if (empty($syncedData)) {
            return response()->json(['status' => 'error', 'message' => 'No data was synced.']);
        }

        return response()->json(['status' => 'success', 'message' => 'All APIs synced successfully.', 'synced_data' => $syncedData]);
    }

    public function coreApis()
    {
        if (Schema::hasTable('core_api_list')) {
            $apiList = DB::table('core_api_list')->get();
            return view('core_apis', ['api_list' => $apiList]);
        } else {
            
            return view('core_apis', ['api_list' => collect(), 'error' => 'API table does not exist.']);
        }
    }

    public function fetchApis()
    {
        $url = 'https://core.vnrin.in/api/project/apis';
        $response = Http::withHeaders([
            'api-key' => '8X2Rk0bDB4BoVqcShyOmtcJ7y6PKtXuX',
            'Content-Type' => 'application/json',
        ])->get($url);

        $apiData = $response->json();

        if (!empty($apiData['api_list'])) {
            foreach ($apiData['api_list'] as &$api) {
                $api['api_id'] = $api['id'];
                unset($api['id']);
            }

            $this->syncApiList($apiData['api_list']);

            return response()->json(['status' => 'success', 'message' => 'API list updated']);
        }

        return response()->json(['status' => 'error', 'message' => 'No API data found']);
    }

    private function syncApiList(array $apiList)
    {
        $columns = array_keys($apiList[0]);
        $this->createOrUpdateTable('core_api_list', $columns);

        foreach ($apiList as $api) {
            DB::table('core_api_list')->updateOrInsert(
                ['api_id' => $api['api_id']],
                $api
            );
        }
    }

    public function syncSingleApi(Request $request)
    {
        $apiEndPoint = $request->input('api_end_point');
        $tableName = $request->filled('table_name') ? 'core_' . $request->input('table_name') : null;
        $params = $request->input('params', []);

        if (!$apiEndPoint || !$tableName) {
            return response()->json(['status' => 'error', 'message' => 'Invalid API data.']);
        }

        if (!empty($params)) {
            $apiEndPoint .= '?' . http_build_query($params);
        }

        // Pass empty($params) as $deleteOld: true for full sync (no params), false for partial (with params)
        return response()->json($this->syncApiData($apiEndPoint, $tableName, empty($params)));
    }

    private function syncApiData($apiEndPoint, $tableName, $deleteOld = true)
    {
        $url = "https://core.vnrin.in/api/{$apiEndPoint}";
        $response = Http::withHeaders([
            'api-key' => '8X2Rk0bDB4BoVqcShyOmtcJ7y6PKtXuX',
            'Content-Type' => 'application/json',
        ])->get($url);

        $data = $response->json();

        if (!empty($data['list']) && is_array($data['list'])) {
            $apiIds = [];

            foreach ($data['list'] as &$row) {
                $row['api_id'] = $row['api_id'] ?? $row['id'];
                unset($row['id']);
                $apiIds[] = $row['api_id'];
            }

            $columns = array_keys($data['list'][0]);
            $this->createOrUpdateTable($tableName, $columns);

            foreach ($data['list'] as $row) {
                DB::table($tableName)->updateOrInsert(['api_id' => $row['api_id']], $row);
            }

            // Only delete non-matching records if this is a full sync
            if ($deleteOld) {
                DB::table($tableName)->whereNotIn('api_id', $apiIds)->delete();
            }

            return ['status' => 'success', 'message' => "Data synced successfully for $apiEndPoint."];
        }

        return ['status' => 'error', 'message' => "No data found for $apiEndPoint."];
    }

    private function createOrUpdateTable($tableName, $columns)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($columns) {
                $table->increments('id');
                $table->integer('api_id')->unique();
                foreach ($columns as $column) {
                    if (!in_array($column, ['id', 'api_id'])) {
                        $table->text($column)->nullable();
                    }
                }
            });
        } else {
            foreach ($columns as $column) {
                if (!Schema::hasColumn($tableName, $column) && $column !== 'id') {
                    Schema::table($tableName, function (Blueprint $table) use ($column) {
                        $table->text($column)->nullable();
                    });
                }
            }

            if (!Schema::hasColumn($tableName, 'api_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->integer('api_id')->nullable();
                });
            }
        }
    }

    public function getApiData(Request $request)
    {
        $table = $request->input('table_name');

        if (!$table || !Schema::hasTable('core_' . $table)) {
            return response()->json(['status' => 'error', 'message' => 'Table not found.']);
        }

        $data = DB::table('core_' . $table)->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function emptyTable(Request $request)
    {
        $table = 'core_' . $request->input('table_name');

        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
            return response()->json(['status' => 'success', 'message' => 'Table data emptied successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Table not found.']);
    }

    public function dropTable(Request $request)
    {
        $table = 'core_' . $request->input('table_name');

        if (Schema::hasTable($table)) {
            Schema::drop($table);
            DB::table('core_api_list')->where('table_name', $request->input('table_name'))->delete();
            return response()->json(['status' => 'success', 'message' => 'API table and entry deleted successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Table not found.']);
    }
}