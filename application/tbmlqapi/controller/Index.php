<?php
namespace app\tbmlqapi\controller;

use app\tbmlqapi\tool\ArrayToXml;
use app\tbmlqapi\tool\ReposeText;
use think\Controller;

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
                $content = "- [转圈] 机器人使用简单帮助 [转圈] -\r\n
[勾引]发送商品给我查询奖励\r\n
[太阳]常用一般指令：\r\n
签到-说明:每天签到领取奖励\r\n
资料-说明:查询您的详细资料\r\n
余额-说明:查询您的账户余额\r\n
提现-说明:申请提现如:提现10\r\n
[月亮]常用修改指令:\r\n
修改姓名发送：姓名 XX \r\n
修改微信发送：微信 XX \r\n
修改支付宝发送：支付宝 XX\r\n
[疑问]常用查询指令：\r\n
发送订单：查询最近省钱订单\r\n
发送徒弟：查询当前徒弟数量\r\n
发送明细：查询收入支出明细\r\n
[拥抱]常用搜索指令\r\n
发送：搜/买/找XX搜索优惠\r\n
例如：搜女装|买男装|找音箱";
                break;
            default:
                $content = '联系开发者,此处未完成';
                break;
        }

        return ReposeText::reposeText($this->postObj,$content);
    }




    /**
     * 回复消息
     */
    public function outMessage($text)
    {
        switch( $text )
        {
            case 1:
                $content = '你输入了个数字1';
                break;
            case '电话':
                $content = '12345678901';
                break;
            case '教程':
                $content = "<a href='www.imooc.com'>慕课网</a>";
                break;
            case '博客':
                $content = "<a href='blog.abc.com'>测试微信</a>";
                break;
            default:
                $content = "[微笑]您好，我是可以为您购物省钱的小管家
- - - - - - - - - -
[握手]发送商品链接，可为您查询优惠和奖励
- - - - - - - - - -
[鼓掌]商品搜索可发送：搜/买/找+关键词(例如：买衣服)
- - - - - - - - - -
<a href='http://www.mengqy.cn'>▶点击查看使用教程>></a>
- - - - - - - - - -
[疑问]更多命令请发送“帮助”查看";
                break;
        }
        return ReposeText::reposeText($this->postObj,$content);
    }

    /**
     * 关注公众号的事件
     */

    public function guanzhuGzh()
    {
        $toUser    =  $this->postObj->FromUserName;
        $fromUser  =  $this->postObj->ToUserName;
        $time 	   =  time();
        $msgType   =  'text';
        $content   =  "「你购物 我奖励」我是您的省钱小管家，么么哒~!\r\n
[勾引]请发送商品链接发送到公众号，我们会第一时间为您找到优惠信息~\r\n
[拥抱]使用教程：<a href='http://www.baidu.com'>点击查看>></a>\r\n
[鼓掌]商品搜索可发送：搜/买/找+关键词(例如：买衣服)\r\n
[红包]新用户完成首次购物后可领取一份惊喜哦~\r\n
[疑问]更多命令请发送“帮助”查看！";
        return ReposeText::reposeText($this->postObj,$content);
    }

}