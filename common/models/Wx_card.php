<?php
namespace common\models;
use Yii;

class Wx_card extends  Base_model {

   public static function  tableName()
    {
        return '{{%wx_card}}';
    }

	public static function getCard($token){
		$model = new self();
		$re = $model -> getData('card','one',"`token`='{$token}'",'','','',true);
		$card=uniqid();
		if($re['card']<=100000){
			$card = $re['card']+1;
			$model -> upData(array('card'=>$card),"`token`='{$token}'");
		}
		return $card;
	}
}