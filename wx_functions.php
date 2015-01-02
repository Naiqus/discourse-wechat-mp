<?php
define("hostname", "http://www.e1zone.de");
define("default_image", "/uploads/default/312/63c8e3efeff68569.png");
error_reporting(E_ERROR);

function return_welcome($fromUsername,$toUsername){
    include_once("wx_template.php");
    $title = "感谢您关注东一区(e1zone.de)";
    $msgType = "news";
    $description = "在这里，关注科技、热爱游戏，喜欢摄影，痴迷HiFi以及怀揣着各种新点子的朋友们正在集结\n\n 请点击本消息或者回复”？“查看本公众号功能说明";
    $picUrl = "http://www.e1zone.de/uploads/default/312/63c8e3efeff68569.png";
    $url = "http://www.e1zone.de/t/dong-qu-gong-zhong-hao-shi-yong-bang-zhu";
    $resultStr;
    $resultStr = sprintf($newsTpl,$fromUsername,$toUsername,time(),$msgType,"1",$title,$description,$picUrl,$url);
    echo $resultStr;
    $msgType = "text";
    $text = "回复”？“查看使用说明";
    $resultStr .= sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $text);
    echo $resultStr;
    exit;
}

function return_posts(){
    //fetch data from discourse
    $postNum = 5;
    if (func_get_arg(0) == "latest") {
        $json_url = hostname."/latest.json";
    }elseif (func_get_arg(0) == "weekly") {
        $json_url = hostname."/top/weekly.json";
    }

    $fromUsername = func_get_arg(1);
    $toUsername = func_get_arg(2);
    
    $json = file_get_contents($json_url);
    if ($json != null){
        $posts = json_decode($json);
    } else{
        throw new Exception("Error Json Request", 1);
    }
    $titles = array();
    $images = array();
    $urls = array();
    
    $topics = $posts->topic_list->topics;
    for ($i=0; $i <= $postNum-1 && $i <= count($topics)-1; $i++) { 
        $titles[$i] = $topics[$i]->title;
        // if there's no img or the only img is from emojis, use default img.
        if ($topics[$i]->image_url == null ||substr($topics[$i]->image_url,0,14) == "/plugins/emoji"){
            $images[$i] =hostname.default_image;
        }
        else{
            $images[$i] =hostname.$topics[$i]->image_url;
        }
        $urls[$i] = hostname."/t/".$topics[$i]->slug;
    }   

    $resultStr ="<xml>\n";
    $resultStr .="<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n";
    $resultStr .="<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n";
    $resultStr .="<CreateTime>".time()."</CreateTime>\n";
    $resultStr .="<MsgType><![CDATA[news]]></MsgType>\n";
    $resultStr .="<ArticleCount>".count($titles)."</ArticleCount>\n";
    $resultStr .="<Articles>\n";
    
    for ($i=0; $i <= count($titles)-1; $i++) { 
      $resultStr .= "<item>\n";
      $resultStr .="<Title><![CDATA[".$titles[$i]."]]></Title>\n"; 
      $resultStr .="<Description><![CDATA[".$titles[$i]."]]></Description>\n";
      $resultStr .="<PicUrl><![CDATA[".$images[$i]."]]></PicUrl>\n";
      $resultStr .="<Url><![CDATA[".$urls[$i]."]]></Url>\n";
      $resultStr .="</item>\n"; 
    }
    $resultStr .= "</Articles>\n";
    $resultStr .= "<FuncFlag>0</FuncFlag>\n";
    $resultStr .= "</xml> ";
  
    echo $resultStr;
    exit;
}

function return_text($fromUsername,$toUsername,$text){
    include_once("wx_template.php");
    // //回复文字消息
    $msgType = "text";
    $resultStr .= sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $text);
    echo $resultStr;
    exit;
}

function return_notification($fromUsername,$toUsername, $userAPI){
    //fetch data from discourse

    $notiNum = 6;
    $json_prefix = "http://www.e1zone.de/notifications.json?";
    $json_url = $json_prefix.$userAPI;
    $json = file_get_contents($json_url);
    if ($json != null){
        $notifications = json_decode($json);
    } else{
        throw new Exception("Error Json Request", 1);
    }
    $titles = array();
    $images = array();
    $urls = array();
    $categories = array(
        "1" => "%s 在 %s 中@了你",
        "2" => "%s 回复了你的帖子 “%s”",
        "4" => "%s 修改了主题： “%s”",
        "5" => "%s 赞了 “%s”",
        "6" => "私信: %s “%s”。",
        "9" => "%s 回复了你的主题 “%s”",
        "12" => "您获得了徽章“%s”.",
    );
    
    for ($i=0; $i <= $notiNum-1 && $i <= count($notifications)-1; $i++) { 
        if ($notifications[$i]->read == false) {
            $titles[$i] = "[未读]";
        }
        if ($notifications[$i]->notification_type == "12") {
            // badge information is different.
            $titles[$i] .= sprintf($categories[$notifications[$i]->notification_type], 
                                    $notifications[$i]->data->badge_name);
            $urls[$i] = hostname."badges/".$notifications[$i]->data->badge_id;

        }else{
            $titles[$i] .= sprintf($categories[$notifications[$i]->notification_type],
                                    $notifications[$i]->data->original_username, 
                                    $notifications[$i]->data->topic_title);
            $urls[$i] = hostname."/t/".$notifications[$i]->slug;
        }
        
    }   

    $resultStr ="<xml>\n";
    $resultStr .="<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n";
    $resultStr .="<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n";
    $resultStr .="<CreateTime>".time()."</CreateTime>\n";
    $resultStr .="<MsgType><![CDATA[news]]></MsgType>\n";
    $resultStr .="<ArticleCount>".count($titles)."</ArticleCount>\n";
    $resultStr .="<Articles>\n";
    
    for ($i=0; $i <= count($titles)-1; $i++) { 
      $resultStr .= "<item>\n";
      $resultStr .="<Title><![CDATA[".$titles[$i]."]]></Title>\n"; 
      $resultStr .="<Description><![CDATA[".$titles[$i]."]]></Description>\n";
      $resultStr .="<PicUrl><![CDATA[".$images[$i]."]]></PicUrl>\n";
      $resultStr .="<Url><![CDATA[".$urls[$i]."]]></Url>\n";
      $resultStr .="</item>\n"; 
    }
    $resultStr .= "</Articles>\n";
    $resultStr .= "<FuncFlag>0</FuncFlag>\n";
    $resultStr .= "</xml> ";
  
    echo $resultStr;
    exit;
}

?>