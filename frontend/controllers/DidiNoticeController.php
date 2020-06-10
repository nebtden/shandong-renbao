<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\DianDian;
use common\components\DiDi;
use common\components\DianDianOilCard;
use common\components\W;
use common\models\CallbackLog;
use common\models\CarCoupon;
use common\models\CarInsorder;
use common\models\CarOilor;
use common\models\CarSubstituteDriving;
use frontend\util\PController;
use Yii;
use common\models\FansAccount;
use yii\helpers\VarDumper;


class DidiNoticeController extends PController {

//    private $type;
    private $diandian_return = [
        'code'=>200,
        'msg'=>"success",
    ];



    public function actionOrderNotice(){
        $baseUrl = Yii::$app->request->queryString;
        $origin_data = $data = Yii::$app->request->post();
        $request = yii::$app->getRequest();
        $post =  $request->post();
        $outer_order_id = $post['outerOrderId'];
        $driving = new CarSubstituteDriving();

        try {
//            订单状态 1-新单 2-已取消 3-已超时 4-已接单 5-已到达 6-开始服务 7-结束服务 8-计费完成

            $didi = new DiDi();
            $result = $didi->checkToken($post);
            if (!$result) {
                throw new \Exception('token 验证失败');
                //处理相关接口相关逻辑
            }
            $status = $post['status'];
            $order_id = $post['orderId'];
            $outer_order_id = $post['outerOrderId'];
            $driving = new CarSubstituteDriving();

            $driving_info = $driving->table()->where(['company_id' => 1, 'orderid' => $outer_order_id])->one();
            if (!$driving_info) {
                throw new \Exception('订单不存在');
            }
            //更改状态
            $update = [];
            $update['status'] = DiDi::$order_status[$status];
            $update['order_id'] = $order_id;

            if(isset($post['driverName']) && !$driving_info['drivername']){
                $update['drivername'] = $post['driverName'];
            }
            if(isset($post['startPoiName']) && !$driving_info['departure']){
                $update['departure'] = $post['startPoiName'];
            }
            if(isset($post['endPoiName']) && !$driving_info['destination']){
                $update['destination'] = $post['endPoiName'];
            }
            if($status==4){
                $update['receive_time'] = time();
            }
            if($status==7){
                $update['end_time'] = time();
            }


            if (in_array($status,[2,3])) {
                //恢复优惠券
                $r = (new CarCouponAction())->unuseCoupon($driving_info['coupon_id']);
                if ($r === false) {
                    throw new \Exception('优惠券恢复失败');
                }
            }

            //查看优惠券状态，
            $couponModel = new CarCoupon();
            $coupon_info = $couponModel->table()->select('id,status')->where(['id' => $driving_info['coupon_id']])->one();
            if($coupon_info['status']==1 && in_array($status,[4,5,6,7,8]) ){
//                (new CarCouponAction())->useCoupon($driving_info['coupon_id']);
                $couponModel->myUpdate(['status'=>2],['id'=>$coupon_info['id']]);
            }


            $r = $driving->myUpdate($update,['id'=>$driving_info['id']]);
            if ($r === false) {
                throw new \Exception('订单状态更新失败');
            }
        } catch (\Exception $e) {
            $this->diandian_return['code'] = 201;
            //这个字段为我添加
            $this->diandian_return['msg'] = $e->getMessage();
        }

        $origin_data = \GuzzleHttp\json_encode($origin_data);
        $return_data = \GuzzleHttp\json_encode($this->diandian_return);
        (new DianDian())->requestlog($baseUrl,$origin_data,$return_data,'didi',$status,'didi');


        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->diandian_return;

    }

    public function actionPayNotice(){
        $baseUrl = Yii::$app->request->queryString;
        $origin_data = $data = Yii::$app->request->post();
        $request = yii::$app->getRequest();
        $post =  $request->post();


        try {
//            订单状态 1-新单 2-已取消 3-已超时 4-已接单 5-已到达 6-开始服务 7-结束服务 8-计费完成

            $didi = new DiDi();
            $result = $didi->checkToken($post);
            if (!$result) {
                throw new \Exception('token 验证失败');
                //处理相关接口相关逻辑
            }
            $status = $post['status'];
            $order_id = $post['orderId'];
            $outer_order_id = $post['outerOrderId'];
            $driving = new CarSubstituteDriving();
            $driving_info = $driving->table()->where(['company_id' => 1, 'order_id' => $order_id])->one();
            if (!$driving_info) {
                throw new \Exception('订单不存在');
            }
            //更改状态
            $update = [];
            $update['amount'] = $post['fee'];
            $update['cast'] = $post['paiedFee'];
            $update['status'] = DiDi::$order_status[$status];

            $r = $driving->myUpdate($update,['id'=>$driving_info['id']]);
            if ($r === false) {
                throw new \Exception('订单状态更新失败');
            }
        } catch (\Exception $e) {
            $this->diandian_return['code'] = 201;
            //这个字段为我添加
            $this->diandian_return['msg'] = $e->getMessage();
        }

        $origin_data = \GuzzleHttp\json_encode($origin_data);
        $return_data = \GuzzleHttp\json_encode($this->diandian_return);
        (new DianDian())->requestlog($baseUrl,$origin_data,$return_data,'didi',$status,'didi');


        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->diandian_return;
    }

    /**
     * 此部分待评论
     */
    public function actionCommentNotice(){

    }





}
