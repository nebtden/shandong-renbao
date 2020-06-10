<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/21 0021
 * Time: 上午 8:37
 */


namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\DianDian;
use common\models\CarOilor;
use frontend\util\PController;
use Yii;


class NoticeBangchongController extends PController
{

    private function log($type, $return)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/notice/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath, 0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'post_body:' . yii::$app->getRequest()->getRawBody() . "\n");
        fwrite($f, 'url:' . Yii::$app->request->queryString . "\n");
        fwrite($f, 'return:' . $return . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }


    /**
     *用于帮冲的回调
     */
    public function actionStatus()
    {
        $baseUrl = Yii::$app->request->absoluteUrl;

        $return = ['code' => 0, "message" => 'suc'];
        $origin_data = $data = $_GET;

        $sign = $data['sign'];
        unset($data['sign']);
        $trans = Yii::$app->db->beginTransaction();


        try {

            //订单状态更改
            $order_no = $data['order_no'];
            $user_order_no = $data['user_order_no'];
            $status = $data['status'];

            $obj = new CarOilor();


            $lcfg = Yii::$app->params['bangchong'];

            $str = $lcfg['uid'].$order_no.$user_order_no.$lcfg['oil_key'];

            $mysign = md5($str);

            if (strtolower($mysign) != $sign) {
                throw new \Exception('sign不正确');
            }


            //检查订单是否存在
            $info = $obj->table()->where(['orderid' => $user_order_no, 'bizorderid' => $order_no])->one();
            if (!$info) {
                throw new \Exception('order not found!');
            }


            $update = [];
            if ($status == 200) {
                $update['status'] = 2;  //2 表示成功
                $update['s_time'] = time();
            } elseif ($status == 202) {
                $update['status'] = 3;  //3 表示失败
                $update['s_time'] = time();
                $update['errmsg'] = $data['memo'];
                (new CarCouponAction())->unuseCoupon($info['coupon_id']);
            }


            //更新状态
            $obj->myUpdate($update, ['id' => $info['id']]);

            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            $msg = $e->getMessage();
            $return['code'] = -1;
            $return['message'] = $msg;
        }
        $origin_data = \GuzzleHttp\json_encode($origin_data);
        $return_data = \GuzzleHttp\json_encode($return);
        (new DianDian())->requestlog($baseUrl, $origin_data, $return_data, 'BangChong', $status, 'BangChong');

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $return;
    }


}