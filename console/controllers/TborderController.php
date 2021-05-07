<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\6\17 0017
 * Time: 16:57
 */

namespace console\controllers;


use common\components\DianDian;
use common\components\W;
use common\models\Car_wash_order_taibao;
use yii\console\Controller;

class TborderController extends Controller
{
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
                echo $val['id'].'status';

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