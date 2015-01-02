<?php
include_once("wx_functions.php");
include_once("db_operations.php");


//获取微信发送数据
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

  //返回回复数据
if (!empty($postStr)){
        //解析数据
          $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        //发送消息方ID
          $fromUsername = $postObj->FromUserName;
      //接收消息方ID
          $toUsername = $postObj->ToUserName;
     //消息类型
          $form_MsgType = $postObj->MsgType;
      //事件消息
          if($form_MsgType=="event"){
            //获取事件类型
            $form_Event = $postObj->Event;
            //订阅事件
            if($form_Event=="subscribe"){
              //welcome message
              return_welcome($fromUsername,$toUsername);
            }
          }
          if($form_MsgType=="text"){
            $form_Content = trim($postObj->Content);
            if (!empty($form_Content)) {
              if ($form_Content == "?" ||$form_Content == "？") {
                return_text($fromUsername, $toUsername, "回复  ”最新“  查看东一区最新主题\n\n回复  ”精选“  查看东一区每周精选\n\n回复  ”绑定“  绑定您的东一区帐号\n\n回复  ”解除绑定“  取消您东一区帐号和微信的绑定\n\n绑定账户之后回复  ”消息“  查看您东一区账户消息和提醒。");
              }elseif ($form_Content == "绑定") {
                return_text($fromUsername, $toUsername, "请输入东一区用户8位绑定key:");
              }elseif (preg_match("/[A-Za-z0-9]+/", $form_Content) && strlen($form_Content) == 8) {
                // check input, trigger only when input is combination of letters and numbers.
                $text = db_operation("bind",$form_Content,$fromUsername);
                return_text($fromUsername, $toUsername,$text);
              }elseif ($form_Content == "解除绑定"){
                $text = db_operation("unbind",$fromUsername);
                return_text($fromUsername, $toUsername,$text);
              }elseif ($form_Content == "最新") {
                return_posts("latest",$fromUsername,$toUsername);
              }elseif ($form_Content == "精选") {
                return_posts("weekly",$fromUsername,$toUsername);
              }elseif ($form_Content == "消息") {
                $userAPI = db_operation("getNotification",$fromUsername);
                // return_text($fromUsername, $toUsername, $userAPI);
                if ($userAPI == "miss") { // not activated yet.
                  return_text($fromUsername, $toUsername,"对不起，您还未与任何东一区帐号绑定。");
                }else{
                  return_notification($fromUsername,$toUsername,$userAPI);
                }
              }else{
              }
              return_text($fromUsername, $toUsername, "输入”？“查看使用说明");
            }else{
              return_text($fromUsername, $toUsername, "输入”？“查看使用说明");
              //回复tuwen消息
            }
          }
  }else{
          echo "数据为空！";
          exit;
  }

// ?>