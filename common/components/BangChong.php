<?php

namespace common\components;

use GuzzleHttp\Client;
use Yii;
use yii\helpers\ArrayHelper;

ini_set('date.timezone', 'Asia/Shanghai');


class BangChong
{

    private $i = 'bangchong';

    private $oil_key = null;
    private $oil_url = null;
    private $uid = null;
    private $business_id = null;
    private $http = null;
    protected $data;

    /*10000(中石化50元加油卡)[暂不支持]
     10001(中石化100元加油卡)
    10002(中石化200元加油卡)
    10003(中石化500元加油卡)
    10004(中石化1000元加油卡)
    10007(中石化任意金额充值)[暂不支持]
    10008(中石油任意金额充值))
    */
    public static $product_list = [
        2 => 101,//石化
        1 => 102,//石油
        3 => 201//话费
    ];


    public function __construct()
    {
        $this->http = new Client();
        $lcfg = Yii::$app->params['bangchong'];
        $this->oil_key = $lcfg['oil_key'];
        $this->oil_url = $lcfg['oil_url'];
        $this->data = [
            'business_id' => $lcfg['business_id'],
            'uid' => $lcfg['uid'],
        ];
    }

    /**
     * 油卡充值接口
     */
    public function OilerPay($params = [])
    {
        $params['recharge_type'] = BangChong::$product_list[$params['recharge_type']];
        $params = ArrayHelper::merge($this->data, $params);
        $params['sign'] = $this->getsign($params);
        $url = $this->oil_url . '/bc/order/apply';
        $result = $this->urlpost($url, $params);
        return $result;
    }

    private function getsign($params)
    {
        return md5($params['business_id'] . $params['recharge_no'] . $params['amount'] . $params['user_order_no'] . $this->oil_key);
    }

    public function urlpost($url, $data)
    {



        try{
            $urlParam = http_build_query($data);


//            $res = $this->http->request('POST',$url, $data);

            $url = $url.'?'.$urlParam;
            $res = $this->http->request('get',$url, $data);
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }



        // $res = $this->http->request('POST', $url, $data);
        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($data), $return_data, 'bangchong', 0, 'bc');

        return $return;
    }


}