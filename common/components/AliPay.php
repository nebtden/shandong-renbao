<?php

namespace common\components;


class AliPay
{
    private static $instance;

    //支付宝网关
    private $gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    //沙箱
    //private $gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
    //appid
    // 正式
    private $appId = '2018052460230244';
    //沙箱
    //private $appId = '2016091400508575';
    //私钥
    private $rsaPrivateKey = 'MIIEpQIBAAKCAQEA3hzA8tGx02ESkkzM15w7oBsFiiUDY4Gapg2CFf2Ka5eVDEEgePrMCxYspoW2dq0MxrHX9iWN4sWa31Lfpe52Zx8hnKDKZfEmW1mdNyb4ZUK0ig0uKvGXbzXTl+i+fKbPrFfY26/QmOsSY8wgvG2QhxrgLYugKJ8JF5UXDpodzmEx/Q6hlFtN0oyecePRKTiMLD3r8DH4kYEVZKmHSNwkEPljCQyWOKF418R0Fz5wEr9lxU7mg1wdyOm+OCokB4JD//1S1JMr8Zi5Jdah1IRWEZMBiZcUjs5QKRbanamSeePbYUiaEGMyMBsBfJxsVqUwQeLRMumQeTPnROPSbu6JhwIDAQABAoIBAGTggE2IKZCMZQfnM521SmtT+nccimZ2JYvHVM6yAV0OUlZned0YYvWiE5Np5U4PGF9hxGj583AMOWO2Wvccz8/UFsrxSBt2o+oXUE0NOQGcgyy0AcTlRtbuhnRW87TfejCXEVnthvr8wLRssG2EAYErFqf44zuvx0xbnUY3ftQM/fAmfm88NBJUs0lXIiMizneZsXyizl5h5dprZz16r3Ek3XZwDhzhU+Rlz+kCj0QTPw4mos/lPFeP45DhuS6aIaSX3891Gd/s2z61yPIPwOYmVgJvilcFTOjkixE4IMug/tvP4uBZQl5onSxU+awJz8/Nvfi8u1s1n5llzXAhlKECgYEA+l5PgARcjaAuH8oyDTw3JVGdk7jPLo744kRDWTUXYePKjQiIsjTgAS9LlOxAvOJkulet+bABXgCRkzcAMlXuvWNzSl+Akxlc2KUeUU38zJAPjSAm0zZE3E8pDt/qW6ogwxDDEFzVHMmyHYW5MoVKwnUWtnqwSIoYTo6sXzfekjcCgYEA4xu8qF0ZaQqMwKx5B+3kTxqdPS525cNDUIv95Ajl3zSRfUqu6BvyGzqKXOlqSkNvSwLLFgkd+XpVgl9bNVpEj4YwqxFojukekHWzxIqvqOY5yc9rwk8AeD6dC0+hYwELwhXkj6jfLGjnceKWsl16+asAFsbAvcXVS8c+F1n9WzECgYEA+CQk8yJfgNazIDrMJKX7mfcsEE7ouKJnNgqmHXIrPJACHonIwab6JPJ1HKKS/yH6510jRwcUM9Cod5nZjgnxVq+MgrfovOI0TVxJkheTaEOXxi2JjWiKEzg53045/qO9WNfyHPOFHMUizXPNu660C8r0ueMbeKm1sZZibPT4mT0CgYEA1gpgJbWIq0z8FuL2NpjoYf1NUEooWKdNG+60XGRecZ0TuafXbH5aEXt8x9BdEpy3mVSKnrv8+hFn4bWJOqFWmIX5/GAt0PK4kaG8yXGD2IUS1badcsYORUWNsQldqvdjY4pKnXKk0zATYWwSZwxfSEr3jH/JS5HWUtL3G/7+HyECgYEAtIgq/7aFkfy41/MZzd/liWeePfOI+EJgiyjUwhKLd6CYx4Rwsxy1i9KsQCpswF6qWcezaJdlnoL8o2lUr6AnOoOnavTdQhs4b6HpdSM605VAkAWbslnzuSvltp3zf7u6W4fNOFPIcJKFWekW9bL/egbaOT45f9nyk2jExo1TLPo=';
    //支付宝公钥
    private $alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjPH6eWBjUPrrifDZJOLQmKJmp/rbfNAgcoLWhL9nzqvAatCjIK7fzTrpD6ovnFixBnaRdzlY0radmkoVhWfg+Kxe1D43nWtEc+jXAx33Jb9sl2kAJk6cioza1qvberCAzyIA6zi0pazwKiBnZmvVhPMBaFb7xUgVX/HM7Hacn9fSt0v+w0gnqBR6q11SYOvZKRbIP+swVao+zVoCEEEmHo0TON18tvn8HPjHs7092cP+01FC/6dBpJQLYnlCySctyzO0cact2JOEVPrYvnxsqDKE7ZWtx1NwnwN1g/P3zJzIY/5yTncxBG/mk7GfeeqNC9qdSN4J0ZyM6YuJH1ncOQIDAQAB';
    //沙箱
    //private $alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApjl6T7vwLf6EFig+ZdVGt7jZ/ojIHCtWtuitAIEtLhEyTBuOFGwebme8+fXA3X9kJXjwE2IZLlhgLwPESEoVxCqtjBTsUlcZEyFd5+6bmPaETW7mT1I4N7IztCvfuOOL4wDsUmO1eV5zDDIo3CjhrfQi8l6h3U6Esb5D6Cvf56IsZKywRXHsE1tHyf0IQDaCztFenKOux5FUcCp7sru67UCOE3zoJewFDcEdZBA+PCdvmhRVuxjFQhokqvTyU8Eph6SC02FdlHmYZB83pYQw16NtIS+mDuDq6UqIqZxqH103zqcobOWk/Hv3DpTkukiKe2No8ULmn6bTjSa2NsYDcwIDAQAB';
    //private $apiVersion = '1.0';
    //加密类型
    private $signType = 'RSA2';
    //private $postCharset = 'UTF8';
    //private $format = 'json';


    private $result = null;

    private function __construct($config = [])
    {
        include '../../vendor/alipay/AopSdk.php';
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance($config = [])
    {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * @param string $out_biz_no 商户转账唯一订单号
     * @param string $payee_account 收款方账户
     * @param string $amount 转账金额，单位：元。
     * @param string $payee_type 收款方账户类型,1ALIPAY_USERID,2,ALIPAY_LOGONID
     * @param string $payer_show_name 付款方姓名
     * @param string $payee_real_name 收款方真实姓名
     * @param string $remark 转账备注
     * @return bool|array
     * 正确时返回数组
     * [
     *      code=>'错误码',
     *      msg =>'描述',
     *      order_id => '支付宝订单流水号，需要保存',
     *      out_biz_no =>'我方定义的唯一流水号',
     *      pay_date => 2018-05-28 11:04:47
     * ]
     */
    public function transfer_account($out_biz_no, $payee_account, $amount, $payer_show_name = '', $remark = '', $payee_real_name = '', $payee_type = 'ALIPAY_LOGONID')
    {
        $aop = new \AopClient ();
        $aop->gatewayUrl = $this->gatewayUrl;
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $aop->signType = $this->signType;
        $request = new \AlipayFundTransToaccountTransferRequest();
        $data = [
            "out_biz_no" => $out_biz_no,
            "payee_type" => $payee_type,
            "payee_account" => $payee_account,
            "amount" => $amount,
        ];
        if ($payer_show_name) $data['payer_show_name'] = $payer_show_name;
        if ($payee_real_name) $data['payee_real_name'] = $payee_real_name;
        if ($remark) $data['remark'] = $remark;
        $content = json_encode($data, JSON_UNESCAPED_UNICODE);
        $request->setBizContent($content);
        $result = $aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;

        $this->result = json_decode(json_encode($result->$responseNode), true);

        $logcontent = json_encode(array_merge($data, $this->result), JSON_UNESCAPED_UNICODE);

        if (!empty($resultCode) && $resultCode == 10000) {
            $this->writelog($logcontent, 'info');
            return $this->result;
        }
        $this->writelog($logcontent, 'error');
        return false;
    }

    /**
     * 写日志
     * @param $content
     * @param string $type
     */
    public function writelog($content, $type = 'info')
    {
        $path = './../../log/transfer/';
        if ($type == 'info') {
            $filename = 'zfb_' . date("Ymd") . '.log';
        } else {
            $filename = 'zfb_' . date("Ymd") . '.err';
        }
        $content = PHP_EOL . '[' . date("Y-m-d H:i:s") . ']' . PHP_EOL . $content . PHP_EOL;
        //error_log($content, 3, $path);
        file_put_contents($path.$filename,$content,FILE_APPEND);
    }

    public function getError()
    {
        if ($this->result) {
            $code = $this->result['code'];
            $msg = $this->result['msg'];
            if (isset($this->result['sub_msg'])) $msg = $this->result['sub_msg'];
            return "$code:$msg";
        }
        return 'Not doing anything;';
    }

    public function getResult()
    {
        return $this->result;
    }

    public function __toString()
    {
        return $this->getError();
    }
}