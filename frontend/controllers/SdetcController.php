<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\5\14 0014
 * Time: 15:11
 */

namespace frontend\controllers;


use common\components\CarCouponAction;
use common\components\ShangDongETC;
use common\components\W;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarEtcBill;
use common\models\CarEtcCode;
use common\models\CarEtcorder;
use common\models\CarUserCarno;
use frontend\models\EtcapplyForm;
use frontend\models\EtcreceivingForm;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Url;

class SdetcController extends CloudcarController
{
    public $menuActive = 'accoupon';
    public $site_title = '山东ETC';
    public $layout = "cloudcarv2";
    protected $user;
    protected $userCar;
    protected $status = 1;
    protected $msg = 'ok';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        $user = $this->isLogin();
        $this->user = $user;

        return true;
    }

    /**
     * 获取用户绑定的汽车
     */
    protected function selectCar()
    {
        $resCar = (new CarUserCarno())->get_user_bind_car($this->user['uid']);
        $this->userCar = ArrayHelper::index($resCar, 'id');
    }

    /**
     * ETC入口，判断卡券跳转链接
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        //查询用户ETC优惠券
        $couponres = (new CarCoupon())->table()->orderBy('use_limit_time asc')
            ->where(['uid' => $this->user['uid'], 'coupon_type' => ETC])
            ->all();
        if (!$couponres) {
            return $this->redirect(['sdetc/verifycode', 'curtimestamp' => $_SERVER['REQUEST_TIME']]);
        }
        //url请求优惠券id
        $couponId = Yii::$app->request->get('couponId',null);
        //如果只有一张优惠券
        if(count($couponres) ==1){
            //优惠券尚未使用，则直接跳转到到申请设备
            if($couponres[0]['status']==1){
                return $this->redirect(['sdetc/equipment','couponId'=>$couponId]);
            }else {  //如果已使用直接跳转etc订单详情
                return $this->redirect(['sdetc/orderdetail','couponId'=>$couponres[0]['id']]);
            }
        }

        return $this->redirect(['caruorder/index']);
    }

    /**
     * 申请设备
     * @return array|string
     * @throws \yii\db\Exception
     */
    public function actionEquipment()
    {
        $this->site_title = 'ETC设备申请';
        $request = Yii::$app->request;
        if($request->isPost){
            $trans = Yii::$app->db->beginTransaction();
            $post = $request->post();
            try{
                //rules验证
                $etcModel = new EtcreceivingForm();
                $data['EtcreceivingForm'] = $post;
                $etcModel->load($data);
                if(!$etcModel->validate()) {
                    //获取验证错误信息并循环输出
                    $errors = $etcModel->errors;
                    $msg = '';
                    foreach ($errors as $val) {
                        if (is_array($val)) {
                            foreach ($val as $v) {
                                $msg .= $v . PHP_EOL;
                            }
                        } else {
                            $msg .= $val . PHP_EOL;
                        }
                    }
                    throw new \Exception($msg);
                }
                //写入主订单
                $mainOrder = $this->main_order($this->user['uid'], $etcModel->couponId, '1');
                if($mainOrder == false){
                    throw new \Exception('主订单写入失败');
                }
                //使用卡券
                $couponObj = new CarCouponAction($this->user);
                $useCoupon = $couponObj->useCoupon($etcModel->couponId);
                if($useCoupon == false){
                    throw new \Exception($couponObj->msg);
                }
                $now = time();
                $insertData = [
                    'uid' => $this->user['uid'],
                    'username' => $etcModel->name,
                    'coupon_id' => $etcModel->couponId,
                    'mobile' => $etcModel->mobile,
                    'province' => $etcModel->province,
                    'city' => $etcModel->city,
                    'district' => $etcModel->district,
                    'address' => $etcModel->address,
                    'is_receiving' => 0, //发货状态
                    'main_id' => $mainOrder['id'],//主订单id
                    'order_sn' => $mainOrder['order_no'],
                    'status' => 0,
                    'c_time' => $now,
                    'u_time' => $now
                ];

                //写入ETC订单
                $res = (new CarEtcorder())->myInsert($insertData);
                if(!$res){
                    throw new \Exception('数据写入订单失败');
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);
        }
        $couponId = $request->get('couponId');

        return $this->render('equipment',['couponId'=>$couponId,'mobile'=>$this->user['mobile']]);
    }


    /**
     * ETC订单详情
     * @return string
     */
    public function actionOrderdetail()
    {
        $this->menuActive = 'caruorder';
        $request = Yii::$app->request;
        $couponId = $request->get('couponId',null);
        $orderId = $request->get('orderId',null);
        $etcModel = new CarEtcorder();
        $map = [
            'uid' => $this->user['uid'],
            'is_del' => 0
        ];
        if($orderId){
            $map['id'] = $orderId;
        }
        if($couponId){
            $map['coupon_id'] = $couponId;
        }
        $etcOrder = $etcModel->select('*',$map)->one();
        if(!$etcOrder){
            return $this->redirect(Url::to(['caruser/coupon']));
        }
        //如果已经发货且未确认收货就查询快递信息
        if($etcOrder['is_receiving'] == 1 && $etcOrder['exp_number'] !=0 && $etcOrder['status']==0){
            $express = (new ShangDongETC())->express('youzheng',$etcOrder['exp_number']);
        }
        //如果是设备待激活状态，加载wx jssdk，跳转小程序激活
        if($etcOrder['status'] == 4){
            $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        }

        return $this->render('order',['etcOrder'=>$etcOrder,'express'=>$express,'alxg_sign'=>$alxg_sign]);
    }

    /**
     * 验证设备编码
     * @return array|string
     */
    public function actionVerifycode()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        if($request->isPost){
            $code = $request->post('code');
            //如果是卡券兑换的ETC设备，会带上订单号，保险公司发放的ETC设备，则直接进入这个页面
            $orderId = $request->post('orderId',null);
            $code = trim($code);
            if(strlen($code) != 8){
               return $this->json(0,'请填入正确的设备编码后8位');
            }
            //查询数据库中的设备码
            $res = (new CarEtcCode())->table()->select()->where('RIGHT(`code`,length(`code`)-3) = :code1',[':code1' => $code])->one();
            if(!$res){
                return $this->json(0,'没有该设备编码');
            }

            //验证信息存入session,用于判断是否验证设备码
            Yii::$app->session->set('etc_verify_code',true);
            $url  = Url::to(['sdetc/apply','id'=>$orderId]);
            return $this->json(1,'','',$url);
        }

        $orderId = $request->get('id',null);
        //如果已经验证过设备，直接跳转到申请审核页面
        $is_verify = $session->get('etc_verify_code');
        if($is_verify){
            return $this->redirect(['sdetc/apply','id'=>$orderId]);
        }
        return $this->render('verifycode',['orderId'=>$orderId]);
    }

    /**
     * etc申请认证
     * @return array|string
     * @throws \yii\db\Exception
     */
    public function actionApply()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $isVerify = $session->get('etc_verify_code');
        $orderId = $request->get('id',null);
        $this->selectCar();
        if($request->post()){
            //验证post数据
            $data['EtcapplyForm'] = $request->post();
            $verifyModel = new EtcapplyForm();
            $verifyModel->load($data);
            $trans = Yii::$app->db->beginTransaction();
            try {
                if(!$verifyModel->validate()) {
                    //数据验证并循环输出错误信息
                    $errors = $verifyModel->errors;
                    $msg = '';
                    foreach ($errors as $val) {
                        if (is_array($val)) {
                            foreach ($val as $v) {
                                $msg .= $v . PHP_EOL;
                            }
                        } else {
                            $msg .= $val . PHP_EOL;
                        }
                    }
                    throw new \Exception($msg);
                }
                $orderModel = new CarEtcorder();
                //如果带有订单id，则先查询订单
                if($orderId){
                    $orderInfo = $orderModel->select('*',['id'=>$orderId,'status'=>0,'is_del'=>0])->one();
                }
                //没有订单信息，则新建主订单
                if(!$orderInfo){
                    //写入主订单
                    $mainOrder = $this->main_order($this->user['uid'], '0', '1');
                    if($mainOrder == false){
                        throw new \Exception('主订单写入失败');
                    }
                }
                //请求数据
                $requestData = [
                    'user_id' => $this->user['uid'],
                    'order_sn' => $orderInfo['order_sn']?:$mainOrder['order_no'],
                    'pay_status' => 1, //0-未支付 1-已支付
                    'user_info' => [
                        'notify_url' => Url::to(['sdetc/notice']),
                        'user_type' => 1, //0-不记名 1-个人用户 2-单位用户
                        'username' => $verifyModel->username,
                        'cert_type' => '1', //证件类型 1-身份证
                        'cert_no' => $verifyModel->cert_no,
                        'link_phone' => $verifyModel->link_phone,
                        'link_address' => $verifyModel->link_address,
                        'contact' => $verifyModel->contact,
                    ],
                    'car_info' => [
                        'plate_no' => $verifyModel->plate_no,
                    ],
                    'picture_info' => [
                        'cert_front' => $verifyModel->cert_front,
                        'cert_back' => $verifyModel->cert_back,
                        'user_hold_cert' => $verifyModel->user_hold_cert,
                        'driving_license_front' => $verifyModel->driving_license_front,
                        'driving_license_back' => $verifyModel->driving_license_back,
                        'car_head' => $verifyModel->car_head,
                    ],
                    'need_receiving' => false,
                ];

                //下单
                $sdEtc = new ShangDongETC();
                $res = $sdEtc->orderSubmit($requestData);
                if($res['response']['code'] == '000002'){
                    throw new \Exception('车牌号为：'.$verifyModel->plate_no.'的车辆已经绑卡，申请失败');
                }
                if($res['response']['code'] == '000003'){
                    throw new \Exception('车辆已有正在处理中的订单，请勿重复提交');
                }

                if($res['response']['code'] != '000000'){
                    throw new \Exception($res['response']['message']);
                }
                $now = time();
                //写入数据
                $orderData = [
                    'uid' => $requestData['user_id'],
                    'username' => $requestData['username'],
                    'cert_type' => $requestData['cert_type'],
                    'cert_no' => $requestData['cert_no'],
                    'link_phone' => $requestData['link_phone'],
                    'link_address' => $requestData['link_address'],
                    'contact' => $requestData['contact'],
                    'plate_no' => $requestData['plate_no'],
                    'cert_front' => $requestData['cert_front'],
                    'cert_back' => $requestData['cert_back'],
                    'user_hold_cert' => $requestData['user_hold_cert'],
                    'driving_license_front' => $requestData['driving_license_front'],
                    'driving_license_back' => $requestData['driving_license_back'],
                    'car_head' => $requestData['car_head'],
                    'main_id' => $orderInfo['main_id']?:$mainOrder['main_id'],
                    'order_sn' => $requestData['order_sn'],
                    'order_id' => $requestData['order_id'],
                    'is_receiving' => $requestData['is_receiving'], //物流状态改为已收货
                    'status' => 1, //订单状态 1：审核中
                    'u_time' => $now
                ];
                //判断订单是否存在，插入或者更新
                if($orderInfo){
                    $result = $orderModel->myUpdate($orderData,['id'=>$orderInfo['id']]);
                }else {
                    $orderData['c_time'] = $now;
                    $result = $orderModel->myInsert($orderData);
                }
                if(!$result){
                    throw new \Exception('订单数据写入失败');
                }

                $trans->commit();
            } catch (\Exception $e){
                $trans->rollBack();
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);

        }

        return $this->render('apply',[
            'isVerify' => $isVerify,
            'orderId' => $orderId,
            'userCar' => $this->userCar
        ]);
    }

    /**
     * 取消订单
     * @return array|\yii\web\Response
     */
    public function actionCancelorder()
    {
        $request = Yii::$app->request;
        if($request->isPost){

            $trans = Yii::$app->db->beginTransaction();
            try {
                $orderId = $request->post('id',null);
                if(!$orderId){
                    throw new \Exception('没有订单id');
                }
                $orderId = trim($orderId);
                $orderModel = new CarEtcorder();
                $order = $orderModel->select('*',['id'=>$orderId,'uid'=>$this->user['uid'],'is_del'=>0])->one();
                if(!$order){
                    throw new \Exception('该订单编号不存在');
                }
                //如果是通过卡券兑换并且尚未发货，则执行卡券恢复
                if(!empty($order['coupon_id']) || $order['is_receiving'] == 0 ){
                    //恢复卡券
                    $couponObj = new CarCouponAction($this->user);
                    $useCoupon = $couponObj->unuseCoupon($order['coupon_id']);
                    if($useCoupon == false){
                        throw new \Exception($couponObj->msg);
                    }
                    //将订单标记为删除
                    $res = $orderModel->myUpdate(['is_del'=>1,'u_time'=>time()],['id'=>$order['id']]);
                    if(!$res){
                        throw new \Exception('订单状态写入失败');
                    }

                }
                //如果已经向ETC机构提交了订单，先向机构取消订单
                if($order['status'] ==1 || $order['status']==4){
                    $params = [
                        'order_id' => $order['order_id'], //ETC机构订单id
                        'user_id' => $order['uid'] //用户uid
                    ];
                    $sdEtc = new ShangDongETC();
                    $res = $sdEtc->cancelOrder($params);
                    if($res['response']['code'] != '000000'){
                        throw new \Exception('订单取消失败,错误代码'.$res['response']['message']);
                    }
                    $res = $orderModel->myUpdate(['status'=>-1,'u_time'=>time()],['id'=>$order['id']]);
                    if(!$res){
                        throw new \Exception('订单状态更新失败');
                    }
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);
        }

        return $this->redirect(['carhome/index']);
    }

    /**
     * 订单状态回调
     * @return string
     */
    public function actionNotice()
    {
        $baseUrl = Yii::$app->request->queryString;
        parse_str($baseUrl,$params);
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $etcObj = new ShangDongETC();
        $this->msg = 'success';   //etc机构定义通知响应
        try{
            $verify = $etcObj->verifySign(json_encode($params),$sign);
            if(!$verify){
                throw new \Exception('sign验证失败');
            }
            $etcModel = new CarEtcorder();
            $res = $etcModel->select('*',['order_id'=>$params['order_id'],'order_sn'=>$params['order_sn'],'is_del'=>0])->one();
            if(!$res){
                throw new \Exception('没有该订单编号');
            }
            //如果审核成功，将订单状态标记为 4：已审核
            if($params['order_status'] == 5){
                $map['status'] = 4;  //订单状态 4：已审核
                $map['card_no'] = $params['card_no'];
            } elseif($params['order_status'] == 12){  //如果已激活，将订单状态标记为 2：成功激活
                $map['status'] = 2;  //订单状态 2：成功激活
            } else {
                $map['status'] = 3;  //订单状态 3：审核失败
                //订单审核失败描述，如果没有则调用订单状态码定义的消息
                $map['order_msg'] = $params['audit_desc']?:CarEtcorder::$etc_status[$params['order_status']];
            }

            $result = $etcModel->myUpdate($map,['id'=>$res['id']]);
            if(!$result){
                throw new \Exception('订单状态写入失败');
            }
        }catch (\Exception $e){

            $this->msg = $e->getMessage();
        }

        return $this->msg;
    }

    /**
     * etc账单详情
     * @return string
     */
    public function actionEtcbill()
    {
        $this->menuActive = 'caruorder';
        $billId = Yii::$app->request->get('id',null);
        $billId = trim($billId);
        if(!is_numeric($billId)){
            return "参数不正确";
        }
        $bill = (new CarEtcBill())->table()->select()->where(['id'=>$billId])->one();

        return $this->render('etcbill',['bill'=>$bill]);
    }

    /**
     * 异步加载账单
     * @return array|string
     */
    public function actionBill()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $post = $request->post();
            $list = (new CarEtcBill())->carBill($this->user['uid'],$post['plate_no'],$post['pages']);
            return $this->json(1,'请求成功',$list);
        }

        return '非法请求';
    }

    /**
     * 图片上传
     * @return array|\yii\web\Response
     */
    public function actionImgupload()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $post = $request->post('img');
            //获取图片
            list($type,$data) = explode(',',$post);
            // 判断类型
            if(strstr($type,'image/jpeg')!=''){
                $ext = '.jpg';
            }elseif(strstr($type,'image/gif')!=''){
                $ext = '.gif';
            }elseif(strstr($type,'image/png')!='') {
                $ext = '.png';
            }

            $path = Yii::$app->basePath.'/../static/upload/sdetc/'.date('Y-m').'/';
            if(!is_dir($path)){
                mkdir($path,0755);
            }
            // 生成的文件路径
            $imgUrl = $path.time().$ext;
            // 生成文件
            file_put_contents($imgUrl, base64_decode($data), true);
            $imgUrl = strstr($imgUrl,'/static');

            return $this->json(1,'ok',$imgUrl);
        }

        return $this->redirect(['carhome/index']);
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
            'order_no' => $obj->create_order_no($uid, 'ETC'),
            'uid' => $uid,
            'type' => ETC,
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
}