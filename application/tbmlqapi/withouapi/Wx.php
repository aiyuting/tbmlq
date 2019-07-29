<?php
namespace app\tbmlqapi\withouapi;

use app\tbmlqapi\tool\Curl;
use app\tbmlqapi\tool\GetWxToken;
use think\Controller;

/**
 * 微信官方api
 * Class Wx
 * @package app\tbmlqapi\withouapi
 */
class Wx extends Controller
{
    public static function getWxUserInfo($openId)
    {
        $access_token = GetWxToken::getWxToken();
        $getUserInfoUrl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openId}&lang=zh_CN";
        $result = json_decode(Curl::send($getUserInfoUrl,'','get'),true);
        return $result;
    }
}