<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//判断是否是正确的邮箱格式;
function isEmail($email){
    $mode = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
    if(preg_match($mode,$email)){
        return true;
    }
    else{
        return false;
    }
}

//是否为手机号
function isPhoneNum($phonenumber)
{
    if(preg_match("/^1[34578]{1}\d{9}$/",$phonenumber)){
        return true;
    }
    else{
        return false;
    }
}

