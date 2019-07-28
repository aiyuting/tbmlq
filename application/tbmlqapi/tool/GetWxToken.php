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
            $appId = Config::get('wx.appId');
            $appSecret = Config::get('wx.appSecret');;
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appSecret;
            $data = json_decode(Curl::send($url,'','get'))->access_token;
            session('access_token',$data);
//        $ch = curl_init();//初始化curl
//        curl_setopt($ch, CURLOPT_URL,$url); //要访问的地址
//        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书验证
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
//        $data = json_decode(curl_exec($ch));
//        if(curl_errno($ch)){
//            var_dump(curl_error($ch)); //若错误打印错误信息
//        }
        }

        return $data;
    }
}