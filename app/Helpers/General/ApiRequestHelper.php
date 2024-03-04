<?php

namespace App\Helpers\General;

use Exception;
use App\Events\Backend\SystemLog;

/**
 * Class ApiRequestHelper.
 */
class ApiRequestHelper
{

    public function request($httpRequest, $endpoint, $params, &$bodyResponse)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request($httpRequest, $endpoint, $params);
            $bodyResponse = json_decode($response->getBody());

            return true;
        } catch (Exception $e) {
            event(new SystemLog($e->getMessage()));
            return false;
        }
    }
}
