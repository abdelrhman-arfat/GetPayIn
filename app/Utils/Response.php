<?php

namespace App\Utils;

class Response
{
  public static function success($data, $message = 'Success', int $code = 200)
  {
    return response()->json([
      'message' => $message,
      'status' => true,
      'data' => $data,
      "errors" => null
    ], $code);
  }

  public static function error($message = 'Error', $errors = null, int $code = 400)
  {
    return response()->json([
      'message' => $message,
      'status' => false,
      'data' => null,
      "errors" => $errors
    ], $code);
  }
}
