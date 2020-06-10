<?php

namespace frontend\controllers;

use common\components\DianDianOilCard;
use common\components\DianDianInspection;
use common\models\Ad_list;
use common\models\CarBrand;
use common\models\CarBrandSeries;
use common\models\CarOilor;
use common\models\FansAccount;
use Yii;
use common\components\DianDian;
use frontend\util\PController;
use yii\widgets\Menu;

class DiandianCarController extends PController
{
    public $site_title = '云车驾到';
    public $menuActive = 'caruorder';

    public function actionOilinfo(){
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $info = (new CarOilor())->table()->where([ 'm_id' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }
        $info['status_text'] = CarOilor::$status_text[(string)$info['status']];

        return $this->render('oilinfo', ['info' => $info]);
    }

    public function actionTest(){
        //调用典典用车充值接口
        $diandian_oil = new DianDianOilCard();

        $data = [];
        $data['payMethod'] = 5;
        $data['orderId'] = 7951317;
        $data['payPrice'] = 108;

        $result = $diandian_oil->pay_pre(1643,$data);
        print_r($result);
    }

    public function actionRedis(){
        $redis = Yii::$app->redis;
        $token_key =  'simontest_';
        $redis->set($token_key,'test');
        echo $redis->get($token_key);
    }


    public function actionInspection(){
        try{
            $diandian_oil = new DianDianInspection();

            $data =[];
            $data['cardNumber'] = '222222222';
            $data['money'] = 100;
            $data['rechargePhone'] = '13365802535';
            $result = $diandian_oil->vehicleAdd(1,$data);
            print_r($result);

            $data = [];
            $data['payMethod'] = 5;
            $data['orderId'] = 7951317;
            $data['payPrice'] = 108;
            $result = $diandian_oil->pay_pre(1,$data);
            print_r($result);



        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }


    public function actionOil()
    {

        try{

            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            $diandian_oil = new DianDianOilCard();


            $result = $diandian_oil->oil_card_list();
            print_r($result);
//            die();
//
            $data = [];
            $data['money']=50.00;
            $result = $diandian_oil->oil_card_instore($data);
            print_r($result);
//           throw new \Exception('aaa');

//
//
            $data =[];
            $data['cardNumber'] = '9030000005107514';
            $data['money'] = 100;
            $data['rechargePhone'] = '13365802535';
            $result = $diandian_oil->oil_recharge_1_1(1,$data);
            print_r($result);

            $data = [];
            $data['payMethod'] = 5;
            $data['orderId'] = 7951317;
            $data['payPrice'] = 108;
            $result = $diandian_oil->pay_pre(1,$data);
            print_r($result);
            $trans->commit();

        }catch (\Exception $exception){
            $trans->rollBack();
            echo $exception->getMessage();
        }
    }


    public function actionApi()
    {
        $baseurl = Yii::$app->request->queryString;
        //对baseurl进行处理，把&符号前面的去掉
        $params =  strstr($baseurl,"&");
        $params = substr($params,1);


        $post_data = yii::$app->getRequest()->getRawBody();
        $diandian = new DianDian();
        $result = $diandian->checkToken($params,$post_data);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if($result){

            return ['success'=>true];
//            return ['status' => $status,'msg' => $msg, 'data' => $data, 'url' => $url, 'waiting' => $waiting];
        }else{

            return ['success'=>false];
        }

    }

    public function actionBrand(){
        try{
            $diandian = new DianDianInspection();


            $result = $diandian->getBrandList();

//            $result = json_decode($result,true);
            foreach ($result as $item){
                print_r($item);
                $brand = new CarBrand();
                $brand->id = $item['brandId'];
                $brand->name = $item['brandName'];
                $brand->logo = $item['icon'];
                $brand->status = intval($item['isValid']);
                $brand->save();
                print_r('success!');
            }
            print_r($brand->id );

        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }

    public function actionSeries()
    {
        try{
            $diandian = new DianDianInspection();

            $brands = CarBrand::find()
//              ->where(['>', 'id', 140])
                ->orderBy('id')
                ->all();
            foreach($brands as $brand){
                $result = $diandian->getSerieslist($brand['id']);
                //添加到表
                print_r($brand->id );

//                $result = json_decode($result,true);
                foreach ($result as $item){

                    $series = new CarBrandSeries();
                    $series->id = $item['seriesId'];
                    $series->name = $item['seriesName'];
                    $series->brand_id = $brand['id'];

                    $series->save();
                    print_r('success!');
                }
//                print_r($brand->id );
            }


        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }


    public function update(){
        try{
            $diandian = new DianDian();


            $data = [];
            $data['code']=2234;
            $result = $diandian->couponExchange(1,$data);
            print_r($result);


        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }



}
