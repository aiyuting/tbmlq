<?php
namespace app\common\model\tbmlqapi;

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
     * 付款订单的处理
     * @param $pid 推广位id
     * @param $orderNum 订单号
     * @param $itemId 商品id
     */
    public static function fukuanchuli($pid,$orderNum,$itemId)
    {
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
        $user = GuanzhuUserInfo::field('id','tb_order_num')
            ->where(['openid'=>$openid['openid']])
            ->find();
        if($user['tb_order_num'] == $orderNumHou6wei){
            $user->dongjie_money = session($itemId);
            $saveUserInfoResult = $user->save();
        }else{
            $user->tb_order_num = $orderNumHou6wei;
            $user->dongjie_money = session($itemId);
            $saveUserInfoResult = $user->save();
        }
        if($saveUserInfoResult){
            //删除用户的搜索记录。
            if($openid->delete()){
                return true;
            }
        }




    }
}