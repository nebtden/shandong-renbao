<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class PayCompany extends ActiveRecord{
	
    public  static function tableName()
    {
        return '{{%pay_company}}';
    }

}
?>