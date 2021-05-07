<?php
namespace common\models;
use Yii;

class Comment extends Base_model
{
    public  static function tableName()
    {
        return '{{%comment}}';
    }


}