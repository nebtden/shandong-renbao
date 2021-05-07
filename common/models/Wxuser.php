<?php
namespace common\models;

use Yii;
use common\components\W;
class Wxuser extends Base_model 
{
	public static function tableName()
	{
		return '{{%wxuser}}';
	}
	
	public static function getUsrByToken($token){
		$model = new self();
		$usr = $model ->getData('*','one',"`token`='".$token."'");
		return  $usr;
	}
	
	public static function getUsrByUid($uid){
		$model = new self();
		$usr = $model ->getData('*' , 'one' , "`uid`='$uid' and status=1");
		return $usr;
	}
	
	public static function getUsrById($id){
		$model = new self();
		$usr = $model ->getData('*' , 'one' , "`id`='$id'");
		return $usr;
	}
	
	public static function delUsrById($id){
		$where = "`id` = ".intval($id);
		$usr['status'] = 0; 
		unset($usr['id']);
		$model = new self();
		$model -> upData($usr,$where);
	}
	
	public function addUsr($usr=NULL){
		$usr['token'] = W::createNumber('token',0);
		if(!Yii::app()->session['token']){
			Yii::app()->session['token'] = $usr['token'];
		}
		$usr['uid'] = Yii::app ()->user->user['id'];
		$usr['headpic'] = W::dealPic($usr['headpic']);
		$usr['createtime'] = time();
		$this -> addData($usr);
	}
	
	public function editUsr($usr=NULL){
		$where = "`id` = ".intval($usr['id']);
		$usr['headpic'] = W::dealPic($usr['headpic']);
		unset($usr['id']);
		$this -> upData($usr,$where);
	}
}
