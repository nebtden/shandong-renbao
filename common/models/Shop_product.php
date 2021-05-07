<?php
namespace common\models;

use Yii;

class Shop_product extends Base_model
{
	public static function tableName()
	{
		return '{{%shop_product}}';
	}

	public static function getProById($id){
		$model = new self();
		$pro = $model->getData('*','one',"`id`={$id}");
		return $pro;
	}

	public static function getHotpros($num=10){
		$model = new self();
		$pros = $model->getData('`id`,`proname`,`pic`','all'," `status`= 1 and `token`='".Yii::$app->session['token']."'",'`sell_count` desc',"0,$num");
		return $pros;
	}
}
