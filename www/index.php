<?php
/**
 * Created by PhpStorm.
 * User: Иван
 * Date: 10.02.2019
 * Time: 18:26
 */
ini_set('display_errors',true);

////супер цикл переобхода групп в ВК
//1 получили список групп для мониторинга
//2 для каждой группы из списка проверяем дату посл. загруженного поста в БД (чтобы не дублироваться)
//3 формируем запрос в ВК
//4 получаем свежие записи
//4.1 + проверка есть-ли пост в БД или нет и рекламный пост или нет
//5 сохраняем их в БД
//6 продолжаем дальше с след группой из списка

//наши паблики для слежки
$groupNames = array('bot_maxim','bot_lena','bot_rudi','club163528123');
initGroupNameInDB($groupNames);

//перебираем паблики
foreach ($groupNames as &$domain) {
    //запрос последнего поста
    $jsonRes = downloadPostsFromWall($domain);
    //$res = json_decode($jsonRes);
    $res1 = json_decode($jsonRes);

    $items = $res1->response->items;

    $wallPosts = [];

    foreach ($items as $wallPostRawItem) {
        $wallPosts[] = WallPost::fromVkArray($wallPostRawItem);
    }

    //var_dump($res->attachments);
/*
    $massage = $wallPost[massage];
    $fromGroup = 1;
    $friendOnly = null;
    $ownerId = '-163528123';
    $attachments = $wallPost[attachments];
    $accessToken = 'a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7';
    $version = '5.37';
*/
    $wp = new WallPost;
    $wp->post('-163528123', '', '1', '123aaa', 'photo101754541_456246326', '', '0', '', '', '', '', '', '', '0', '1');
    var_dump($wp);
    //$res->fromVkArray(json_decode($jsonRes));
    //var_dump($res1->response->items);
    //$res->fromVkArray ($res->response);
    //$res = new Response ($jsonRes);

    //var_dump($res1->response->items);
    //echo '<br><br>';
/*
    //формируем свой пост из главных данных полученного поста
    $wallPost = array(
        date => getDateFromRes($res),
        domainId  => getDomainIdFromDB($domain),
        massage => getMassageFromRes($res),
        attachments => getAttachmentsFromRes ($res)
    );

    //пытаемся добавить запись в бд
    $successfulTransfer = saveWallPostToDB($wallPost);

    //публикуем у себя, если удалось добавление в бд
    if ($successfulTransfer) {
        $res = uploadWallPostTowall($wallPost);
    }

    sleep(1);// = 20*кол-во пабликов (чтоб не превысить кол-во запросов в день = 5000)
*/
    echo '<br><br>';
}

echo PHP_EOL.'DONE';

Class Response
{
    /** @var int */
    public $count;
    /** @var Items */
    public $items;

    public function __construct($vkClass)
    {
        $this->count = $vkClass->count;
        $this->items = new Items ($vkClass->items[0]);
    }

    public function toVkArray()
    {
        return [
            'count' => $this->count,
            'items' => $this->items
        ];
    }

}


Class Attachment {
    const ATTACHMENT_TYPE_PHOTO = 'photo';
    const ATTACHMENT_TYPE_AUDIO = 'audio';
    const ATTACHMENT_TYPE_VIDEO = 'video';

    public $type;
    public $video;
    public $audio;
    public $photo;


    public function __construct(string $type, object $childObject)
    {
        $this->type = $type;
        if ($type == self::ATTACHMENT_TYPE_AUDIO) {
            $this->audio = $childObject;
        }
        if ($type == self::ATTACHMENT_TYPE_VIDEO) {
            $this->video = $childObject;
        }
        if ($type == self::ATTACHMENT_TYPE_PHOTO) {
            $this->photo = $childObject;
        }
    }

    public function getValue() {
        if ($this->type == self::ATTACHMENT_TYPE_VIDEO) {
            return $this->video;
        }
    }

    public function isVideo() {
        return $this->type == self::ATTACHMENT_TYPE_VIDEO;
    }
    public function isPhoto() {
        return $this->photo == self::ATTACHMENT_TYPE_PHOTO;
    }
    public function isAudio() {
        return $this->type == self::ATTACHMENT_TYPE_AUDIO;
    }



}


Class WallPost
{

    /** @var int */
    public $id;
    /** @var int */
    public $owner_id;
    /** @var int */
    public $from_id;
    /** @var int */
    public $created_by;
    /** @var int */
    public $date;
    /** @var string */
    public $text;
    /** @var int */
    public $reply_owner_id;
    /** @var int */
    public $reply_post_id;
    /** @var int */
    public $friends_only;
    /** @var comments */
    public $comments;
    /** @var likes */
    public $likes;
    /** @var reposts */
    public $reposts;
    /** @var views */
    public $views;
    /** @var string */
    public $post_type;
    /** @var post_source */
    public $post_source;
    /** @var string */
    public $attachments;
    /** @var geo */
    public $geo;
    /** @var int */
    public $signer_id;
    /** @var array */
    public $copy_history;
    /** @var int */
    public $can_pin;
    /** @var int */
    public $can_delete;
    /** @var int */
    public $can_edit;
    /** @var int */
    public $is_pinned;
    /** @var int */
    public $marked_as_ads;
    /** @var bool */
    public $is_favorite;

    public function saveToDb() {

    }

    public function fromDB(array $params) {
        $res = ['message' => '123132'];

        return new self(
            $params['message']
        );
    }
    /*
    public function preparedWallPostRequest() {
        return [
            'message' => $this->text,
            'geo' => $this->geo->toVkArray()
        ];
    }
    */

    public function __construct(int $id,
                                int $owner_id,
                                int $from_id,
                                int $created_by,
                                int $date,
                                string $text,
                                int $reply_owner_id,
                                int $reply_post_id,
                                int $friends_only,
                                Comments $comments,
                                Likes $likes,
                                Reposts $reposts,
                                Views $views,
                                string $post_type,
                                Post_source $post_source,
                                string $attachments,
                                geo $geo,
                                int $signer_id,
                                string $copy_history,
                                int $can_pin,
                                int $can_delete,
                                int $can_edit,
                                int $is_pinned,
                                int $marked_as_ads,
                                bool $is_favorite)
    {
        $this->id = $id;
        $this->owner_id = $owner_id;
        $this->from_id = $from_id;
        $this->created_by = $created_by;
        $this->date = $date;
        $this->text = $text;
        $this->reply_owner_id = $reply_owner_id;
        $this->reply_post_id = $reply_post_id;
        $this->friends_only = $friends_only;
        $this->comments = $comments;
        $this->likes = $likes;
        $this->reposts = $reposts;
        $this->views = $views;
        $this->post_type = $post_type;
        $this->post_source = $post_source;
        $this->attachments = $attachments;
        $this->geo = $geo;
        $this->signer_id = $signer_id;
        $this->copy_history = $copy_history;
        $this->can_pin = $can_pin;
        $this->can_delete = $can_delete;
        $this->can_edit = $can_edit;
        $this->is_pinned = $is_pinned;
        $this->marked_as_ads = $marked_as_ads;
        $this->is_favorite = $is_favorite;
    }

        public function fromVkArray(array $vkArray)
        {
            if (!isset($vkArray['id']) && !intval($vkArray['id'])) {
                throw new Exception('Invalid id field in presented array',1);
            }
            if (!isset($vkArray['owner_id']) && !intval($vkArray['owner_id'])) {
                throw new Exception('Invalid owner_id field in presented array',2);
            }
            if (!isset($vkArray['from_id']) && !intval($vkArray['from_id'])) {
                throw new Exception('Invalid from_id field in presented array',3);
            }
            if (!isset($vkArray['created_by']) && !intval($vkArray['created_by'])) {
                throw new Exception('Invalid created_by field in presented array',4);
            }
            if (!isset($vkArray['date']) && !intval($vkArray['date'])) {
                throw new Exception('Invalid date field in presented array',5);
            }
            if (!isset($vkArray['text']) && !intval($vkArray['text'])) {
                throw new Exception('Invalid text field in presented array',6);
            }
            if (!isset($vkArray['reply_owner_id']) && !intval($vkArray['reply_owner_id'])) {
                throw new Exception('Invalid reply_owner_id field in presented array',7);
            }
            if (!isset($vkArray['reply_post_id']) && !intval($vkArray['reply_post_id'])) {
                throw new Exception('Invalid reply_post_id field in presented array',8);
            }
            if (!isset($vkArray['friends_only']) && !intval($vkArray['friends_only'])) {
                throw new Exception('Invalid friends_only field in presented array',9);
            }
            if (!isset($vkArray['comments']) && !intval($vkArray['comments'])) {
                throw new Exception('Invalid comments field in presented array',10);
            }
            if (!isset($vkArray['likes']) && !intval($vkArray['likes'])) {
                throw new Exception('Invalid likes field in presented array',11);
            }
            if (!isset($vkArray['reposts']) && !intval($vkArray['reposts'])) {
                throw new Exception('Invalid reposts field in presented array',12);
            }
            if (!isset($vkArray['views']) && !intval($vkArray['views'])) {
                throw new Exception('Invalid views field in presented array',13);
            }
            if (!isset($vkArray['post_type']) && !intval($vkArray['post_type'])) {
                throw new Exception('Invalid post_type field in presented array',14);
            }
            if (!isset($vkArray['post_source']) && !intval($vkArray['post_source'])) {
                throw new Exception('Invalid post_source field in presented array',15);
            }
            if (!isset($vkArray['attachments']) && !intval($vkArray['attachments'])) {
                throw new Exception('Invalid attachments field in presented array',16);
            }
            if (!isset($vkArray['geo']) && !intval($vkArray['geo'])) {
                throw new Exception('Invalid geo field in presented array',17);
            }
            if (!isset($vkArray['signer_id']) && !intval($vkArray['signer_id'])) {
                throw new Exception('Invalid signer_id field in presented array',18);
            }
            if (!isset($vkArray['copy_history']) && !intval($vkArray['copy_history'])) {
                throw new Exception('Invalid copy_history field in presented array',19);
            }
            if (!isset($vkArray['can_pin']) && !intval($vkArray['can_pin'])) {
                throw new Exception('Invalid can_pin field in presented array',20);
            }
            if (!isset($vkArray['can_delete']) && !intval($vkArray['can_delete'])) {
                throw new Exception('Invalid can_delete field in presented array',21);
            }
            if (!isset($vkArray['can_edit']) && !intval($vkArray['can_edit'])) {
                throw new Exception('Invalid can_edit field in presented array',22);
            }
            if (!isset($vkArray['is_pinned']) && !intval($vkArray['is_pinned'])) {
                throw new Exception('Invalid is_pinned field in presented array',23);
            }
            if (!isset($vkArray['marked_as_ads']) && !intval($vkArray['marked_as_ads'])) {
                throw new Exception('Invalid marked_as_ads field in presented array',24);
            }
            if (!isset($vkArray['is_favorite']) && !intval($vkArray['is_favorite'])) {
                throw new Exception('Invalid is_favorite field in presented array',25);
            }
            $this->id = $vkArray['id'];
            $this->owner_id = $vkArray['owner_id'];
            $this->from_id = $vkArray['from_id'];
            $this->created_by = $vkArray['created_by'];
            $this->date = $vkArray['date'];
            $this->text = $vkArray['text'];
            $this->reply_owner_id = $vkArray['reply_owner_id'];
            $this->reply_post_id = $vkArray['reply_post_id'];
            $this->friends_only = $vkArray['friends_only'];
            $this->comments = Comments::fromVkArray($vkArray['comments']);
            $this->comments->fromVkArray(vkArray['comments']);
            $this->likes->fromVkArray(vkArray['likes']);
            $this->reposts->fromVkArray(vkArray['reposts']);
            $this->views->fromVkArray(vkArray['views']);
            $this->post_type = $vkArray['post_type'];
            $this->post_source->fromVkArray(vkArray['post_source']);
            $this->attachments = $vkArray['attachments'];
            $this->geo->fromVkArray(vkArray['geo']);
            $this->signer_id = $vkArray['signer_id'];
            $this->copy_history->fromVkArray(vkArray['copy_history']);
            $this->can_pin = $vkArray['can_pin'];
            $this->can_delete = $vkArray['can_delete'];
            $this->can_edit = $vkArray['can_edit'];
            $this->is_pinned = $vkArray['is_pinned'];
            $this->marked_as_ads = $vkArray['marked_as_ads'];
            $this->is_favorite = $vkArray['is_favorite'];
        }

    public function toVkArray()
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'from_id' => $this->from_id,
            'created_by' => $this->created_by,
            'date' => $this->date,
            'text' => $this->text,
            'reply_owner_id' => $this->reply_owner_id,
            'reply_post_id' => $this->reply_post_id,
            'friends_only' => $this->friends_only,
            'comments' => $this->comments,
            'likes' => $this->likes,
            'reposts' => $this->reposts,
            'views' => $this->views,
            'post_type' => $this->post_type,
            'post_source' => $this->post_source,
            'attachments' => $this->attachments,
            'geo' => $this->geo->toVkArray(),
            'signer_id' => $this->signer_id,
            'copy_history' => $this->copy_history,
            'can_pin' => $this->can_pin,
            'can_delete' => $this->can_delete,
            'can_edit' => $this->can_edit,
            'is_pinned' => $this->is_pinned,
            'marked_as_ads' => $this->marked_as_ads,
            'is_favorite' => $this->is_favorite
        ];
    }

}

Class Comments
{
    /** @var int */
    public $count;
    /** @var int */
    public $can_post;
    /** @var int */
    public $groups_can_post;
    /** @var bool */
    public $can_close;
    /** @var bool */
    public $can_open;

/*
    public function __construct(int $count, int $can_post, int $groups_can_post, bool $can_close, bool $can_open)
    {
        $this->count = $count;
        $this->can_post = $can_post;
        $this->groups_can_post = $groups_can_post;
        $this->can_close = $can_close;
        $this->can_open = $can_open;
    }

    public function fromVkArray(array $vkArray)
    {
        if (!isset($vkArray['count']) && !intval($vkArray['count'])) {
            throw new Exception('Invalid count field in presented array',1);
        }
        if (!isset($vkArray['can_post']) && !intval($vkArray['can_post'])) {
            throw new Exception('Invalid can_post field in presented array',2);
        }
        if (!isset($vkArray['groups_can_post']) && !intval($vkArray['groups_can_post'])) {
            throw new Exception('Invalid groups_can_post field in presented array',3);
        }
        if (!isset($vkArray['can_close']) && !intval($vkArray['can_close'])) {
            throw new Exception('Invalid can_close field in presented array',4);
        }
        if (!isset($vkArray['can_open']) && !intval($vkArray['can_open'])) {
            throw new Exception('Invalid can_open field in presented array',5);
        }
        $this->count = $vkArray['count'];
        $this->can_post = $vkArray['can_post'];
        $this->groups_can_post = $vkArray['groups_can_post'];
        $this->can_close = $vkArray['can_close'];
        $this->can_open = $vkArray['can_open'];
    }
*/
    public function __construct($vkClass)
    {
        $this->count = $vkClass->count;
        $this->can_post = $vkClass->can_post;
        $this->groups_can_post = $vkClass->groups_can_post;
        $this->can_close = $vkClass->can_close;
        $this->can_open = $vkClass->can_open;
    }

    public function toVkArray()
    {
        return [
            'count' => $this->count,
            'can_post' => $this->can_post,
            'groups_can_post' => $this->groups_can_post,
            'can_close' => $this->can_close,
            'can_open' => $this->can_open
        ];
    }
}

Class Likes
{
    /** @var int */
    public $count;
    /** @var int */
    public $user_likes;
    /** @var int */
    public $can_like;
    /** @var int */
    public $can_publish;
/*
    public function __construct(int $count, int $likes, int $can_like, int $can_publish)
    {
        $this->count = $count;
        $this->can_like = $can_like;
        $this->likes = $likes;
        $this->can_publish = $can_publish;
    }

    public function fromVkArray(array $vkArray)
    {
        if (!isset($vkArray['count']) && !intval($vkArray['count'])) {
            throw new Exception('Invalid count field in presented array',1);
        }
        if (!isset($vkArray['user_likes']) && !intval($vkArray['user_likes'])) {
            throw new Exception('Invalid user_likes field in presented array',2);
        }
        if (!isset($vkArray['can_like']) && !intval($vkArray['can_like'])) {
            throw new Exception('Invalid can_like field in presented array',3);
        }
        if (!isset($vkArray['can_publish']) && !intval($vkArray['can_publish'])) {
            throw new Exception('Invalid can_publish field in presented array',4);
        }
        $this->count = $vkArray['count'];
        $this->user_likes = $vkArray['user_likes'];
        $this->can_like = $vkArray['can_like'];
        $this->can_publish = $vkArray['can_publish'];
    }
*/
    public function __construct($vkClass)
    {
        $this->count = $vkClass->count;
        $this->user_likes = $vkClass->user_likes;
        $this->can_like = $vkClass->can_like;
        $this->can_publish = $vkClass->can_publish;
    }



    public function toVkArray()
    {
        return [
            'count' => $this->count,
            'user_likes' => $this->user_likes,
            'can_like' => $this->can_like,
            'can_publish' => $this->can_publish
        ];
    }
}

Class Reposts
{
    /** @var int */
    public $count;
    /** @var int */
    public $user_reposted;
/*
    public function __construct(int $count, int $user_reposted)
    {
        $this->count = $count;
        $this->user_reposted = $user_reposted;
    }

    public function fromVkArray(array $vkArray)
    {
        if (!isset($vkArray['count']) && !intval($vkArray['count'])) {
            throw new Exception('Invalid count field in presented array',1);
        }
        if (!isset($vkArray['user_reposted']) && !intval($vkArray['user_reposted'])) {
            throw new Exception('Invalid user_reposted field in presented array',2);
        }
        $this->count = $vkArray['count'];
        $this->user_reposted = $vkArray['user_reposted'];
    }
*/
    public function __construct($vkClass)
    {
        $this->count = $vkClass->count;
        $this->user_reposted = $vkClass->user_reposted;
    }

    public function toVkArray()
    {
        return [
            'count' => $this->count,
            'user_reposted' => $this->user_reposted
        ];
    }
}

Class Views
{
    /** @var int */
    public $count;
/*
    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function fromVkArray(array $vkArray)
    {
        if (!isset($vkArray['count']) && !intval($vkArray['count'])) {
            throw new Exception('Invalid count field in presented array',1);
        }
        $this->count = $vkArray['count'];
    }
*/
    public function __construct($vkClass)
    {
        $this->count = $vkClass->count;
    }

    public function toVkArray()
    {
        return [
            'count' => $this->count
        ];
    }
}

Class Geo
{
    /** @var string */
    public $type;
    /** @var string */
    public $coordinates;
    /** @var Place */
    public $place;
/*
    public function __construct(string $type, string $coordinates, Place $place)
    {
        $this->type = $type;
        $this->coordinates = $coordinates;
        $this->place = $place;
    }

    public function fromVkArray(array $vkArray)
    {
        if (!isset($vkArray['type']) && !intval($vkArray['type'])) {
            throw new Exception('Invalid type field in presented array',1);
        }
        if (!isset($vkArray['coordinates']) && !intval($vkArray['coordinates'])) {
            throw new Exception('Invalid coordinates field in presented array',2);
        }
        if (!isset($vkArray['place']) && !intval($vkArray['place'])) {
            throw new Exception('Invalid place field in presented array',3);
        }
        $this->type = $vkArray['type'];
        $this->coordinates = $vkArray['coordinates'];
        $this->place->fromVkArray(vkArray['place']);
    }
*/
    public function __construct($vkClass)
    {
        $this->type = $vkClass->type;
        $this->coordinates = $vkClass->coordinates;
        $this->place = new Place ($vkClass->place);
    }
    public function toVkArray()
    {
        return [
            'type' => $this->type,
            'coordinates' => $this->coordinates,
            'place' => $this->place
        ];
    }
}

Class Place
{
    /** @var int */
    public $id;
    /** @var string */
    public $title;
    /** @var int */
    public $latitude;
    /** @var int */
    public $longitude;
    /** @var int */
    public $created;
    /** @var string */
    public $icon;
    /** @var int */
    public $checkins;
    /** @var int */
    public $updated;
    /** @var int */
    public $type;
    /** @var int */
    public $country;
    /** @var int */
    public $city;
    /** @var string */
    public $address;
/*
    public function __construct(int $id, string $title, int $latitude, int $longitude, int $created, string $icon, int $checkins, int $updated, int $type, int $country, int $city, string $address)
    {
        $this->id = $id;
        $this->title = $title;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->created = $created;
        $this->icon = $icon;
        $this->checkins = $checkins;
        $this->updated = $updated;
        $this->type = $type;
        $this->country = $country;
        $this->city = $city;
        $this->address = $address;
    }

    public function fromVkArray(array $vkArray)
    {
        if (!isset($vkArray['id']) && !intval($vkArray['id'])) {
            throw new Exception('Invalid id field in presented array',1);
        }
        if (!isset($vkArray['title']) && !intval($vkArray['title'])) {
            throw new Exception('Invalid title field in presented array',2);
        }
        if (!isset($vkArray['latitude']) && !intval($vkArray['latitude'])) {
            throw new Exception('Invalid latitude field in presented array',3);
        }
        if (!isset($vkArray['longitude']) && !intval($vkArray['longitude'])) {
            throw new Exception('Invalid longitude field in presented array',4);
        }
        if (!isset($vkArray['created']) && !intval($vkArray['created'])) {
            throw new Exception('Invalid created field in presented array',5);
        }
        if (!isset($vkArray['icon']) && !intval($vkArray['icon'])) {
            throw new Exception('Invalid icon field in presented array',6);
        }
        if (!isset($vkArray['checkins']) && !intval($vkArray['checkins'])) {
            throw new Exception('Invalid checkins field in presented array',7);
        }
        if (!isset($vkArray['updated']) && !intval($vkArray['updated'])) {
            throw new Exception('Invalid updated field in presented array',8);
        }
        if (!isset($vkArray['type']) && !intval($vkArray['type'])) {
            throw new Exception('Invalid type field in presented array',9);
        }
        if (!isset($vkArray['country']) && !intval($vkArray['country'])) {
            throw new Exception('Invalid country field in presented array',10);
        }
        if (!isset($vkArray['city']) && !intval($vkArray['city'])) {
            throw new Exception('Invalid city field in presented array',11);
        }
        if (!isset($vkArray['address']) && !intval($vkArray['address'])) {
            throw new Exception('Invalid address field in presented array',12);
        }
        $this->id = $vkArray['id'];
        $this->title = $vkArray['title'];
        $this->latitude = $vkArray['latitude'];
        $this->longitude = $vkArray['longitude'];
        $this->created = $vkArray['created'];
        $this->icon = $vkArray['icon'];
        $this->checkins = $vkArray['checkins'];
        $this->updated = $vkArray['updated'];
        $this->type = $vkArray['type'];
        $this->country = $vkArray['country'];
        $this->city = $vkArray['city'];
        $this->address = $vkArray['address'];
    }
*/
    public function __construct($vkClass)
    {
        $this->id = $vkClass->id;
        $this->title = $vkClass->title;
        $this->latitude = $vkClass->latitude;
        $this->longitude = $vkClass->longitude;
        $this->created = $vkClass->created;
        $this->icon = $vkClass->icon;
        $this->checkins = $vkClass->checkins;
        $this->updated = $vkClass->updated;
        $this->type = $vkClass->type;
        $this->country = $vkClass->country;
        $this->city = $vkClass->city;
        $this->address = $vkClass->address;
    }

    public function toVkArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created' => $this->created,
            'icon' => $this->icon,
            'checkins' => $this->checkins,
            'updated' => $this->updated,
            'type' => $this->type,
            'country' => $this->country,
            'city' => $this->city,
            'address' => $this->address
        ];
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
            $this->connection =  mysqli_connect($this->host,$this->user,$this->pass,$this->db);
        } catch (Exception $e) {
            print_r($e->getMessage());
            print_r(PGP_EOL.$e->getCode());
        }

        var_dump(mysqli_error($this->connection));

    }

}

//old code------------------------------


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

function initGroupNameInDB ($groupNames){
    $dbConnector = new DBConnector("posts_of_group", "127.0.0.1", "root", "Gtnh.1511475");
    foreach ($groupNames as &$domain) {
        $dbConnector->connection->query("SET NAMES 'utf8' ");//Кодировка данных получаемых из базы
        $dbConnector->connection->query("INSERT INTO name_of_group (domain) VALUES  ('$domain')");
    }
}

function getDateFromRes ($res){
    return date('Y-m-d g:i:s', $res->response->items[0]->date);
}

function getTypeOfAttachmentsFromRes ($res) {
    return $res->response->items[0]->attachments[0]->type;
}

function getAttachmentsFromRes ($res){
    return getTypeOfAttachmentsFromRes($res) . $res->response->items[0]->attachments[0]->{getTypeOfAttachmentsFromRes($res)}->owner_id . '_' . $res->response->items[0]->attachments[0]->{getTypeOfAttachmentsFromRes($res)}->id;
}

function saveWallPostToDB($wallPost) {
    $connect = new mysqli("localhost", "root", "Gtnh.1511475", "posts_of_group" );//подключили бд
    $connect->query("SET NAMES 'utf8' ");//Кодировка данных получаемых из базы

    if (strlen($wallPost[massage]) > 0) {
        return $connect->query("INSERT INTO post (date, massage, attachments, name_id) VALUES  ('$wallPost[date]', '$wallPost[massage]', '$wallPost[attachments]', '$wallPost[domainId]')");
    }
    else {
        return $connect->query("INSERT INTO post (date, attachments, name_id) VALUES  ('$wallPost[date]', '$wallPost[attachments]', '$wallPost[domainId]')");
    }
}

function getMassageFromRes ($res){
    return $res->response->items[0]->text;
}

function getDomainIdFromDB($domain){
    $connect = new mysqli("localhost", "root", "Gtnh.1511475", "posts_of_group" );//подключили бд
    $connect->query("SET NAMES 'utf8' ");//Кодировка данных получаемых из базы
    return mysqli_fetch_array($connect->query("select distinct id from name_of_group where domain like '$domain'; "))[id];
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
    $jsonResponse = file_get_contents($url);
    return json_decode($jsonResponse);
}

Class BasicWall
{
    /** @var string */
    public $token;
    /** @var int */
    public $v;

    /**
     * BasicWall constructor.
     * @param string $token
     * @param string $v
     */
    public function __construct(string $token = 'a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7', string $v = '5.37')
    {
        $this->token = $token;
        $this->v = $v;
    }
}

Class VkApi {
    public $token;
    public $version;

    public function executeMethod(string $methodName, array $params) {


        $urlEncodedParams = urlencode($params);

        $url = "https://api.vk.com/method/$methodName?$urlEncodedParams&v=".$this->version."&access_token=".$this->token;
        //$url = "https://api.vk.com/method/wall.post?owner_id=-163528123&friends_only=null&from_group=1&message=aaa111&attachments=photo101754541_456246326&v=5.37&access_token=a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7";
        echo ($url);
        $jsonResponse = file_get_contents($url);
        return json_decode($jsonResponse);
    }


    public function sendPostToWall(string $text, array  $attachments) {
        $wallPostParams = [
            'text'  =>  $text,
            'attachments' => $attachments
        ];
        $res = $this->executeMethod('wall.post',$wallPostParams);
    }


    public function downloadWallopostFromWall(int $wallId, int $offset, int $count) {
        $params = [
            ''
        ];
        $res = $this->executeMethod('wall.getById',$params);
        $wallPost = WallPost::fromVkArray($res);
    }
}



Class WallPost extends BasicWall
{
    /** @var int */
    public $owner_id;//
    /** @var int */
    public $friends_only;//
    /** @var int */
    public $from_group;//
    /** @var string */
    public $message;//
    /** @var string */
    public $attachments;//
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
    public $close_comments = 1;


    public static function fromVkArray (array $vkParams) {

        return new self($vkParams);

    }

    public function __construct()
    {
        parent::__construct();
    }


    public function post(string $owner_id, string $friends_only, string $from_group, string $message, string $attachments, string $services, string $signed, string $publish_date,  string $lat,  string $long,  string $place_id,  string $post_id,  string $guid, string $mark_as_ads, string $close_comments)
    {

        $s = $this->token;

        $url = "https://api.vk.com/method/wall.post?owner_id=$owner_id&friends_only=$friends_only&from_group=$from_group&message=$message&attachments=$attachments&services=$services&signed=$signed&publish_date=$publish_date&lat=$lat&long=$long&place_id=$place_id&post_id=$post_id&guid=$guid&mark_as_ads=$mark_as_ads&close_comments=$close_comments&v=".parent::$v."&access_token=".parent::$token;
        //$url = "https://api.vk.com/method/wall.post?owner_id=-163528123&friends_only=null&from_group=1&message=aaa111&attachments=photo101754541_456246326&v=5.37&access_token=a9794233aaf78df30cf6e13636d9baf1985ef4d824d9f2d4fb26d258d8763e8247902e6dfe4d429f51ef7";
        echo ($url);
        $jsonResponse = file_get_contents($url);
        return json_decode($jsonResponse);
    }
}

