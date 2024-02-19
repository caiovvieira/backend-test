<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUrl;
use App\Http\Requests\UpdateUrl;
use App\Models\Redirect as ModelsRedirect;
use App\Models\RedirectLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Hashids\Hashids;
use Carbon\Carbon;

class Redirect extends Controller
{
    public function index()
    {
        try {
            $urls = ModelsRedirect::get();
            return response()->json($urls);
        } catch (\Throwable $th) {
            return response(['message' => 'Unable to list URLs'], 500)
                ->header('Content-Type', 'application/json');
        }
    }

    public function redirect(Request $request)
    {
        try {
            $redirectTo = ModelsRedirect::where("code", $request->redirect)->first();

            if (!$redirectTo->status) {
                return response(['message' => 'Unable to redirect url is disabled'], 500)
                    ->header('Content-Type', 'application/json');
            }

            $querys = array_filter($request->query());

            $redirectLog = RedirectLog::create([
                "redirect_id" => $redirectTo->id,
                "request_ip" => $request->ip(),
                "request_user_agent" => $request->userAgent(),
                "request_header" => $request->headers->get('referer'),
                "request_query_params" => json_encode($querys),
                "date_time_acess" => Carbon::now()
            ]);

            ModelsRedirect::where('code', $request->redirect)->update(['last_acess' => Carbon::now()]);

            $url = $redirectTo->url;


            if (sizeof($querys)) {

                $querysRequest = "";

                foreach ($querys as $key => $value) {

                    if (empty($querysRequest)) {
                        $querysRequest .= "?$key=$value";
                    } else {
                        $querysRequest .= "&$key=$value";
                    }
                }

                $url .= "$querysRequest";
            };

            return redirect()->away($url);
        } catch (\Throwable $th) {
            return response(['message' => 'Unable to redirect url'], 404)
                ->header('Content-Type', 'application/json');
        }
    }

    public function store(StoreUrl $request)
    {
        try {
            $url = $request->url;
            $localPath = env('APP_URL');

            $stringFormatted = substr($url, 8);
            $positionString = strpos($stringFormatted, "/");
            $requestUrl = substr($stringFormatted, 0, $positionString);

            if ($requestUrl === substr($localPath, 8)) {
                return response(['message' => 'Unable to save url'], 400)
                    ->header('Content-Type', 'application/json');
            }

            $response = Http::get("$url");

            if ($response->status() !== 200) {
                $response->throw();
            }

            $hashids = new Hashids('', 12);

            $storeUrl = ModelsRedirect::create([
                "url" => $url,
            ]);

            $modelsRedirect = ModelsRedirect::find($storeUrl->id);
            $modelsRedirect->code = $hashids->encode($storeUrl->id);
            $modelsRedirect->save();


            return response()->json(ModelsRedirect::find($storeUrl->id));
        } catch (\Throwable $th) {
            return response(['message' => 'Unable to save url'], 404)
                ->header('Content-Type', 'application/json');
        }
    }

    public function show(UpdateUrl $request)
    {
        try {
            $body = $request->only(['url', 'status']);

            if (!sizeof($body)) {
                return response(['message' => 'Unable to update url'], 400)
                    ->header('Content-Type', 'application/json');
            }

            ModelsRedirect::where('code', $request->redirect)->update([...$body]);
        } catch (\Throwable $th) {
            return response(['message' => 'Unable to update url'], 500)
                ->header('Content-Type', 'application/json');
        }
    }

    public function update(UpdateUrl $request)
    {
        try {
            $body = $request->only(['url', 'status']);

            if (!sizeof($body)) {
                return response(['message' => 'Unable to update url'], 400)
                    ->header('Content-Type', 'application/json');
            }

            $updateRedirect = ModelsRedirect::where('code', $request->redirect)->first();

            if (!$updateRedirect) {
                return response(['message' => 'url not found'], 404)
                    ->header('Content-Type', 'application/json');
            };

            ModelsRedirect::where('code', $request->redirect)->update([...$body]);

            return response(['message' => 'Url successfully updated'], 200)
                ->header('Content-Type', 'application/json');
        } catch (\Throwable $th) {
            return response(['message' => 'Unable to update url'], 500)
                ->header('Content-Type', 'application/json');
        }
    }

    public function delete(Request $request)
    {
        try {
            $deleteRedirect = ModelsRedirect::where('code', $request->redirect)->first();

            if (!$deleteRedirect) {
                return response(['message' => 'url not found'], 404)
                    ->header('Content-Type', 'application/json');
            };

            ModelsRedirect::where('code', $request->redirect)->update(['status' => 0]);
            ModelsRedirect::where('code', $request->redirect)->delete();

            return response(['message' => 'Url successfully deleted'], 200)
                ->header('Content-Type', 'application/json');
        } catch (\Throwable $th) {
            return response(['message' => 'Unable to delete URL'], 500)
                ->header('Content-Type', 'application/json');
        }
    }
}
