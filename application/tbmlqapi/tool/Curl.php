<?php
namespace app\tbmlqapi\tool;

use think\Controller;

/**
 * http ： curl类
 * Class Curl
 * @package app\tbmlqapi\tool
 */
class Curl extends Controller
{
    private static $url = ''; //访问的url
    private static $oriUrl = '';//referer url
    private static $data;//可能发出的数据post、put
    private static $method; //访问方式，默认为get请求。


    public static function send($url,$data,$method='get')
    {
        if(!$url) exit('url can not be null');

        self::$url = $url;
        self::$method = $method;
        $urlArr = parse_url($url);
        self::$oriUrl = $urlArr['scheme'] .'://'. $urlArr['host'];
        self::$data = $data;
        if(!in_array(self::$method,['get','post','put','delete'])){
            exit('error request method type!');
        }
        $func = self::$method . 'Request';
        return self::$func(self::$url);
    }

    /**
     * 基础发起curl请求函数
     * @param int $is_post 是否是post请求
     */
    private  static function doRequest($is_post = 0)
    {
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, self::$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        // 来源一定要设置成来自本站
        curl_setopt($ch, CURLOPT_REFERER, self::$oriUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        if($is_post == 1) curl_setopt($ch, CURLOPT_POST, $is_post);//post提交方式
        if (!empty(self::$data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, self::$data);
        }

        $data = curl_exec($ch);//运行curl
        if(curl_errno($ch)){
            return curl_error($ch);
        }
        curl_close($ch);
        return $data;
    }



    /**
     * 发起get请求
     */
    public static function getRequest() {
        return self::doRequest(0);
    }
    /**
     * 发起post请求
     */
    public static function postRequest() {
        return self::doRequest(1);
    }
    /**
     * 发起put请求
     */
    public static function putRequest($param) {
        return self::doRequest(2);
    }

    /**
     * 发起delete请求
     */
    public static function deleteRequest($param) {
        return self::doRequest(3);
    }
}
