<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 8:44
 */
namespace common\models;
use Yii;
class TravelAgenc extends Base_model
{
    public $tablePK = 'id';//设置本表我主键
    public static function tableName()
    {
        return '{{%travel_agency}}';
    }
}