<?php
namespace app\gzhadmin\controller;


use app\common\model\tbmlqapi\GzhAdminUser;
use think\Controller;
use think\Request;

class Login extends Controller
{
    public function index(Request $request)
    {
        if($request->isPost()){
            $username = $request->post('username');
            $password = $request->post('password');
            if(empty($username) || empty($password)){
                return $this->error('账号密码不能为空');
            }
            $checkResult = GzhAdminUser::checkAdminUser($username,$password);
            if(empty($checkResult)){
                return $this->error('账号密码不正确,请重新输入.');
            }
            $this->success('登录成功,跳转中......','home/index');
        }
        return $this->fetch();
    }

    public function logout()
    {
        if(!empty(session('gzhadmininfo'))){
            session('gzhadmininfo',null);
        }
        $this->success('已退出.','login/index');
    }
}