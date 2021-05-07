<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/12 0012
 * Time: 下午 5:24
 */
namespace common\models;

use Yii;

class Car_type extends Base_model
{
    public static function tableName()
    {
        return '{{%car_type}}';
    }
}