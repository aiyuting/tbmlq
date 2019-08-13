<?php
namespace app\gzhadmin\controller;

use app\common\model\tbmlqapi\SzmxLog;
use think\Request;

class Szmx extends Base
{
    public function index()
    {
        $list = SzmxLog::order('id','desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
}