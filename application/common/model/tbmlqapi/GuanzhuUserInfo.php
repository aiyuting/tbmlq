<?php
namespace app\common\model\tbmlqapi;

use think\Model;
use think\Url;

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


    /**
     * 根据openid 查询是否存在 tb_order_num (用户淘宝账号对应的淘宝订单后六位。) 此字段
     */

    public static function isTbOrderNum()
    {
        $openId = session('wxuserinfo')->FromUserName;
        $result = self::field('tb_order_num')
            ->where(['openid'=>$openId])
            ->find();
        if(!empty($result['tb_order_num'])){
            return true;
        }
        return false;
    }
}