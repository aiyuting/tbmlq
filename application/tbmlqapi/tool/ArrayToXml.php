<?php
namespace app\tbmlqapi\tool;

use think\Controller;

/**
 * 数组转换xml
 * Class ArrayToXml
 * @package app\tbmlqapi\tool
 */
class ArrayToXml extends Controller
{
    /**
     *   将数组转换为xml
     *   @param array $data    要转换的数组
     *   @param bool $root     是否要根节点
     *   @return string         xml字符串
     */
    public static function arrayToXml($arr,$root = true)
    {
        $str = '';
        if($root) $str.= '<xml>';
        foreach ($arr as $k => $v) {
            //去掉key中的下标[]
            $k = preg_replace('/\[\d*\]/', '', $k);
            if(is_array($v)){
                $child = arr2xml($v, false);
                $str .= "<$k>$child</$k>";
            }else{
                $str.="<{$k}><![CDATA[$v]]></{$k}>";
            }
        }
        if($root) $str.= '</xml>';
        return $str;
    }
}