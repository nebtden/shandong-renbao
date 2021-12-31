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

class WakejinNoticeController extends PController {


    //核销接口
    public function actionIndex(){
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
            $model->aiqiyi_order_id = $aiqiyi_order_id;
            if($state==1){
                $model->status = 2;
            }else{
                $model->status = 3;
                $coupon =   (new CarCoupon())->table()->where(['id'=>$model->coupon_id])->one();

                (new CarCoupon())->myUpdate(['status'=>1],['id'=>$coupon['id']]);
            }
            //

            return $this->json(1,'提交成功',[],'');
        }catch (\Exception $exception){
            return $this->json(0,$exception->getMessage());
        }

    }


}