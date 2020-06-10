<?php
namespace frontend\util;

use Yii;
use common\components\BaseController;
use common\components\W;
use yii\helpers\Url;

class WController extends BaseController {

    protected $where = '';
    public $title = '云车在线';

    public function init(){
        parent::init();
    }

    public function beforeAction($action = NULL){

        Yii::$app->session['token'] = $token = 'dhcarcard';
        $cookie = \Yii::$app->request->cookies;
        $c_mobile=$cookie->getValue('p_xxz_mobile');
        $s_mobile=Yii::$app->session['xxz_mobile'];

        if(!$s_mobile){
            if(!$c_mobile){
                Yii::$app->session['isshow']='yes';
                $curUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
                Yii::$app->session['xxz_url'] = $curUrl;
                $url= Url::to(['webcarhome/index']);
                header("location:$url");
                exit();
            }else{
                Yii::$app->session['xxz_mobile']=$c_mobile;
            }
        }
        return true;
    }


    protected function isWeixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {

            return false;
        }
    }

}
