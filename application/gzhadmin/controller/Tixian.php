<?php
namespace app\gzhadmin\controller;

use app\common\model\tbmlqapi\TixianList;
use think\Request;

class Tixian extends Base
{
    public function index()
    {
        $tixianList = TixianList::select();
        $this->assign('list',$tixianList);
        return $this->fetch();
    }

    public function agree(Request $request)
    {
        $id = $request->get('id');

        $tixian = TixianList::find($id);
        if($tixian['is_agree'] == 1){
            return $this->error('您已经转过账了......');
        }
        $tixian->is_agree = 1;
        $tixian->agree_time = date('Y-m-d H:i:s');
        $result = $tixian->save();
        if($result){
            return $this->success('转账成功');
        }
    }
}