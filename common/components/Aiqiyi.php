<?php

namespace common\components;

use GuzzleHttp\Client;

class Aiqiyi
{
    public static $types = [
//        1 => ['name' => '鼎翰雀省_黄金月卡',
//            'product_id' => 'DHQS_HJYK',
//            'old_name' => '黄金月卡',
//            'price'=>18.75,
//        ],
        1 => ['name' => '鼎翰雀省_黄金月卡',
            'product_id' => '111',
            'old_name' => '黄金月卡',
            'price'=>18.75,
        ],


        2 => ['name' => '鼎翰雀省_黄金季卡',
            'product_id' => 'DHQS_HJJK',
            'old_name' => '爱奇艺黄金会员季卡',
            'price'=>51,],


        3 => ['name' => '鼎翰雀省_黄金半年卡',
            'product_id' => 'DHQS_HJBNK',
            'old_name' => '黄金半年卡',
            'price'=>97.5,],


        4 => ['name' => '鼎翰雀省_黄金年卡',
            'product_id' => 'DHQS_HJNK',
            'old_name' => '黄金年卡',
            'price'=>186,],


        5 => ['name' => '鼎翰雀省_星钻月卡',
            'product_id' => 'DHQS_XZYK',
            'old_name' => '星钻月卡',
            'price'=>27,],


        6 => ['name' => '鼎翰雀省_星钻季卡',
            'product_id' => 'DHQS_XZJK',
            'old_name' => '星钻季卡',
            'price'=>75.6,],


        7 => ['name' => '鼎翰雀省_星钻半年卡',
            'product_id' => 'DHQS_XZBNK',
            'old_name' => '爱奇艺星钻会员半年卡',
            'price'=>147.6,],


        8 => ['name' => '鼎翰雀省_星钻年卡',
            'product_id' => 'DHQS_XZNK',
            'old_name' => '星钻年卡',
            'price'=>278.1,],


        9 => ['name' => '鼎翰雀省_黄金一天卡',
            'product_id' => 'DHQS_HJYTK',
            'old_name' => '爱奇艺黄金会员1天卡',
            'price'=>3.6,],


        10 => ['name' => '鼎翰雀省_黄金七天卡',
            'product_id' => 'DHQS_HJQTK',
            'old_name' => '爱奇艺黄金会员7天卡',
            'price'=>5.85,],


        11 => ['name' => '鼎翰雀省_奇异果黄金月卡',
            'product_id' => 'DHQS_QYGHJYK',
            'old_name' => '奇异果VIP月卡',
            'price'=>22.41,],


        12 => ['name' => '鼎翰雀省_奇异果黄金季卡',
            'product_id' => 'DHQS_QYGHJJK',
            'old_name' => '奇异果VIP季卡',
            'price'=>66.6,],


        13 => ['name' => '鼎翰雀省_奇异果黄金半年卡',
            'product_id' => 'DHQS_QYGHJBNK',
            'old_name' => '奇异果黄金会员半年卡',
            'price'=>120.6,],


        14 => ['name' => '鼎翰雀省_奇异果黄金年卡',
            'product_id' => 'DHQS_QYGHJNK',
            'old_name' => '奇异果黄金会员年卡',
            'price'=>224.1,],


        15 => ['name' => '鼎翰雀省_奇异果星钻月卡',
            'product_id' => 'DHQS_QYGXZYK',
            'old_name' => '奇异果星钻会员月卡',
            'price'=>27,],


        16 => ['name' => '鼎翰雀省_奇异果星钻季卡',
            'product_id' => 'DHQS_QYGXZJK',
            'old_name' => '奇异果星钻会员季卡',
            'price'=>75.6,],

        17 => ['name' => '鼎翰雀省_奇异果星钻半年卡',
            'product_id' => 'DHQS_QYGXZBNK',
            'old_name' => '奇异果星钻会员半年卡',
            'price'=>147.6,],

        18 => ['name' => '鼎翰雀省_奇异果星钻年卡',
            'product_id' => 'DHQS_QYGXZNK',
            'old_name' => '奇异果星钻会员年卡',
            'price'=>278.1,
            ],

    ];

    public function __construct()
    {


    }


    public function make_sign($data,$key)
    {
        ksort($data);
        $str = http_build_query( $data);
        $str .= $key;
        $sign = md5($str);

        return $sign;
    }

    public function encrypt($input)
    {

    }

    public function decrypt($input)
    {

    }

    public function sendOrder($product_id,$account,$order_no,$amount){
        $aiqiyi_params = \Yii::$app->params['aiqiyi'];
        $url = $aiqiyi_params['url'];
        $data = [];

        $aiqiyi = Aiqiyi::$types[$product_id];

        $data['partnerNo'] = $aiqiyi_params['partnerNo'];
        $data['timestamp'] = time();
        $data['orderNo'] = $order_no;
        $data['mobile'] = $account;
        $data['amount'] = $amount;
        $data['sum'] = $aiqiyi['price']*$amount*100;
        $data['item'] = $aiqiyi['product_id'];
        $data['sign'] = $this->make_sign($data,$aiqiyi_params['key']);


        $client = new Client();
        $result = $client->request('POST', $url,  ['form_params' =>$data]);
        $return = $result->getBody()->getContents();

        //写入日志
        (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($data), $return, 'aiqiyi', $return['code'], 'aiqiyi');

        return \GuzzleHttp\json_decode($return,true);
    }

}