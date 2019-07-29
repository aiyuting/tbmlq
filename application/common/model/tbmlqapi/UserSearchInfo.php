<?php
namespace app\common\model\tbmlqapi;

use think\Model;

class UserSearchInfo extends Model
{
    /**
     * 查询表中 用户和商品id的信息
     */
    public static function findUserAndItemId($ItemId,$FromUserName)
    {
        $result = self::field('id')
            ->where(['itemid'=>$ItemId])
            ->where(['openid'=>$FromUserName])
            ->find();
        return $result['id'];
    }

    /**
     * 查询表中 商品id对应的pid
     */
    public static function selectItemIdPid($ItemId)
    {
        $result = self::field('tk_pid')
            ->where(['itemid'=>$ItemId])
            ->select();
        //用来存放tk_pid的数组 一位数组
        $resultArr = [];
        foreach ($result as $k => $v) {
            array_push($resultArr,$v['tk_pid']);
        }
        return $resultArr;
    }
}