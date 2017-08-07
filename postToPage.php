<?php
    /**
     * share data on facebook page
     *
     * @param array  $images
     *
     * @author vishnujith <vishnuvaranad@gmail.com>
     *
     */

    public function  shareonfb($image)
    {
        $appid = FB_APP_ID;   //facebook app id
        $appsecret = FB_APP_SECRET; //facebook app secret
        $pageId = FB_PAGE_ID; //facebook page id
        $data= DATA_TO_POST;  //data to be shared
        $images = IMAGES_TO_POST; //images to be shared

        $facebook = new \Facebook(array(
            'appId' => $appid,
            'secret' => $appsecret,
            'cookie' => false,
        ));

        $result = $facebook->api("/".$pageId."/albums", "POST", array('access_token' => FB_PAGE_ACC_TOKEN, 'name' => 'name', 'message' => $data));
        $album_id = $result['id'];

        foreach($images as $image) {
            $images = IMAGE_URL . '/' . $image['image'];
            try {
                $args = array(
                    'access_token' => FB_PAGE_ACC_TOKEN,    //permanant page access token that has been generated earlier
                    "url" => $images,
                    "message" => $data
                    //"name" => $caption,
                    //"description" => $message
                );
                $facebook->api("/$album_id/photos", "post", $args);

            } catch (\FacebookApiException $e) {
                $logger = LOGGER;   // logger service
                $error = LOG_FILE. $e->getMessage(); //log file name
                $logger->excpt($error);
            }
        }
    }

    /**
     * create album on facebook page
     *
     * @param string $couple, $story
     *
     * @author vishnujith <vishnuvaranad@gmail.com>
     *
     * @return $album_id
     */
    public function createAlbum()
    {
        // Create a new album
        $graph_url = "https://graph.facebook.com/me/albums?". "access_token=". FB_PAGE_ACC_TOKEN;

        $postdata = http_build_query(array(
            'name' => $name,    //name of the album
            'message' => $data    //data to be shared
        ));

        $opts = array('http' => array(
            'method'=> 'POST',
            'header'=>'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        ));

        $context  = stream_context_create($opts);

        $result = json_decode(file_get_contents($graph_url, false, $context));

        return $result->id;
    }


?>
