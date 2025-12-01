<?php

namespace App\Traits;

use App\Models\Logs;
use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

trait LoggerTrait
{


    protected $allowedCodes = [
        200,
        201,
        400,
        401,
        403,
        404,
        409,
        422,
        429,
    ];

    private function getRouteData()
    {
        return [
            'route_name' => Route::currentRouteName(),
            'route_method' => Request::method(),
            'route_url' => Request::url()
        ];
    }

    private function extractUserData()
    {
        /** @var \App\Models\User $user */
        $user =  request()->user();
        $data = $this->getRouteData();
        if ($user) {
            $data = array_merge($data, [
                'user_id' => $user->id,
            ]);
        }

        return $data;
    }

    private function routeLogging()
    {
        $data = $this->extractUserData();
        Log::channel('routes')->info("Route Info", $data);
    }

    public function errorLogging(Exception $e)
    {
        if (in_array($e->getCode(), $this->allowedCodes)) {
            return;
        }
        $data = $this->extractUserData();
        $data = array_merge($data, [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file_name' => $e->getFile(),
            'line_number' => $e->getLine(),
            'request_body' => request()->all() ?? null,
        ]);
        Log::channel('errors')->error("Error Info", $data);
    }

    public function paymentLogging($data = [])
    {
        $logData = $this->extractUserData();
        $logData = array_merge($logData, $data);
        Log::channel('payments')->info("Payment Info", $logData);
    }
}
