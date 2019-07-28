<?php
namespace app\tbmlqapi\tool;


use think\Controller;

/**
 * 用来回复内容，
 * Class ReposeText
 * @package app\tbmlqapi\tool
 */
class ReposeText extends Controller
{
    public static function reposeText($postObj,$content)
    {
        $toUser 	=  $postObj->FromUserName;
        $fromUser 	=  $postObj->ToUserName;
        $time 		=  time();
        $msgType 	=  'text';
        $tmplateArr = [
            'ToUserName' =>  $toUser,
            'FromUserName' =>  $fromUser,
            'CreateTime' =>  $time,
            'MsgType' =>  $msgType,
            'Content' =>  $content,
        ];
        $template   =  ArrayToXml::arrayToXml($tmplateArr);
        echo $template;
    }
}