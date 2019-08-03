<?php
namespace app\tbmlqapi\tool;

use app\common\model\tbmlqapi\GuanzhuUserInfo;
use app\common\model\tbmlqapi\SzmxLog;
use think\Controller;

/**
 * 增加钱的控制器.
 * Class AddMoney
 * @package app\tbmlqapi\tool
 */
class UserMoney extends Controller
{
    /**
     * @param $userid 被操作的用户id
     * @param $money 多少钱
     * @param $note 加钱的备注
     * @param $type 类型 1:加钱 2:减钱
     * @param bool $isHasDongjie 是否需要减去冻结佣金.
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function userMoney($userid,$money,$note,$type,$isHasDongjie = false)
    {
        $userinfo = GuanzhuUserInfo::field('id,dongjie_money,yunxu_money')
            ->find($userid);
        if($isHasDongjie){
            $userinfo->dongjie_money = $userinfo['dongjie_money'] - $money;
        }
        if($type == 1){
            $userinfo->yunxu_money = $userinfo['yunxu_money'] + $money;
        }else{
            $userinfo->yunxu_money = $userinfo['yunxu_money'] - $money;
        }

        $moneyResult = $userinfo->save();
        if(!$moneyResult){
            return $moneyResult;
        }
        //写入收支明细日志
        $result = self::szmxLog($userid,$money,$note,$type);
        return $result;
    }

    /**
     * @param $note 增加的日志.
     * @param $money 增加的money
     */
    public static function szmxLog($userid,$money,$note,$type)
    {
        $sxmxLog = new SzmxLog();
        $sxmxLog->user_id = $userid;
        $sxmxLog->note = $note;
        $sxmxLog->money = $money;
        $sxmxLog->type = $type;
        $sxmxLog->create_time = date('Y-m-d H:i:s');
        $result = $sxmxLog->save();
        return $result;
    }
}