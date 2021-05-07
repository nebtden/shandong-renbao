<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\5\30 0030
 * Time: 8:57
 */

namespace console\controllers;


use common\components\ShangDongETC;
use common\models\CarEtcBill;
use common\models\CarEtcorder;
use common\models\Redis;
use yii\console\Controller;

class EtcbillController extends Controller
{


    //定时任务，每天1点查询上一天的etc账单
    public function actionEtcbillquery()
    {
        //查询所有已经激活ETC设备的订单信息
        $etcUsers = (new CarEtcorder())->select('*',['status'=>2])->all();
        $now = time();
        foreach($etcUsers as $user){
            $res = $this->etcBillQuery($user,$now);
        }

        return true;
    }

    /**
     * ETC机构查询订单
     * @param $uid
     * @param $card
     * @param $time
     * @return bool
     */
    private function etcBillQuery($user,$time)
    {

        $end = date('Ymd',$time);
        $begin = date('Ymd',strtotime($end.' -1 day'));
        $month = date('Ym');

        $params = [
            'user_id' => $user['uid'],
            'card_no' => $user['card_no'],
            'begin_time' => $begin,
            'end_time' => $end
        ];

        $sdEtcObj = new ShangDongETC();
        $res = $sdEtcObj->etcBill($params);

        if($res['response']['code'] != '000000' || empty($res['response']['data']['bill_detail'])){
            return false;
        }

        foreach ($res['response']['data']['bill_detail'] as $key => $v){
            $listData[$key] = [
                'uid' => $user['uid'],
                'plate_no' => $user['plate_no'],
                'card_no' => $user['card_no'],
                'name' => $user['username'],
                'month' => $month,
                'trans_info' => $v['trans_info'],
                'in_time' => $v['in_time'],
                'in_station' => $v['in_station'],
                'out_time' => $v['out_time'],
                'out_station' => $v['out_station'],
                'bill_money' => $v['bill_money'],
                'factorage' => $v['factorage'],
                'status' => $v['status'],
                'pay_time' => $v['pay_time']?:'',
                'pay_channel' => $v['pay_channel']?:'',
                'reason' => $v['reason']?:'',
                'c_time' => $time,
            ];
            $insertData[] = array_values($listData[$key]);
        }

        $columns = array_keys($listData[0]);

        //批量插入
        $result = (new CarEtcBill())->table()->batchInsert('',$columns,$insertData);
        return true;
    }

}