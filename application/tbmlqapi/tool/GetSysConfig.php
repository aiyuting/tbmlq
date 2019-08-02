<?php

namespace app\tbmlqapi\tool;

use app\common\model\tbmlqapi\SysConfig;
use think\Controller;

class GetSysConfig extends Controller
{
    public static function sysConfig()
    {
        if(!empty(session('sysConfig'))){
            $sysConfig = session('sysConfig');
        }else{
            //获取当前系统的设置.存储到session里面
            $sysConfig = SysConfig::find();
            if(empty($sysConfig)){
                ReposeText::reposeText('请管理员配置系统后在进行使用.');
                exit;
            }
            session('sysConfig',$sysConfig);
        }

        return $sysConfig;
    }
}