<?php
namespace app\gzhadmin\controller;

use app\common\model\tbmlqapi\TaobaokeOrderList;

class Order extends Base
{
    public function index()
    {
        $list = TaobaokeOrderList::order('id','desc')
            ->paginate(10);
        $this->assign('list',$list);
        return $this->fetch();
    }
}