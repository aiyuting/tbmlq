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
    /**
     * 根据openid获取微信用户的详细信息
     * @param $openId
     * @return mixed
     */
    public static function getWxUserInfo($openId)
    {
        $access_token = GetWxToken::getWxToken();
        $getUserInfoUrl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openId}&lang=zh_CN";
        $result = json_decode(Curl::send($getUserInfoUrl,'','get'),true);
        return $result;
    }

    /**
     * 获取带参数的推广二维码(直接使用永久的(最多可十万个用户))
     * @param $openId
     * @return mixed
     */
    public static function getParQrcode($openId)
    {
        $access_token = GetWxToken::getWxToken();
        $getParQrcodeUrl = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $data = "{
                \"action_name\": \"QR_LIMIT_STR_SCENE\", 
                \"action_info\": {
                    \"scene\": {
                        \"scene_str\": \"{$openId}\"
                    }
                }
            }";
        $result = json_decode(Curl::send($getParQrcodeUrl,$data,'post'),true);
        return $result;
    }
}