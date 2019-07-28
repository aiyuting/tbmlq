<?php
namespace app\tbmlqapi\tool;

use think\Controller;

/**
 * 创建微信公众号菜单.
 * Class AddWxMenu
 * @package app\tbmlqapi\tool
 */
class AddWxMenu extends Controller
{

    public static function defindItem($postArr)
    {
        if(!is_array($postArr)){
            exit('必须为数组');
        }
        $accessToken = GetWxToken::getWxToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accessToken;
        $postArr = $postArr;
        $postJson = json_encode($postArr,JSON_UNESCAPED_UNICODE);
        $result = json_decode(Curl::send($url,$postJson,'post'),true);
        return $result;
    }
}