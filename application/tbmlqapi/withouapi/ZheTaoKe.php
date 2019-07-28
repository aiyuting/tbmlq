<?php
namespace app\tbmlqapi\withouapi;

use app\tbmlqapi\tool\Curl;
use think\Controller;

/**
 * 折淘客的api http://www.zhetaoke.com
 * Class ZheTaoKe
 * @package app\tbmlqapi\withouapi
 */
class ZheTaoKe extends Controller
{
    //折淘客的对接秘钥appkey
    private $appkey = "2bf9a3d2e35e452e883edb0ba15d251e";
    //对应的淘客账号授权ID
    private $sid = 20148;
    //对应的淘客账号pid
    private $pid = 'mm_130728145_634850168_109199600246';
    //api路径
    private $apiUrl = 'https://api.zhetaoke.com:10001/api/';
    /**
     * 高拥转链api 商品id
     */
    public function gaoyongApiShopId($shopId)
    {
        $url = $this->apiUrl."open_gaoyongzhuanlian.ashx?appkey={$this->appkey}&sid={$this->sid}&pid={$this->pid}&num_iid={$shopId}&signurl=1";
        $result = json_decode(Curl::send($url,'','get'),true);
        $result = json_decode(Curl::send($result['url'],'','get'),true);

        return $result['tbk_privilege_get_response']['result']['data'];
    }

    /**
     * 获取商品id api
     */
    public function getShopId($Oldurl)
    {
        $url = $this->apiUrl."open_shangpin_id.ashx?appkey={$this->appkey}&sid={$this->sid}&content={$Oldurl}&type=0";
        $result = json_decode(Curl::send($url,'','get'),true);
        return $result;
    }

    /**
     * 获取淘口令
     */
    public function getTkl($Oldurl,$logo)
    {
        $text = '买领券精心推荐';
        $url = $this->apiUrl."open_tkl_create.ashx?appkey={$this->appkey}&sid={$this->sid}&text={$text}&url={$Oldurl}&logo={$logo}&signurl=1";
        $result = json_decode(Curl::send($url,'','get'),true);
        $result = json_decode(Curl::send($result['url'],'','get'),true);
        return $result['tbk_tpwd_create_response']['data'];
    }

    /**
     * 商品详情api
     */

    public function getItemInfo($shopId)
    {
        $url = $this->apiUrl."api_detail.ashx?appkey={$this->appkey}&tao_id={$shopId}";
        $result = json_decode(Curl::send($url,'','get'),true);
        return $result['content'];
    }
}