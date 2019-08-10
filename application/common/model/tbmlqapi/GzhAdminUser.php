<?php
namespace app\common\model\tbmlqapi;

use think\Model;

/**
 * 公众号后台管理的用户
 * Class GzhAdminUser
 * @package app\common\model\tbmlqapi
 */
class GzhAdminUser extends Model
{
    public static function checkAdminUser($username,$password)
    {
        $result = self::where(['username'=>$username])
            ->where(['password'=>md5($password)])
            ->find();
        if(!empty($result)){
            session('gzhadmininfo',$result);
        }
        return $result;
    }
}