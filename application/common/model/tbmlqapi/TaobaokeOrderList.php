<?php
namespace app\common\model\tbmlqapi;

use app\tbmlqapi\tool\UserMoney;
use app\tbmlqapi\tool\YonjingJisuan;
use app\tbmlqapi\withouapi\Wx;
use think\facade\Log;
use think\Model;

/**
 * 查询出来的订单表
 * Class TaobaokeOrderList
 * @package app\common\model\tbmlqapi
 */
/*
 * trade_parent_id	Number	123	淘宝父订单号
trade_id	Number	123	淘宝订单号
num_iid	Number	123	商品ID
item_title	String	女装	商品标题
item_num	Number	123	商品数量
price	String	88.00	单价
pay_price	String	85.00	实际支付金额
seller_nick	String	我是卖家	卖家昵称
seller_shop_title	String	XX旗舰店	卖家店铺名称
commission	String	5.00	推广者获得的收入金额，对应联盟后台报表“预估收入”
commission_rate	String	20.00	推广者获得的分成比率，对应联盟后台报表“分成比率”
unid	String	demo	推广者unid（已废弃）
create_time	Date	2015-03-05 10:37:48	淘客订单创建时间
earning_time	Date	2015-03-05 10:37:48	淘客订单结算时间
tk_status	Number	1	淘客订单状态，3：订单结算，12：订单付款， 13：订单失效，14：订单成功
tk3rd_type	String	爱淘宝	第三方服务来源，没有第三方服务，取值为“--”
tk3rd_pub_id	Number	123	第三方推广者ID
order_type	String	天猫	订单类型，如天猫，淘宝
income_rate	String	0.008	收入比率，卖家设置佣金比率+平台补贴比率
pub_share_pre_fee	String	0.03	效果预估，付款金额*(佣金比率+补贴比率)*分成比率
subsidy_rate	String	0.003	补贴比率
subsidy_type	String	1	补贴类型，天猫:1，聚划算:2，航旅:3，阿里云:4
terminal_type	String	2	成交平台，PC:1，无线:2
auction_category	String	办公设备/耗材	类目名称
site_id	String	123	来源媒体ID
site_name	String	返利推广	来源媒体名称
adzone_id	String	123	广告位ID
adzone_name	String	右下广告位	广告位名称
alipay_total_price	String	3.6	付款金额
total_commission_rate	String	0.005	佣金比率
total_commission_fee	String	0.02	佣金金额
subsidy_fee	String	0.01	补贴金额
relation_id	Number	3223	渠道关系ID
special_id	Number	1122	会员运营id
click_time	Date	2015-03-05 10:37:48	跟踪时间
tk_commission_pre_fee_for_media_platform	String	1.05	预估专项服务费：内容场景专项技术服务费，内容推广者在内容场景进行推广需要支付给阿里妈妈专项的技术服务费用。专项服务费＝付款金额＊专项服务费率。
tk_commission_fee_for_media_platform	String	1.05	结算专项服务费：内容场景专项技术服务费，内容推广者在内容场景进行推广需要支付给阿里妈妈专项的技术服务费用。专项服务费＝结算金额＊专项服务费率。
tk_commission_rate_for_media_platform	String	0.01	专项服务费率：内容场景专项技术服务费率，内容推广者在内容场景进行推广需要按结算金额支付一定比例给阿里妈妈作为内容场景专项技术服务费，用于提供与内容平台实现产品技术对接等服务。
 */
class TaobaokeOrderList extends Model
{

    /**
     * 根据数组获取状态的中文
     */
    private static function getTkStatusName($tk_status)
    {
        $tkStName = [3=>'订单结算',12=>'订单付款',13=>'订单失效',14=>'订单成功'];
        return $tkStName[$tk_status];
    }





    /**
     * 付款订单的处理
     * @param $pid 推广位id
     * @param $orderNum 订单号
     * @param $itemId 商品id
     * @param $suoyouyongjin 自己能拿到的所有佣金.
     * @param $allItemData 所有商品数据
     */
    public static function fukuanchuli($pid,$orderNum,$itemId,$suoyouyongjin,$allItemData)
    {
        //如果订单付款已经处理 那么就不需要管了。
        if(TaobaokeOrderList::where(['trade_id'=>$orderNum])->value('fk_cl') == 1){
            return false;
        }
        //取订单号的后六位
        $orderNumHou6wei = substr($orderNum,-6);
        $openid = UserSearchInfo::field('openid')
            ->where(['itemid'=>$itemId])
            ->where(['tk_pid'=>$pid])
            ->find();
        //如果用户搜索表的pid不匹配 那么就搜索用户绑定的订单后六位. 如果还不匹配的话就返回false；
        if(empty($openid)){
            $openid = GuanzhuUserInfo::field('openid')
                ->where(['tb_order_num'=>$orderNumHou6wei])
                ->find();
            if(empty($openid)){
                return false;
            }
        }


        //此处用来判断用户存储的订单后六位是否和当前商品一致. 如果一致的话:就不用判断pid是否相等了..
        $user = GuanzhuUserInfo::field('id,tb_order_num,nickname')
            ->where(['openid'=>$openid['openid']])
            ->find();
        $yongjing = YonjingJisuan::yongjingjisuan('','',$suoyouyongjin); //计算佣金;
        if($user['tb_order_num'] == $orderNumHou6wei){
//            $user->dongjie_money = $yongjing + $user['dongjie_money'];
//            $user->save();
        }else{
            $user->tb_order_num = $orderNumHou6wei;
//            $user->dongjie_money = $yongjing + $user['dongjie_money'];
            $saveUserInfoResult = $user->save();
            if($saveUserInfoResult){
                //删除用户的搜索记录。
                $openid->delete();
            }
        }

        //此处后期要二次修改
        $data = [
            'name' => [
                'value' => $user['nickname']
            ],
            'itemname' => [
                'value' => $allItemData['item_title']
            ],
            'ordernum' => [
                'value' => $allItemData['trade_id']
            ],
            'ordername' => [
                'value' => $allItemData['alipay_total_price']
            ],
            'jldz' => [
                'value' => $yongjing
            ],
            'tkstatus' => [
                'value' => $allItemData['tk_status']
            ],
            'xdsj' => [
                'value' => $allItemData['tb_paid_time']
            ]
        ];
        $ceshi = Wx::seedTemMessage($openid['openid'],'6dp0QlBuchqmkbDbxJsXY0Txc6ZWaZUSobDL7U_7M6g',$data);


    }

    /**
     * 付款订单的处理
     * @param $orderNum 订单号
     * @param $suoyouyongjin 自己能拿到的所有佣金.
     */

    public static function jiesuanchuli($orderNum,$suoyouyongjin)
    {
        //如果订单结算已经处理 那么就不需要管了。
        if(TaobaokeOrderList::where(['trade_id'=>$orderNum])->value('js_cl') == 1){
            return false;
        }


        $yongjing = YonjingJisuan::yongjingjisuan('','',$suoyouyongjin); //计算佣金;
        //取订单号的后六位
        $orderNumHou6wei = substr($orderNum,-6);
        //查询淘宝号对应的用户
        $userId = GuanzhuUserInfo::where(['tb_order_num'=>$orderNumHou6wei])
            ->value('id');
        //如果没有对应的账户 那么就不进行以下的操作
        if(empty($userId)){
            return false;
        }
        UserMoney::userMoney($userId,$yongjing,'订单结算',1,true);




        /*********进行三级分销处理***********/
        $one_user = GuanzhuUserInfo::getSupId($userId);
        //如果不为空的话代表有上级,
        if(!empty($one_user['sj_id'])){
            //如果有上级 那么给上级加钱.
            $one_yongjin = $suoyouyongjin * $one_user['one_bili'] / 100;
            UserMoney::userMoney($one_user['sj_id'],$one_yongjin,'下级返利',1,false);
            $two_user = GuanzhuUserInfo::getSupId($one_user['sj_id']);
            //如果不为空的话代表有上上级
            if(!empty($two_user['sj_id'])){
                $two_yongjin = $suoyouyongjin * $two_user['two_bili'] / 100;
                UserMoney::userMoney($two_user['sj_id'],$two_yongjin,'下下级返利',1,false);
            }
        }
        /***************结束***************/
    }


    /***
     * 获取用户的订单.
     * @param $showLength 需要展示的数量
     */
    public static function getUserOrder($showLength)
    {
        $orderNum = session('userinfo')['tb_order_num'];
        if(empty($orderNum)){
            return '很抱歉,您还没有成功订单.';
        }
        $result = self::where('','exp',"substring(trade_id,-6) = ({$orderNum})")
            ->field('item_title,tk_status')
            ->order('id','desc')
            ->limit(0,$showLength)
            ->select();
        if(empty($result)){
            return '很抱歉,您还没有成功订单.';
        }
        $content = "显示最近{$showLength}条："."\r\n";
        foreach ($result as $k => $v) {
            $num = $k+1;
            $content.="{$num}:".$v['item_title'].'>>>>>>'.self::getTkStatusName($v['tk_status'])."\r\n";
        }
        return $content;
    }
}