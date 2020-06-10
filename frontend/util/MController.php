<?php
namespace frontend\util;

use Yii;
use common\components\BaseController;
use common\components\W;

class MController extends BaseController {
	public $where;
	public function init(){
		parent::init();
		header('Content-Type:text/html;charset=utf-8');
// 		$agent = $_SERVER ['HTTP_USER_AGENT'];
// 		if(! strpos ( $agent, "MicroMessenger" )) {
// 			echo '此功能只能在微信浏览器中使用!';die;
// 		}
		$this->layout = 'main';
		//W::filterParams();
	}
	
	public function beforeAction($action = null){
		parent::beforeAction($action);
		$token = trim($_GET['token']);
		if($token && (!Yii::$app->session['token'] || Yii::$app->session['token'] != $token)){
			Yii::$app->session['token'] = $token;
			Yii::$app->cache->set($token.'flag',1);
		}else{
			Yii::$app->cache->delete($token.'flag');
		}
		if(Yii::$app->cache->get($token.'flag')){
			$token = Yii::$app->session['token'];
			$info = W::getOpenid($token);
			Yii::$app->session['openid'] = $info['openid'];
		} 
		
		Yii::$app->session['token'] = 'dhcylife';
		Yii::$app->session['openid'] = 'ocLJ3jisXCX6xN3GJQ3I1M5TGRsI';
		$this->where = "`token`='".Yii::$app->session['token']."' and `openid`='".Yii::$app->session['openid']."'";
		return true;
	}
}
