<?php
	//пробую послать запрос (не работает)
$massage = "hi";
$fromGroup = 1;
$friendOnly = null;
$ownerId = '-163528123';
$attachments = 'photo101754541_456245658';
$accessToken = 'a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7';
$version = '5.37';


$url = "https://api.vk.com/method/wall.post?owner_id=$ownerId&friends_only=$friendOnly&from_group=$fromGroup&message=".$massage."&attachments=$attachments&v=$version&access_token=$accessToken";
$jsonAnswer = file_get_contents($url);
$response = json_decode($jsonAnswer);

print_r($response);

?>
