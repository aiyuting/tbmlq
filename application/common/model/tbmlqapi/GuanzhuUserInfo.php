<?php
namespace app\common\model\tbmlqapi;

use think\Model;

/**
 * 关注用户的表
 * Class GuanzhuUserInfo
 * @package app\common\model\tbmlqapi
 */
class GuanzhuUserInfo extends Model
{
    /**
     * 根据openid删除一个用户信息.(取消关注的时候调用, 其他不可调用.)
     */
    public static function delUserForOpenId($openId)
    {
        $result = self::where(['openid'=>$openId])
            ->delete();
        return $result;
    }
}