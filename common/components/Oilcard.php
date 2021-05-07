<?php

namespace common\components;

use Yii;

class Oilcard
{
    //中石油编号
    const COMPANY_SHIYOU = 1;

    //中石华编号
    const COMPANY_SHIHUA = 2;

    private $host_normal = 'http://123.56.48.89:8170/';//正式地址
    private $host_test = 'http://60.205.115.219:6160/';//测试地址
    private $host = '';

    //充值地址
    private $buy = 'unicomAync/buy.do?';
    //订单查询
    private $query = 'unicomAync/queryBizOrder.do?';
    //余额查询
    private $balance = 'unicomAync/queryBalance.do?';

    private $is_debug = false;

    //测试
//    private $userid = '243';
//
//    private $privateKey = 'b8e597cf7870d090801fcc8e910dcec808d6a1b2610eebf486cfccf6bc27d123';

    //正式
    private $userid = '322';

    private $privateKey = 'fbb174ab2a7d17bf67d4953a10d5c59d98a6c8ff4040548882fed1d3087bb90f';

    public $errCode = '00';
    public $errMsg = 'ok';

    //正式产口id
    public static $shiyou = [
        '50' => 14928,
        '100' => 14913,
        '200' => 14914,
        '500' => 14909,
        '1000' => 14915
    ];

    //测试产品id
//    public static $shiyou = [
//        '50' => 20000,
//        '100' => 20001,
//        '200' => 20002,
//        '500' => 20003,
//        '1000' => 20004
//    ];

    public static $shihua = [
        '100' => 14908,
        '200' => 14910,
        '500' => 14912,
        '1000' => 14911
    ];


    /**
     * 获得对应种类油卡的对应面额的产品编号
     * @param string $card_no 待充值油卡卡号
     * @param int $amount 充值金额
     * @param int $type 油卡类型 1 中石油，2 中石化
     * @return string
     */
    public static function get_item_id($card_no, $amount, $type = self::COMPANY_SHIYOU)
    {
        $amount = (string)((int)$amount);
        $item_id = null;
        //先验证卡号
        $card_type = self::check_oil_card_no($card_no);
        if ($card_type != $type) {
            return '卡号类型与充值类型不一致';
        }

        $itemIds = ($type == self::COMPANY_SHIHUA) ? self::$shihua : self::$shiyou;

        if (isset($itemIds[$amount])) {
            $item_id = $itemIds[$amount];
        } else {
            $card_company = ($type == self::COMPANY_SHIHUA) ? '中石化' : '中石油';
            return $card_company . '暂无对应面额的充值产品';
        }
        return $item_id;
    }

    /**
     * 中石油可充值卡号以 90，或 96 开头 16位号码
     * 中石化可充值卡号以 10 开头 19位号码
     * 验证油卡是否为允许充值的卡号类型
     * @param string $card_no 待验证的油卡卡号
     * @return bool|int 返回 1 表示是可充值的中石油卡号，返回 2 表示是可充值的中石化卡号
     * 返回false 表示该卡号不可充值，不能绑定或使用
     */
    public static function check_oil_card_no($card_no)
    {
        $pattern_shiyou = '/^9[06]\d{14}$/';
        $pattern_shihua = '/^10\d{17}$/';
        if (preg_match($pattern_shiyou, $card_no)) {
            return self::COMPANY_SHIYOU;
        } elseif (preg_match($pattern_shihua, $card_no)) {
            return self::COMPANY_SHIHUA;
        } else {
            return false;
        }
    }

    public function __construct($userid = null, $privateKey = null)
    {
        $this->host = \Yii::$app->params['chehoujia']['url'];
        $this->userid = \Yii::$app->params['chehoujia']['user_id'];
        $this->privateKey = \Yii::$app->params['chehoujia']['privateKey'];

//        if ($userid) $this->userid = $userid;
//        if ($privateKey) $this->privateKey = $privateKey;
//        $this->host = $this->is_debug ? $this->host_test : $this->host_normal;
    }

    public function set_env($is_debug = false)
    {
        $this->is_debug = $is_debug;
        $this->host = $is_debug ? $this->host_test : $this->host_normal;
    }

    public function sign($data)
    {
        ksort($data);
        $str = implode('', $data);
        $str .= $this->privateKey;
        $sign = strtolower(md5($str));
        return $sign;
    }

    private function get_query_str($data)
    {
        $data = array_map(function ($val) {
            return urlencode($val);
        }, $data);
        $str = http_build_query($data);
        return $str;
    }

    /**
     * 特别说明，当code为31时要做特别处理
     * @param int $itemId 产品编号
     * @param string $card_no 要充值的卡号
     * @param string $serialno 订单号
     * @return bool|mixed
     */
    public function buy($itemId, $card_no, $serialno)
    {
        $data['userId'] = $this->userid;
        $data['itemId'] = $itemId;
        $data['uid'] = $card_no;
        $data['serialno'] = $serialno;
        $data['dtCreate'] = date("YmdHis");
        $sign = $this->sign($data);
        $data['sign'] = $sign;

        $str = $this->get_query_str($data);

        $url = $this->host . $this->buy . $str;
        $res = W::http_get($url);
        if ($res) {
            $resobj = simplexml_load_string($res);
/*            $this->errCode = $resobj->code;
            $this->errMsg = $resobj->desc;
            $this->write_log(
                "[itemId]:$itemId" . PHP_EOL .
                "[cardNo]:$card_no" . PHP_EOL .
                "[orderNo]:$serialno" . PHP_EOL .
                "[desc]:{$this->errCode}--{$this->errMsg}"
            );*/
            return json_decode(json_encode($resobj), true);
        }
        return false;
    }

    protected function write_log($content)
    {
        $file = './log/oil_' . date('Y_m_d') . '.log';
        $str = str_repeat('-', 20) . date("Y-m-d H:i:s") . str_repeat('-', 20) . PHP_EOL;
        $str .= $content . PHP_EOL;
        error_log($str, 3, $file);
    }

    /**
     * 查询订单状态
     * @param $serialno
     * @return bool|mixed
     */
    public function query_order($serialno)
    {
        $data['userId'] = $this->userid;
        $data['serialno'] = $serialno;
        $sign = $this->sign($data);

        $data['sign'] = $sign;

        $str = $this->get_query_str($data);

        $url = $this->host . $this->query . $str;

        $res = W::http_get($url);
        if ($res) {
            $resobj = simplexml_load_string($res);
            if ($resobj->code == '00' && $resobj->status == 'success') {
                $data = json_decode(json_encode($resobj->data), true);
                $this->errCode = $data['status'];
                $this->errMsg = $data['statusDesc'];
                return $data;
            } else {
                $this->errCode = $resobj->code;
                $this->errMsg = $resobj->desc;
            }
        }
        return false;
    }


    /**
     * 商户余额查询
     * @return bool|\SimpleXMLElement
     */
    public function query_balance()
    {
        $data['userId'] = $this->userid;
        $sign = $this->sign($data);
        $data['sign'] = $sign;

        $str = $this->get_query_str($data);

        $url = $this->host . $this->balance . $str;

        $res = W::http_get($url);
        if ($res) {
            $resobj = simplexml_load_string($res);
            $this->errCode = $resobj->code;
            $this->errMsg = $resobj->desc;
            if ($resobj->code == '00' && $resobj->status == 'success') {
                return $resobj->balance;
            }
        }
        return false;
    }
}