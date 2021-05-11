<?php

namespace backend\controllers;

use common\components\AliPay;
use common\models\Shop_product;
use Yii;
use common\models\User;
use backend\util\BController;
use yii\helpers\Url;

class TestController extends BController {
	
	public function actionIndex() {
        $pro=new   Shop_product();
        $data= $pro->getProById(1);

		$user = new User ();
		//var_dump($user->findOne(['id' => 1]));
		$connection = \Yii::$app->db;
		$command = $connection->createCommand ( 'SELECT * FROM ' . $user->tableName () );
		//$posts = $command->queryAll();
		$post = $command->queryOne ();
		//var_dump($post);
		
	
		$users = $user->getData ( '*', 'all' );
		//var_dump ( $users );
		
// 		$flag = $user->upData ( array (
// 				'username' => '安徽发' 
// 		), "id = 1" );
// 		echo $flag;
		
// 		$user->addData ( array (
// 				'username' => 'fsafasf',
// 				'auth_key' => md5 ( '1234' ) 
// 		) );
		return $this->render('index', ['users' => $users]);
	}
	
	public function actionTest() {
		
		echo Url::home();;
		
	}

	public function actionAlipay(){
//	    $alipay = AliPay::getInstance([]);
//	    $res = $alipay->transfer_account('20180528105102','nhbsli0482@sandbox.com','1');
//	    echo $alipay,'<br>';
//	    print_r($res);
//	    //20180528110070001502930000165025
//        //20180528110070001502930000165025


        //            $arrall=[];
//            $chennum=20;
//            $xxz_i=ceil($arrlength/$chennum);
//            for($i=0;$i<$xxz_i;$i++){
//                $arrall[]=array_slice($data_2,($i*$chennum),$chennum);
//            }
    }


}
