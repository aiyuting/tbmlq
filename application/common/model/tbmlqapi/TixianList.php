<?php
namespace app\common\model\tbmlqapi;

use think\Model;

class TixianList extends Model
{
    public function getIsAgreeAttr($value)
    {
        $status = [0=>'未同意',1=>'已同意'];
        return $status[$value];
    }
}