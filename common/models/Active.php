<?php
namespace common\models;

use Yii;
class Active extends Base_model
{
    public $tablePK = 'id';//设置本表我主键

    public static function tableName()
    {
        return '{{%active}}';
    }
}