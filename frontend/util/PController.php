<?php
namespace frontend\util;

use Yii;
use common\components\BaseController;

class PController extends BaseController {
	
	protected $where = '';
	public $title = '云车在线';
	
	public function init(){
		parent::init();
	}
	
	public function beforeAction($action = NULL){
		//Yii::$app->session['token'] = $token = 'dhygbuy';
		Yii::$app->session['token'] = $token = 'dhcarcard';
		return true;
	}
	
}
