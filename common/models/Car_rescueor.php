<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9 0009
 * Time: 下午 2:52
 */

namespace common\models;

use Yii;
use common\components\AlxgBase;

class Car_rescueor extends AlxgBase
{
    protected $currentTable = '{{%car_rescueor}}';

    //0待受理，1已受理，2已派车，3已出发，4到现场，5已完成，6取消
    public static $status = [
        '0' => '已下单',
        '1' => '待受理',
        '2' => '已受理',
        '3' => '已调派',
        '4' => '已回拨',
        '5' => '已到达',
        '6' => '已完成',
        '7' => '用户取消',
        '8' => '系统取消',
        '9' => '客服取消'
    ];

    public static $status_text = [
        '0' => '已下单,正在下发订单',
        '1' => '正在等待救援方受理',
        '2' => '救援方已经受理',
        '3' => '救援方已调派，正在火速赶来',
        '4' => '救援方已回拨',
        '5' => '救援方已到达救援地点，开始救援',
        '6' => '救援已完成',
        '7' => '您已经取消此次救援',
        '8' => '系统自动取消此次救援',
        '9' => '客服取消此次救援，如有疑问，可联系我们'
    ];

    //把之前的记录更改为现在的记录
    public static $old_status_to_new = [
        '0' => '1',
        '1' => '1',
        '2' => '2',
        '3' => '2',
        '4' => '2',
        '5' => '2',
        '6' => '3',
        '7' => '0',
        '8' => '0',
        '9' => '0'
    ];

    /**
     * 下救援订单
     */
    public function plac_an_order($info = [])
    {
        $now = time();
        $info['date_day'] = date("Ymd", $now);
        $info['date_month'] = date("Ym", $now);
        $id = $this->myInsert($info);
        if (!$id) return false;
        $info['id'] = $id;
        return $info;
    }
}