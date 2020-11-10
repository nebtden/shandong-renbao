<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class PayOrder extends ActiveRecord{
	
    public  static function tableName()
    {
        return '{{%pay_order}}';
    }

}
?>