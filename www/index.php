<?php
/**
 * Created by PhpStorm.
 * User: Иван
 * Date: 10.02.2019
 * Time: 18:26
 */
ini_set('display_errors',true);
/*
$connect = new mysqli("localhost", "root", "Gtnh.1511475", "mydb" );//подключили бд
$connect->query("SET NAMES 'utf8' ");
$connect->query("insert into sites (url) values ('5');");
*/
//$P = new Post('https://rb.ru/news/',-163528123, 1, 1, 'aa', '','',0, '', '','','','','','','','a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7','5.37');
$P = new Post();
$P->siteUrl = 'https://rb.ru/news/';
$P->message = 'aaa';
$P->saveToDb();

//var_dump($P);
echo PHP_EOL.'DONE';


Class Post
{
    /** @var string */
    public $siteUrl = '';
    /** @var int */
    public $owner_id = '';
    /** @var int */
    public $friends_only = 1;
    /** @var int */
    public $from_group = 1;
    /** @var string */
    public $message = '';
    /** @var string */
    public $attachments = '';
    /** @var string */
    public $services = '';
    /** @var int */
    public $signed = 0;
    /** @var int */
    public $publish_date = '';
    /** @var float */
    public $lat = '';
    /** @var float */
    public $long = '';
    /** @var int */
    public $place_id = '';
    /** @var int */
    public $post_id = '';
    /** @var string */
    public $guid = '';
    /** @var int */
    public $mark_as_ads = 0;
    /** @var int */
    public $close_comments = 0;
    /** @var int */
    public $accessToken = '';
    /** @var int */
    public $version = '5.37';

    public function findIdOfSite (){
        $connect = new DBConnector('posts_of_group', 'localhost', 'root','Gtnh.1511475');
        $id = mysqli_fetch_array($connect->connection->query("select distinct id from id_of_sites where url like '$this->siteUrl'; "))[id];
        if($id != ''){
            return $id;
        } else {
            $connect->connection->query("INSERT INTO id_of_sites (url) VALUES ('$this->siteUrl')");
            $a =  mysqli_fetch_array($connect->connection->query("select distinct id from id_of_sites where url like '$this->siteUrl'; "))[id];
            var_dump ($a);
            return $a;
        }
    }
    public function findIdOfPost (){
        $connect = new DBConnector('posts_of_group', 'localhost', 'root','Gtnh.1511475');
        try {
            return mysqli_fetch_array($connect->connection->query("select distinct id from posts where message='$this->message' and attachments ='$this->attachments'; "))[id];
        }catch(Exception $e){
            return false;
        }
    }
    public function saveToDb()
    {
        if ($this->findIdOfPost() == false) {
            echo PHP_EOL.'ttttrr';
            $connect = new DBConnector('posts_of_group', 'localhost', 'root','Gtnh.1511475');
            return $connect->connection->query("INSERT INTO posts (url_id, owner_id, friends_only, from_group, message, attachments, services, " .
                "signed, publish_date, lat, long, place_id, post_id, guid, mark_as_ads, close_comments) " .
                "VALUES  ('".$this->findIdOfSite()."', '$this->owner_id', ' '$this->friends_only', ' '$this->from_group', ' '$this->message', ' '$this->attachments', ' '$this->services', " .
                "' '$this->signed', ' '$this->publish_date', ' '$this->lat', ' '$this->long', ' '$this->place_id', ' '$this->post_id', ' '$this->guid', ' '$this->mark_as_ads', ' '$this->close_comments'");
        }
        else return false;
    }
    /*
    public function __construct ()
    {
        $this->siteUrl = '';
        $this->owner_id = '';
        $this->friends_only = 1;
        $this->from_group = 1;
        $this->message = '';
        $this->attachments = '';
        $this->services = '';
        $this->signed = 0;
        $this->publish_date = '';
        $this->lat = '';
        $this->long = '';
        $this->place_id = '';
        $this->post_id = '';
        $this->guid = '';
        $this->mark_as_ads = 0;
        $this->close_comments = 0;
        $this->accessToken = '';
        $this->version = '5.37';
    }

    public function __construct (int $siteUrl,
                                int $owner_id,
                                int $friends_only,
                                int $from_group,
                                string $message,
                                string $attachments,
                                string $services,
                                int $signed,
                                int $publish_date,
                                float $lat,
                                float $long,
                                int $place_id,
                                int $post_id,
                                string $guid,
                                int $mark_as_ads,
                                int $close_comments,
                                string $accessToken,
                                string $version)
    {
        $this->siteUrl = $siteUrl;
        $this->owner_id = $owner_id;
        $this->friends_only = $friends_only;
        $this->from_group = $from_group;
        $this->message = $message;
        $this->attachments = $attachments;
        $this->services = $services;
        $this->signed = $signed;
        $this->publish_date = $publish_date;
        $this->lat = $lat;
        $this->long = $long;
        $this->place_id = $place_id;
        $this->post_id = $post_id;
        $this->guid = $guid;
        $this->mark_as_ads = $mark_as_ads;
        $this->close_comments = $close_comments;
        $this->accessToken = $accessToken;
        $this->version = $version;
    }
    */
    public function fromHTML(array $vkArray)
    {
        $this->message = $vkArray['friends_only'];
        $this->attachments = Comments::fromVkArray($vkArray['comments']);
    }

    public function toVkArray()
    {
        return [
            'owner_id' => $this->owner_id,
            'friends_only' => $this->friends_only,
            'from_group' => $this->from_group,
            'message' => $this->message,
            'attachments' => $this->attachments,
            'services' => $this->services,
            'signed' => $this->signed,
            'publish_date' => $this->publish_date,
            'lat' => $this->lat,
            'long' => $this->long,
            'place_id' => $this->place_id,
            'post_id' => $this->post_id,
            'guid' => $this->guid,
            'mark_as_ads' => $this->mark_as_ads,
            'close_comments' => $this->close_comments,
            'v' => $this->version,
            'access_token' => $this->accessToken
        ];
    }

    public function post () {
        $jsonRequest = "https://api.vk.com/method/wall.post?";
        $jsonResponse = file_get_contents($jsonRequest);
        return json_decode($jsonResponse);
    }
}

class DBConnector {

    public $db;
    public $host;
    public $user;
    public $pass;
    public $connection;


    /**
     * DBConnector constructor.
     * @param string $db
     * @param string $host
     * @param string $user
     * @param string $pass
     */
    public function __construct(string $db, string $host, string $user, string $pass)
    {
        $this->db = $db;
        $this->host = $host;
        $this->pass = $pass;
        $this->user = $user;


        try {
            $this->connection =  mysqli_connect($host,$user,$pass,$db);
        } catch (Exception $e) {
            print_r($e->getMessage());
            print_r(PGP_EOL.$e->getCode());
        }

        //var_dump(mysqli_error($this->connection));

    }

}

