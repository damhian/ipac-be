<?php

namespace App\Traits;

use Illuminate\Support\Facades\Lang;

trait HttpResponses
{
    protected function success(array $data, string $message = NULL, int $code = 200, array $option = [])
    {
        $message = $message ? Lang::get($message, $option) : "";

        if ($data)
            $res = [
                "message" => $message,
                "data" => $data,
            ];
        else
            $res = [
                "message" => $message,
            ];

        return response()->json($res, $code);
    }

    protected function error(array $data, string $message = "Error has occured...", int $code, array  $option = [])
    {
        $message = $message ? Lang::get($message, $option) : "";

        if ($data)
            $res = [
                "message" => $message,
                "data" => $data,
            ];
        else
            $res = [
                "message" => $message,
            ];

        return response()->json($res, $code);
    }
}
