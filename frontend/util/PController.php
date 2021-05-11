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
    /**
     * 判断是否是微信浏览器
     * @return mixed
     */
    public function is_weixin()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            // 非微信浏览器
            return false;
        } else {
            // 微信浏览器
            return true;
        }
    }
	
}
