<?php

namespace App\Traits;

trait ApiResponse
{
    public function withSuccess($data, $info = null)
    {
        return [
            'status' => 'success',
            'info' => $info,
            'data' => $data,
        ];
    }
    public function withError($data)
    {
        return [
            'status' => 'error',
            'data' => $data
        ];
    }
}
