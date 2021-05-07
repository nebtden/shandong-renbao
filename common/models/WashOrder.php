<?php

namespace common\models;

use common\components\AlxgBase;
use frontend\controllers\CarwashController;
use common\components\DianDianWash;
use Yii;
use common\components\CarCouponAction;
use common\components\ShengDaCarWash;


class WashOrder extends AlxgBase
{
    protected $currentTable = '{{%car_wash_order}}';

    public static $status_text = [
        '-1' => '已取消',
        '0' => '处理中',
        '1' => '处理中',
        '2' => '已完成',
        '3' => '失败',
        '4' => '过期'
    ];

    public static $old_status_to_new = [
        '0' => '2',
        '1' => '2',
        '2' => '3',
        '3' => '0',
        '4' => '0',
    ];
    public static $status_arr = [
        '-1' => '已取消',
        '1' => '处理中',
        '2' => '已完成',
        '3' => '失败',
        '4' => '过期'
    ];
    //洗车平台用于区分url
    public static $company = [
        '1' => 'diandian',
        '2' => 'shengda',
        '3' => 'ziying'
    ];

    //山东太保订单状态
    public static $order_status = [
        '1' => 'ORDER_ON_SENDING',
        '2' => 'ORDER_FINISH',
        '-1' => 'ORDER_CANCEL'
    ];


    /**
     * 获取消费凭证
     * @param $data
     * @return mixed
     */
    public function getConsCode($uid,$couponId,$company_id)
    {
        $map = [
            'uid' => $uid,
            'couponId' => $couponId,
            'company_id' => $company_id,
            'date_month' => date('Ym')
        ];

        $sql = self::table()->select()->where($map)->limit(1)->orderBy('id desc')->getLastSql();
        $db = \Yii::$app->db;
        $res = $db->createCommand($sql)->queryOne();

        return $res;
    }
}