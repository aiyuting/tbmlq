<?php
namespace app\tbmlqapi\controller;

use app\tbmlqapi\tool\ArrayToXml;
use app\tbmlqapi\tool\ReposeText;
use app\tbmlqapi\withouapi\ZheTaoKe;
use think\Controller;
use think\facade\Config;

class Index extends Controller
{

    private $wechatToken = 'wenhao';
    private $postObj;
    public function index()
    {
        return 'this is for Wechat';
    }

    //用户首次开发环境配置
    public function echoStr()
    {
        /*获取微信发送确认的参数。*/
        $token = $this->wechatToken;
        $signature = input('signature');/*微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。*/
        $timestamp = input('timestamp'); /*时间戳 */
        $nonce = input('nonce'); /*随机数 */
        $echostr = input('echostr'); /*随机字符串*/
        /*加密/校验流程*/
        /*1. 将token、timestamp、nonce三个参数进行字典序排序*/
        $array = [$token,$timestamp,$nonce];
        sort($array,SORT_STRING);
        /*2. 将三个参数字符串拼接成一个字符串进行sha1加密*/
        $str = sha1( implode($array) );
        /*3. 开发者获得加密后的字符串可与signature对比，标识该请求来源于微信*/
        if( $str==$signature && $echostr ){
            return $echostr;
        }else{
            $this->reposeMsg();
        }
    }

    //回复消息
    public function reposeMsg()
    {
        //1.接受数据
        $postArr = file_get_contents("php://input");	//接受xml数据
        //2.处理消息类型,推送消息
        $postObj = simplexml_load_string( $postArr );	//将xml数据转化为对象
        $this->postObj = $postObj;
        //获取msgType
        $msgType = strtolower( $postObj->MsgType );
        switch ($msgType)
        {
            case $msgType == 'event':
                //关注公众号事件
                if( strtolower( $postObj->Event ) == 'subscribe' )
                {
                    $this->guanzhuGzh();
                }
                //单机菜单事件
                if( strtolower( $postObj->Event ) == 'click' )
                {
                    $this->clickMenu(strtolower( $postObj->EventKey ));
                }
                break;
            //回复文本消息
            case $msgType == 'text';
                $this->outMessage(trim( $postObj->Content ));
                break;
        }
    }
    /**
     * 单机菜单事件
     */
    public function clickMenu($key)
    {
        switch ($key)
        {
            case 'lqshop':
                $content = "<a href='http://www.mengqy.cn'>▶点击进入綯寳领券商城>></a>
- - - - - - - - - -
★领券商城下单的必须发送订单号手动跟单";
                break;
            case 'helpCommand':
                $content = Config::get('message.help');
                break;
            default:
                $content = '联系开发者,此处未完成';
                break;
        }

        ReposeText::reposeText($this->postObj,$content);
    }




    /**
     * 回复消息
     */
    public function outMessage($text)
    {
        switch( $text )
        {
            case in_array(mb_substr(trim($text),0,1),['搜','买','领']):
                $searchText = mb_substr(trim($text),1);
                $url = 'http://www.mengqy.cn/index.php?input=2&r=l&kw='.$searchText;
                $content = "[玫瑰]您好亲，已经为您搜索到相关优惠产品~\r\n
<a href=\"{$url}\">▶点击查看綯寳商品>></a>\r\n
- - - - - - - - - -
提示：领券商城购买，必须手动发送订单号至公众号才能跟单哦~";
                break;
            case '帮助':
                $content = Config::get('message.help');
                break;
            default:
                $zhetaoke = new ZheTaoKe();
                //调用查询商品id接口
                $shopId = $zhetaoke->getShopId($text)['item_id'] ?? '';
                if(!empty($shopId)){
                    ReposeText::reposeText($this->postObj,'正在努力查询中,请稍后...');
                    //获取商品详情api
                    $itemInfo = $zhetaoke->getItemInfo($shopId);
                    //调取转链api
                    $gaoyongInfo = $zhetaoke->gaoyongApiShopId($shopId);

                    //如果有优惠券,那么就走二合一链接
                    if(!empty($gaoyongInfo['coupon_remain_count'])){
                        $url = $gaoyongInfo['coupon_click_url'];
                    }else{
                        $url = $gaoyongInfo['item_url'];
                    }

                    //调用转换淘口令链接.
                    $tkl = $zhetaoke->getTkl($url,$itemInfo['pict_url'])['model'];

                    //用到的变量。
                    $logo = $itemInfo['pict_url'];//商品logo
                    $yuanjia = $itemInfo['size'];//原价
                    $quanhoujia = $itemInfo['quanhou_jiage'];//券后价格
                    $shangpingName = $itemInfo['title'];//商品名字
                    //此处所有佣金. (高级用户,后期需要配合设置的百分比进行.)
                    $yongjin = $itemInfo['tkrate3'] * 0.9;//商品的全部佣金.
                    $kapianArr = [
                        'title'=>$shangpingName,
                        'description'=>"原价：$yuanjia 券后价格：$quanhoujia 佣金：$yongjin",
                        'picUrl'=>$logo,
                        'url'=>'http://www.mengqy.cn',
                    ];
                    //卡片.
                    $template = "<xml>
                     <ToUserName><![CDATA[$this->postObj->FromUserName]]></ToUserName>
                     <FromUserName><![CDATA[$this->postObj->ToUserName]]></FromUserName>
                     <CreateTime>time()</CreateTime>
                     <MsgType><![CDATA[news]]></MsgType>
                     <ArticleCount>count($kapianArr)</ArticleCount>
                     <Articles>
                        <item>
                        <Title><![CDATA[{$kapianArr['title']}]]></Title>
                        <Description><![CDATA[{$kapianArr['description']}]]></Description>
                        <PicUrl><![CDATA[{$kapianArr['picUrl']}]]></PicUrl>
                        <Url><![CDATA[{$kapianArr['url']}]></Url>
                        </item>
                    </Articles>
                        </xml>";
                    $content = $template;
                }else{
                    $content = Config::get('message.otherMessage');
                }
                break;
        }
        ReposeText::reposeText($this->postObj,$content);
    }

    /**
     * 关注公众号的事件
     */

    public function guanzhuGzh()
    {
        $content   =  Config::get('message.gzgzh');
        ReposeText::reposeText($this->postObj,$content);
    }

}