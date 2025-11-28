<?php

namespace App\Utils;

class Response
{
  public static function success($data, $message = 'Success', int $code = 200)
  {
    return response()->json([
      'message' => $message,
      'status' => true,
      'data' => $data
    ], $code);
  }

  public static function error($data, $message = 'Error', int $code = 400)
  {
    return response()->json([
      'message' => $message,
      'status' => false,
      'data' => $data
    ], $code);
  }
}
