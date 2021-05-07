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
class CallbackLog extends Base_model
{

    /**
     * @var array
     * 日志记录的类型，后续可以增加
     */
    public static $type = [
        'oil'=>1,
        'inspection'=>2,
        'wash'=>3
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
        return '{{%callback_log}}';
    }



}