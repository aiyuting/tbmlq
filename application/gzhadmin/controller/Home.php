<?php
namespace app\gzhadmin\controller;

class Home extends Base
{
    /**
     * 后台首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }
}