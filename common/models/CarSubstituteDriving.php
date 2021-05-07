<?php

namespace common\models;

use Yii;
use common\components\AlxgBase;

class CarSubstituteDriving extends AlxgBase
{
    protected $currentTable = '{{%car_substitute_driving}}';

    public static $company=[
        '1'=>'典典',
        '2'=>'滴滴',
    ];

    public static $polling_text = [
        '0' => '正在派单',
        '1' => '派单失败',
        '2' => '派单成功'
    ];

    public static $old_status_to_new = [
        0 => '1',
        101 => '1',
        201 => '1',
        301 => '1',
        302 => '2',
        303 => '2',
        304 => '3',
        401 => '0',
        402 => '0',
        403 => '0',
        404 => '0',
        405 => '0',
        501 => '3',
        506 => '0',
    ];

    public static $status_text = [

        -1 => '预约中',
        0 => '派单中',
        101 => '派单中',
        102 => '开始派单',
        201 => '派单中',
        301 => '司机已接单',
        302 => '司机已就位',
        303 => '司机已开车',
        304 => '代驾结束',
        401 => '系统取消',
        402 => '客户取消',
        403 => '用户取消',

        404 => '司机销单',
        405 => '司机拒绝取消',
        501 => '服务结束',
        506 => '系统取消'
    ];
}