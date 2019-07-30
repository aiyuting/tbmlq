<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    // 定时任务： 轮训查询付款订单.
    'orderFukuan'	=>	'app\common\command\OrderFukuan',
    // 定时任务： 轮训查询已经结算的订单.
    'orderJiesuan'	=>	'app\common\command\OrderJiesuan',
];
