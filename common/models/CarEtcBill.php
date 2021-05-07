<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\5\29 0029
 * Time: 11:12
 */

namespace common\models;


use common\components\AlxgBase;
use common\components\ShangDongETC;

class CarEtcBill extends AlxgBase
{
    protected $currentTable = '{{%car_etc_bill}}';

    /**
     * ETC机构查询订单
     * @param $uid
     * @param $card
     * @param $time
     * @return bool
     */
    public function etcBillQuery($uid,$card,$plate,$time)
    {
        $end = date('Ymd',$time);
        $begin = date('Ymd',strtotime($end.' -1 week'));
        $month = date('Ym');

        $params = [
            'user_id' => $uid,
            'card_no' => $card,
            'begin_time' => '20150701',
            'end_time' => '20150730'
        ];

        $sdEtcObj = new ShangDongETC();
        $res = $sdEtcObj->etcBill($params);
        if($res['response']['code'] != '000000' || empty($res['response']['data']['bill_detail'])){
            return false;
        }
        foreach ($res['response']['data']['bill_detail'] as $key => $v){
            $listData[$key] = [
                'uid' => $uid,
                'plate_no' => $plate,
                'card_no' => $card,
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
        $result = self::table()->batchInsert('',$columns,$insertData);

        return $listData;
    }

    /**
     * 返回etc账单
     * @param int $uid //用户uid
     * @param string $car //用户车牌号
     * @param int $pages //当前页码
     * @param int $size //页面显示数量
     * @return array
     */
    public function carBill($uid,$car=0,$pages=1,$size=6)
    {
        $map['uid'] = $uid;
        if($car != 0){
            $map['plate_no'] = $car;
        }
        $list = [];
        $tmp = self::table()->select()->where($map)->limit($size)->offset($pages)->orderBy(['out_time'=>SORT_ASC])->all();
        array_walk($tmp,function($val) use(&$list,&$index){
            $month = '';
            $monthbill = [];
            list($month,$monthbill) = [$val['month'],$val];
            $list[$index][$month][] = $monthbill;
            $list[0][$month][] = $monthbill;
        });

        return $list;
    }

}