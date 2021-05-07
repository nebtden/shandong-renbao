<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26 0026
 * Time: 下午 2:23
 */
namespace common\models;

use Yii;
use common\components\AlxgBase;
use common\components\W;

class CarDemand extends AlxgBase
{
    protected $currentTable = '{{%car_demand}}';

    public static $errInfo = [
        0 =>'success',
        10 => '缺少必填参数',
        11 => '参数错误',
        12 => '签名错误',
        13 => '系统错误',
        14 => '重复请求',

    ];

//错误信息转json
    public function aderrinfo($code=0,$msg=''){

        $errinfo=self::$errInfo;

        $err['errno'] = $code;
        $err['errmsg'] = $errinfo[$code];

        if($msg){
            $err['errno'] = $code;
            $err['errmsg'] = $msg;
        }

        return json_encode($err);
    }

}