<?php
/**
 * Created by PhpStorm.
 * User: Иван
 * Date: 10.02.2019
 * Time: 18:26
 */
ini_set('display_errors',true);



//наши паблики для слежки
$groupName = array('bot_maxim','bot_lena');

initGroupName($groupName);


//перебираем паблики
foreach ($groupName as &$value) {
    //запрос последнего поста
    $jsonRes = downloadPostsFromWall($value);
    $res = json_decode($jsonRes);

    //формируем свой пост из главных данных полученного поста
    $wallPost = array(
        date => date('Y-m-d g:i:s', $res->response->items[0]->date),
        domain => $value,
        massage => $res->response->items[0]->text,
        attachments => 'photo' . $res->response->items[0]->attachments[0]->photo->owner_id . '_' . $res->response->items[0]->attachments[0]->photo->id
    );
    var_dump($wallPost);
    //пытаемся добавить запись в бд
    $successfulTransfer = saveWallPostToDB($wallPost);
    var_dump($successfulTransfer);

    //публикуем у себя, если удалось добавление в бд
    if($successfulTransfer) {
        $res = uploadWallPostTowall($wallPost);
    }

    sleep(1);// = 20*кол-во пабликов (чтоб не превысить кол-во запросов в день = 5000)
}
//супер цикл переобхода групп в ВК
//1 получили список групп для мониторинга
//2 для каждой группы из списка проверяем дату посл. загруженного поста в БД (чтобы не дублироваться)
//3 формируем запрос в ВК
//4 получаем свежие записи
//4.1 + проверка есть-ли пост в БД или нет и рекламный пост или нет
//5 сохраняем их в БД
//6 продолжаем дальше с след группой из списка





function downloadPostsFromWall($domain){
    $ownerId = NULL;
    $offset = 1;
    $count = 1;//кол-во записей для получения max = 100
    $filter = "all";
    $extended = 0;
    $fields = NULL;
    $accessToken = 'a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7';
    $version = '5.37';

    $url = "https://api.vk.com/method/wall.get?ownerId=$ownerId&domain=$domain&offset=$offset&count=$count&filter=$filter&extended=$extended&fields=$fields&v=$version&access_token=$accessToken";

    $jsonResponse = file_get_contents($url);

    return $jsonResponse;
}

function initGroupName ($groupName){
    foreach ($groupName as &$value) {
        $connect = new mysqli("localhost", "root", "1511475", "posts_of_group" );//подключили бд
        $connect->query("SET NAMES 'utf8' ");//Кодировка данных получаемых из базы
        var_dump($groupName);
        var_dump($value);
        $connect->query("INSERT INTO name_of_group (domain) VALUES  ('".$value."')");
    }
}

function saveWallPostToDB($wallPost) {
    $connect = new mysqli("localhost", "root", "1511475", "posts_of_group" );//подключили бд
    $connect->query("SET NAMES 'utf8' ");//Кодировка данных получаемых из базы
    var_dump($connect);
    return $connect->query("INSERT INTO post (date, massage, attachments) VALUES  ('".$wallPost[date]."', '".$wallPost[massage]."', '".$wallPost[attachments]."')");
}

function uploadWallPostTowall($wallPost) {
    $massage = $wallPost[massage];
    $fromGroup = 1;
    $friendOnly = null;
    $ownerId = '-163528123';
    $attachments = $wallPost[attachments];
    $accessToken = 'a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7';
    $version = '5.37';

    $url = "https://api.vk.com/method/wall.post?owner_id=$ownerId&friends_only=$friendOnly&from_group=$fromGroup&message=$massage&attachments=$attachments&v=$version&access_token=$accessToken";
    var_dump($url);
    $jsonResponse = file_get_contents($url);

    return json_decode($jsonResponse);
}
