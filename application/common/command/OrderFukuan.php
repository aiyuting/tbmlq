<?php
namespace app\common\command;


use app\common\model\tbmlqapi\TaobaokeOrderList;
use app\tbmlqapi\withouapi\ZheTaoKe;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * 定时任务： 轮训查询付款订单.
 * Class OrderLunxun
 * @package app\command\OrderLunxun
 */
class OrderFukuan extends Command
{
    protected function configure()
    {
        $this->setName('orderFukuan')
            ->setDescription('Lunxun select fukuan Order');
    }

    protected function execute(Input $input, Output $output)
    {
        $zhetaoke = new ZheTaoKe();
        //返回来一个订单数组.
        $orderList = $zhetaoke->selectTaoKeOrder('create_time',12,20); //此处之后查询出来代付款的订单之后, 可以存储到redis中,然后来循环状态,循环成功入库操作.
        //此处可以后期优化成redis  用队列在处理一遍.以防后期数据量大了 数据存储不完整。
        if(!empty($orderList)){
            foreach ($orderList as $k => $v){
                //如果订单付款了.
                TaobaokeOrderList::fukuanchuli($v['adzone_id'],$v['trade_id'],$v['num_iid'],$v['pub_share_pre_fee']);
                //如果订单号一样 那么就修改
                $trade_id_re = TaobaokeOrderList::field('id')->where(['trade_id'=>$v['trade_id']])->find();
                if(!$trade_id_re){
                    $taobaokeOrerList = new TaobaokeOrderList();
                    $taobaokeOrerList->adzone_id = $v['adzone_id'] ?? '';
                    $taobaokeOrerList->adzone_name = $v['adzone_name'] ?? '';
                    $taobaokeOrerList->alipay_total_price = $v['alipay_total_price'] ?? '';
                    $taobaokeOrerList->auction_category = $v['auction_category'] ?? '';
                    $taobaokeOrerList->commission = $v['commission'] ?? '';
                    $taobaokeOrerList->commission_rate = $v['commission_rate'] ?? '';
                    $taobaokeOrerList->create_time = $v['create_time'] ?? '';
                    $taobaokeOrerList->income_rate = $v['income_rate'] ?? '';
                    $taobaokeOrerList->item_num = $v['item_num'] ?? '';
                    $taobaokeOrerList->item_title = $v['item_title'] ?? '';
                    $taobaokeOrerList->num_iid = $v['num_iid'] ?? '';
                    $taobaokeOrerList->order_type = $v['order_type'] ?? '';
                    $taobaokeOrerList->pay_price = $v['pay_price'] ?? '';
                    $taobaokeOrerList->price = $v['price'] ?? '';
                    $taobaokeOrerList->pub_share_pre_fee = $v['pub_share_pre_fee'] ?? '';
                    $taobaokeOrerList->seller_nick = $v['seller_nick'] ?? '';
                    $taobaokeOrerList->seller_shop_title = $v['seller_shop_title'] ?? '';
                    $taobaokeOrerList->site_id = $v['site_id'] ?? '';
                    $taobaokeOrerList->site_name = $v['site_name'] ?? '';
                    $taobaokeOrerList->subsidy_fee = $v['subsidy_fee'] ?? '';
                    $taobaokeOrerList->subsidy_rate = $v['subsidy_rate'] ?? '';
                    $taobaokeOrerList->subsidy_type = $v['subsidy_type'] ?? '';
                    $taobaokeOrerList->tk3rd_type = $v['tk3rd_type'] ?? '';
                    $taobaokeOrerList->tk_status = $v['tk_status'] ?? '';
                    $taobaokeOrerList->total_commission_fee = $v['total_commission_fee'] ?? '';
                    $taobaokeOrerList->total_commission_rate = $v['total_commission_rate'] ?? '';
                    $taobaokeOrerList->trade_id = $v['trade_id'] ?? '';
                    $taobaokeOrerList->trade_parent_id = $v['trade_parent_id'] ?? '';
                    $taobaokeOrerList->save();
                }else{
                    $trade_id_re->adzone_id = $v['adzone_id'] ?? '';
                    $trade_id_re->adzone_name = $v['adzone_name'] ?? '';
                    $trade_id_re->alipay_total_price = $v['alipay_total_price'] ?? '';
                    $trade_id_re->auction_category = $v['auction_category'] ?? '';
                    $trade_id_re->commission = $v['commission'] ?? '';
                    $trade_id_re->commission_rate = $v['commission_rate'] ?? '';
                    $trade_id_re->create_time = $v['create_time'] ?? '';
                    $trade_id_re->income_rate = $v['income_rate'] ?? '';
                    $trade_id_re->item_num = $v['item_num'] ?? '';
                    $trade_id_re->item_title = $v['item_title'] ?? '';
                    $trade_id_re->num_iid = $v['num_iid'] ?? '';
                    $trade_id_re->order_type = $v['order_type'] ?? '';
                    $trade_id_re->pay_price = $v['pay_price'] ?? '';
                    $trade_id_re->price = $v['price'] ?? '';
                    $trade_id_re->pub_share_pre_fee = $v['pub_share_pre_fee'] ?? '';
                    $trade_id_re->seller_nick = $v['seller_nick'] ?? '';
                    $trade_id_re->seller_shop_title = $v['seller_shop_title'] ?? '';
                    $trade_id_re->site_id = $v['site_id'] ?? '';
                    $trade_id_re->site_name = $v['site_name'] ?? '';
                    $trade_id_re->subsidy_fee = $v['subsidy_fee'] ?? '';
                    $trade_id_re->subsidy_rate = $v['subsidy_rate'] ?? '';
                    $trade_id_re->subsidy_type = $v['subsidy_type'] ?? '';
                    $trade_id_re->tk3rd_type = $v['tk3rd_type'] ?? '';
                    $trade_id_re->tk_status = $v['tk_status'] ?? '';
                    $trade_id_re->total_commission_fee = $v['total_commission_fee'] ?? '';
                    $trade_id_re->total_commission_rate = $v['total_commission_rate'] ?? '';
                    $trade_id_re->trade_id = $v['trade_id'] ?? '';
                    $trade_id_re->trade_parent_id = $v['trade_parent_id'] ?? '';
                    $trade_id_re->save();
                }

            }
        }
    }
}