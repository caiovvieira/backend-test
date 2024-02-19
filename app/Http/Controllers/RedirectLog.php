<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RedirectLog as ModelsRedirectLog;
use Hashids\Hashids;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RedirectLog extends Controller
{
    public function logs(Request $request)
    {
        try {
            $hashids = new Hashids('', 12);
            $code = $request->redirect;
            $logs = ModelsRedirectLog::where("redirect_id", $hashids->decode($code))->first();

            if (!$logs) {
                return response(['message' => 'Unable to list URLs'], 500)
                ->header('Content-Type', 'application/json');
            }

            return response()->json($logs);

        } catch (\Throwable $th) {
            return response(['message' => 'Unable to list URLs'], 500)
                ->header('Content-Type', 'application/json');
        }
    }

    public function stats(Request $request)
    {
        try {
            $hashids = new Hashids('', 12);
            $code = $request->redirect;
            $stats = ModelsRedirectLog::where("redirect_id", $hashids->decode($code))->get();

            if (!$stats) {
                return response(['message' => 'Unable to list URLs'], 500)
                ->header('Content-Type', 'application/json');
            }

            $databaseQuery = DB::table('redirect_logs')->value('request_header');

            $last10Days = DB::table('redirect_logs')
            ->select('*')
            ->whereRaw("created_at < DATE_SUB('" .Carbon::now() . "', INTERVAL 10 DAY)")
            ->count();

            $uniqueIds = DB::table('redirect_logs')
            ->select('request_ip', DB::raw('count(*) as total'))
            ->whereRaw("created_at < DATE_SUB('" .Carbon::now() . "', INTERVAL 10 DAY)")
            ->havingRaw('SUM(request_ip)')
            ->groupBy('request_ip')
            ->count();
           
            
            $data = [
                "total_hits" => $stats->count(),
                "total_unique_accesses" => $stats->groupBy('request_ip')->count(),
                "top_referrers" => $databaseQuery,
                "last_hits" => [
                    "date"=>Carbon::now()->subDays(10)->format('Y-m-d'),
                    "total"=>$last10Days, 
                    "unique"=>$uniqueIds 
                ]
            ];

            return response()->json($data);
            return response()->json($stats);

        } catch (\Throwable $th) {
            return response(['message' => $th->getMessage()], 500)
            ->header('Content-Type', 'application/json');

            return response(['message' => 'Unable to list URLs'], 500)
                ->header('Content-Type', 'application/json');
        }
    }
}
