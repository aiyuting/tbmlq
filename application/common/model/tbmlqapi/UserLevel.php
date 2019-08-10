<?php
namespace app\common\model\tbmlqapi;

use app\tbmlqapi\tool\ReposeText;
use think\Model;

class UserLevel extends Model
{
    /**
     * 根据用户id获取等级详情
     */
    public static function getInfoForId($id = '',$field = '*')
    {
        if(empty($id)){
            ReposeText::reposeText('getInfoForId方法的openid不能为空');
        }
        $result = self::field($field)
            ->where(['id'=>$id])
            ->find();
        return $result;
    }
}