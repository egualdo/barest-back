<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ApiResponse
{
    protected function success($data, $code = 200)
    {
        return response()->json(['data' => $data, 'ok' => true, 'code' => $code], $code);
    }

    protected function error($message, $code)
    {
        return response()->json(['data' => $message, 'ok' => false, 'code' => !$code || $code > 503 ? 500 : $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        return $this->success($collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        return $this->success($instance, $code);
    }

    protected function showMessage($message)
    {
        return $this->success($message);
    }

    protected function showMessageCustom($data,$message, $code = 200)
    {
        return response()->json(['data' => $data,'message'=>$message ,'ok' => false, 'code' => $code], $code);
    }
}
