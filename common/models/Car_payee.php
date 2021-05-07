<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 下午 4:37
 */
namespace common\models;

use Yii;

class Car_payee extends Base_model
{
    public static function tableName()
    {
        return '{{%car_payee}}';
    }
}