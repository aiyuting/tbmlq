<?php
namespace app\gzhadmin\controller;

use think\App;
use think\Controller;

class Base extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if(empty(session('gzhadmininfo'))){
            $this->error('请登录.','login/index');
        }
    }
}