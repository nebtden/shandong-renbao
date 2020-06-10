<?php

/**
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/3
 * Time: 10:16
 *
 */

namespace frontend\controllers;

use common\components\AlxgBase;
use common\components\BaiduMap;
use common\components\CarCouponAction;
use common\components\DianDian;
use common\components\DianDianWash;
use common\components\Helpper;
use common\components\ShengDaCarNewApi;
use common\models\Car_coupon_explain;
use common\models\Car_tuhunotice;
use common\models\Car_wash_order_taibao;
use common\models\Car_washarea;
use common\models\CarCoupon;
use common\models\CarCouponMeal;
use common\models\CarCouponPackage;
use common\models\CarMobile;
use common\models\FansAccount;
use common\models\Wash_area;
use common\models\CarWashArea;
use common\models\Wash_shop;
use common\models\WashOrder;
use common\models\WashShop;
use common\models\WashShopService;
use common\components\W;
use common\components\ShengDaCarWash;
use common\components\NationalLife;
use common\models\Car_paternalor;
use common\models\CarDisinfectionOrder;
use yii\helpers\Url;
use Yii;
use yii\db\Exception;
use yii\web\Response;

class CarDisinfectController extends CloudcarController
{

    protected $uid;
    public $menuActive = 'caruser';
    public $layout = "cloudcarv2";
    public $site_title = '臭氧消毒服务';
    protected $status = 1;
    protected $msg = 'ok';

    public function beforeAction($action = null)
    {
        $this->uid = Yii::$app->session['wx_user_auth']['uid'];
        return parent::beforeAction($action);
    }

    //清理session
    public function actionCleantoken()
    {
        Yii::$app->session['openid'] = null;
        Yii::$app->session['token'] =null;
        Yii::$app->session['wx_user_auth'] = null;
        Yii::$app->session['xxz_mobile'] = null;


        return $this->redirect(Url::to(['carhome/index']));
    }

    /**
     * 检测臭氧消毒限制条件，每天用户只能使用一次洗车服务
     * @return bool|Response
     */
    public function actionChecklimit()
    {
        $res = $this->washLimit();
        return $this->json($res['status'],$res['msg'],'',$res['url']);
    }

    /**
     * 洗车限制条件
     * @return array
     */
    protected function washLimit($couponId=0)
    {
        $data = [
            'status' => 1,
            'msg' => ''
        ];
        $now = time();
        //禁用账号
        $jinyong=(new FansAccount())->select('status',['status' => 0 , 'uid'=>$this->uid])->one();
        if($jinyong){
            $data['status'] = 0;
            $data['msg'] = '鉴于之前的违规操作，此账号不可核销卡券';
            return $data;
        }
        //当天的时间范围
        $startDay = strtotime(date('Y-m-d 00:00:00',$now));
        $endDay = strtotime(date('Y-m-d 23:59:59',$now));


        //查询用户当天范围内有没有使用卡券
        $order = (new CarDisinfectionOrder())->table()->where(['uid'=>$this->uid])->andWhere(['between','s_time',$startDay,$endDay])->one();
        if($order){
            if($order['status']== 1){
                $data['url'] = Url::to(['car-disinfect/shoplist','couponId'=>$order['couponId'],'company'=>$order['company_id']]);
            }elseif($order['status']==2) {
                $data['status'] = 0;
                $data['msg'] = '每天只能使用进行一次服务！';
            }

        }
        if($couponId != 0 && $order['status'] == 1 && $order['couponId'] != $couponId){
            $data['status'] = 0;
            $data['msg'] = '当前还有进行中的订单，请完成后再试！';
        }

        return $data;
    }

    /**
     * 显示门店列表
     * @param $company 2:盛大
     * @return string
     */
    public function actionShoplist()
    {

        $request = Yii::$app->request;
        $couponId = $request->get('couponId');
        $company = $request->get('company');
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        $areaObj = new CarWashArea();
        $province = $areaObj->getProvince();
        $is_weixin = $this->is_weixin();
        return $this->render('shoplist',compact('is_weixin','couponId','company','alxg_sign','province'));
    }
    /**
     * 显示门店列表
     * @param $company 默认盛大
     * @return string
     */
    public function actionShoplistnew()
    {
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        $areaObj = new Car_washarea();
        $province = $areaObj->getProvince();
        $is_weixin = $this->is_weixin();
        return $this->render('shoplistnew',compact('is_weixin','alxg_sign','province'));
    }
    /**
     * 根据经纬度获取门店数据
     * @return array|string
     */
    public function actionGetshoplist()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        if($request->isPost){
            $post = $request->post();
            $now = time();
            Yii::$app->session['wash_point'] = [
                'lat' => $post['lat'],
                'lng' => $post['lng']
            ];

            $data = $session['disinfect_sd_shop_list'];
            if($data['limit_time']<$now){
                $data =  $this->shengDaShopList($post);
            }
            return $this->json($this->status,$this->msg,$data);
        }
        return '非法请求';
    }

    /**
     * 根据地理位置获取门店数据
     * @return array|string
     */
    public function actionGetareashoplist()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $post = $request->post();
            $data =  $this->shengDaShopList($post);
            return $this->json($this->status,$this->msg,$data);
        }
        return '非法请求';
    }
    /**
     * 盛大平台门店数据
     * @param $post
     * @return array
     */
    public function shengDaShopList($post)
    {
        $data = [];
        //盛大请求数据
        $postData = [
            'sourceCode' => Yii::$app->params['shengda_sourceApp_new'],
            'serviceId' => '4102',
            'pageNo' => '1',
            'pageSize' => '8'
        ];

        try {
            //经纬度查询门店
            if($post['lng'] && $post['lat']){
                $postData['longitude'] = $post['lng'];
                $postData['latitude'] = $post['lat'];
                //经纬度存入session,用于计算门店距离

                //通过百度地图api将经纬度转换省市区
                $address = BaiduMap::geocoder($post['lat'],$post['lng']);
                if(!$address){
                    throw new \Exception('经纬度转换地理位置失败');
                }
                (new DianDian())->requestlog('getshoplist.html', json_encode($address), '', 'Shengda', 0, 'Shengda');
                $location = (new CarWashArea())->getLocation($address);
            }else {
                //地理位置查询门店
                $postData['cityNumber'] = $post['city_id'];
                $postData['areaNumber'] = $post['area_id'];
                $address = [
                    'province' => $post['province'],
                    'city' => $post['city'],
                    'district' => $post['area'],
                ];
                //获取省市区
                $location = (new CarWashArea())->getLocation($address);
            }


            $shengDa = new ShengDaCarNewApi();
            $result = $shengDa->merchantDistanceList($postData);

            if(!$result){
                throw new \Exception('服务器连接失败');
            }

            // if($post['lng']=='112.93134'){
            // return '非法请求4444';
            // }


            $res=$result['data'];
            $resultarr = json_decode( $res,true);
            $resultJson = $shengDa->decrypt($resultarr['encryptJsonStr']);
            $resultdata = explode('|',$resultJson);
            $shoparr = json_decode( $resultdata[0],true);
            $shopList = $shoparr['coEnterprises'];
            if(empty($shopList)){
                throw new \Exception('没有门店数据');
            }
            $newData = [];
            $now = time();
            $wash_point = Yii::$app->session['wash_point'];

            foreach($shopList as $val){

                $coordinate=explode(',',$val['coordinate']);
                $point = $this->changePoint($coordinate[0],$coordinate[1]);
                if($wash_point){
                    $val['distance'] = BaiduMap::getDistance($wash_point['lat'], $wash_point['lng'], $point['lat'], $point['lng']);
                }
                $newData[] = [
                    'shopName' => $val['shopName'],
                    'shopId' =>$val['shopId'],
                    'prov' =>$val['proName'] ,
                    'city' =>$val['cityName'] ,
                    'district' =>$val['areaName'] ,
                    'shopLng' => $point['lng'],
                    'shopLat' => $point['lat'],
                    'distance' => round($val['distance'],2),
                    'shopAvator' => $val['logoPath'],
                    'images' => $val['logoPath'],
                    'shopTel' => $val['telephone'],
                    'shopAddress' => $val['address'],
                    'serviceStartTime' => $val['openTime'],
                    'serviceEndTime' => $val['restTime'],
                    'score' => 4,
                    'c_time' => $now,
                    'u_time' => $now,
                    'company' => 2,
                ];
            }
            $last_names = array_column($newData,'distance');
            array_multisort($last_names,SORT_ASC,$newData);
            //将数据存入session
            $data = [
                'shopList' => $newData,
                'location' => $location,
                'limit_time' => time()+300,
                'wash_point'=>$wash_point
            ];
            Yii::$app->session['disinfect_sd_shop_list'] = $data;


        } catch (\Exception $e){
            $this->status = 0;
            $this->msg = $e->getMessage();
        }
        return $data;
    }

    /**
     * 门店详情
     * @return string
     */
    public function actionShopdetail()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $couponId = $request->get('couponId',null);
        $shopId = $request->get('shopId',null);
        $company = $request->get('company',null);

        $shopObj = new WashShop();
        $shopDetail = [];


        $shopInfo = $session['disinfect_sd_shop_list'];
        foreach ($shopInfo['shopList'] as $val){
            if($val['shopId'] == $shopId){
                $shopDetail = $val;
                break;
            }
        }
        //如果没有门店图片就显示默认图片
        if(empty($shopDetail['images'])){
            $shopDetail['images'] = '/frontend/web/images/qiche.jpg';
        }
        //插入或者更新门店
        $shopObj->insertOrUpdate($shopDetail,'shopId',$shopDetail['shopId']);

        $url = WashOrder::$company[$company];
        $coupon = (new CarCoupon())->table()->where(['id'=>$couponId,'uid'=>$this->uid])->one();
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);

        return $this->render('shopdetail',compact('shopDetail','couponId','alxg_sign','url','coupon'));
    }

    /**
     * 门店详情
     * @return string
     */
    public function actionShopdetailnew()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $shopId = $request->get('shopId',null);
        $company = $request->get('company',null);

        $shopObj = new WashShop();
        $shopDetail = [];
        $shopInfo = $session['disinfect_sd_shop_list'];
        foreach ($shopInfo['shopList'] as $val){
            if($val['shopId'] == $shopId){
                $shopDetail = $val;
                break;
            }
        }
        //插入或者更新门店
        $shopObj->insertOrUpdate($shopDetail,'shopName',$shopDetail['shopName']);
        //如果没有门店图片就显示默认图片
        if(empty($shopDetail['images'])){
            $shopDetail['images'] = '/frontend/web/images/qiche.jpg';
        }
        $url = WashOrder::$company[$company];
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        return $this->render('shopdetailnew',compact('shopDetail','alxg_sign','url'));
    }

    /**
     * 盛大消毒服务码
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionShengdagetcode()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $shopId = trim($request->post('shopId',null));
            $couponId = trim($request->post('couponId',null));
            $status = 1;
            $msg = '';
            try {
                if(!is_numeric($shopId) || !is_numeric($couponId)){
                    throw new \Exception('优惠券id错误');
                }
                //查询卡券
                $coupon = (new CarCoupon())->table()->where([
                    'id' => $couponId,
                    'uid' => $this->uid,
                    'company' => 2 //盛大
                ])->one();

                if(!$coupon){
                    throw new \Exception('优惠券编码不存在');
                }
                $obj = new CarDisinfectionOrder();
                //检测客户限制条件
                $check = $this->washLimit($couponId);
                if($check['status'] == 0 ){
                    throw new \Exception($check['msg']);
                }

                $orderInfo = $obj->table()->where([
                    'uid' => $this->uid,
                    'coupon_id' => $couponId,
                    'company_id' => 2,
                    'date_month' => date('Ym')
                ])->limit(1)->orderBy('id desc')->one();

                if(!$orderInfo || $orderInfo['status'] == -1){
                    $res = $this->shengDaPlayOrder($coupon,$shopId);
                    $orderInfo = $res['data'];
                    if($res['success'] == false){
                        throw new \Exception($res['msg']);
                    }
                }
                //如果当月已经使用过卡券
                if($orderInfo['status'] == ORDER_SUCCESS){
                    //当月限制使用1次返回信息，否则继续下单
                    if($coupon['is_mensal'] == 1){
                        throw new \Exception('臭氧杀菌券每月限制使用1次');
                    }else {
                        $res = $this->shengDaPlayOrder($coupon,$shopId);
                        $orderInfo = $res['data'];
                        if($res['success'] == false){
                            throw new \Exception($res['msg']);
                        }
                    }
                }
                if($orderInfo['shop_id'] != $shopId || empty($orderInfo['shop_name'])){
                    $shop = (new WashShop())->getShop($shopId);
                    $orderInfo['shop_id'] = $shopId;
                    $orderInfo['shop_name'] = $shop['shopName'];
                    $obj->myUpdate($orderInfo);
                }
                $cons = [
                    'consumerCode' => $orderInfo['consumer_code'],
                    'expiredTime' => date('Y-m-d', $orderInfo['expired_time'])
                ];

            }catch (\Exception $e){
                $status = 0;
                $msg = $e->getMessage();
                $cons = [];
            }

            return $this->json($status,$msg,$cons);
        }


        return $this->redirect(['carhome/index']);


    }

    /**
     * 盛大消毒下单
     * @param $data
     * @return array
     * @throws \yii\db\Exception
     */
    public function shengDaPlayOrder($coupon,$shopId)
    {
        $user = $this->fans_account();
        $shopDetail = (new WashShop())->table()->select()->where(['shopId' => $shopId])->one();
        $trans = Yii::$app->db->beginTransaction();
        try {
            $now =time();
            if($coupon['is_mensal'] == 1){
                list($today['y'],$today['m']) = explode('-',date('Y-m',$now));
                list($activeDay['y'],$activeDay['m']) = explode('-',date('Y-m',$coupon['active_time']));
                $num = ($today['y']-$activeDay['y'])*12 + $today['m'] - $activeDay['m'];
            }else{
                $num = $coupon['used_num'];
            }
            $num = $coupon['amount'] - $num;
            if($num <= 0){
                throw new \Exception('优惠劵共'.$coupon['amount'].'次，剩余0次！');
            }

            //写入主订单
            $mainOrder = $this->main_order($this->uid, $coupon['id'], $coupon['amount']);
            if($mainOrder == false){
                throw new \Exception('主订单写入失败');
            }
            //使用卡券
            $couponObj = new CarCouponAction($user);
            $useCoupon = $couponObj->useCoupon($coupon['id']);
            if($useCoupon == false){
                throw new \Exception('卡券核销失败');
            }
            //通过盛大接口下单

            $sourceApp = Yii::$app->params['shengda_sourceApp_new'];
            $param = [
                'sourceCode' => $sourceApp,
                'orgSource' => $sourceApp,
                'order' => $mainOrder['order_no'],
                'phoneNum' => $user['mobile'],
                'randStr' => $mainOrder['order_no'],
                'carType' => '03',
                'endTime' => date('Y-m-t',$now),
                'activityType' => '4102'
            ];

            $washObj = new ShengDaCarNewApi();
            $result = $washObj->receiveOrder($param);
            $res=$result['data'];
            $resultarr = json_decode( $res,true);
            if($resultarr['resultCode'] != 'SUCCESS'){
                throw new \Exception('服务器响应失败');
            }
            $mcity = (new CarMobile())->table()->select('city')->where(['package_id'=>$coupon['package_id']])->one();
            $city = $mcity['city'] ? $mcity['city'] : '';
            $resultJson = $washObj->decrypt($resultarr['encryptJsonStr']);
            $resultdata = explode('|',$resultJson);
            $resultCode = json_decode( $resultdata[0],true);
            $insertData = [
                'consumer_code' => $resultCode['orderEncrypt'],
                'expired_time' => strtotime(date('Y-m-t',$now)),
                'uid' => $this->uid,
                'mobile' => $user['mobile'],
                'main_id' => $mainOrder['id'],
                'main_ordersn' => $mainOrder['order_no'],
                'out_orderno' => $resultCode['orderCode'], //盛大订单编号
                'coupon_id' => $coupon['id'],
                'shop_id' => $shopId,
                'shop_name' => $shopDetail['shopName'],
                'used_num' => $coupon['used_num'] + 1,
                'c_time' => $now,
                'date_day' => date('d', $now),
                'date_month' => date('Ym', $now),
                'server_type' => '4102',
                'amount' => $shopDetail['service']['price']?:'30.00',
                'service_name' => $shopDetail['service']['serviceName']?:'臭氧杀菌（不含洗车）',
                'status' => ORDER_HANDLING,
                'company_id' => $coupon['company'],
                'companyid' => $coupon['companyid'],
                'city' =>$city
            ];
            //写入洗车订单
            $orderObj = new CarDisinfectionOrder();
            $query = $orderObj->myInsert($insertData);
            if ($query == false) {
                throw new \Exception('订单写入失败');
            }
            $trans->commit();
            $order['success'] = true;
        } catch (\Exception $e){
            if(isset($trans)){
                $trans->rollBack();
            }
            $order['success'] = false;
            $order['msg'] = $e->getMessage();
        }

        $order['data'] = $insertData;

        return $order;
    }



    /**
     * 门店地址导航
     * @return string
     */
    public function actionLocate()
    {

        $request = Yii::$app->request;
        $shopId = $request->get('shopId');
        $shopObj = new WashShop();

        $shop = $shopObj->table()->select()->where(['shopId' => $shopId])->one();
        if(!$shop){
            return $this->json(ERROR_STATUS,'error');
        }

        return $this->json(SUCCESS_STATUS,'ok', $shop);
    }



    /**
     * 生成主订单
     * @param $uid
     * @param $coupon_id
     * @param $coupon_amount
     * @return array|bool
     */
    protected function main_order($uid, $coupon_id, $coupon_amount)
    {
        $obj = new Car_paternalor();
        $data = [
            'order_no' => $obj->create_order_no($uid, 'DIS'),
            'uid' => $uid,
            'type' => 10,
            'coupon_id' => $coupon_id,
            'coupon_amount' => $coupon_amount,
            'c_time' => time()
        ];
        $id = $obj->myInsert($data);
        if ($id && is_numeric($id)) {
            $data['id'] = $id;
            return $data;
        }
        return false;
    }

    /**
     * 取消订单
     * @return array|string
     */
    public function actionCancelorder()
    {
        if(Yii::$app->request->isAjax){
            $id = Yii::$app->request->post('id');
            $washObj = new CarDisinfectionOrder();
            $washOrder = $washObj->table()->select()->where(['id' => $id, 'status' => 1])->one();
            $jsonData['status'] = 1;
            $jsonData['msg'] = '订单取消成功';
            try {
                //订单是否存在
                if(!$washOrder){
                    throw new \Exception('没有可取消订单');
                }
                //订单用户与登录用户是否匹配
                if($washOrder['uid'] != $this->uid){
                    throw new \Exception('订单用户与当前登录用户不匹配');
                }
                $this->cancelShengDaOrder($washOrder);
            } catch(\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);
        }

        return '非法访问！';
    }

    /**
     * 取消盛大订单
     * @param $washOrder
     * @return mixed
     * @throws Exception
     */
    public function cancelShengDaOrder($washOrder)
    {
        $user = $this->isLogin();
        $sourceApp = Yii::$app->params['shengda_sourceApp_new'];
        $param = [
            'sourceCode' => $sourceApp,
            'orderId' => $washOrder['out_orderno']
        ];
        $washObj = new ShengDaCarNewApi();
        $result = $washObj->cancelOrder($param);
        $res=$result['data'];
        $resultCode = json_decode( $res,true);
        try {
            if(!$resultCode){
                throw new \Exception('接口连接失败');
            }
            $trans = Yii::$app->db->beginTransaction();

            $model = new CarDisinfectionOrder();
            //resultCode状态 SUCCESS=成功 ERROR=错误 FAIL=已使用
            switch($resultCode['resultCode']){
                case 'ERROR':
                    throw new \Exception('订单取消失败');
                    break;
                case 'IS_CANCEL':
                    throw new \Exception('订单已取消');
                    break;
                case 'IS_USE':
                    $washOrder['status'] = ORDER_SUCCESS;
                    $washOrder['s_time'] = time();
                    $r = $model->myUpdate($washOrder);
                    throw new \Exception('服务码已使用');
                    break;
                case 'SUCCESS':
                    $washOrder['status'] = ORDER_CANCEL;
                    $washOrder['s_time'] = time();
                    $r = $model->myUpdate($washOrder);
                    $couponObj = new CarCouponAction($user);
                    $useCoupon = $couponObj->unuseCoupon($washOrder['coupon_id']);
                    if($useCoupon == false){
                        throw new \Exception('卡券恢复失败');
                    }
                    break;
            }
            $trans->commit();
        } catch (\Exception $e){
            if(isset($trans)){
                $trans->rollBack();
            }
            $this->status = 0;
            $this->msg = $e->getMessage();
        }

        return true;
    }

    /**
     * 百度经纬度转高德
     * @param $lng
     * @param $lat
     * @return mixed
     */
    public function changePoint($lng, $lat)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        // $data['gg_lon'] = $z * cos($theta);
        // $data['gg_lat'] = $z * sin($theta);
        $gg_lon = $z * cos($theta);
        $gg_lat = $z * sin($theta);
        // 保留小数点后六位
        $data['lng'] = round($gg_lon, 8);
        $data['lat'] = round($gg_lat, 8);
        return $data;
    }

    /**
     * 计算门店距离
     * @param $lat
     * @param $lng
     * @param float $radius
     * @return float
     */
    public function distance($lat, $lng, $radius=6378.135)
    {
        $session = Yii::$app->session;
        $point = $session['wash_point'];
        $rad = floatval(M_PI/180.0);
        $lat1 = floatval($lat) * $rad;
        $lon1 = floatval($lng) * $rad;
        $lat2 = floatval($point['lat']) * $rad;
        $lon2 = floatval($point['lng']) * $rad;
        $theta = $lon2 - $lon1;
        $dist = acos(sin($lat1) * sin($lat2) +
            cos($lat1) * cos($lat2) * cos($theta));
        if($dist < 0) {
            $dist += M_PI;
        }
        $data = round($dist * $radius, 2);

        return $data;
    }


    //获取省份
    public function actionPro()
    {
        $areaObj = new CarWashArea();
        $province = $areaObj->getProvince();

        return $this->render('city',['province' => $province]);
    }

    /**
     * 获取城市
     * @return string
     */
    public function actionCity()
    {
        $request = Yii::$app->request;
        if($request->isPost){

            $pid = Yii::$app->request->post('pid',null);
            $areaObj = new CarWashArea(); //典典区域数据表
            $city = $areaObj->getCity($pid);
            $area = $areaObj->getArea($city[0]['id']);

            $data = [
                'city' => $city,
                'area' => $area
            ];
            return json_encode($data);
        }

        return '非法请求';
    }

    /**
     * 获取区域
     * @return string
     */
    public function actionArea()
    {
        $request = Yii::$app->request;
        if($request->isPost){

            $pid = Yii::$app->request->post('pid',null);
            $areaObj = new CarWashArea();
            $area = $areaObj->getArea($pid);
            return json_encode($area);
        }
        return '非法请求';

    }

    public function geocoder($address)
    {
        $addr = $address['province'].$address['city'].$address['area'];
        $ak = Yii::$app->params['BmapServer'];
        $url = 'http://api.map.baidu.com/geocoder/v2/?address='.$addr.'&output=json&ak='.$ak.'&output=json';
        $list = [];
        $res = W::http_get($url);
        $res = json_decode($res,true);
        if($res['status'] == 0){
            $list = $res['result']['location'];
        } else {
            return false;
        }
        return $list;
    }

}