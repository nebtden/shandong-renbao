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
class CarBrandSeries extends Base_model
{

    public static function tableName()
    {
        return '{{%car_brand_series}}';
    }


}