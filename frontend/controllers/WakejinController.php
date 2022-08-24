<?php

namespace frontend\controllers;

use common\components\Aiqiyi;
use common\components\DianDian;
use common\models\AiqiyiType;
use common\models\CarCoupon;
use common\models\CarUseroilcard;
use frontend\util\PController;
use GuzzleHttp\Client;
use Yii;

class WakejinController extends CloudcarController {

    public $site_title = '爱奇艺充值';
    public $menuActive = 'caruser';

    public static $types =[


    ];


    public function actionTest(){
        return 2222;
        return $uid = \Yii::$app->session['wx_user_auth']['uid'];
    }

    public function actionGoods(){

        $wakejin_params = \Yii::$app->params['wakejin'];
        $url = $wakejin_params['url'].'/Query/Index/getCategories';
        $client = new Client();

        $result = $client->request('GET', $url );

        return $return = $result->getBody()->getContents();
        return \GuzzleHttp\json_decode($return,true);


    }

    public function actionSubGoods(){

        $wakejin_params = \Yii::$app->params['wakejin'];
        $url = $wakejin_params['url'].'/Query/Index/getSubcategories';
        $client = new Client();
        $data = [];
        $data['username'] = $wakejin_params['username'];
        $data['secretkey'] = $wakejin_params['secretkey'];
        $data['pcode'] = 'recharge';
        $data['timestamp'] = time();
        $data['msgencrypt'] = '1';
        ksort($data);
        $data["appsign"] = md5(implode("", $data));
        $result = $client->request('POST', $url,['form_params' =>$data] );
        $return = $result->getBody()->getContents();

        (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($data), $return, 'aiqiyi_wakejin', '555', 'aiqiyi_wakejin');

        $result = \GuzzleHttp\json_decode($return,true);
        $list = $result['retinfo'];
        foreach ($list as $value){
            $model = new AiqiyiType();
            $model->name = $value['product_name'];;
            $model->product_id = $value['pcode'];
            $model->price = $value['sell_price'];
            $model->save();
        }
        return $return;
        //return \GuzzleHttp\json_decode($return,true);
    }

    //测试下单
    public function actionOrder(){
        $wakejin_params = \Yii::$app->params['wakejin'];
        $url = $wakejin_params['url'].'/Trade/Index/agentApi';
        $client = new Client();
        $data = [];
        $data['username'] = $wakejin_params['username'];
        $data['secretkey'] = $wakejin_params['secretkey'];
        $data['pcode'] = 'recharge_IQYZCHJYK';
        //$data['pcode'] = 'recharge_XKEC0700000TXVIPYK';
        $data['requestid'] = time();
        // $data['pcode'] = 'recharge';
        $data['telphone'] = '13365802535';
        $data['recharge_no'] = '13365802535';
        $data['timestamp'] = time();
        $data['msgencrypt'] = '1';
        ksort($data);
        $data["sign"] = md5(implode("", $data));
        $result = $client->request('POST', $url,['form_params' =>$data] );
        $return = $result->getBody()->getContents();

        (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($data), $return, 'aiqiyi_wakejin', '555', 'aiqiyi');

        return $return;
    }


    //提交的表单
    public function actionIndex(){
        $this->layout = 'cloudcarv2';
        $id = \Yii::$app->request->get('coupon_id');
        return $this->render('index',[
            'id'=>$id
        ]);
    }

    public function actionDetail(){
        $this->layout = 'cloudcarv2';
        $id = \Yii::$app->request->get('id');
        $model  =  \common\models\Aiqiyi::findOne($id);
        return $this->render('detail',[
            'info'=>$model
        ]);
    }


    public function actionSubmit(){
        //开启事务处理
        try{
            $request = \Yii::$app->request;
            $coupon_id = $request->post('coupon_id');
            $account = $request->post('account');

            $aiqiyi_components = new Aiqiyi();

            $uid = \Yii::$app->session['wx_user_auth']['uid'];
            $coupon =   (new CarCoupon())->table()->where(['id'=>$coupon_id,'uid'=>$uid,'status'=>1])->one();
            if(!$coupon){
                throw new \Exception('优惠券不存在，请检查');
            }
            (new CarCoupon())->myUpdate(['status'=>2],['id'=>$coupon['id']]);
            $bindid = $coupon['bindid'];

            //生成订单
            $aiqiyi_params = \Yii::$app->params['aiqiyi'];

            $model  = new \common\models\Aiqiyi();

            $order_no = 'wakejin-'.$uid.'-'.time();;
            $model->order_no = $order_no;
            $model->coupon_id = $coupon_id;
            $model->uid = $uid;
            $model->status = 1;
            $model->company = 1;  //1 表示挖客金
            $model->company_id = $coupon['companyid'];  //1 表示挖客金
            $model->account = $account;
            $model->c_time = time();
            $model->save();

            $wakejin_params = \Yii::$app->params['wakejin'];
            $url = $wakejin_params['url'].'/Trade/Index/agentApi';
            $client = new Client();
            $data = [];
            $data['username'] = $wakejin_params['username'];
            $data['secretkey'] = $wakejin_params['secretkey'];
            //每个优惠券的code不一样
            $data['pcode'] = 'recharge_'.$coupon['bindid'];  //  ['recharge_IQYZCHJYK';
            $data['requestid'] = $order_no;
            $data['telphone'] =  \Yii::$app->session['wx_user_auth']['mobile'];
            $data['recharge_no'] = $account;
            $data['timestamp'] = time();
            $data['msgencrypt'] = '1';
            ksort($data);
            $data["sign"] = md5(implode("", $data));
            $result = $client->request('POST', $url,['form_params' =>$data] );
            $return = $result->getBody()->getContents();
            $result =  \GuzzleHttp\json_decode($return,true);
            (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($data), $return, 'aiqiyi_wakejin', '1', 'aiqiyi_wakejin');

            $model->code = $result['retcode'];
            $model->s_time = time();

            if(in_array($result['retcode'],['0000'])){
                $model->status = 2;
            }else{
                 $model->status = 3;
                //如果没使用成功，则可以继续使用

                //(new CarCoupon())->myUpdate(['status'=>1],['id'=>$coupon['id']]);

            }

            $model->save();
            return $this->json(1,'提交成功，请等待',[],'/frontend/web/aiqiyi/detail.html?id='.$model->id);

        }catch (\Exception $exception){

            return $this->json(0,$exception->getMessage());
        }


    }



    //核销接口
    public function actionNotice(){
        try{
            $request = Yii::$app->request;
            $aiqiyi_order_id = $request->post('requestid');
            $order_no =$request->post('orderid');
            $state =$request->post('state');
            //查询订单

            $model  =  \common\models\Aiqiyi::find()->where([
                'order_no'=>$order_no
            ])->one();
            if(!$model){
                throw new \Exception('订单不存在');
            }
            if($state==1){
                $model->status = 2;
            }else{
                $model->status = 3;
                $coupon =   (new CarCoupon())->table()->where(['id'=>$model->coupon_id])->one();

                (new CarCoupon())->myUpdate(['status'=>1],['id'=>$coupon['id']]);
            }
            //

            return $this->json(1,'提交成功，请等待',[],'/frontend/web/aiqiyi/detail.html?id='.$model->id);
        }catch (\Exception $exception){
            return $this->json(0,$exception->getMessage());
        }

    }


}