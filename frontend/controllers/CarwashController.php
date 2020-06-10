<?php

namespace frontend\controllers;

use common\components\AlxgBase;
use common\components\BaiduMap;
use common\components\CarCouponAction;
use common\components\DianDianWash;
use common\components\Helpper;
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
use common\models\Wash_shop;
use common\models\WashOrder;
use common\models\WashShop;
use common\models\WashShopService;
use common\components\W;
use common\components\ShengDaCarWash;
use common\components\NationalLife;
use common\models\Car_paternalor;
use yii\helpers\Url;
use Yii;
use yii\db\Exception;
use yii\web\Response;

class CarwashController extends CloudcarController
{

    protected $uid;
    public $menuActive = 'caruser';
    public $layout = "cloudcarv2";
    public $site_title = '洗车服务';
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
     * 检测洗车限制条件，每天用户只能使用一次洗车服务
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
        $order = (new WashOrder())->table()->where(['uid'=>$this->uid])->andWhere(['between','s_time',$startDay,$endDay])->one();
        if($order){
            if($order['status']== 1){
                $data['url'] = Url::to(['carwash/shoplist','couponId'=>$order['couponId'],'company'=>$order['company_id']]);
            }elseif($order['status']==2) {
                $data['status'] = 0;
                $data['msg'] = '每天只能使用一次洗车服务！';
            }

        }else{
            $order = (new Car_tuhunotice())->table()->where(['uid'=>$this->uid])->andWhere(['between','c_time',$startDay,$endDay])->one();
            if($order){
                $data['status'] = 0;
                $data['msg'] = '每天只能使用一次洗车服务！';
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
     * @param $company 1:典典 2:盛大
     * @return string
     */
    public function actionShoplist()
    {
        $request = Yii::$app->request;
        $couponId = $request->get('couponId');
        $company = $request->get('company');

        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        $areaObj = new Car_washarea();
        $province = $areaObj->getProvince();
        $info = (new CarCoupon())->table()->select('companyid')->where(['id'=>$couponId])->one();
        if($info['companyid'] == Yii::$app->params['national_life']['companyid'] ){
            $footer = 'hidden';
            $this->site_title = '中国人寿综合服务平台';
        }
       
        $is_weixin = $this->is_weixin();
        return $this->render('shoplist',compact('is_weixin','couponId','company','alxg_sign','province','footer'));
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
			// if($post['lng']=='112.93134'){
				// return '非法请求';
			// }
            switch($post['company']){
                case 1:
                    $data = $session['wash_dd_shop_list'];
                    if($data['limit_time']<$now){
                        $data = $this->dianDianShopList($post);
                    }
                    break;
                case 2:
                    $data = $session['wash_sd_shop_list'];
                    if($data['limit_time']<$now){
                        $data =  $this->shengDaShopList($post);
                    }
                    break;
                case 3:
                    $data = $session['wash_zy_shop_list'];
                    if($data['limit_time']<$now){
                        $data = $this->ziYingShopList($post);
                    }
                    break;
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
            switch($post['company']){
                case 1:
                    $data = $this->dianDianShopList($post);
                    break;
                case 2:
                    $data =  $this->shengDaShopList($post);
                    break;
                case 3:
                    $data = $this->ziYingShopList($post);
                    break;
            }
            return $this->json($this->status,$this->msg,$data);
        }
        return '非法请求';
    }

    /**
     * 典典平台请求门店数据
     * @param $post
     * @return array
     */
    protected function dianDianShopList($post)
    {
        //纬度查询门店
        if($post['lng'] && $post['lat']){
            $postData = [
                'pageIndex' => 1,
                'distance' => 50,
                'lng' => $post['lng'],
                'lat' => $post['lat'],
            ];
            //经纬度存入session,用于计算门店距离
            $session['wash_point'] = [
                'lat' => $post['lat'],
                'lng' => $post['lng']
            ];
            $data = $this->dianDianLonShop($postData);

        } else {  //区域查询门店
            $postData = [
                'provIds' => $post['province'],
                'cityIds' => $post['city'],
                'districtIds' => $post['area'],
                'pageSize' => 100,
                'pageNumber' => 1,
            ];
            $data = $this->dianDianAreaShop($postData);
        }
        //门店数据存入session
        $data['limit_time'] = time()+500;
        Yii::$app->session['wash_dd_shop_list'] = $data;

        return $data;
    }

    /**
     * 典典平台经纬度查询门店
     * @param $postData
     * @return array
     */
    protected function dianDianLonShop($postData)
    {

        $DianDianWash = new DianDianWash();
        $data = [];
        $res = $DianDianWash->offlineNearbyShopList($postData);
        try {
            if(!$res['data']['list']){
                throw new \Exception('没有该区域门店数据');
            }
			
            //通过百度地图api将经纬度转换省市区
            $address = BaiduMap::geocoder($postData['lat'],$postData['lng']);
            $location = (new Car_washarea())->getLocation($address);
            if(!$location){
                throw new \Exception('经纬度转换地址失败');
            }
            $data = [
                'shopList' => $res['data']['list'],
                'location' => $location
            ];

        } catch (\Exception $e){
            $this->status = 0;
            $this->msg = $e->getMessage();
        }
		

        return $data;
    }

    /**
     * 典典平台区域查询门店
     * @param $postData
     * @return array
     */
    protected function dianDianAreaShop($postData)
    {
        $DianDianWash = new DianDianWash();
        $data = [];
        $res = $DianDianWash->offlineAreaShoplist($postData);
        try {
            if(!$res['data']['data']){
                throw new \Exception('没有该区域的门店数据');
            }
            $newData = [];
            $now = time();
            foreach ($res['data']['data'] as $val){
                $newData[]['shopName'] = $val['careShopName'];
                $newData[]['shopId'] = $val['careShopId'];
                $newData[]['city'] = $val['city'];
                $newData[]['district'] = $val['district'];
                $newData[]['shopLng'] = $val['longitude'];
                $newData[]['shopLat'] = $val['latitude'];
                $newData[]['shopAddress'] = $val['address'];
                $newData[]['shopAvator'] = $val['avatar'];
                $newData[]['score'] = $val['score'];
                $newData[]['serviceStartTime'] = $val['serviceStartTime'];
                $newData[]['serviceEndTime'] = $val['serviceEndTime'];
                $newData[]['company'] = 1;
                $newData[]['c_time'] = $now;
                $newData[]['u_time'] = $now;
                //计算当前位置与门店的距离
                $newData[]['distance'] = $this->distance($val['latitude'],$val['longitude'])?:'0.00';
            }
            //按距离排序
            array_multisort(array_column($newData,'distance'),SORT_ASC,$newData);
            $address = [
                'province' => $postData['province'],
                'city' => $postData['city'],
                'district' => $postData['area'],
            ];
            //获取省市区
            $location = (new Car_washarea())->getLocationById($address);
            if($location){
                throw new \Exception('地理位置解析失败');
            }
            $data = [
                'shopList' => $newData,
                'location' => $location
            ];
        } catch (\Exception $e){
            $this->status = 0;
            $this->msg = $e->getMessage();
        }

        return $data;
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
            'source' => Yii::$app->params['shengda_sourceApp'],
           // 'pro' => $post['pro'],
            'isMap' => 1,
            'page' => '1',
            'pageSize' => '50'
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
                $location = (new Car_washarea())->getLocation($address);
            }else {
                //地理位置查询门店
                $postData['city'] = $post['city'];
                $postData['area'] = $post['area'];
                $address = [
                    'province' => $post['province'],
                    'city' => $post['city'],
                    'district' => $post['area'],
                ];
                //获取省市区
                $location = (new Car_washarea())->getLocation($address);
            }
 
 
            $shengDa = new ShengDaCarWash();
            $result = $shengDa->merchantDistanceList($postData);
            if(!$result){
                throw new \Exception('服务器连接失败');
            }
			
			// if($post['lng']=='112.93134'){
				// return '非法请求4444';
			// }


            $result = strstr($result['encryptJsonStr'],'|',true);
            $result = json_decode($result,true);
            $shopList = $result['coEnterprises'];
            if(empty($shopList)){
                throw new \Exception('没有门店数据');
            }
            $newData = [];
            $now = time();
            $wash_point = Yii::$app->session['wash_point'];
			
            foreach($shopList as $val){
                $point = $this->changePoint($val['map']['longitude'],$val['map']['latitude']);
                if($wash_point){
                    $val['distance'] = BaiduMap::getDistance($wash_point['lat'], $wash_point['lng'], $point['lat'], $point['lng']);
                }
                $newData[] = [
                    'shopName' => $val['name'],
                    'shopId' =>intval($val['map']['latitude']*1000000),
                    'prov' => $location['province']['name'],
                    'city' => $location['city']['name'],
                    'district' => $location['area']['name'],
                    'shopLng' => $point['lng'],
                    'shopLat' => $point['lat'],
                    'distance' => round($val['distance'],2),
                    'shopAvator' => $val['logo'],
                    'images' => $val['logo'],
                    'shopTel' => $val['telephone'],
                    'shopAddress' => $val['address'],
                    'serviceStartTime' => '09:00',
                    'serviceEndTime' => '18:00',
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
            Yii::$app->session['wash_sd_shop_list'] = $data;
        } catch (\Exception $e){
            $this->status = 0;
            $this->msg = $e->getMessage();
        }
        return $data;
    }

    protected function ziYingShopList($post)
    {
        $shopObj = new Wash_shop();
        $where = '';
        $data = '';
        $session = Yii::$app->session;
        if($post['lng'] && $post['lat']){
            //经纬度存入session,用于计算门店距离
            $session['wash_point'] = [
                'lat' => $post['lat'],
                'lng' => $post['lng']
            ];
            //通过百度地图api将经纬度转换省市区
            $address = BaiduMap::geocoder($post['lat'],$post['lng']);
            $location = (new Car_washarea())->getLocation($address);
            //如果有地址位置就赋值给请求数据
            if($location){
                $postData['pro'] = $location['province']['name'];
                $postData['city'] = $location['city']['name'];
                $postData['area'] = $location['area']['name'];
            }
            $where = 'lng > "'.($post['lng']-0.25).'" AND lng<"'.($post['lng']+0.25).'"';
            $where.= ' AND lat > "'.($post['lat']-0.25).'" AND lat<"'.($post['lat']+0.25).'"';
            //根据经纬度计算距离并排序
            $order = ' order by ACOS(SIN(('.$post['lat'].' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS(('.$post['lat'].' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS(('.$post['lng'].'* 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6380 asc';

        } else {
            $address = [
                'province' => $post['province'],
                'city' => $post['city'],
                'district' => $post['area'],
            ];
            //获取省市区
            $location = (new Car_washarea())->getLocationBypid($address);
            //根据经纬度计算距离并排序
            $point = $session['wash_point'];
            if($point){
                $order = ' order by ACOS(SIN(('.$point['lat'].' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS(('.$point['lat'].' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS(('.$point['lng'].'* 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6380 asc';

            }

        }
        if($post['province']){
            $where.= 'province = "'.$post['province'].'"';
        }
        if($post['city']){
            $where.= ' AND city = "'.$post['city'].'"';
        }
        if($post['area']){
            $where.= ' AND area = "'.$post['area'].'"';
        }
        $where.= ' AND shop_status = 2 ';
        $where.=$order;

        try {
            $res = $shopObj->select('*',$where)->all();
            if(!$res){
                throw new \Exception('该区域没有门店');
            }
            $shopList = [];
            foreach ($res as $val){
                //计算门店距离
                $val['distance'] = $this->distance($val['lat'],$val['lng']);
                $shopList[] = [
                    'shopId' => $val['id'],
                    'shopName' => $val['shop_name'],
                    'shopAddress' => $val['shop_address'],
                    'shopAvator' => $val['shop_pic'],
                    'shopLng' => $val['lng'],
                    'shopLat' => $val['lat'],
                    'distance' => round($val['distance'],2),
                ];
            }
            //将数据存入session
            $data = [
                'shopList' => $shopList,
                'location' => $location,
                'limit_time' => time()+300
            ];
            $session['wash_zy_shop_list'] = $data;
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
        switch($company) {
            //典典洗车门店列表
            case 1:
                //从session缓存中取出门店
                $shopInfo = $session['wash_dd_shop_list'];
                foreach($shopInfo['shopList'] as $val){
                    if($shopId == $val['shopId']){
                        $shopDetail = $val;
                    }
                }
                $shopDetail['images'] = $shopObj->getImages($shopId);
                $shopDetail['c_time'] = time();
                $shopDetail['company'] = $company;
                //删除门店服务列表
                unset($shopDetail['serviceList']);
                //插入或者更新数据库门店信息
                $shopObj->insertOrUpdate($shopDetail,'shopId',$shopDetail['shopId']);
                break;
            //盛大洗车门店列表
            case 2:
                $shopInfo = $session['wash_sd_shop_list'];
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
                break;
            case 3:
                $shopInfo = (new Wash_shop())->select('*','id = '.$shopId)->one();
                $shopDetail = [
                    'shopId' => $shopInfo['id'],
                    'shopName' => $shopInfo['shop_name'],
                    'shopAddress' => $shopInfo['shop_address'],
                    'images' => $shopInfo['shop_pic'],
                    'shopLng' => $shopInfo['lng'],
                    'shopLat' => $shopInfo['lat'],
                    'serviceStartTime' => $shopInfo['start_time'],
                    'serviceEndTime' => $shopInfo['end_time'],
                    'score' => $shopInfo['score']
                ];
        }
        $url = WashOrder::$company[$company];
        $coupon = (new CarCoupon())->table()->where(['id'=>$couponId,'uid'=>$this->uid])->one();

        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        if($coupon['companyid'] == Yii::$app->params['national_life']['companyid'] ){
            $footer = 'hidden';
            $this->site_title = '中国人寿综合服务平台';
        }
        return $this->render('shopdetail',compact('shopDetail','couponId','alxg_sign','url','coupon','footer'));
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
        $shopInfo = $session['wash_sd_shop_list'];
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
     * 获取典典洗车消费凭证
     * @return string
     */
    public function actionDiandiangetcode()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $data['shopId'] = $request->post('shopId')?:$session['wash_info']['shop_id'];
        $data['couponId'] = $request->post('couponId')?:$session['wash_info']['coupon_id'];
        $data['uid'] = $this->uid;
        $check = $this->washLimit($data['couponId']);
        if($check['status'] == 0){
            return $this->json('0',$check['msg']);
        }
        if(!isset($session['wash_info']['wash_coupon_id'])){
            $coupon = (new CarCoupon())->getDianDianCouponWash($data['couponId'], $this->uid);
        } else {
            $coupon = $session['wash_info']['wash_coupon_id'];
        }
        //获取当前用户的洗车订单
        $obj = new WashOrder();
        $washOrder = $obj->getConsCode($data['uid'],$data['couponId'],1);

        //如果没有订单或者订单状态为-1,通过典典接口下单
        if(!$washOrder || $washOrder['status']==-1){
            $res = $this->playOrder($data);
            $washOrder = $res['data'];
            if($res['success'] == false){
                return $this->json(ERROR_STATUS,$res['msg']);
            }
        }
        //如果当月已经使用过卡券
        if($washOrder['status'] == 2){
            //每月限制使用1次，否则继续下单
            if($coupon['is_mensal'] == 1){
                return $this->json(ERROR_STATUS,'洗车券每月限制使用1次');
            } else {
                $res = $this->playOrder($data);
                $washOrder = $res['data'];
                if($res['success'] == false){
                    return $this->json(ERROR_STATUS,$res['msg']);
                }
            }
        }
        //如果当前门店ID与订单门店ID不同，则取消订单重新下单
        if ($washOrder['shopId'] != $data['shopId']){
            $res = $this->cancelDianDianOrder($washOrder);
            if($res['success'] == false){
                return $this->json(ERROR_STATUS,$washOrder['msg']);
            }
            $order = $this->playOrder($data);
            if($order['success'] == false){
                return $this->json(ERROR_STATUS,'洗车订单创建失败');
            }
            $washOrder = $order['data'];
        }
        $cons = [
            'consumerCode' => $washOrder['consumerCode'],
            'expiredTime' => date('Y-m-d', $washOrder['expiredTime'])
        ];
        return $this->json(SUCCESS_STATUS, 'ok', $cons);
    }

    /**
     * 创建典典洗车订单
     * @param $data
     * @return array|bool
     */
    public function playOrder($data)
    {
        $user = $this->fans_account();
        $shopDetail = (new WashShop())->table()->select()->where(['shopId' => $data['shopId']])->one();
        $shopDetail['service'] = (new WashShopService())->table()->select()->where(['shopId' => $data['shopId'], 'lv2ServiceTypeId' =>1036 ])->one();
        //查询用户绑定的优惠券 并比较剩余次数是否为0
        $couponlist = (new CarCoupon())->get_user_bind_coupon_list($this->uid);
        $key = array_search($data['couponId'],array_column($couponlist, 'id'));
        $coupon=$couponlist[$key];
        if($coupon['show_coupon_all'] <= 0){
            return [
                'success' => false,
                'msg' => '优惠劵共'.$coupon['amount'].'次，剩余0次！'
            ];
        }
        $dianDianWash = new DianDianWash();
        $data['phone'] = $user['mobile'];
        $data['lv1ServiceTypeId'] = 1;
        $data['lv2ServiceTypeId'] = 1036;

        $now =time();
        try {
            $trans = Yii::$app->db->beginTransaction();
            //写入主订单
            $mainOrder = $this->main_order($this->uid, $coupon['id'], $coupon['amount']);
            if($mainOrder == false){
                throw new \Exception('主订单写入失败');
            }
            //使用卡券
            $couponObj = new CarCouponAction($user);
            $useCoupon = $couponObj->useCoupon($data['couponId']);
            if($useCoupon == false){
                throw new \Exception('卡券核销失败');
            }
            //典典接口下单
            $data['outOrderNo'] = $mainOrder['order_no'];
            $res = $dianDianWash->offlineCreateOrder($data);
            if($res['success']  == false){
                throw new \Exception($res['message']);
            }
            $insertData = [
                'consumerCode' => $res['data']['consumerCode'],
                'expiredTime' => strtotime($res['data']['expiredTime']),
                'uid' => $this->uid,
                'mobile' => $user['mobile'],
                'mainId' => $mainOrder['id'],
                'mainOrderSn' => $mainOrder['order_no'],
                'couponId' => $data['couponId'],
                'shopId' => $data['shopId'],
                'shopName' => $shopDetail['shopName'],
                'used_num' => $coupon['used_num'] + 1,
                'c_time' => $now,
                'date_day' => date('d', $now),
                'date_month' => date('Ym', $now),
                'serverType' => 1036,
                'amount' => $shopDetail['service']['price']?:'30.00',
                'serviceName' => $shopDetail['service']['serviceName']?:'普洗（轿车）',
                'status' => ORDER_HANDLING,
                'company_id' => $coupon['company'],
                'companyid' => $coupon['companyid'],
            ];
            //写入洗车订单
            $orderObj = new WashOrder();
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
     * 盛大洗车服务码
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
                    throw new \Exception('优惠券编号不存在,编号id为'.$couponId.'-'.$this->uid);
                }
                $obj = new WashOrder();
                //检测客户限制条件
                $check = $this->washLimit($couponId);
                if($check['status'] == 0 ){
                    throw new \Exception($check['msg']);
                }
                $washOrder = $obj->table()->where([
                    'uid' => $this->uid,
                    'couponId' => $couponId,
                    'company_id' => 2,
                    'date_month' => date('Ym')
                ])->limit(1)->orderBy('id desc')->one();
                if(!$washOrder || $washOrder['status'] == -1){
                    $res = $this->shengDaPlayOrder($coupon,$shopId);
                    $washOrder = $res['data'];
                    if($res['success'] == false){
                        throw new \Exception($res['msg']);
                    }
                }
                //如果当月已经使用过卡券
                if($washOrder['status'] == ORDER_SUCCESS){
                    //当月限制使用1次返回信息，否则继续下单
                    if($coupon['is_mensal'] == 1){
                        throw new \Exception('洗车券每月限制使用1次');
                    }else {
                        $res = $this->shengDaPlayOrder($coupon,$shopId);
                        $washOrder = $res['data'];
                        if($res['success'] == false){
                            throw new \Exception($res['msg']);
                        }
                    }
                }
                if($washOrder['shopId'] != $shopId){
                    $shop = (new WashShop())->getShop($shopId);
                    $washOrder['shopId'] = $shopId;
                    $washOrder['shopName'] = $shop['shopName'];
                    $obj->myUpdate($washOrder);
                    if($coupon['companyid'] == Yii::$app->params['national_life']['companyid']){
                        $datags = [
                            'num' => $washOrder['id'],
                            'order_id' => $washOrder['outOrderNo'],
                            'cdkey' => $coupon['coupon_sn'],
                            'mobile' => $washOrder['mobile'],
                            'shop_name' => $washOrder['shopName'],
                            'service_name' => $washOrder['serviceName'],
                            'certificate' => $washOrder['consumerCode'],
                            'status' => WashOrder::$status_text['1'],
                            'create_time' => date("Y-m-d H:i:s",time()),
                            'update_time' => ''
                        ];
                        $objgs = new NationalLife();
                        $objgs->notice($datags);
                    }
                }
                $cons = [
                    'consumerCode' => $washOrder['consumerCode'],
                    'expiredTime' => date('Y-m-d', $washOrder['expiredTime'])
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
     * 盛大洗车下单
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
            $sourceApp = Yii::$app->params['shengda_sourceApp'];
            $param = [
                'source' => $sourceApp,
                'orgSource' => $sourceApp,
                'order' => $sourceApp.$mainOrder['order_no'],
                'randStr' => $mainOrder['order_no'],
                'carType' => '03',
                'userInfo' => $user['mobile'],
                'endTime' => date('Y-m-t',$now),
                'userOrderType' => 'order',
                'generationRule' => '02',
            ];
            $washObj = new ShengDaCarWash();
            $result = $washObj->receiveOrder($param);

            if($result['resultCode'] != 'SUCCESS'){
                throw new \Exception('服务器响应失败');
            }

            $resultJson = strstr($result['encryptJsonStr'],'|',true);
            $mcity = (new CarMobile())->table()->select('city')->where(['package_id'=>$coupon['package_id']])->one();
            $city = $mcity['city'] ? $mcity['city'] : '';
            $resultCode = json_decode( $resultJson,true);
            $insertData = [
                'consumerCode' => $resultCode['order'],
                'expiredTime' => strtotime(date('Y-m-t',$now)),
                'uid' => $this->uid,
                'mobile' => $user['mobile'],
                'mainId' => $mainOrder['id'],
                'mainOrderSn' => $mainOrder['order_no'],
                'outOrderNo' => $resultCode['encryptCode'], //盛大订单编号
                'couponId' => $coupon['id'],
                'shopId' => $shopId,
                'shopName' => $shopDetail['shopName'],
                'used_num' => $coupon['used_num'] + 1,
                'c_time' => $now,
                'date_day' => date('d', $now),
                'date_month' => date('Ym', $now),
                'serverType' => 1036,
                'amount' => $shopDetail['service']['price']?:'30.00',
                'serviceName' => $shopDetail['service']['serviceName']?:'普洗（轿车或SUV）',
                'status' => ORDER_HANDLING,
                'company_id' => $coupon['company'],
                'companyid' => $coupon['companyid'],
                'city' =>$city
            ];
            //写入洗车订单
            $orderObj = new WashOrder();
            $query = $orderObj->myInsert($insertData);
            if ($query == false) {
                throw new \Exception('订单写入失败');
            }
            if($coupon['companyid'] == Yii::$app->params['national_life']['companyid']){
                $datags = [
                    'num' => $query,
                    'order_id' => $insertData['outOrderNo'],
                    'cdkey' => $coupon['coupon_sn'],
                    'mobile' => $insertData['mobile'],
                    'shop_name' => $insertData['shopName'],
                    'service_name' => $insertData['serviceName'],
                    'certificate' => $insertData['consumerCode'],
                    'status' => WashOrder::$status_text['1'],
                    'create_time' => date("Y-m-d H:i:s",$now),
                    'update_time' => ''
                ];
                $objgs = new NationalLife();
                $objgs->notice($datags);
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
     * 自营洗车门店服务码
     * @return array|string
     */
    public function actionZiyinggetcode()
    {
        $request = Yii::$app->request;
        if($request->isPost){

            $shopId = $request->post('shopId',null);
            $couponId = $request->post('couponId',null);
            //根据传入的couponId查询用户绑定的洗车券
            $coupon = (new CarCoupon())->getDianDianCouponWash($couponId, $this->uid);
            if(!$coupon) {
                return $this->json('0', '该优惠券号码不存在，请激活后使用');
            }
            $order = (new WashOrder())->getConsCode($this->uid,$coupon['couponId'],3);
            //如果没有进行中的订单则重新下单
            if(!$order || $order['status'] == -1){
                $order = $this->ziYingPlayOrder($coupon,$shopId);
                if(!$order){
                    return $this->json($this->status,$this->msg);
                }
            }
            //如果当月已经使用过卡券
            if($order['status'] == 2){
                //每月限制使用1次，否则继续下单
                if($coupon['is_mensal'] == 1){
                    return $this->json(0,'洗车券每月限制使用1次');
                } else {
                    $res = $this->ziYingPlayOrder($coupon,$shopId);
                    if(!$res){
                        return $this->json($this->status,$this->msg);
                    }
                }
            }
            //如果当前门店ID与订单门店ID不同，则取消订单重新下单
            if ($order['shopId'] != $shopId){
                $res = $this->ziYingCanceOrder($order);
                if(!$res){
                    return $this->json($this->status,$this->msg);
                }
                $order = $this->ziYingPlayOrder($coupon,$shopId);
                if(!$order){
                    return $this->json($this->status,$this->msg);
                }
            }
            $cons = [
                'consumerCode' => $order['consumerCode'],
                'expiredTime' => date('Y-m-d', $order['expiredTime'])
            ];

            return $this->json(SUCCESS_STATUS, 'ok', $cons);
        }

        return '非法请求';
    }

    protected function ziYingPlayOrder($coupon,$shopId)
    {
        $user = $this->fans_account();
        $shopDetail = (new Wash_shop())->select('*','id ='.(int)$shopId)->one();
        //查询用户绑定的优惠券 并比较剩余次数是否为0
        $couponlist = (new CarCoupon())->renderCouponList1($coupon,0);
        $trans = Yii::$app->db->beginTransaction();
        $check = $this->washLimit();
        if($check['status'] == 0){
            return $this->json('0',$check['msg']);
        }
        try {
            if($couponlist['show_coupon_all'] <0){
                throw new \Exception('优惠劵共'.$coupon['amount'].'次，剩余0次！');
            }
            //写入主订单
            $mainOrder = $this->main_order($this->uid, $coupon['couponId'], $coupon['amount']);
            if($mainOrder == false){
                throw new \Exception('主订单写入失败');
            }
            //使用卡券
            $couponObj = new CarCouponAction($user);
            $useCoupon = $couponObj->useCoupon($coupon['couponId']);
            if($useCoupon == false){
                throw new \Exception('卡券核销失败');
            }
            $consumerCode = $this->checkCode($coupon['couponId']);
            $now = time();
            $insertData = [
                'consumerCode' => $consumerCode,
                'expiredTime' => strtotime(date('Y-m-t',$now)),
                'uid' => $this->uid,
                'mobile' => $user['mobile'],
                'mainId' => $mainOrder['id'],
                'mainOrderSn' => $mainOrder['order_no'],
                'outOrderNo' => $mainOrder['order_no'], //订单编号
                'couponId' => $coupon['couponId'],
                'shopId' => $shopDetail['id'],
                'shopName' => $shopDetail['shop_name'],
                'used_num' => $coupon['used_num'] + 1,
                'c_time' => $now,
                'date_day' => date('d', $now),
                'date_month' => date('Ym', $now),
                'serverType' => 1036,
                'promotion_price' => $shopDetail['promotion_price'],
                'amount' => $shopDetail['price'],
                'serviceName' => '普洗（轿车）',
                'status' => ORDER_HANDLING,
                'company_id' => $coupon['company'],
                'companyid' => $coupon['companyid'],
            ];
            //写入洗车订单
            $orderObj = new WashOrder();
            $query = $orderObj->myInsert($insertData);
            if ($query == false) {
                throw new \Exception('订单写入失败');
            }
            $trans->commit();
        } catch (\Exception $e){
            $trans->rollBack();
            $this->status = 0;
            $this->msg = $e->getMessage();
            $insertData= false;
        }

        return $insertData;
    }

    //检查生成的洗车服务码是否唯一，如果washorder表中存在，则调用自身重新生成
    private function checkCode($couponId)
    {
        //洗车服务码规则 ZY加上8位随机数字加优惠券ID
        $consumerCode = 'ZY'.W::createNoncestr(8).$couponId;
        $code = (new WashOrder())->table()->select('consumerCode')->where(['consumerCode' => $consumerCode])->one();
        if($code){
            self::checkCode($couponId);
        }
        return $consumerCode;
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
            'order_no' => $obj->create_order_no($uid, 'W'),
            'uid' => $uid,
            'type' => 4,
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
            $washObj = new WashOrder();
            $washOrder = $washObj->table()->select()->where(['id' => $id, 'status' => 1])->one();
            $jsonData['status'] = 1;
            $jsonData['msg'] = '订单取消成功';
            try {
                //订单是否存在
                if(!$washOrder){
                    throw new \Exception('没有该订单');
                }
                //订单用户与登录用户是否匹配
                if($washOrder['uid'] != $this->uid){
                    throw new \Exception('订单用户与当前登录用户不匹配');
                }
                switch($washOrder['company_id']){
                    case 1: //典典洗车
                        $this->cancelDianDianOrder($washOrder);
                        break;
                    case 2: //盛大洗车
                        $this->cancelShengDaOrder($washOrder);
                        break;
                    case 3: //自营洗车
                        $this->ziYingCanceOrder($washOrder);
                }

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
        $sourceApp = Yii::$app->params['shengda_sourceApp'];
        $param = [
            'source' => $sourceApp,
            'order' => $washOrder['outOrderNo'],
            'refundStatus' => 2
        ];
        $washObj = new ShengDaCarWash();
        $result = $washObj->cancelOrder($param);
        $resultJson = strstr($result['encryptJsonStr'],'|',true);
        $resultCode = json_decode( $resultJson,true);
        $resultCode['resultCode'] = 'SUCCESS';
        $resultCode['failNum'] = 0;
        $resultCode['succNum'] = 1;
        try {
            if(!$resultCode){
                throw new \Exception('接口连接失败');
            }
            $trans = Yii::$app->db->beginTransaction();
            //resultCode状态 SUCCESS=成功 ERROR=错误 FAIL=已使用
            switch($resultCode['resultCode']){
                case 'FAIL':
                    $washOrder['status'] = ORDER_SUCCESS;
                    $washOrder['s_time'] = time();
                    $r = (new WashOrder())->myUpdate($washOrder);
                    throw new \Exception('服务码已使用');
                    break;
                case 'SUCCESS':
                    if($resultCode['failNum'] ==1 ){
                        throw new \Exception('订单取消失败');
                    }
                    if($resultCode['succNum'] ==1){
                        $washOrder['status'] = ORDER_CANCEL;
                        $washOrder['s_time'] = time();
                        $r = (new WashOrder())->myUpdate($washOrder);
                        if($washOrder['companyid'] == Yii::$app->params['national_life']['companyid']){
                            $couponinfo = (new CarCoupon())->table()->select('coupon_sn')->where(['id'=>$washOrder['couponId']])->one();
                            $datags = [
                                'num' => $washOrder['id'],
                                'order_id' => $washOrder['outOrderNo'],
                                'cdkey' => $couponinfo['coupon_sn'],
                                'mobile' => $washOrder['mobile'],
                                'shop_name' => $washOrder['shopName'],
                                'service_name' => $washOrder['serviceName'],
                                'certificate' => $washOrder['consumerCode'],
                                'status' => WashOrder::$status_text['-1'],
                                'create_time' => date("Y-m-d H:i:s",$washOrder['c_time']),
                                'update_time' => date("Y-m-d H:i:s",$washOrder['s_time']),
                            ];
                            $objgs = new NationalLife();
                            $objgs->notice($datags);
                        }
                        $couponObj = new CarCouponAction($user);
                        $useCoupon = $couponObj->unuseCoupon($washOrder['couponId']);
                        if($useCoupon == false){
                            throw new \Exception('卡券恢复失败');
                        }
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
     * 取消典典订单
     * status: 退款结果：1退款成功 2订单已消费
     * @param $data
     * @param $consumerCode
     * @return array|bool|mixed|string
     * @throws \yii\db\Exception
     */
    public function cancelDianDianOrder($washOrder)
    {
        $user = $this->isLogin();
        $postData['outOrderNo'] = $washOrder['mainOrderSn'];
        $postData['consumerCode'] = $washOrder['consumerCode'];
        //通过典典接口取消订单
        $dianDianWash = new DianDianWash($this->uid);
        $res = $dianDianWash->offlineCancelOrder($postData);
        try {
            if(!$res['success'] == true) {
                throw new \Exception('服务器连接失败');
            }
            $trans = Yii::$app->db->beginTransaction();
            //如果已经消费，核销订单，否则恢复卡券
            if($res['data']['status'] == 2){
                $washOrder['status'] = ORDER_SUCCESS;
                $washOrder['s_time'] = time();
                $r = (new WashOrder())->myUpdate($washOrder);
                throw new \Exception('服务码已使用');
            }else {
                $couponObj = new CarCouponAction($user);
                $useCoupon = $couponObj->unuseCoupon($washOrder['couponId']);
                if($useCoupon == false){
                    throw new \Exception('卡券恢复失败');
                }
            }
            //写入订单状态为取消，状态码为-1
            $washOrderObj = new WashOrder();
            $washOrder['status'] = ORDER_CANCEL;
            $r = $washOrderObj->myUpdate($washOrder);
            if($r == false){
                throw new \Exception('订单状态写入失败');
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

    protected function ziYingCanceOrder($washOrder)
    {
        $user = $this->isLogin();
        $trans = Yii::$app->db->beginTransaction();
        try {
            //如果已经消费，核销订单，否则恢复卡券
            if($washOrder['status'] == 2){
                throw new \Exception('服务码已使用');
            }
            //恢复卡券
            $couponObj = new CarCouponAction($user);
            $useCoupon = $couponObj->unuseCoupon($washOrder['couponId']);
            if($useCoupon == false){
                throw new \Exception('卡券恢复失败');
            }

            //写入订单状态为取消，状态码为-1
            $washOrderObj = new WashOrder();
            $washOrder['status'] = ORDER_CANCEL;
            $r = $washOrderObj->myUpdate($washOrder);
            if($r == false){
                throw new \Exception('订单状态写入失败');
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
        $areaObj = new Car_washarea();
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
            //type区分典典和其他平台，典典区域数据使用Car_washarea表
            $type = $request->post('type',null);
            $pid = Yii::$app->request->post('pid',null);
            if(!$type){
                $areaObj = new Car_washarea(); //典典区域数据表
                $city = $areaObj->getCity($pid);
                $area = $areaObj->getArea($city[0]['pid']);
            }else{
                $areaObj = new Wash_area();
                $city = $areaObj->getCity($pid);
                $area = $areaObj->getArea($city[0]['code']);
            }

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
            //type区分典典和其他平台，典典区域数据使用Car_washarea表
            $type = $request->post('type',null);
            $pid = Yii::$app->request->post('pid',null);
            if(!$type){
                $areaObj = new Car_washarea(); //典典区域数据表
            }else{
                $areaObj = new Wash_area();
            }
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