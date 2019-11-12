<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 8:50  tpy_travel_users
 */
namespace common\models;

use Yii;

class TravelUsersLocked extends Base_model
{
    public $tablePK = 'id';//设置本表我主键
    public static function tableName()
    {
        return '{{%travel_users_locked}}';
    }
}