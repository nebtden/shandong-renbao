<?php

namespace frontend\controllers;

use common\components\Aiqiyi;
use common\models\AiqiyiType;
use common\models\CarCoupon;
use common\models\CarUseroilcard;
use frontend\util\PController;
use Yii;

class AiqiyiController extends PController {

    public $site_title = '爱奇艺充值';
    public $menuActive = 'caruser';

    public static $types =[


    ];


    public function actionTest(){
        return $uid = \Yii::$app->session['wx_user_auth']['uid'];
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

            $order_no = $aiqiyi_params['partnerNo'].'-'.$uid.'-'.time();;
            $model->order_no = $order_no;
            $model->coupon_id = $coupon_id;
            $model->uid = $uid;
            $model->status = 1;
            $model->account = $account;
            $model->c_time = time();
            $model->save();

            $result  = $aiqiyi_components->sendOrder($bindid,$account,$order_no,1);
            $model->code = $result['code'];
            if(in_array($result['code'],['Q00304','Q00308','Q00407','Q00307'])){
                (new CarCoupon())->myUpdate(['status'=>1],['id'=>$coupon['id']]);
            }elseif($result['code']=='A00000'){
                $model->status = 2;
                $model->startTime = $result['startTime'];
                $model->deadline = $result['deadline'];
                $model->s_time = time();

            }else{
                $model->status = 3;
            }


            $model->save();
            return $this->json(1,'提交成功，请等待',[],'/frontend/web/aiqiyi/detail.html?id='.$model->id);

        }catch (\Exception $exception){

            return $this->json(0,$exception->getMessage());
        }


    }

    public function actionSimon(){

//        $model =  \common\models\Aiqiyi::findOne(1);
//        echo $model->uid;
//        die();

        $list = Aiqiyi::$types;
        foreach ($list as $key=>$value){
            $type = new AiqiyiType();

            $type->id = $key;
            $type->name = $value['name'];
            $type->price = $value['price'];
            $type->product_id = $value['product_id'];
            $type->old_name = $value['old_name'];
            $type->save();
        }
    }


}