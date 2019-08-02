<?php
namespace app\tbmlqapi\tool;

use think\Controller;

/**
 * 佣金结算的工具
 * Class YonjingJiesuan
 * @package app\tbmlqapi\tool
 */
class YonjingJisuan extends Controller
{
    /**
     * 佣金结算
     * @param $quanhoujia 券后价格
     * @param $bili 赚钱比例
     * @return float
     */
    public static function yongjingjisuan($quanhoujia,$bili,$suoyouyongjin='')
    {
        //如果没设置佣金比例 那么就默认9成
        $sysYongjingBl = session('sysConfig')['yj_bl'] / 100 ?? 0.9;
        if(!empty($suoyouyongjin)){
            $yongjin = round($suoyouyongjin * 0.9,2);//商品的全部佣金.(保留两位);
        }else{
            $yongjin = round($quanhoujia * $bili * 0.9 / 100,2);//商品的全部佣金.(保留两位);
        }

        $yongjin = round($yongjin * $sysYongjingBl,2);
        return $yongjin;
    }
}