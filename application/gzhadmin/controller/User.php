<?php
namespace app\gzhadmin\controller;

use app\common\model\tbmlqapi\GuanzhuUserInfo;

class User extends Base
{
    public function index()
    {
        $list = GuanzhuUserInfo::where(['subscribe'=>1])
            ->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
}