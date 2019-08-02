<?php
namespace app\tbmlqapi\withouapi;

use app\tbmlqapi\tool\Curl;
use think\App;
use think\Controller;

/**
 * 折淘客的api http://www.zhetaoke.com
 * Class ZheTaoKe
 * @package app\tbmlqapi\withouapi
 */
class ZheTaoKe extends Controller
{
    //折淘客的对接秘钥appkey
    private $appkey;
    //对应的淘客账号授权ID
    private $sid;
    //对应的淘客账号pid前缀.
    private $pidPrefix;
    //对应的淘客账号pid.
    private $pid;
    //api路径
    private $apiUrl = 'https://api.zhetaoke.com:10001/api/';

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->appkey = session('sysConfig')['ztk_appkey'];
        $this->sid = session('sysConfig')['ztk_sid'];
        $this->pidPrefix = session('sysConfig')['user_tblm_pid'];
    }

    /**
     * 高拥转链api 商品id
     */
    public function gaoyongApiShopId($shopId,$givePidToItemId)
    {
        if(!empty($givePidToItemId)){
            $givePidToItemId = $this->pidPrefix.$givePidToItemId;
            $this->pid = $givePidToItemId;
        }
        $url = $this->apiUrl."open_gaoyongzhuanlian.ashx?appkey={$this->appkey}&sid={$this->sid}&pid={$this->pid}&num_iid={$shopId}&signurl=1";
        $result = json_decode(Curl::send($url,'','get'),true);
        $result = json_decode(Curl::send($result['url'],'','get'),true);

        return $result['tbk_privilege_get_response']['result']['data'] ?? '';
    }

    /**
     * 获取商品id api
     */
    public function getShopId($Oldurl)
    {
        $Oldurl = urlencode($Oldurl);
        $url = $this->apiUrl."open_shangpin_id.ashx?appkey={$this->appkey}&sid={$this->sid}&content={$Oldurl}&type=0";
        $result = json_decode(Curl::send($url,'','get'),true);
        return $result ?? '';
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
        return $result['tbk_tpwd_create_response']['data'] ?? '';
    }

    /**
     * 商品详情api
     */

    public function getItemInfo($shopId)
    {
        $url = $this->apiUrl."api_detail.ashx?appkey={$this->appkey}&tao_id={$shopId}";
        $result = json_decode(Curl::send($url,'','get'),true);
        $contentArr = $result['content'] ?? '';
        //如果就一个 那么就直接返回吧。
        if(count($contentArr) == 1){
            return $contentArr[0];
        }
        if(!empty($contentArr) && is_array($contentArr)){
            foreach ($contentArr as $k => $v) {
                //优先选取G券
                if($v['code'] == 0){
                    return $v;
                }
            }
        }else{
            return '';
        }

    }

    /**
     * 订单查询API接口
     * @param $order_query_type 订单查询类型，创建时间“create_time”，或结算时间“settle_time”
     * @param $tk_status 订单状态
     * @param $lunxunTimeMin 轮训的时间 /分钟
     * @return string
     */

    public function selectTaoKeOrder($order_query_type,$tk_status,$lunxunTimeMin)
    {
        //开始时间
        $start_time = urlencode(date("Y-m-d H:i:s", strtotime("-{$lunxunTimeMin} minute")));
        $span = $lunxunTimeMin * 60;
        $page_size = 100;
        $visitUrl = $this->apiUrl."open_dingdanchaxun.ashx?appkey={$this->appkey}&sid={$this->sid}&start_time={$start_time}&span={$span}&page_size={$page_size}&signurl=1&order_query_type={$order_query_type}&tk_status={$tk_status}";
        $result = json_decode(Curl::send($visitUrl,'','get'),true);
        $result = json_decode(Curl::send($result['url'],'','get'),true);
        return $result['tbk_sc_order_get_response']['results']['n_tbk_order'] ?? '';
    }
}