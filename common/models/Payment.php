<?php
namespace common\models;

use Yii;
class Payment extends  Base_model   {

    public  static  function tableName()
    {
        return '{{%payment}}';
    }
}