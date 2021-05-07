<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2 0002
 * Time: 下午 2:35
 */
namespace common\models;

use Yii;
use common\components\W;
class ErrorLog extends Base_model
{

    /**
     * @var array
     * 日志记录的类型，后续可以增加
     * 1道路救援，2代驾，3在线洗车券购买,5油卡订单 7年检
     */
    public static $type = [
        'rescue'=>1,
        'driving'=>2,
        'washing'=>3,
        'oil'=>5,
        'inspection'=>7,
        'coupon'=>7,
    ];

    /**
     * @var array
     * 日志记录的类型，后续可以增加
     */
    public static $status = [
        'success'=>1,
        'fail'=>0,
    ];

    public static function tableName()
    {
        return '{{%error_log}}';
    }



}