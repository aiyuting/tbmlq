<?php
namespace app\common\model\tbmlqapi;

use think\Model;

class SzmxLog extends Model
{
    /**
     * 根据userid来获取对应的收支明细.
     * @param $showLength 要显示的数量.
     */
    public static function getListForUserId($showLength)
    {
        $userId = session('userinfo')['id'];
        $result = self::where(['user_id'=>$userId])
            ->order('id','desc')
            ->limit('0',$showLength)
            ->select();
        if(empty($result)){
            return '很抱歉,您还没有收入.';
        }
        $content = "显示最近{$showLength}条："."\r\n";
        foreach ($result as $k => $v) {
            $content.=$v['note'].',金额：'.$v['money'].'元'."\r\n";
        }

        return $content;
    }
}