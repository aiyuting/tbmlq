<?php
namespace app\tbmlqapi\controller;


use app\tbmlqapi\tool\AddWxMenu;
use think\Controller;

class WxMenu extends Controller
{
    /**
     * 创建菜单
     */

    public function createMenu()
    {
        $postArr = [
            'button' => [
                [
                    'name' => '帮助中心',
                    'sub_button' => [
                        [
                            "type"=>"click",
                            "name"=>"领券商城",
                            "key"=>"lqshop"
                        ],
                        [
                            "type"=>"view",
                            "name"=>"人工客服",
                            "url"=>"http://www.mengqy.cn"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"帮助指令",
                            "key"=>"helpCommand"
                        ],
                        [
                            "type"=>"view",
                            "name"=>"使用教程",
                            "url"=>"http://www.mengqy.cn"
                        ],
                        [
                            "type"=>"view",
                            "name"=>"提现演示",
                            "url"=>"http://www.mengqy.cn"
                        ],
                    ],
                ],
                [
                    'name' => '福利中心',
                    'sub_button' => [
                        [
                            "type"=>"click",
                            "name"=>"生成推广海报",
                            "key"=>"qrcode"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"会员等级",
                            "key"=>"vipLevel"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"新人奖励",
                            "key"=>"newAward"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"每日签到",
                            "key"=>"sign"
                        ],
                    ],
                ],
                [
                    'name' => '个人中心',
                    'sub_button' => [
                        [
                            "type"=>"click",
                            "name"=>"申请提现",
                            "key"=>"sqtx"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"收支明细",
                            "key"=>"szmx"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"我的推广",
                            "key"=>"wdtg"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"我的订单",
                            "key"=>"wddd"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"我的账户",
                            "key"=>"wdzh"
                        ],
                    ],
                ],
            ]
        ];
        $result = AddWxMenu::defindItem($postArr);
        dump($result);die;
    }
}