<?php
namespace app\tbmlqapi\tool;

use think\Controller;
use think\facade\Config;

class GetWxToken extends Controller
{
    public static function getWxToken()
    {
        if(!empty(session('access_token'))){
            $data = session('access_token');
        }else{
            $appId = GetSysConfig::sysConfig()['wx_appid'];
            $appSecret = GetSysConfig::sysConfig()['wx_appsecret'];
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appSecret;
            $shuju = json_decode(Curl::send($url,'','get'));
            if(!empty($shuju->errcode)){
                ReposeText::reposeText($shuju);
            }
            $data = $shuju->access_token;

            session('access_token',$data);
        }

        return $data;
    }
}