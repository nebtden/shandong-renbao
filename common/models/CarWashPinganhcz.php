<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\4\18 0018
 * Time: 10:34
 */

namespace common\models;
use Yii;
use common\components\AlxgBase;

class CarWashPinganhcz extends AlxgBase
{
    protected $currentTable = "{{%car_wash_pinganhcz}}";
    //1：进行中，0：失败，2：成功
    public static $order_status = [
        '0' => '失败',
        '1' => '进行中',
        '2' => '成功'
    ];
    //卡券核销状态0:未使用、1:使用中、2:已使用、3:已过期、4:已收回、5:退兑换、6：已冻结 、7：未激活
    public static $pcoupon_status = [
        '0' => '未使用',
        '1' => '使用中',
        '2' => '已使用',
        '3' => '已过期',
        '4' => '已收回',
        '5' => '退兑换',
        '6' => '已冻结',
        '7' => '未激活'
    ];

    //服务类型
    public static $service_text = [
        '云车驾到洗车'      => '云车驾到洗车',
        '前海云车一次洗车'  => '前海云车一次洗车',
        '十元洗车'         => '十元洗车',
        '普洗轿车'         => '普洗轿车'
    ];

}