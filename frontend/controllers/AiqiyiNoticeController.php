<?php

namespace frontend\controllers;

use common\components\Aiqiyi;
use common\components\DianDian;
use frontend\util\PController;

class AiqiyiNoticeController extends PController {

    public $enableCsrfValidation = false;

    public function actionIndex(){
        $request = \Yii::$app->request;
        $post = \Yii::$app->request->post();

        $orderNo = $request->post('orderNo');
        $startTime = $request->post('startTime');
        $deadline = $request->post('deadline');
        $status = $request->post('status');
        $sign = $request->post('sign');

        //请求写入日志
        //写入日志
        $url = 'aiqiyi-notice/index.html';
        (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($post), '', 'aiqiyi-return', '', 'aiqiyi-return');

        try{
            //检测订单状态
            $model  =  \common\models\Aiqiyi::find()->where([
                'order_no'=>$orderNo
            ])->one();
            if(!$model){
                throw new \Exception('找不到订单'.$orderNo);
            }

            //检测sign是否准确
            $aiqiyi = new Aiqiyi();
            unset($post['sign']);
            //生成订单
            $aiqiyi_params = \Yii::$app->params['aiqiyi'];
            $check_sign = $aiqiyi->make_sign($post,$aiqiyi_params['key']);
            if($check_sign!=$sign){
 //               throw new \Exception('系统错误');
            }

            if($status==1){
                if($model->status==1){
                    $model->status=2;
                    $model->s_time=time();
                    $model->startTime=$startTime;
                    $model->deadline=$deadline;
                    $model->save();
                }else{

                }
            }

            $return  = [];
            $return['code']  = 'A00000';
            $return['msg']  = '成功';
        }catch (\Exception $exception){
            $return  = [];
            $return['code']  = 'A00001';
            $return['msg']  = $exception->getMessage();
        }


        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $return;

    }

    public function actionNotice(){

    }
}