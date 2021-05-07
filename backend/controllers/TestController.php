<?php

namespace backend\controllers;

use common\components\Rsaxu;
use common\models\Shop_product;
use Yii;
use common\models\User;
use backend\util\BController;
use yii\helpers\Url;
use common\components\CarCateType;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use common\components\W;
use common\models\CarBrand;
use common\models\Car_wash_pinganhcz_shop;
use common\components\Rsa;


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
///////////////////////////////////////////////////////////////////////////////////
//更新车型库
    protected function  getCarBrand()
    {
        $Model= new CarCateType();
        $list= $Model->Brand();
        $data1 = [];
        $time=time();
        foreach ($list as $key => $val ){
            foreach ($val as $k => $v){
                $v1['id'] = $v['id'];
                $v1['name'] = $v['name'];
                $v1['fullname'] = '';
                $v1['initial'] = $v['initial'];
                $v1['logo'] = $v['logo'];
                $v1['salestate'] =  '';
                $v1['depth'] = 1;
                $v1['parent_id'] = 0;
                $v1['c_time'] = $time;
                $data1[]=$v1;
            }
        }

        $fileds = ['id','name','fullname','initial','logo','salestate','depth','parent_id','c_time'];
        $tablename='{{%car_brand_model}}';
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $msg = 0;
        try{
            $db->createCommand()->batchInsert($tablename,$fileds,$data1)->execute();
            $msg = 1;
            $transaction->commit();
        }catch (\Exception $e){
            $e->getMessage();
            $transaction->rollBack();
        }
        return $msg;
    }

    protected function  getCarType($type)
    {
        $Model= new CarCateType();
        $list= $Model->Type($type);
        $data1 = [];
        $time=time();
        foreach ($list as $key => $val ){
            $data2=$val['list'];
            unset($val['list']);
            $val['fullname'] = '';
            $val['logo'] = '';
            $val['salestate'] =  '';
            $val['depth'] = 2;
            $val['parent_id'] = (int)$type;
            $val['c_time'] = $time;
            $data1[]=$val;
            foreach($data2 as $k => $v ){
                $v1['id']=$v['id'];
                $v1['name'] = $v['name'];
                $v1['fullname'] = $v['fullname'];
                $v1['initial']='';
                $v1['logo'] = $v['logo'];
                $v1['salestate'] = $v['salestate'];
                $v1['depth'] = 3;
                $v1['parent_id'] = $val['id'];
                $v1['c_time'] = $time;
                $data1[] = $v1;
            }
        }
        $fileds = ['id','name','fullname','initial','logo','salestate','depth','parent_id','c_time'];
        $tablename='{{%car_brand}}';
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try{
            $db->createCommand()->batchInsert($tablename,$fileds,$data1)->execute();
            $transaction->commit();
        }catch (\Exception $e){
            $e->getMessage();
            $transaction->rollBack();
        }

    }
    public  function  actionGetcar()
    {
//        $res=$this->getCarBrand();
//        var_dump($res);exit;
//        if($res === 1){
//            $model=new CarBrand();
//            $list=$model->select('id', ['parent_id' => 0])->all();
//            foreach ($list as $value) {
//                $this->getCarType($value['id']);
//            }
//        }
    }
    //
    public  function  actionGetcarinfo()
    {
        $bmodel=new CarBrand();
        $list=$bmodel->select('id', ['depth' => 3])->all();
        foreach ($list as $value) {
//            $Model= new CarCateType($value['id']);
//            $list= $Model->Info($value['id']);
            $data = [];
            foreach ($list as $key => $val ){
                unset($val['listdate'],$val['productionstate']);
                $val['parentid'] = $value['id'];
                $data[]=$val;
            }
            $fileds = ['id','name','logo','price','yeartype','salestate','sizetype','parentid'];
            $tablename='{{%car_brand_series}}';
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                $db->createCommand()->batchInsert($tablename,$fileds,$data)->execute();
                $transaction->commit();
            }catch (\Exception $e){
                $e->getMessage();
                $transaction->rollBack();
            }

        }
    }
    public function  actionRsatest(){
	    //var_dump(Yii::$app->params['rootPath'].'static/');die;
	    $str = Rsa::encryptByPrivateKey('111');
	    var_dump($str);
    }
}

