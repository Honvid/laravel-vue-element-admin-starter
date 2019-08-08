<?php

namespace App\Traits;

/*
|--------------------------------------------------------------------------
| TRAIT NAME: JsonResponseTrait
|--------------------------------------------------------------------------
| @author    honvid
| @datetime  2019-07-22 17:05
| @package   App\Traits | @description:
|
*/

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;

trait JsonResponseTrait
{
    /**
     * format the response for api
     *
     * @param array|Collection|null $data
     * @param string                $message
     * @param int                   $errorCode
     * @param int                   $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpResponseException
     */
    protected function jsonResponse($data = null, $message = 'success', $errorCode = 0, $statusCode = 200)
    {
        $response['code']    = $errorCode;
        $response['message'] = $message;

        if ($errorCode > 0) {
            if (!is_null($data)) {
                $response['errors'] = $data;
            }
            throw new HttpResponseException(response()->json($response, $statusCode));
        }

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }
}