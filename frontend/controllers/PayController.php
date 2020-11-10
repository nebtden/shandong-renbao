<?php

namespace frontend\controllers;

use common\components\Payments;
use frontend\util\PController;
use common\models\PayCompany;
use common\models\PayOrder;
use common\models\PayProduct;
use Yii;


class PayController extends CloudcarController {

    public function init() {
        $this->layout = false;
    }


    public function actionIndex() {
        $request = Yii::$app->request;
        $id= $request->get('id');
        $product_info=  PayProduct::findOne($id)->toArray();
        $head_imgs = $product_info['head_img'];
        $images = explode(',',$head_imgs);


        //公司显示检测
        $companies = PayCompany::find()->where([
            'status'=>1
        ])->asArray()->all();

        return $this->render('index',array('pro_info'=>$product_info,'companies'=>$companies,'images'=>$images));
    }


    public function actionSave() {

        $return = [
            'status'=>1,
            'message'=>'',
            'data'=>[]
        ];

        try{

            $request = Yii::$app->request;
            $company_id = $request->post('company_id',0);
            $company = $request->post('company','');
            $product_id = $request->post('product_id');
            $name = $request->post('name');
            $phone = $request->post('phone');
            $address = $request->post('address');
            $code = $request->post('code','');
            $number = $request->post('number');


            //逻辑查询，查询库存数量
            $product = PayProduct::findOne($product_id);
            $product_number = $product->product_number;

            if($product->status!=1){
                throw new \Exception('不好意思，您的商品已经下架，请联系店铺管理员');
            }


            if($number>$product_number){
                throw new \Exception('不好意思，商品库存数量不够，请联系店铺管理员');
            }


            //
            $product->product_number = $product_number-$number;
            $product->save();


            //所有的任务
            //定时任务，多久没有购买，取消订单

            //插入到数据库
            $order = new PayOrder();
            $order->company_id = $company_id;
            $order->company_name = $company;
            $order->product_id = $product_id;
            $order->name = $name;
            $order->code = $code;
            $order->phone = $phone;
            $order->address = $address;
            $order->create_time = time();

            $order->number = $number;
            $order->money  = $number*$product->price;
            $res = $order->save();

            if($res){
                $order_id = $order->id;
                $order->order_sn = 'sg'.$order_id;
                $order->save();
            }

            $return['data']['url'] = 'http://www.yunche168.com/frontend/web/payment/jsapi.html?third=pay&product_id=sg'.$order_id;

        }catch (\Exception $exception){
            $return['status'] = 0;
            $return['message'] = $exception->getMessage();
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $return;

    }


}
