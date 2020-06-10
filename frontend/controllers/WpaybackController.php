<?php

namespace frontend\controllers;

use common\models\CarCouponPackage;
use common\models\CarWashpay;
use Yii;
use frontend\util\PController;

class WpaybackController extends PController
{

    public $enableCsrfValidation = false;

    private $notify;

    private $log;

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        require_once './../../vendor/WxPayV3/WxPay.Api.php';
        require_once './../../vendor/WxPayV3/WxPay.Notify.php';
        $this->log_init();
        $this->notify = new \WxPayNotify();
        return true;
    }

    protected function log_init()
    {
        $this->log = [];
        $seprator = str_repeat("-", 20);
        $this->log['begin'] = $seprator . date("Y-m-d H:i:s") . $seprator . PHP_EOL;
    }

    protected function log_in($key, $val)
    {
        $this->log[$key] = $val . PHP_EOL;
    }

    protected function log_write()
    {
        $path = './paylog/wx_' . date("Ymd") . '.log';
        $content = '';
        foreach ($this->log as $k => $v) {
            $content .= "[$k]" . $v;
        }
        error_log($content, 3, $path);
    }

    //获取微信通知消息
    protected function getPayBackData(&$msg)
    {
        //获取通知的数据
        $xml = file_get_contents("php://input");
        //如果返回成功则验证签名
        try {
            $result = \WxPayResults::Init($xml);
        } catch (\WxPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }
        return $result;
    }

    //回复微信服务器
    protected function Reply($code = "SUCCESS", $msg = 'OK')
    {
        $xml = "<xml><return_code><![CDATA[$code]]></return_code><return_msg><![CDATA[$msg]]></return_msg></xml>";
        return $xml;
    }

    public function actionWash()
    {
        $msg = 'ok';
        $data = $this->getPayBackData($msg);
        if ($data === false) {
            $this->log_in('Data', $msg);
            $this->log_write();
            return $this->Reply('FAIL', $msg);
        }
        //获得了数据，进行处理
        $result = $this->washPayBack($data, $msg);
        $this->log_write();
        if ($result === false) {
            return $this->Reply('FAIL', $msg);
        } else {
            return $this->Reply('SUCCESS', 'OK');
        }
    }

    protected function washPayBack($data, &$msg)
    {
        $return_code = $data['return_code'];
        $this->log_in('return_code', $return_code);
        $return_msg = $data['return_msg'];
        $this->log_in('return_msg', $return_msg);
        $out_trade_no = $data['out_trade_no'];
        $this->log_in('out_trade_no', $out_trade_no);
        if ($return_code !== 'SUCCESS') {
            $msg = 'FAIL';
            return false;
        }
        $transaction_id = $data['transaction_id'];
        $this->log_in('transaction_id', $transaction_id);
        $wpObj = new CarWashpay();
        $order = $wpObj->table()->where(['orderid' => $out_trade_no])->one();
        if (!$order) {
            $this->log_in('result', 'order is not exist');
            $msg = "FAIL";
            return false;
        }
        if ($order['status'] > 0) {
            $this->log_in('result', 'SUCCESS');
            $msg = 'ok';
            return true;
        }
        $trans = Yii::$app->db->beginTransaction();
        $pkObj = new CarCouponPackage();
        try {
            $sql = $pkObj->table()->where(['is_pay' => 1, 'status' => 1, 'uid' => 0])->limit($order['num'])->getLastSql();
            $sql .= " FOR UPDATE";
            $package = Yii::$app->db->createCommand($sql)->queryAll();
            if (!$package) {
                $order['has_error'] = 1;
                $order['err_msg'] = 'no package';
                //throw new \Exception('no package');
            } else {
                $ids = [];
                $pwds = [];
                foreach ($package as $p) {
                    $ids[] = $p['id'];
                    $pwds[] = $p['package_pwd'];
                }
                $uid = $order['uid'];
                $r = $pkObj->myUpdate(['uid' => $uid], ['id' => $ids]);
                if (!$r) {
                    throw new \Exception('update package fail');
                }
                $order['package_id'] = implode(',', $ids);
                $order['package_pwd'] = implode(',', $pwds);
            }
            $order['status'] = 1;
            $order['pay_time'] = time();
            $r = $wpObj->myUpdate($order);
            if (!$r) {
                throw new \Exception('update washpay fail');
            }
            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            $msg = 'FAIL';
            $this->log_in('result', $e->getMessage());
            return false;
        }
        $this->log_in('result', 'SUCCESS');
        return true;
    }
}