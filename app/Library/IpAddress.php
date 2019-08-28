<?php


namespace App\Library;

use GuzzleHttp\Client;

class IpAddress
{
    /**
     * 根据ip获取用户的地址
     * @param $ip
     * @return bool
     */
    public static function address($ip)
    {
        $url = "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query={$ip}&resource_id=6006";
        $client = new Client();
        $response = $client->get($url)->getBody()->getContents();
        $response = mb_convert_encoding($response, 'utf-8', 'GB2312');
        $re = json_decode($response, true);
        if (empty($re['data'])) {
            return false;
        } else {
            return $re['data']['0'];
        }
    }
}