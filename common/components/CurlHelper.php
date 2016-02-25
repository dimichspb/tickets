<?php

namespace yii\helpers;

use linslin\yii2\curl\Curl;

class CurlHelper
{
    public static function get($url, array $params = [])
    {
        $curl = new Curl();
        $urlQuery = $url . "?" . http_build_query($params);
        $curl->get($urlQuery);
        return [
            'response' => $curl->response,
            'responseCode' => $curl->responseCode,
        ];
    }
}