<?php
namespace frontend\util;

use Yii;
use common\components\BaseController;
use common\components\W;


class NController extends BaseController {
	
	protected $where = '';
	public $title = '鼎翰易购';
	
	public function init(){
		parent::init();
	}
	
	public function beforeAction($action = NULL){
		

		Yii::$app->session['token'] = $token = 'dhcylife';
// 		if(!Yii::$app->session['openid']){
// 			$info = W::getOpenid($token);
// 			Yii::$app->session['openid'] = $info['openid'];
// 		}
        Yii::$app->session['openid'] ='ozCfxjp8YmCFePLJ98LgVFoszLLc';
		$this->where = "`token`='".Yii::$app->session['token']."' and `openid`='".Yii::$app->session['openid']."'";
		$key = "shopPro" . $this->where;
	    if(Yii::$app->cache->get ( $key )){
			Yii::$app->session['car'] =count(Yii::$app->cache->get ( $key ));
		}else{
			Yii::$app->session['car'] = 0;
		}
		return true;
	}
	
}
