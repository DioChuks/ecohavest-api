<?php

namespace App\Traits;

trait HttpResponses {
    protected function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error($data, $message = null, $code = 422)
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}