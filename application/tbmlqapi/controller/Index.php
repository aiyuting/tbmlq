<?php
namespace app\tbmlqapi\controller;

use think\Controller;

class Index extends Controller
{

    private $wechatToken = 'wenhao';
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
        if( strtolower( $postObj->MsgType ) == 'event')
        {
            //关注公众号事件
            if( strtolower( $postObj->Event ) == 'subscribe' )
            {
                $toUser    =  $postObj->FromUserName;
                $fromUser  =  $postObj->ToUserName;
                $time 	   =  time();
                $msgType   =  'text';
                $content   =  '「你购物 我奖励」我是您的省钱小管家，么么哒~!
                
[勾引]请发送商品链接发送到公众号，我们会第一时间为您找到优惠信息~

[拥抱]使用教程：<a href=\'http://www.baidu.com\'>点击查看>></a>

[鼓掌]商品搜索可发送：搜/买/找+关键词(例如：买衣服)

[红包]新用户完成首次购物后可领取一份惊喜哦~

[疑问]更多命令请发送“帮助”查看！';
                $template  =  "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
                echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            }
        }

        //回复文本信息
        if( strtolower( $postObj->MsgType ) == 'text' && trim($postObj->Content)=='wechat')
        {
            $toUser = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $arr = array(
                array(
                    'title'=>'test',
                    'description'=>"just so so...",
                    'picUrl'=>'http://www.acting-man.com/blog/media/2014/11/secret-.jpg',
                    'url'=>'http://www.imooc.com',
                ),
                array(
                    'title'=>'hao123',
                    'description'=>"hao123 is very cool",
                    'picUrl'=>'https://www.baidu.com/img/bdlogo.png',
                    'url'=>'http://www.hao123.com',
                ),
                array(
                    'title'=>'qq',
                    'description'=>"qq is very cool",
                    'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                    'url'=>'http://www.qq.com',
                ),
            );
            $template = "<xml>
			     <ToUserName><![CDATA[%s]]></ToUserName>
			     <FromUserName><![CDATA[%s]]></FromUserName>
			     <CreateTime>%s</CreateTime>
			     <MsgType><![CDATA[%s]]></MsgType>
			     <ArticleCount>".count($arr)."</ArticleCount>
			     <Articles>";
            foreach($arr as $k=>$v){
                $template .="<item>
				    <Title><![CDATA[".$v['title']."]]></Title> 
				    <Description><![CDATA[".$v['description']."]]></Description>
				    <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
				    <Url><![CDATA[".$v['url']."]]></Url>
				    </item>";
            }

            $template .="</Articles>
			     </xml> ";
            echo sprintf($template, $toUser, $fromUser, time(), 'news');
            //注意：进行多图文发送时，子图文个数不能超过10个
        }else{
            switch( trim( $postObj->Content ) )
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
                    $content = '升级打造中...';
                    break;
            }
            $toUser 	=  $postObj->FromUserName;
            $fromUser 	=  $postObj->ToUserName;
            $time 		=  time();
            $msgType 	=  'text';
            $template   =  "<xml>
				        <ToUserName><![CDATA[%s]]></ToUserName>
				        <FromUserName><![CDATA[%s]]></FromUserName>
				        <CreateTime>%s</CreateTime>
			         	<MsgType><![CDATA[%s]]></MsgType>
			                <Content><![CDATA[%s]]></Content>
				        </xml>";
            echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        }
    }
}