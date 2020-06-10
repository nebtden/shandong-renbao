<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\4\24 0024
 * Time: 12:59
 */

namespace frontend\controllers;


use common\components\CarCouponAction;
use common\components\ShengDaSegway;
use common\models\Car_coupon_explain;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarSegorder;
use common\models\CarUserCarno;
use Yii;
use yii\helpers\ArrayHelper;

class SegwayController extends CloudcarController
{
    const STATIC_PATH = '/frontend/web/cloudcarv2/segway';
    const TELPHONE = '400-8801-768转3';
    public $menuActive = 'carhome';
    public $layout = 'segway';
    public $site_title = '代步车';
    protected $uid;
    protected $user;
    protected $userCar;


    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        $user = $this->isLogin();
        $this->user = $user;
        $this->uid = (int)$user['uid'];
        $resCar = (new CarUserCarno())->get_user_bind_car($this->uid);
        $this->userCar = ArrayHelper::index($resCar, 'id');
        return true;
    }

    public function actionIndex()
    {
        $this->site_title = '代步车预约';
        $couponId = (Yii::$app->request->get('couponid', 0));
        $segId = (Yii::$app->session->get('segid', 0));
        if (!$segId) {
            $this->cleanOrder();
        }
        $couponres = (new CarCoupon())->table()->orderBy('use_limit_time asc')
            ->where(['uid' => $this->uid, 'status' => COUPON_ACTIVE, 'coupon_type' => SEGWAY])->all();
        $use_text = (new Car_coupon_explain())->get_use_text();
        $segInfo = (new CarSegorder())->table()->where(['uid' => $this->uid, 'id' => $segId])->one();
        $couponinfo = array();
        if ($couponres) {
            $couponId = $couponId ?: ($segInfo ? $segInfo['couponid'] : $couponres[0]['id']);//优惠券有值就取，没有的，默认取所持券的第一个的id
            $couponindex = ArrayHelper::index($couponres, 'id');
            $couponinfo = $couponindex[$couponId];
        }

        $resCar = array();
        foreach ($this->userCar as $k => $v) {
            $isExist = (new CarSegorder())->table()
                ->where(['carid' => $v['id']])
                ->andwhere(['!=', 'status', ORDER_CANCEL])
                ->andwhere(['!=', 'status', ORDER_SUCCESS])
                ->andwhere(['!=', 'status', 0])
                ->one();
            if (!$isExist) {
                $resCar[] = $v;
            };
        }

        return $this->render('index', [
            'carinfo' => $resCar,
            'couponinfo' => $couponinfo,
            'seginfo' => $segInfo,
            'couponres' => $couponres,
            'use_text' => $use_text
        ]);
    }

    /**
     * 上传图片
     * @return array
     */
    public function actionUploadimg()
    {
        $picArr = ['png', 'jpg', 'jpeg'];
        $request = Yii::$app->request;
        $data = $request->post();
        $k = $data['attrName'];
        $v = $data['picValue'];
        if ($v) {
            $base64_string = explode(',', $v); //截取data:image/png;base64, 这个逗号后的字符
            $data = base64_decode($base64_string[1]);
            preg_match_all('/data:image\/(.*);base64/', $base64_string[0], $arr);
            if (in_array($arr[1][0], $picArr)) {
                $imgurl = '/frontend' . $this->uploadImg($this->uid, $data, $arr[1][0], date('YmdHis') . $k);
                return $this->json(SUCCESS_STATUS, $imgurl);
            } else {
                return $this->json(ERROR_STATUS, '不支持的格式');
            }
        } else {
            return $this->json(ERROR_STATUS, '错误操作');
        }
    }

    public function actionSaveone()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        try {
            $coupon = (new CarCoupon())->table()->where(['id' => $data['couponid'], 'uid' => $this->uid, 'status' => COUPON_ACTIVE])->one();
            if (!$coupon) {
                throw new \Exception('无效年检券');
            }
            $trans = Yii::$app->db->beginTransaction();
            $id = (Yii::$app->session->get('segid', 0));
            $insert = [
                'uid' => $this->uid,
                'couponid' => $data['couponid'],
                'carid' => $data['carId'],
                'img_url_json' => json_encode(['policyPic1' => $data['policyPic1'], 'policyPic2' => $data['policyPic2']]),
                'policyNum' => $data['policyNum']
            ];
            if ($id) {
                $insert['id'] = $id;
                (new CarSegorder())->myUpdate($insert);
            } else {
                $id = (new CarSegorder())->myInsert($insert);
            }
            if (!$id) {
                throw new \Exception('数据保存失败');
            }
            Yii::$app->session->set('segid', $id);
            $trans->commit();
            return $this->json(SUCCESS_STATUS, 'ok');
        } catch (\Exception $e) {
            if (isset($trans)) {
                $trans->rollBack();
            }
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    public function actionAppointment()
    {
        $this->site_title = '代步车预约';
        $provinceList = (new ShengDaSegway())->getProvince();
        return $this->render('appointment', [
            'provinceList' => $provinceList,
        ]);
    }

    public function actionGetcity()
    {
        $pcode = Yii::$app->request->post('pcode', 0);
        $getData = [
            'provinceCode' => $pcode
        ];
        $cityList = (new ShengDaSegway())->getCity($getData);
        $resStr = '';
        foreach ($cityList as $k => $v) {
            $resStr .= '<option value="' . $v->cityCode . '">' . $v->cityName . '</option>';
        }
        return $resStr;
    }

    public function actionGetcounty()
    {
        $ccode = Yii::$app->request->post('ccode', 0);
        $getData = [
            'cityCode' => $ccode
        ];
        $countyList = (new ShengDaSegway())->getRegion($getData);
        $resStr = '';
        foreach ($countyList as $k => $v) {
            $resStr .= '<option value="' . $v->countyCode . '">' . $v->countyName . '</option>';
        }
        return $resStr;
    }

    public function actionGetstore()
    {
        $pcode = Yii::$app->request->post('pcode', 0);
        $ccode = Yii::$app->request->post('ccode', 0);
        $rcode = Yii::$app->request->post('rcode', 0);
        $data = [
            'secondarySupplierType' => 1,
            'provinceCode' => $pcode,
            'cityCode' => $ccode,
            'countyCode' => $rcode,
            'pageNum' => 1,
            'pageSize' => 50,
        ];
        $storeList = (new ShengDaSegway())->findStore($data);
        $resStr = '';
        foreach ($storeList as $k => $v) {
            $resStr .= '<option value="' . $v->secondarySupplierCode . '">' . $v->secondarySupplierName . '</option>';
        }
        return $resStr;
    }

    public function actionSaveorder()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $data['id'] = Yii::$app->session->get('segid', 0);
        try {
            $resList = Yii::$app->redis->hmget('storeList', $data['pcode'] . '_' . $data['ccode'] . '_' . $data['rcode']);
            $storeList = ArrayHelper::index(json_decode($resList[0]), 'secondarySupplierCode');
            $currentStoreInfo = $storeList[$data['prestorecode']];

            $trans = Yii::$app->db->beginTransaction();
            if (strtotime($data['pre_u_time']) <= time() + 60 * 60 * 3) {
                throw new \Exception('只能预约3小时后的时间');
            }
            if (strtotime($data['pre_r_time']) <= strtotime($data['pre_u_time'])) {
                throw new \Exception('还车时间不能小于预约时间');
            }
            $couponObj = new CarCouponAction($this->user);
            $couponInfo = (new CarSegorder())->table()->where(['id' => $data['id'], 'uid' => $this->uid, 'm_id' => 0])->one();
            $coupon = $couponObj->useCoupon($couponInfo['couponid']);
            if ($coupon === false) {
                throw new \Exception($couponObj->msg);
            }
            $mainOrder = $this->main_order($this->uid, $couponInfo['couponid'], 1);
            if ($mainOrder === false) {
                throw new \Exception('主订单写入失败');
            }
            $updata = [
                'id' => $data['id'],
                'date_day' => date('Ymd'),
                'date_month' => date('Ym'),
                'm_id' => $mainOrder['id'],
                'orderid' => $mainOrder['order_no'],
                'liaison' => $data['liaison'],
                'telphone' => $data['phoneNum'],
                'preprocode' => $data['pcode'],
                'prepro' => $currentStoreInfo->provinceName,//
                'precitycode' => $data['ccode'],
                'precity' => $currentStoreInfo->cityName,//
                'precountycode' => $data['rcode'],//
                'precounty' => $currentStoreInfo->countyName,//
                'prestorecode' => $currentStoreInfo->secondarySupplierCode,//
                'prestore' => $currentStoreInfo->secondarySupplierName,//
                'pre_u_time' => strtotime($data['pre_u_time']),
                'pre_r_time' => strtotime($data['pre_r_time']),
                'c_time' => time(),
                'status' => ORDER_UNSURE,//待确认
                'company_id' => $coupon['company'],
                'companyid' => $coupon['companyid'],
            ];
            $res = (new CarSegorder())->myUpdate($updata);
            if ($res === false) {
                throw new \Exception('订单写入失败');
            }
            //向盛大下单
            $data['orderid'] = $mainOrder['order_no'];
            $data['ticketCode'] = $coupon['coupon_sn'];
            $res = $this->createSdOrder($currentStoreInfo, $data);
            if ($res['resultCode'] !== '0000') {
                throw new \Exception($res['resultDesc']);
            }
            Yii::$app->session->set('segid', 0);
            $trans->commit();
            return $this->json(SUCCESS_STATUS, 'ok', ['id' => $data['id']]);
        } catch (\Exception $e) {
            if (isset($trans)) {
                $trans->rollBack();
            }
            return $this->json(ERROR_STATUS, $e->getMessage());
        }

    }

    /**
     * 预约订单
     */
    public function actionPreorder()
    {
        $this->layout = false;
        $oid = Yii::$app->request->get('id', 0);
        $info = (new CarSegorder())->table()->where(['uid' => $this->uid, 'id' => $oid])->one();
        if (!$info) {
            return '订单不存在';
        }
        return $this->render('preorder', ['info' => $info]);
    }

    /**
     * 订单详情
     * @return string
     */
    public function actionOrderdetail()
    {
        $this->menuActive = 'caruorder';
        $mid = Yii::$app->request->get('mid', -1);
        $info = (new CarSegorder())->table()->where(['uid' => $this->uid, 'm_id' => $mid])->one();
        if (!$info) {
            return '订单不存在';
        }
        $coupon = (new CarCoupon())->table()->where(['id' => $info['couponid'], 'uid' => $this->uid])->one();
        $info['couponname'] = $coupon['name'];
        return $this->render('orderdetail', ['info' => $info]);
    }

    /**
     * 取消订单
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionCancelorder()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $orderinfo = (new CarSegorder())->table()->where(['id' => $id])->one();
        $trans = Yii::$app->db->beginTransaction();
        try {
            //先向盛大取消订单
            $postData = [
                'externalTerminalOrderNo' => $orderinfo['orderid'],
                'whyCancel' => '下错了',
            ];
            $res = (new ShengDaSegway())->cancelOrder($postData);
            if ($res['resultCode'] !== '0000') {
                throw new \Exception($res['resultDesc']);
            }
            $updata['id'] = $id;
            $updata['status'] = ORDER_CANCEL;
            $res = (new CarSegorder())->myUpdate($updata);
            if ($res === false) {
                throw new \Exception('订单取消出错');
            }
            //恢复券
            $r = (new CarCouponAction())->unuseCoupon($orderinfo['couponid']);
            if ($r === false) {
                throw new \Exception('优惠券出错');
            }
            $trans->commit();
            return $this->json(SUCCESS_STATUS, '订单取消成功');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    protected function main_order($uid, $coupon_id, $coupon_amount)
    {
        $obj = new Car_paternalor();
        $data = [
            'order_no' => $obj->create_order_no($uid, 'SEG'),
            'uid' => $uid,
            'type' => SEGWAY,
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

    protected function createSdOrder($obj, $data)
    {
        $endTime = strtotime($data['pre_r_time']);
        $preTime = strtotime($data['pre_u_time']);
        $postData = [
            'arriveLatitude' => $obj->latitude,
            'arriveLongitude' => $obj->longitude,
            'bigServiceTypeCode' => 'S0003',
            'smallServiceTypeCode' => $data['smallService'],
            'cityCode' => $obj->cityCode,
            'country' => 'CHN',
            'countyCode' => $obj->countyCode,
            'customerName' => $data['liaison'],
            'customerPhone' => $data['phoneNum'],
            'depLocation' => $obj->secondarySupplierName,
            'desCityCode' => $obj->cityCode,
            'desCountyCode' => $obj->countyCode,
            'desProvinceCode' => $obj->provinceCode,
            'destination' => $obj->secondarySupplierName,
            'externalTerminalOrderNo' => $data['orderid'],
            'latitude' => $obj->latitude,
            'longitude' => $obj->longitude,
            'provinceCode' => $obj->provinceCode,
            'remark' => '代步下单',
            'reservationTime' => date('Y-m-d H:i:s', $preTime),
            'secondarySupplierCode' => $obj->secondarySupplierCode,
            'endDate' => date('Y-m-d H:i:s', $endTime),
            'useDays' => ceil(($endTime - $preTime) / (60 * 60 * 24)),
            'organizationCode' => 'OR0170',
            'bankCardType' => 'DFMR7469',
            "ticketCode" => $data['ticketCode'],
            "openCaseStyle" => "3"
        ];
        $res = (new ShengDaSegway())->createOrder($postData);
        return $res;
    }


    /**
     * 上传保存图片
     * @param $type
     * @param $data
     * @param $fix
     * @return string
     */
    private function uploadImg($file, $data, $fix, $fileName)
    {
        $path = '/web/upload/segway/' . date('Y-m') . '/' . $file . '/';
        $savePath = Yii::$app->getBasePath() . $path;
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath, 0777);
        }
        $imgName = $fileName . '.' . $fix;
        $url = $savePath . $imgName;
        file_put_contents($url, $data);
        return $path . $imgName;
    }

    private function cleanOrder()
    {
        $orderList = (new CarSegorder())->table()->where(['m_id' => 0])->all();
        foreach ($orderList as $k => $v) {
            $imgArr = json_decode($v['img_url_json']);
            @unlink(Yii::$app->getBasePath() . '/..' . $imgArr->policyPic1);
            @unlink(Yii::$app->getBasePath() . '/..' . $imgArr->policyPic2);
            (new CarSegorder())->myDel(['id' => $v['id']]);
        }
    }
}