<?php
namespace common\models;
use Yii;

class Reply extends Base_model
{
    public  static function tableName()
    {
        return '{{%reply}}';
    }
}
