<?php
namespace common\models;

use Yii;

class News extends Base_model
{
	public static function tableName()
	{
		return '{{%news}}';
	}

	public static function getProById($id){
		$model = new self();
		$pro = $model->getData('*','one',"`id`={$id}");
		return $pro;
	}



}
