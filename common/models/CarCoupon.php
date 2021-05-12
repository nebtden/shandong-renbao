<?php

namespace common\models;

use common\components\Eddriving;
use Yii;
use common\components\AlxgBase;
use common\components\W;
use yii\helpers\Url;

class CarCoupon extends AlxgBase
{
    protected $currentTable = '{{%car_coupon}}';

//优惠券类型状态 0未激活，1已激活，2已使用，3已过期
    public static $coupon_status = [
        '0' => '未激活',
        '1' => '已激活',
        '2' => '已使用',
        '3' => '已过期',
        '4' => '卡异常'
    ];
//优惠券类型，0未知，1代驾券，2救援券，3积分券，4洗车券
    public static $coupon_type = [
        '1' => '代驾券',
        '2' => '道路救援券',
        '3' => '积分券',
        '4' => '洗车券',
        '5' => '油卡充值券',
        '7' => '年检券',
        '8' => '代步券',
        '9' => 'ETC兑换券',
        '10' => '臭氧杀菌券',
        '11' => '爱奇艺',
    ];

    //优惠券类型，0未知，1代驾券，2救援券，3积分券，4洗车券
    public static $coupon_new_type = [
        '1' => '代驾券',
        '2' => '道路救援券',
        '3' => '积分券',
        '4' => '洗车券',
        '5' => '油卡充值券',
        '6' => '在线洗车券',
        '7' => '年检券',
        '8' => '代步券',
        '9' => 'ETC兑换券',
        '10' => '臭氧杀菌券',
        '11' => '爱奇艺',
    ];

    //优惠券类型，0未知，1代驾券，2救援券，3积分券，4洗车券
    public static $coupon_company_info = [
        '1' => [
            'name'=>'代驾券',
            'company'=>[
                '0' => 'e代驾',
                '1' => '滴滴代驾'
            ],
            'showkey'=>'edaicar',
            'showtext'=>'代驾券详情（面额，数量）'

        ],
        '2' => [
            'name'=>'道路救援券',
            'company'=>[
                '1' => '中联救援',
                '2' => '九天救援'
            ],
            'showkey'=>'roadrescue',
            'showtext'=>'道路救援券详情（面额，数量，救援类型）'
        ],
        '3' => [
            'name'=>'积分券',
            'company'=>[],
            'showkey'=>'integral',
            'showtext'=>'积分券详情（面额，数量）'
        ],
        '4' => [
            'name'=>'洗车券',
            'company'=>[
                '0' => '途虎洗车',
                '1' => '典典洗车',
                '2' => '盛大洗车',
                '3' => '自营洗车'
            ],
            'showkey'=>'carwash',
            'showtext'=>'洗车券详情（面额，数量）',
            'is_mensal'=>[
                '0' => '否',
                '1' => '是'
            ]
        ],
        '5' => [
            'name'=>'油卡充值券',
            'company'=>[
                '1' => '车后加',
                '2' => '典典',
                '3' => '聚合',
                '4' => '帮充'
            ],

            'showkey'=>'oil',
            'showtext'=>'油卡充值券详情（面额，数量）'
        ],
        '6' => [
            'name'=>'在线洗车券',
            'company'=>[
                '0' => '途虎洗车',
                '1' => '典典洗车',
                '2' => '盛大洗车',
                '3' => '自营洗车'
            ],
            'showkey'=>'paycarwash',
            'showtext'=>'在线洗车券详情（面额，数量）'
        ],
        '7' => [
            'name'=>'年检券',
            'company'=>[
                '0' => '典典',
                '1' => '车行易'
            ],
            'showkey'=>'annualsurvey',
            'showtext'=>'年检券详情（面额，数量）'
        ],
        '8' => [
            'name'=>'代步券',
            'company'=>[
                '1' => '盛大'
            ],
            'showkey'=>'segway',
            'showtext'=>'代步券详情（面额，数量）'
        ],
        '9' => [
            'name'=>'ETC兑换券',
            'company'=>[
                '0' => '山东鲁卡通'
            ],
            'showkey'=>'etcinfo',
            'showtext'=>'ETC兑换券详情（面额，数量）'
        ],
        '10' => [
            'name'=>'臭氧杀菌券',
            'company'=>[
                '2' => '盛大'
            ],
            'showkey'=>'disinfection',
            'showtext'=>'臭氧杀菌兑换券详情（面额，数量）'
        ],
        '11' => [
            'name'=>'爱奇艺充值',
            'showkey'=>'disinfection',
            'showtext'=>'爱奇艺充值（面额，数量）'
        ],


    ];



    ///0 亏电救援 1 无油救援 2 换胎救援 3 拖车救援 4送水
    public static $coupon_faulttype = [
        '5' => '通用券',
        '0' => '亏电救援',
        '1' => '无油救援',
        '2' => '换胎救援',
        '3' => '拖车救援',
        '4' => '送水'

    ];
    public static $coupon_faulttype_web = [
        '5' => '通用券',
        '0' => '亏电救援',
        '1' => '无油救援',
        '2' => '换胎救援',
        '3' => '拖车救援',
        '4' => '送水',
    ];
    //供应商
    public static $coupon_company = [
        '1' => '中联汽车救援综合服务平台',
        '2' => '九天汽车救援服务平台'
    ];

    //油卡类型

    public static $oil_xxz = [
        '50' => '油券50面',
        '100' => '油券100面',
        '200' => '油券200面',
        '500' => '油券500面',
        '1000' => '油券1000面'
    ];

    //油卡供应商
    public static $oil_company = [
        '4' => '帮充',
        '3' => '聚合',
        '1' => '车后加',
        '2' => '典典',

    ];

    //洗车券供应商
    public static $wash_company = [
        '0' => '途虎洗车',
        '1' => '典典洗车',
        '2' => '盛大洗车',
        '3' => '自营洗车'
    ];


    //年检券供应商
    public static $ins_company = [
        '0' => '典典',
        '1' => '车行易'
    ];
    public static $driving_company = [
        '0' => 'e代驾',
        '1' => '滴滴代驾'
    ];

    //代驾券或代步券面值
    public static $coupon_amount = [
        1 => '1',
        5 => '5',
        10 => '10',
        15 => '15',
        20 => '20',
        30 => '30',
        50 => '50',
    ];

    //检查卡编号的唯一性
    public function checkCardNo($coupon_sn = '', $coupon_pwd = '', $id = 0)
    {
        if (!$coupon_sn) return false;
        $where = 'coupon_sn = "' . $coupon_sn . '"';
        if(!empty($coupon_pwd))$where .= ' or  coupon_pwd = "' . $coupon_pwd . '"';
        if ($id) $where .= ' and id <> ' . $id;
        $res = $this->table()->select('count(id) as cot')->where($where)->one();
        return $res['cot'];
    }
    public function generateCardNo($len = 8, $len2 = 8)
    {
        $data = [];
        $data['coupon_sn'] = W::createNoncestr($len);
        $data['coupon_pwd'] = W::createNoflow($len2);
        while ($this->checkCardNo($data['coupon_sn'], $data['coupon_pwd'])) {
            $data['coupon_sn'] = W::createNoncestr($len);
            $data['coupon_pwd'] = W::createNoflow($len2);
        }
        return $data;
    }
    public function generateCardNoxu($len = 8)
    {
        $coupon_sn = W::createNoncestr($len);
        while ($this->checkCardNo($coupon_sn)) {
            $coupon_sn= W::createNoncestr($len);
        }
        return $coupon_sn;
    }


    /**
     * 获得用户绑定的优惠券列表
     * 只显示未使用的
     * @param int $uid
     * @param int $is_web 是否是web端调用
     * @return mixed
     */
    public function get_user_bind_coupon_list($uid = 0, $type = null,$is_web=0)
    {
        $map = ['uid' => $uid];
        $map['status'] = [1,2,3];
        if ($type) $map['coupon_type'] = $type;
        $temp = $this->table()->where($map)->orderBy('`status` asc,coupon_type asc,use_limit_time asc')->all();
        $list = [];
        $lose_expire = [];//已效但并未改成效的卡
        $now = time();
        foreach ($temp as $val) {
            $val = $this->renderCouponList1($val,$is_web);
            if ($val['status'] == 3) {
                $lose_expire[] = $val;
            } else {
                $list[] = $val;
            }
        }
        $list = array_merge($list, $lose_expire);

        return $list;
    }

    public function renderCouponList($coupon)
    {
        $info = [
            'show_coupon_name' => '',
            'show_coupon_desc' => '',
            'show_coupon_type_text' => '',
            'show_coupon_endtime' => '',
            'show_coupon_usetime' => '',
            'show_coupon_short' => '立减',
            'show_coupon_style' => ''
        ];
        $info['show_coupon_endtime'] = date("Y-m-d", $coupon['use_limit_time']);
        $info['show_coupon_usetime'] = $coupon['use_time'] ? date("Y-m-d H:i:s", $coupon['use_time']):'';
        switch ($coupon['coupon_type']) {
            case 1:
                $info['show_coupon_type_text'] = '代驾券';
                $info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>公里';
                $info['show_coupon_desc'] = '仅用于抵扣使用代驾服务时所产生的费用';
                $info['show_coupon_style'] = 'daijia-wrapper';
                break;
            case 2:
                if ($coupon['amount'] < 0) {
                    $info['show_coupon_name'] = '不限次';
                } else {
                    $info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>次';
                }
                $info['show_coupon_type_text'] = '救援券';
                //$info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>次';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于抵扣呼叫道路救援时所产生的费用';
                $info['show_coupon_style'] = 'rescue-wrapper';
                break;
            case 3:
                $info['show_coupon_type_text'] = '积分券';
                $info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>积分';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于兑换平台各类代金券或优惠券';
                $info['show_coupon_style'] = 'testing-wrapper';
                break;
            case 4:
                $info['show_coupon_type_text'] = '洗车券';
                $info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>次';
                $info['show_coupon_desc'] = '仅用于抵扣使用洗车服务时所产生的费用';
                $info['show_coupon_style'] = 'washCar-wrapper';
                $info['show_coupon_short'] = '全额';
                break;
            case 5:
                $info['show_coupon_type_text'] = '加油券';
                $info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>元';
                $info['show_coupon_desc'] = '仅用于对加油卡充值时抵扣相应的费用';
                $info['show_coupon_style'] = 'testing-wrapper';
                $info['show_coupon_short'] = '立减';
                break;
            case 10:
                $info['show_coupon_type_text'] = '臭氧杀菌券';
                $info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>次';
                $info['show_coupon_desc'] = '仅用于抵扣使用臭氧杀菌服务时所产生的费用';
                $info['show_coupon_style'] = 'washCar-wrapper';
                $info['show_coupon_short'] = '全额';
                break;
        }
        $coupon = array_merge($coupon, $info);
        return $coupon;
    }

    /**
     * @param $coupon
     * @param $is_web
     * @return array
     */
    public function renderCouponList1($coupon, $is_web)
    {
        $info = [
            'show_coupon_name' => '',
            'show_coupon_desc' => '',
            'show_coupon_type_text' => '',
            'show_coupon_endtime' => '',
            'show_coupon_usetime' => '',
            'show_coupon_short' => '立减',
            'show_coupon_style' => '',
            'show_coupon_url' => ''
        ];

        if ($coupon['status'] == 1) {
            if ($coupon['amount'] < 0) {
                //不限次数
            } else {
                if (!((int)$coupon['amount'] > (int)$coupon['used_num'])) {
                    $coupon['status'] = 2;
                } elseif ($coupon['use_limit_time'] < time()) {
                    $coupon['status'] = 3;
                }
            }
        }

        $info['show_coupon_endtime'] = date("Y-m-d", $coupon['use_limit_time']);
        $info['show_coupon_usetime'] = $coupon['use_time'] ? date("Y-m-d H:i:s", $coupon['use_time']):'';
        switch ($coupon['coupon_type']) {
            case 1:
                if ($coupon['status'] == 1 || $coupon['status'] == 2) {
                    if($coupon['company']==0 ){
                        $url = 'http://'.$_SERVER['HTTP_HOST'].'/frontend/web/carecarnew/index.html?coupon_id='.$coupon['id'];
                    }elseif($coupon['company']==1){
                        $order = (new CarSubstituteDriving())->table()->select('url,status')->where(['coupon_id'=>$coupon['id']])->one();
                        if($coupon['status'] == 2 && $order['status'] == 0){
                            $info['show_button'] = 'show';
                            $url = $order['url'];
                        }else{
                            $url = 'http://'.$_SERVER['HTTP_HOST'].'/frontend/web/didi/index.html?coupon_id='.$coupon['id'];
                        }
                    }
                }
                $info['show_coupon_type_text'] = '代驾券';
                $info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . floatval($coupon['amount']) . '</em>公里</span><div class="link-callDrive-wrapper"><a href="javascript:;" data-url="' . $url . '">呼叫代驾<i class="iconfont icon-car-jiantou2"></i></a></div></div>';
                $info['show_coupon_desc'] = '仅用于抵扣使用代驾服务时所产生的费用';
                $info['show_coupon_style'] = 'daijia';
                $info['show_coupon_url'] = $url;
                $info['company'] = $coupon['company'];

                break;
            case 2:
                if ($coupon['amount'] < 0) {
                    $info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . '不限' . '</em>次</span></div>';
                } else {
                    $info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . floatval($coupon['amount'] - $coupon['used_num']) . '</em>次</span></div>';
                }
                $info['show_coupon_type_text'] = '救援券';
                //$info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . floatval($coupon['amount'] - $coupon['used_num']) . '</em>次</span></div>';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于抵扣呼叫道路救援时所产生的费用';
                $info['show_coupon_style'] = 'rescue';
                $info['show_coupon_url'] =  Url::to(['carrescue/index']);
                break;
            case 3:
                $info['show_coupon_type_text'] = '积分券';
                $info['show_coupon_name'] = '<em>' . floatval($coupon['amount']) . '</em>积分';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于兑换平台各类代金券或优惠券';
                $info['show_coupon_style'] = 'testing';
                break;
            case 4:
                $w_sn = '';
                $w_stauts = '';
                $all = 0;
                $desc = '仅用于抵扣使用洗车服务时所产生的费用';
                if ($w_sn) $coupon['coupon_sn'] = $w_sn;
                //在未失效的情况下
                if($coupon['status'] == COUPON_ACTIVE){
                    switch($coupon['company']) {
                        case 0: //途虎洗车
                            $info['show_coupon_url'] = 'https://wx.tuhu.cn/vue/wx/pages/kawash/shoplist?productid=FU-MD-DKHBZXC&variantid=1&hide=1&np=1';
                            $wash_info = (new Car_wash_coupon())->get_service_sn($coupon['uid'], $coupon['id']);
                            $text = '当月洗车卡源卡未上传，请联系客服';
                            break;
                        case 1: //典典洗车
                            $info['show_coupon_url'] = Url::to(['carwash/shoplist', 'couponId' => $coupon['id'],'company'=> 1]);
                            $wash_info = (new WashOrder())->getConsCode($coupon['uid'], $coupon['id'],1);
                            $wash_info['sn'] = $wash_info['consumerCode'];
                            $text= '';
                            break;
                        case 2: //盛大洗车
                            $info['show_coupon_url'] = Url::to(['carwash/shoplist', 'couponId' => $coupon['id'],'company'=>2]);
                            $wash_info = (new WashOrder())->getconsCode($coupon['uid'], $coupon['id'],2);
                            $wash_info['sn'] = $coupon['coupon_sn'];
                            $text= '';
                            break;
                        case 3: //自营洗车
                            $info['show_coupon_url'] = Url::to(['carwash/shoplist', 'couponId' => $coupon['id'],'company'=>3]);
                            $wash_info = (new WashOrder())->getconsCode($coupon['uid'], $coupon['id'],3);
                            $wash_info['sn'] = $wash_info['consumerCode'];
                            $text= '';
                            break;
                    }
                    if(is_array($wash_info)) {
                        $w_sn = $wash_info['sn'];
                        $w_stauts = $wash_info['status'];
                    }else {
                        $w_sn = $text;
                    }
                    $num = ($w_stauts > 1) ? 0 : 1;
                    list($today['y'], $today['m']) = explode('-', date("Y-m", time()));
                    list($active['y'], $active['m']) = explode('-', date('Y-m', $coupon['active_time']));
                    $e_num = ($today['y'] - $active['y']) * 12 + $today['m'] - $active['m'];
                    if (!$num) $e_num++;
                    $all = floatval($coupon['amount'] - $e_num);
                    //是否限制每个月必须使用一次
                    if($coupon['is_mensal'] == 0){
                        $num = $coupon['amount'] - $coupon['used_num'];
                        $all = $num;
                    }
                    $desc = "剩余免费洗车<i>{$num}</i>次</span>";
                    if($coupon['is_mensal'] == 1)$desc = "本月剩余免费洗车<i>{$num}</i>次&nbsp;&nbsp;&nbsp;共计剩余<i>{$all}</i>次</span>";
                }
                $info['show_coupon_type_text'] = '洗车券';
                $info['show_coupon_company'] = self::$wash_company[$coupon['company']];
                $info['show_coupon_left'] = $all;
                $info['coupon_sn'] = $w_sn;
                $info['w_status'] = $w_stauts;
                $info['servicecode'] = $w_sn;
                $info['show_coupon_all'] = $all;
                $info['show_coupon_desc'] = $desc;
                $info['show_coupon_style'] = 'washCar';
                $info['show_coupon_short'] = '全额';
                break;
            case 5:
                $info['show_coupon_type_text'] = '加油券';
                $hasuse = '';
                if($coupon['status'] < 2){
                    $url =Url::to(['caroil/index']);
                    $hasuse='<i class="iconfont icon-car-jiantou2" onclick="location=\'' . $url . '\'"></i>';
                    $info['show_coupon_url'] = $url;
                }
                $info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . floatval($coupon['amount']) . '</em>元</span>'.$hasuse.'</div>';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于对加油卡充值时所产生的消费抵扣';
                $info['show_coupon_style'] = 'jiayou';
                break;
            case 11:
                $info['show_coupon_type_text'] = '爱奇艺';
                $hasuse = '';
                if($coupon['status'] < 2){
                    $url =Url::to(['aiqiyi/index','coupon_id'=>$coupon['id']]);
                    $info['show_coupon_url'] = $url;
                }
                $info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . floatval($coupon['amount']) . '</em>元</span>'.$hasuse.'</div>';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '可用于爱奇艺账号续费';
                $info['show_coupon_style'] = 'aiqiyi';
                break;
            case INSPECTION:
                $info['show_coupon_type_text'] = '年检券';
                $hasuse = '';
                if($coupon['status'] < 2){
                    $session = Yii::$app->session;
                    if ($session->has('carId') && $session->get('carId')) {
                        if($coupon['company'] == COMPANY_CHEXINGYI){
                            $url = Url::to(['cyxinspection/checktype','checkDate'=>$session->get('checkDate'), 'carId' => $session->get('carId'),'couponid'=>$coupon['id']]);
                        }else{
                            $url = Url::to(['inspection/checktype', 'carId' => $session->get('carId'),'couponid'=>$coupon['id']]);
                        }
                    } else {
                        if($coupon['company'] == COMPANY_CHEXINGYI){
                            $url = Url::to(['cyxinspection/index','couponid'=>$coupon['id']]);
                        }else{
                            $url = Url::to(['inspection/index','couponid'=>$coupon['id']]);
                        }
                    }
                    $hasuse = '<i class="iconfont icon-car-jiantou2" onclick="location=\'' . $url . '\'"></i>';
                    $info['show_coupon_url'] = $url;
                }
                $info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . floatval($coupon['amount']) . '</em>元</span>'.$hasuse.'</div>';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于对年检时所产生的消费抵扣';
                $info['show_coupon_style'] = 'jiayou';
                break;
            case SEGWAY:
                $info['show_coupon_type_text'] = '代步券';
                $hasuse = '';
                if($coupon['status'] < 2){
                    $url = Url::to(['segway/index','couponid'=>$coupon['id']]);
                    $hasuse = '<i class="iconfont icon-car-jiantou2" onclick="location=\'' . $url . '\'"></i>';
                    $info['show_coupon_url'] = $url;
                }
                $info['show_coupon_name'] = '<div class="number-wrapper"><span><em>' . floatval($coupon['amount']) . '</em>次</span>'.$hasuse.'</div>';
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于抵扣使用代步车服务时所产生的费用';
                $info['show_coupon_style'] = 'daibu';
                break;
            case 9:
                $info['show_coupon_type_text'] = 'ETC券';
                $info['show_coupon_url'] = Url::to(['sdetc/index', 'couponId' => $coupon['id']]);
                $info['show_coupon_short'] = '全额';
                $info['show_coupon_desc'] = '仅用于兑换平台ETC';
                $info['show_coupon_style'] = 'etc';
            case 10:
                $w_sn = '';
                $w_stauts = '';
                $all = 0;
                $desc = '仅用于抵扣使用臭氧消毒服务时所产生的费用';
                if ($w_sn) $coupon['coupon_sn'] = $w_sn;
                //在未失效的情况下

                if($coupon['status'] == COUPON_ACTIVE){
                    $info['show_coupon_url'] = Url::to(['car-disinfect/shoplist', 'couponId' => $coupon['id'],'company'=>2]);
                    $wash_info = (new WashOrder())->getconsCode($coupon['uid'], $coupon['id'],2);
                    $wash_info['sn'] = $wash_info['consumerCode'];
                    $text= '';
                    if(is_array($wash_info)) {
                        $w_sn = $wash_info['sn'];
                        $w_stauts = $wash_info['status'];
                    }else {
                        $w_sn = $text;
                    }
                    $num = ($w_stauts > 1) ? 0 : 1;
                    list($today['y'], $today['m']) = explode('-', date("Y-m", time()));
                    list($active['y'], $active['m']) = explode('-', date('Y-m', $coupon['active_time']));
                    $e_num = ($today['y'] - $active['y']) * 12 + $today['m'] - $active['m'];
                    if (!$num) $e_num++;
                    $all = floatval($coupon['amount'] - $e_num);
                    //是否限制每个月必须使用一次
                    if($coupon['is_mensal'] == 0){
                        $num = $coupon['amount'] - $coupon['used_num'];
                        $all = $num;
                    }
                    $desc = "剩余免费消毒<i>{$num}</i>次</span>";
                    if($coupon['is_mensal'] == 1)$desc = "本月剩余免费消毒<i>{$num}</i>次&nbsp;&nbsp;&nbsp;共计剩余<i>{$all}</i>次</span>";
                }
                $info['show_coupon_type_text'] = '洗车券';
                $info['show_coupon_company'] = self::$wash_company[$coupon['company']];
                $info['show_coupon_left'] = $all;
                $info['coupon_sn'] = $w_sn;
                $info['w_status'] = $w_stauts;
                $info['servicecode'] = $w_sn;
                $info['show_coupon_all'] = $all;
                $info['show_coupon_desc'] = $desc;
                $info['show_coupon_style'] = 'etc';
                $info['show_coupon_short'] = '全额';
                break;
        }
        $coupon = array_merge($coupon, $info);
        return $coupon;
    }

    /**
     * 检查券是否过期
     */
    public function update_status_auto_ecoupon()
    {
        //检查频率，每小时检查一次
        $now = time();
        $condition = "`use_limit_time` <> 0 AND `status` = 1 AND `use_limit_time` < $now";
        return $this->myUpdate(['status' => 3], $condition);

    }

    /**
     * 检查救援券是否过期
     */
    public function update_status_auto_savecoupon()
    {
        //检查频率，每小时检查一次
        $now = time();
        $condition = "`coupon_type` = 2 AND `status` = 1 AND `use_limit_time` < $now";
        return $this->myUpdate(['status' => 3], $condition);
    }



    /**
     * 检查洗车券是否过期
     */
    public function update_status_auto_washcoupon()
    {
        //检查频率，每月第一天检查
        $now = time();
        $condition = "`coupon_type` = 4 AND `status` = 1 AND `use_limit_time` < $now";
        return $this->myUpdate(['status' => 3], $condition);
    }

    /**
     * 获得用户的代驾券列表
     * @param int $uid
     * @param null|int $status
     * @return array
     */
    public function get_user_ecar_coupon($uid,$status = null,$company_id=0){
        $map['uid'] = $uid;
        $map['coupon_type'] = 1;
        // $map['companyid'] = $company_id;
        if($status !== null){
            $map['status'] = $status;
        }

        $list = [];
        $list = $this->table()->where($map)->orderBy('use_limit_time asc')->all();
        return $list;
    }

    public function getCouponById($id,$field='*')
    {
        return $this->table()->select($field)->where(['id' => $id])->one();
    }


    /**
     * 获取洗车券信息
     * @param $id
     * @param $uid
     * @return mixed
     */
    public function getDianDianCouponWash($id, $uid)
    {
        $res = $this->table()->where(['id' => $id,'uid' => $uid])->one();
        if(!$res){
            return false;
        }
        $coupon['couponId'] = $res['id'];
        $coupon['name'] = $res['name'];
        $coupon['amount'] = $res['amount'];
        $coupon['used_num'] = $res['used_num'];
        $coupon['coupon_sn'] = $res['coupon_sn'];
        $coupon['status'] = $res['status'];
        $coupon['companyid'] = $res['companyid'];
        $coupon['company'] = $res['company'];
        $coupon['active_time'] = date('Y-m-d', $res['active_time']);
        $coupon['use_limit_time'] = date('Y-m-d', $res['use_limit_time']);
        $coupon['is_mensal'] = $res['is_mensal'];

        return $coupon;
    }


    public function getDrivingCompanyList($uid){

        $now = time();
        $condition = "`coupon_type` = 1 AND `status` = 1 AND `use_limit_time` > $now and `uid` =$uid";

        $list = $this->table()->select('company')->where($condition)->groupBy('company')->all();
        return $list;
    }
}