<?php
namespace app\tbmlqapi\controller;

use app\common\model\tbmlqapi\GuanzhuUserInfo;
use app\common\model\tbmlqapi\UserSearchInfo;
use app\tbmlqapi\tool\ArrayToXml;
use app\tbmlqapi\tool\Curl;
use app\tbmlqapi\tool\ReposeText;
use app\tbmlqapi\tool\YonjingJisuan;
use app\tbmlqapi\withouapi\Wx;
use app\tbmlqapi\withouapi\ZheTaoKe;
use think\Controller;
use think\facade\Config;

class Index extends Controller
{

    private $wechatToken = 'wenhao';
    private $postObj;
    private $wxUserInfo; //用户关注之后才有哦 look : guanzhuGzh(); 这个方法之后 才有信息。
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

        //将当前的数据存储到session里面
        session('wxuserinfo',$postObj);
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
                //取消关注公众号事件
                if( strtolower( $postObj->Event ) == 'unsubscribe' )
                {
                    $this->quxiaoguanzhuGzh();
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
            case 'helpcommand':
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

                    //调取转链api
                    $gaoyongInfo = $zhetaoke->gaoyongApiShopId($shopId);
                    if(empty($gaoyongInfo)){
                        ReposeText::reposeText($this->postObj,'此物品没优惠券');
                        exit;
                    }
                    //如果有优惠券,那么就走二合一链接
                    if(!empty($gaoyongInfo['coupon_remain_count'])){
                        $url = $gaoyongInfo['coupon_click_url'];
                    }else{
                        $url = $gaoyongInfo['item_url'];
                    }
                    //获取商品详情api
                    $itemInfo = $zhetaoke->getItemInfo($shopId);
                    //调用转换淘口令链接.
                    $tkl = $zhetaoke->getTkl($url,$itemInfo['pict_url'])['model'];

                    //用到的变量。
                    $logo = $itemInfo['pict_url'];//商品logo
                    $yuanjia = $itemInfo['size'];//原价
                    $quanhoujia = $itemInfo['quanhou_jiage'];//券后价格
                    $shangpingName = $itemInfo['title'];//商品名字
                    //此处所有佣金. (高级用户,后期需要配合设置的百分比进行.)
                    $yongjin = YonjingJisuan::yongjingjisuan($quanhoujia,$itemInfo['tkrate3']); //计算佣金
                    //用session存储佣金.
                    session($shopId,$yongjin);

                    $kapianArr = [
                        [
                            'title'=>$shangpingName,
                            'description'=>"原价：$yuanjia\r\n券后价格：$quanhoujia\r\n佣金：$yongjin",
                            'picUrl'=>$logo,
                            'url'=>"http://vip1234.zhiku.electronics-power.com/wx_api.html?taowords=({$tkl})&image=".base64_encode($logo),
                        ]
                    ];


                    //如果当前用户表有存储的该用户的订单号后六位,那么就不用在存储到搜索库里面了..
                    if(GuanzhuUserInfo::isTbOrderNum() === false){
                        //将用户搜索的商品存储到库里面
                        $this->saveUserSearchInfo($shopId,$this->postObj->FromUserName);
                    }


                    //卡片.
                    $toUser = $this->postObj->FromUserName;
                    $fromUser = $this->postObj->ToUserName;
                    $template = "<xml>
			     <ToUserName><![CDATA[%s]]></ToUserName>
			     <FromUserName><![CDATA[%s]]></FromUserName>
			     <CreateTime>%s</CreateTime>
			     <MsgType><![CDATA[%s]]></MsgType>
			     <ArticleCount>".count($kapianArr)."</ArticleCount>
			     <Articles>";
                    foreach($kapianArr as $k=>$v){
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
        /***对关注公众号的用户进行入库操作.(详细信息)*****/

        //获取用户的详细信息
        $wxUserInfo = Wx::getWxUserInfo($this->postObj->FromUserName);
        $this->wxUserInfo = $wxUserInfo;

        //入库
        $guanzhuUserInfo = new GuanzhuUserInfo();
        $guanzhuUserInfo->subscribe = $wxUserInfo['subscribe'] ?? '';
        $guanzhuUserInfo->openid = $wxUserInfo['openid'] ?? '';
        $guanzhuUserInfo->nickname = $wxUserInfo['nickname'] ?? '';
        $guanzhuUserInfo->sex = $wxUserInfo['sex'] ?? '';
        $guanzhuUserInfo->city = $wxUserInfo['city'] ?? '';
        $guanzhuUserInfo->country = $wxUserInfo['country'] ?? '';
        $guanzhuUserInfo->province = $wxUserInfo['province'] ?? '';
        $guanzhuUserInfo->language = $wxUserInfo['language'] ?? '';
        $guanzhuUserInfo->headimgurl = $wxUserInfo['headimgurl'] ?? '';
        $guanzhuUserInfo->subscribe_time = $wxUserInfo['subscribe_time'] ?? '';
        $guanzhuUserInfo->unionid = $wxUserInfo['unionid'] ?? '';
        $guanzhuUserInfo->remark = $wxUserInfo['remark'] ?? '';
        $guanzhuUserInfo->groupid = $wxUserInfo['groupid'] ?? '';
        $guanzhuUserInfo->tagid_list = $wxUserInfo['tagid_list'] ?? '';
        $guanzhuUserInfo->subscribe_scene = $wxUserInfo['subscribe_scene'] ?? '';
        $guanzhuUserInfo->qr_scene = $wxUserInfo['qr_scene'] ?? '';
        $guanzhuUserInfo->qr_scene_str = $wxUserInfo['qr_scene_str'] ?? '';
        $guanzhuUserInfo->save();

        /***************结束************/
        $content   =  Config::get('message.gzgzh');
        ReposeText::reposeText($this->postObj,$content);
    }
    /**
     * 取消关注公众号的事件
     */
    public function quxiaoguanzhuGzh()
    {
        //对取消关注公众号的用户进行表数据删除操作.
        GuanzhuUserInfo::delUserForOpenId($this->postObj->FromUserName);
    }

    /**
     * 将用户搜索到的数据存储到库里面。
     */
    public function saveUserSearchInfo($ItemId,$FromUserName)
    {
        //如果该用户以及他搜索的商品id已经存储到库里面了。那么就不存储了
        $userAndIeemrResult = UserSearchInfo::findUserAndItemId($ItemId,$FromUserName);
        if(!empty($userAndIeemrResult)){
            return false;
        }

        //查询出这个商品id已经拿到的pid 那么分配pid的时候去除此pid
        $pidArr = UserSearchInfo::selectItemIdPid($ItemId);
        $allPidArr = Config::get('tkpid');
        //取出可以分配的pid
        $newPidArr = array_diff($allPidArr,$pidArr);
        if(empty($newPidArr)){
            ReposeText::reposeText($this->postObj,'抱歉,无可分配的pid,请联系管理员qq:854854321');
        }

        //取第一个pid给这个商品的pid
        $givePidToItemId = $newPidArr[0];
        $usersearchInfo = new UserSearchInfo();
        $usersearchInfo->openid = $FromUserName;
        $usersearchInfo->itemid = $ItemId;
        $usersearchInfo->tk_pid = $givePidToItemId;
        $usersearchInfo->save();

    }

}