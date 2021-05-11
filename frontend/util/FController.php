<?php
namespace frontend\util;

use common\models\Fans;
use common\models\FansAccount;
use Yii;
use common\components\BaseController;
use common\components\W;


class FController extends BaseController {
	
	protected $where = '';
	public $title = '云车在线';
	public $is_web = null;
	
	public function init(){
		parent::init();
	}
	
	public function beforeAction($action = NULL){

		Yii::$app->session['token'] = $token = 'dhcarcard';
		
		//测试专用
		$mobile = $_GET['m'];
		if(!empty($mobile)){
		    $fansac = (new FansAccount())->select('*',['mobile'=>$mobile,'is_web'=>0])->one();
            $openid = (new Fans())->select('*',['id'=>$fansac['uid']])->one();
            if(!empty($fansac) && !empty($openid)){
                Yii::$app->session['openid'] = $openid['openid'];
                unset(Yii::$app->session['wx_user_auth']);
            }
        }
        //如果是web端传过来的链接，
        $is_web = $this->checkWeb();
        if(!$is_web){
           
            if(!Yii::$app->session['openid']){
                W::getOpenid($token,'snsapi_userinfo');
            }
            // Yii::$app->session['openid'] ='oBidps5jpMzfXJcGPgbGzb2F7gqQ';//'oH1cBwhLQn5jfcfZbCVzZGHYAoAg';
            $this->where = "`token`='".Yii::$app->session['token']."' and `openid`='".Yii::$app->session['openid']."'";
        }
        return true;
	}

    /**
     * 根据cookies判断是否为web端用户
     * @return mixed
     */
	public function checkWeb()
    {
        $session = \Yii::$app->session;
        return $this->is_web = $session['xxz_mobile'];
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
