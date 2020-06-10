<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\util\BController;
use backend\models\Admin;
use common\components\ValidataCode;
use common\models\Wxuser;



/**
 * Site controller
 */
class SiteController extends BController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {

        return $this->redirect(['admin/index']);
    }

    /**
     * 验证码
     */
    public function actionYzcode() {
    	$yzcode=new ValidataCode();
    	$yzcode->doimg ();
    }
    
    public function actionLogin() {

     	if (Yii::$app->request->post ()) {
    		$username = $_POST ['username'];
    		$password = $_POST ['password'];
    		$yzm = strtolower($_POST['yzcode']);
    		
    		if (empty ( $username ) || empty ( $password )) {
    			echo "account or passwd is error!";  die;
    		}
    		if (empty ( $yzm ) || $yzm !=  strtolower(Yii::$app->session ['yzcode'])) {
    			echo "code is error!";  die;
    		}
    		
    		$where="username='".$username."'";
    		$model = new Admin();
    		$user=$model->getData('*','one',$where);


    		if($user){

    			if(md5($password.$user['salt'])==$user['password']){
    				if($user['status']==1){
    					$data['last_login_time'] = time();
    					$data['last_login_ip']= Yii::$app->request->userIP;
    					$where=" id='".$user['id']."' ";
    					$flag=$model->myUpdate($data,$where);
    					if($flag){
    						Yii::$app->session ['UNAME'] = $user['username'];
    						Yii::$app->session ['UID'] = $user['id'];
    						$wxUsr = Wxuser::getUsrById('1');

    						if($wxUsr['token'])Yii::$app->session['token'] = $wxUsr['token'];

    						return $this->redirect(['admin/index']);

	    				}
    				}
    				if($user['status']==0){
    					echo 'account is lock'; die;
    				}
    				if($user['status']==2){
    					echo 'user is delete';  die;
    				}
    			}else{
    				echo 'user is passerror'; die;
    			}
    		}else{
    			echo 'user is empty'; die;
    		}
     	 }
    	return $this->render ( 'login' );
       
    }
    
    public function actionLogout()
    {
        Yii::$app->session ['UNAME'] = null;
    	Yii::$app->session ['UID'] = null;
    	unset(Yii::$app->session ['UNAME']);
    	unset(Yii::$app->session ['UID']);
        return $this->goHome();
    }





}
