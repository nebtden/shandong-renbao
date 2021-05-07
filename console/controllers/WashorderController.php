<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\6\20 0020
 * Time: 14:55
 */

namespace console\controllers;

use Yii;
use common\models\Car_wash_order_taibao;
use common\models\WashOrder;
use common\models\CarCoupon;
use yii\console\Controller;
use common\components\DianDian;
use common\components\W;
use common\components\NationalLife;


class WashorderController extends Controller
{
    /**
     * 定时任务 每月1号1点检查上一个月洗车订单状态，将未完成订单状态改为失败
     * @return bool
     */
    public function actionCheckorder()
    {
        $where = [
            'status' => 1,
            'date_month' => date('Ym',strtotime("-1 month"))
        ];
        $res = (new WashOrder())->myUpdate(['status' => 3],$where);
        $this->NationalLifestatus();
        return true;
    }
    /**
     * 定时任务 每月1号1点检查上一个月洗车订单状态，将国寿商城失败订单推送给国寿商城
     * @return bool
     */
    private function NationalLifestatus()
    {
        $where = [
            'status' => 1,
            'date_month' => date('Ym',strtotime("-1 month")),
            'companyid'=>Yii::$app->params['national_life']['companyid']
        ];
        $washOrder = (new WashOrder())->select('*')->where($where)->all();
        $couponIds  = array_column($washOrder,'couponId');
        $coupons = (new CarCoupon())->table()->select('id,coupon_sn')->where(['id'=>$couponIds])->all();
        $coupon_sns = array_column($coupons,'coupon_sn','id');

        $objgs = new NationalLife();
        foreach ($washOrder as $key => $val){
            $datags = [
                'num' => $val['id'],
                'order_id' => $val['outOrderNo'],
                'cdkey' => $coupon_sns[$val['couponId']],
                'mobile' => $val['mobile'],
                'shop_name' => $val['shopName'],
                'service_name' => $val['serviceName'],
                'certificate' => $val['consumerCode'],
                'status' => WashOrder::$status_text['3'],
                'create_time' => date("Y-m-d H:i:s",time()),
                'update_time' => ''
            ];
            $objgs->notice($datags);
        }
        return true;
    }
    /**
     * 定时任务 每天1点 检测太保订单过期转体,过期就恢复权益
     * @return bool
     */
    public function actionChecktborder()
    {
        $res = (new Car_wash_order_taibao())->myUpdate(['status'=>4,'equity_status'=>4],'status = 3 AND point_time < "'.date('Y-m-d').'"');
        //恢复权益
        $this->recoveryEquity();
        return true;
    }
    /**
     * 太保订单过期恢复权益
     * @return bool
     */
    private function recoveryEquity()
    {
        $filde=['id','ticket_id','service_type'];
        $model = new Car_wash_order_taibao();
        $res = $model->table()->select($filde)->where('status IN(3,4) AND equity_status <> 3 AND point_time < "'.date('Y-m-d').'"')->all();
        $data = array();
        foreach($res as $k=>$V){
            $data['ticket_id'] = $res[$k]['ticket_id'];
            $data['service_type'] = $res[$k]['service_type'];
            $re = $this->tbOrderStatus($data,'DW01002','1007','0');
            if($re['ReturnCode'] == 1) $model->myUpdate(['equity_status'=>3],['id'=>$res[$k]['id']]);
        }
        return true;
    }

    /**
     * 山东太保定时任务，回传已接单信息
     * @return bool
     */
    public function actionTbstatus()
    {
        $orderObj = new Car_wash_order_taibao();
        $order = $orderObj->table()->where(['status'=>1])->all();
        if($order){
            foreach ($order as $val){
                //传已接单状态
                $r = $this->tbOrderStatus($val,'DW01002','1002','0');
                if($r['ReturnCode'] == 1){
                    //写入订单状态已接单
                    $res = $orderObj->myUpdate(['status'=>3],['id'=>$val['id']]);
                }
            }

        }

        return true;
    }

    /**
     * 回调给山东太保洗车核销信息
     * @param $washOrder
     * @return bool
     */
    private function tbOrderStatus($washOrder,$code,$status,$type)
    {
        $params = [
            'TicketId' => $washOrder['ticket_id'],
            'ServiceType' => $washOrder['service_type'],
            'SurveyUnitCode' => $status,
            'TicketType' => $type,
            'StatusTime' => date('Y-m-d H:i:s',time()),
            'TheTimeStamp' => (string)sprintf('%.0f', microtime(true)*1000),
        ];

        $repuestData = [
            'RequestCode' => $code,
            'AppId' => "cpic_e_rescue",
            'RequestBodyJson' => $params
        ];

        $url = \Yii::$app->params['shandongtaibao']['url'];
        $params = json_encode($repuestData);
        $res = W::http_post($url,$params);
        (new DianDian())->requestlog($url,$params,json_encode($res,JSON_UNESCAPED_UNICODE),'taibaowash',$status,'taibaowash');

        return json_decode($res,true);
    }

}