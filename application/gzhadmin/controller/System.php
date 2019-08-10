<?php
namespace app\gzhadmin\controller;

use app\common\model\tbmlqapi\SysConfig;
use app\common\model\tbmlqapi\UserLevel;
use think\Request;

/**
 * 系统设置
 * Class System
 * @package app\gzhadmin\controller
 */
class System extends Base
{
    public function index(Request $request)
    {
        $sysconfig = SysConfig::find();
        if($request->isPost()){
            if(empty($sysconfig)){
                $sysconfig = new SysConfig();
            }
            $sysconfig->yj_bl = $request->post('yjbl') ?? '';
            $sysconfig->wx_appid = $request->post('gzhappid') ?? '';
            $sysconfig->wx_appsecret = $request->post('gzhappid') ?? '';
            $sysconfig->user_tblm_pid = $request->post('tblmpid') ?? '';
            $sysconfig->ztk_appkey = $request->post('ztkappkey') ?? '';
            $sysconfig->ztk_sid = $request->post('ztksid') ?? '';
            $saveResult = $sysconfig->save();
            if($saveResult){
                $this->success('修改成功');
            }
        }
        $this->assign('sysconfig',$sysconfig);
        return $this->fetch();
    }

    public function level()
    {
        $level = UserLevel::select();
        $this->assign('level',$level);
        return $this->fetch();
    }
    public function levelEdit(Request $request)
    {
        $id = $request->get('id');
        $level = UserLevel::find($id);
        if($request->isPost()) {
            $level->name = $request->post('name') ?? '';
            $level->where_num = $request->post('where_num') ?? '';
            $level->one_bili = $request->post('one_bili') ?? '';
            $level->two_bili = $request->post('two_bili') ?? '';
            $result = $level->save();
            if($result){
                $this->success('修改成功');
            }
        }
        $this->assign('list',$level);
        return $this->fetch();
    }
}