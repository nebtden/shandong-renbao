<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8 0008
 * Time: 上午 10:26
 */

namespace common\models;

use Yii;
use common\components\W;
class Car_menu extends Base_model
{
    public static function tableName()
    {
        return '{{%car_menu}}';
    }
}