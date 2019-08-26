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
        $orderList = $zhetaoke->selectTaoKeOrder(2,12,20); //此处之后查询出来代付款的订单之后, 可以存储到redis中,然后来循环状态,循环成功入库操作.
        //此处可以后期优化成redis  用队列在处理一遍.以防后期数据量大了 数据存储不完整。
        if(!empty($orderList)){
            foreach ($orderList as $k => $v){
                //如果订单付款了.
                TaobaokeOrderList::fukuanchuli($v['adzone_id'],$v['trade_id'],$v['item_id'],$v['pub_share_pre_fee'],$v);
                //如果订单号一样 那么就修改
                $trade_id_re = TaobaokeOrderList::field('id')->where(['trade_id'=>$v['trade_id']])->find();
                if(empty($trade_id_re)){
                    $taobaokeOrerList = new TaobaokeOrderList();
                    $taobaokeOrerList->tb_paid_time = $v['tb_paid_time'] ?? '';
                    $taobaokeOrerList->tk_paid_time = $v['tk_paid_time'] ?? '';
                    $taobaokeOrerList->pay_price = $v['pay_price'] ?? '';
                    $taobaokeOrerList->pub_share_fee = $v['pub_share_fee'] ?? '';
                    $taobaokeOrerList->trade_id = $v['trade_id'] ?? '';
                    $taobaokeOrerList->tk_order_role = $v['tk_order_role'] ?? '';
                    $taobaokeOrerList->tk_earning_time = $v['tk_earning_time'] ?? '';
                    $taobaokeOrerList->adzone_id = $v['adzone_id'] ?? '';
                    $taobaokeOrerList->pub_share_rate = $v['pub_share_rate'] ?? '';
                    $taobaokeOrerList->refund_tag = $v['refund_tag'] ?? '';
                    $taobaokeOrerList->subsidy_rate = $v['subsidy_rate'] ?? '';
                    $taobaokeOrerList->tk_total_rate = $v['tk_total_rate'] ?? '';
                    $taobaokeOrerList->item_category_name = $v['item_category_name'] ?? '';
                    $taobaokeOrerList->seller_nick = $v['seller_nick'] ?? '';
                    $taobaokeOrerList->pub_id = $v['pub_id'] ?? '';
                    $taobaokeOrerList->alimama_rate = $v['alimama_rate'] ?? '';
                    $taobaokeOrerList->subsidy_type = $v['subsidy_type'] ?? '';
                    $taobaokeOrerList->item_img = $v['item_img'] ?? '';
                    $taobaokeOrerList->pub_share_pre_fee = $v['pub_share_pre_fee'] ?? '';
                    $taobaokeOrerList->alipay_total_price = $v['alipay_total_price'] ?? '';
                    $taobaokeOrerList->item_title = $v['item_title'] ?? '';
                    $taobaokeOrerList->site_name = $v['site_name'] ?? '';
                    $taobaokeOrerList->item_num = $v['item_num'] ?? '';
                    $taobaokeOrerList->subsidy_fee = $v['subsidy_fee'] ?? '';
                    $taobaokeOrerList->alimama_share_fee = $v['alimama_share_fee'] ?? '';
                    $taobaokeOrerList->trade_parent_id = $v['trade_parent_id'] ?? '';
                    $taobaokeOrerList->order_type = $v['order_type'] ?? '';
                    $taobaokeOrerList->tk_create_time = $v['tk_create_time'] ?? '';
                    $taobaokeOrerList->flow_source = $v['flow_source'] ?? '';
                    $taobaokeOrerList->terminal_type = $v['terminal_type'] ?? '';;
                    $taobaokeOrerList->click_time = $v['click_time'] ?? '';
                    $taobaokeOrerList->tk_status = $v['tk_status'] ?? '';
                    $taobaokeOrerList->item_price = $v['item_price'] ?? '';
                    $taobaokeOrerList->item_id = $v['item_id'] ?? '';
                    $taobaokeOrerList->adzone_name = $v['adzone_name'] ?? '';
                    $taobaokeOrerList->total_commission_rate = $v['total_commission_rate'] ?? '';
                    $taobaokeOrerList->item_link = $v['item_link'] ?? '';
                    $taobaokeOrerList->site_id = $v['site_id'] ?? '';
                    $taobaokeOrerList->seller_shop_title = $v['seller_shop_title'] ?? '';
                    $taobaokeOrerList->income_rate = $v['income_rate'] ?? '';
                    $taobaokeOrerList->total_commission_fee = $v['total_commission_fee'] ?? '';
                    $taobaokeOrerList->tk_commission_pre_fee_for_media_platform = $v['tk_commission_pre_fee_for_media_platform'] ?? '';
                    $taobaokeOrerList->tk_commission_fee_for_media_platform = $v['tk_commission_fee_for_media_platform'] ?? '';
                    $taobaokeOrerList->tk_commission_rate_for_media_platform = $v['tk_commission_rate_for_media_platform'] ?? '';
                    $taobaokeOrerList->special_id = $v['special_id'] ?? '';
                    $taobaokeOrerList->relation_id = $v['relation_id'] ?? '';
                    $taobaokeOrerList->fk_cl = 1;
                    $taobaokeOrerList->save();
                }

            }
        }
    }
}