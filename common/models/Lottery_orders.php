<?php
namespace common\models;

use Yii;

class Lottery_orders extends Base_model
{
	public static $proInfo = array(
		'NO0001'=>'蛋糕卡通毛巾',
		'NO0002'=>'萌萌哒的鸡',
		'NO0003'=>'金鸡吊坠',
		'NO0004'=>'户外便携包',
		'NO0005'=>'健康瑜伽垫',
		'NO0006'=>'萌猫暖宝',
		'NO0007'=>'反向晴雨伞',
		'NO0008'=>'3D益智拼图',
		'NO0009'=>'司庆纪念币',
		'NO0010'=>'便携式餐具'
	);
	public static function tableName()
	{
		return '{{%lottery_orders}}';
	}
}
