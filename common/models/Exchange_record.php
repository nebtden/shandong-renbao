<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2 0002
 * Time: 下午 5:26
 */
namespace common\models;

use Yii;

class Exchange_record extends Base_model
{
    public static $status = [

        '1' => '待审核',
        '2' => '审核通过',
        '3' => '拒绝通过',
    ];

    public static function tableName()
    {
        return '{{%exchange_record}}';
    }

}