<?php
/**
 * Created by PhpStorm.
 * User: Иван
 * Date: 10.02.2019
 * Time: 18:26
 */


ini_set('display_errors',true);

$massage = "hi";
$fromGroup = 1;
$friendOnly = null;
$ownerId = '-163528123';
$attachments = 'photo101754541_456245658';
$accessToken = 'a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7';
$version = '5.37';


$url = "https://api.vk.com/method/wall.post?owner_id=$ownerId&friends_only=$friendOnly&from_group=$fromGroup&message=".$massage."&attachments=$attachments&v=$version&access_token=$accessToken";


$jsonResponse = file_get_contents($url);

$res = json_decode($jsonResponse);


var_dump($res);
//$response = getUrlResponse($url);



function getUrlResponse($url) {
    if( $curl = curl_init() ) {
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);

        try {
            $out = curl_exec($curl);

        } catch (Exception $e) {
            $s =2;
        }
        curl_close($curl);
        return  $out;
    }
}