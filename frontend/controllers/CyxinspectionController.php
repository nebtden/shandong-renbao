<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\3 0003
 * Time: 14:19
 */

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\CheYiXing;
use common\components\CheYiXingInspection;
use common\components\Juhe;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarInsorder;
use common\models\CarUseraddr;
use common\models\CarUserCarno;
use Yii;
use yii\helpers\ArrayHelper;

class CyxinspectionController extends CloudcarController
{
    public $menuActive = 'carhome';
    public $layout = 'cloudcarv2';
    public $site_title = '代办年检';
    protected $uid;
    protected $user;
    protected $insType = ['1' => '免上线检测', '2' => '上线检测'];
    protected $inspectionStatusArr = ['-1' => '未知', '0' => '未进入预约期', '1' => '可预约办理',
        '2' => '逾期但不足一年', '3' => '逾期且超过一年', '4' => '严重逾期（两次未年检）',
        '5' => '报废（3次未年检）', '13' => '粤牌车逾期超过一年不可办理', '15' => '运营车辆暂未开通办理'];
    protected $uploadStatus = [
        '',
        '<div class="check-status check-ing"><i></i><span>已确认</span></div>',
        '<div class="check-status check-ing"><i></i><span>审核中</span></div>',
        '<div class="check-status check-success"><i></i><span>审核通过</span></div>',
        '<div class="check-status check-failed"><i></i><span>审核失败</span></div>'
    ];
    protected $userCar;
    protected $callbackurl;

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        $user = $this->isLogin();
        $this->user = $user;
        $this->uid = (int)$user['uid'];
        $resCar = (new CarUserCarno())->get_user_bind_car($this->uid);

        $this->userCar = ArrayHelper::index($resCar, 'id');
        $this->callbackurl = Yii::$app->request->getHostInfo() . '/frontend/web/diandian/cxyorderstatus.html';
        return true;
    }

    /**
     * 【1】代办年检首页
     * @return string
     */
    public function actionIndex()
    {
        $couponId = Yii::$app->request->get('couponid', 0);
        Yii::$app->session->set('couponid', $couponId);
        $checkDate = Yii::$app->request->get('checkDate', '');
        Yii::$app->session->set('checkDate', $checkDate);

        $resCar = array();
        foreach ($this->userCar as $k => $v) {
            $isExist = (new CarInsorder())->table()
                ->where(['carid' => $v['id'], 'status' => ORDER_HANDLING])
                ->orwhere(['transactType' => 4, 'carid' => $v['id'],'status'=>ORDER_UNSURE])
                ->one();
            if (!$isExist) {
                $resCar[] = $v;
            };
        }

        return $this->render('index', ['carinfo' => $resCar, 'carjson' => json_encode($this->userCar)]);
    }

    /**
     * 【1-1】ajax获取年检有效期列表
     * @return string
     */
    public function actionChecklist()
    {
        $carArr = $this->userCar;
        $carId = Yii::$app->request->post('carId', 0);
        $carInfo = $carArr[$carId];
        $checkList = (new CheYiXingInspection())->getCheckList($carInfo);
        $res = '';
        if ($checkList['code'] == '0') {
            foreach ($checkList['data'] as $k => $v) {
                $str = $v['default'] ? 'selected' : '';
                $res .= "<option value='{$v['checkDate']}' {$str}>{$v['checkDate']}</option>";
            }
        }
        return $res;
    }

    /**
     * 【2】判断年检类型
     */
    public function actionChecktype()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $carArr = $this->userCar;
        $carId = $request->get('carId', 0);
        $checkDate = $request->get('checkDate', '');
        $couponId = ($request->get('couponid', 0)) ?: $session->get('couponid');
        $session->set('carId', $carId);//保存供选券用
        $session->set('checkDate', $checkDate);//保存供选券用

        $couponres = (new CarCoupon())->table()->orderBy('use_limit_time asc')->where(['uid' => $this->uid, 'status' => COUPON_ACTIVE, 'coupon_type' => INSPECTION])->all();
        $couponinfo = array();
        if ($couponres) {
            $couponId = $couponId ?: $couponres[0]['id'];//优惠券有值就取，没有的，默认取所持券的第一个的id
            $session->set('couponid', $couponId);//选择的券id
            $couponindex = ArrayHelper::index($couponres, 'id');
            $couponinfo = $couponindex[$couponId];
        }
        $carInfo = $carArr[$carId];
        $carInfo['checkDate'] = $checkDate;
        $res = (new CheYiXingInspection())->checkInfo($carInfo);
        if ($res['code'] == 0) {
            $res['data']['insType'] = $this->insType[$res['data']['inspectionType']];
            $insTimeStart = date('Y-m-d', strtotime("-3 month", strtotime($res['data']['checkDate'])));
            $res['data']['insTime'] = $insTimeStart . '至' . $res['data']['checkDate'];
            $res['data']['inspectionStatusString'] = $this->inspectionStatusArr[$res['data']['state']];
            $res['data']['isOK'] = 1;
        } else {
            $res['data']['insType'] = '未知类型';
            $res['data']['insTime'] = '未知';
            $res['data']['inspectionStatusString'] = '数据异常';
            $res['data']['isOK'] = 0;
        }
        $carArr[$carId] = ArrayHelper::merge($carInfo, $res['data']);
        return $this->render('checktype', ['carinfo' => $carArr[$carId], 'coupon' => $couponres, 'couponinfo' => $couponinfo]);
    }

    /**
     * 【3】预约年检
     */
    public function actionPreorder()
    {
        $carArr = $this->userCar;
        $carId = Yii::$app->request->get('carId', 0);
        $transactType = Yii::$app->request->get('transactType', 0);
        $carInfo = $carArr[$carId];
        $carInfo['transactType'] = $transactType;

        $useraddr = (new CarUseraddr())->getUserDefaultAddr($this->uid);
        return $this->render('preorder', ['carinfo' => $carInfo, 'useraddr' => $useraddr]);
    }

    /**
     * 【3-1】查询违章
     * @return string
     */
    public function actionPeccancy()
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $carArr = $cache->get('diandian_car_' . $this->uid);
        $carId = $request->get('carId', 0);
        $type = $request->get('type', 0);
        $pecResult = array();
        if ($type) {
//            $pecResult['data'] = array();
        }
        $carInfo = $carArr[$carId];
        return $this->render('peccancy', ['carinfo' => $carInfo, 'type' => $type, 'pecResult' => $pecResult]);
    }

    /**
     * 【3-2】获得用户的回寄地址列表
     * @return string
     */
    public function actionUseraddr()
    {
        $useraddrs = (new CarUseraddr())->getUserAddr($this->uid);
        return $this->render('useraddr', ['useraddrs' => $useraddrs]);
    }

    /**
     * 【3-3】编辑或新增回寄地址
     */
    public function actionHandleuseraddr()
    {
        $request = Yii::$app->request;
        $addrobj = new CarUseraddr();
        if ($request->isPost) {
            $data = $request->post();
            if ($data['type'] == 'del') {
                $res = $addrobj->delUserAddr($data['id']);
                if ($res) {
                    return $this->json(SUCCESS_STATUS, '操作成功');
                }
            } elseif ($data['type'] == 'setdefault') {
                $res = $addrobj->setUserDefault($data['id'], $this->uid);
                if ($res) {
                    return $this->json(SUCCESS_STATUS, '操作成功');
                }
            } else {
                $data['uid'] = $this->uid;
                $useraddr = $addrobj->getUserDefaultAddr($this->uid);
                if (!$useraddr) {
                    $data['isdefault'] = 1;
                }
                unset($data['type']);
                $res = $addrobj->addUserAddr($data);
                if ($res !== false) {
                    return $this->json(SUCCESS_STATUS, '操作成功');
                }
            }
            return $this->json(ERROR_STATUS, '操作失败');

        } else {
            $addrid = $request->get('id', 0);
            $addrinfo = (new CarUseraddr())->getUserAddr($this->uid, ' id = ' . $addrid, 'one');
            return $this->render('handleuseraddr', ['addrinfo' => $addrinfo]);
        }
    }

    /**
     * 【4】预约办理
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionPresureorder()
    {
        $request = Yii::$app->request;
        $carArr = $this->userCar;
        $carId = $request->post('carId', 0);
        $transactType = $request->post('transactType', 0);
        $coupon_id = (int)Yii::$app->session->get('couponid');
        try {
            $coupon = (new CarCoupon())->table()->where(['id' => $coupon_id, 'uid' => $this->uid, 'status' => COUPON_ACTIVE])->one();
            if (!$coupon) {
                throw new \Exception('无效年检券');
            }
            $carInfo = $carArr[$carId];
            $carInfo['transactType'] = $transactType;
            $carInfo['checkDate'] = Yii::$app->session->get('checkDate');
            $res = (new CheYiXingInspection())->priceInfo($carInfo);
            if (isset($res['code']) && $res['code'] == 0) {

            } else {
                throw new \Exception($res['msg']);
            }

            $trans = Yii::$app->db->beginTransaction();
            $couponObj = new CarCouponAction($this->user);
            $coupon = $couponObj->useCoupon($coupon_id);
            if ($coupon === false) {
                throw new \Exception($couponObj->msg);
            }
            $mainOrder = $this->main_order($this->uid, $coupon_id, $res['data']['orderAmount']);
            if ($mainOrder === false) {
                throw new \Exception('主订单写入失败');
            }
            $useraddr = (new CarUseraddr())->getUserDefaultAddr($this->uid);
            $insdata = [
                'uid' => $this->uid,
                'couponid' => $coupon_id,
                'date_day' => date('Ymd'),
                'date_month' => date('Ym'),
                'm_id' => $mainOrder['id'],
                'orderid' => $mainOrder['order_no'],
                'carid' => $carId,
                'carnum' => $carInfo['card_province'] . $carInfo['card_char'] . $carInfo['card_no'],
                'carphone' => $useraddr['mobile'],
                'uaddrid' => $useraddr['id'],
                'c_time' => time(),
                'amount' => $res['data']['orderAmount'],
                'status' => ORDER_UNSURE,//待确认
                'errmsg' => json_encode($res),
                'company_id' => $coupon['company'],
                'companyid' => $coupon['companyid'],
                'transactType' => $transactType,
                'check_date' => Yii::$app->session->get('checkDate', ''),
            ];
            $car_insorder = new CarInsorder();
            $oid = $car_insorder->myInsert($insdata);
            if (!$oid) {
                throw new \Exception('订单详细信息写入失败');
            }
            //如果是上传资料年检，那么在此时就需要向供应商生成订单，后续才能更新订单的上传文件资料
            if ($transactType >= CheYiXing::$limitFlag) {
                $postdata = array(
                    'exOrderId' => $mainOrder['order_no'],
                    'carNumber' => $carInfo['card_province'] . $carInfo['card_char'] . $carInfo['card_no'],
                    'registerDate' => date('Y-m-d', $carInfo['rg_time']),
                    'checkDate' => Yii::$app->session->get('checkDate'),
                    'name' => $useraddr['name'],
                    'phone' => $useraddr['mobile'],
                    'callbackUrl' => $this->callbackurl,
                    'transactType' => $transactType,
                    'payType' => 0,
                );
                $res = (new CheYiXingInspection())->orderPay($postdata);
                if (isset($res['code']) && $res['code'] == 0) {
                    $data['errmsg'] = json_encode($res);
                    $data['bizorderid'] = $res['data']['orderId'];
                    $data['id'] = $oid;
                    (new CarInsorder())->myUpdate($data);
                } else {
                    throw new \Exception($res['msg']);
                }
            }
            $trans->commit();
            return $this->json(SUCCESS_STATUS, 'ok', ['id' => $mainOrder['id']]);
        } catch (\Exception $e) {
            if (isset($trans)) {
                $trans->rollBack();
            }
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    /**
     * 【5】区分邮寄还是在线
     * @return string
     */
    public function actionPreorderres()
    {
        $this->menuActive = 'caruorder';
        $mid = Yii::$app->request->get('mid', 0);
        $info = (new CarInsorder())->table()->where(['uid' => $this->uid, 'm_id' => $mid])->one();
        if (!$info) {
            return '订单不存在';
        }
        $carArr = $this->userCar;
        $carInfo = $carArr[$info['carid']];
        $carInfo['transactType'] = $info['transactType'];
        $carInfo['checkDate'] = $info['check_date'];


        $useraddr = (new CarUseraddr())->getUserDefaultAddr($this->uid);

        $postdata = [
//            'orderId' =>'',
            'exOrderId' => $info['orderid'],
        ];
        $reqListStatus = 0;

        $mailAddress = array();
        $requirementList = array();
        $res = (new CheYiXingInspection())->priceInfo($carInfo);
        if ($res['code'] == 0) {
            $mailAddress = $res['data']['mailAddress'];
            $requirementList = $res['data']['requirementList'];
        }

        if ($info['transactType'] < CheYiXing::$limitFlag) {//自主寄送(您需要亲自寄送年检资料至车行易业务办理处，资料签收并办结后寄还)
            if ($info['status'] != ORDER_CANCEL && $info['bizorderid']) {
                $res = (new CheYiXingInspection())->getOrder($postdata);
                if ($res['code'] || !isset($res['data']) || !$res['data']) {
                    return $res['msg'];
                }
            }
            $tpl = 'sendmail';
        } else {//上传材料
            $res['data'] = array();
            $res['code'] = -1;
            if ($info['status'] != ORDER_CANCEL) {
                $res = (new CheYiXingInspection())->getOrder($postdata);
                if ($res['code'] || !isset($res['data']) || !$res['data']) {
                    return $res['msg'];
                }
                $requirementList = $res['data']['requirementList'];
            }
            $i2 = 0;
            $i3 = 0;
            $i4 = 0;
            $htmlStr = '';
            foreach ($requirementList as $k => $v) {
                $imgUrl = $v['imgUrl'] ?: '/frontend/web/cloudcarv2/img/camera.png';
                $imgSecUrl = $v['imgSecUrl'] ?: '/frontend/web/cloudcarv2/img/camera.png';
                $confirmType = $v['confirmType'] == 1 ? '<i style="color: red">*</i>' : '';
                if ($v['requirementType'] == 'XSZ') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgUrl . '"><input class="file" name="req_xsz_imgUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>行驶证原件<br />(正本)' . $confirmType . '</span><span class="see-exam blue" data-name="xingshi-front">查看示例图</span>' .
                        '<input type="hidden" name="xsz_imgUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'XSZ') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgSecUrl . '"><input class="file" name="req_xsz_imgSecUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>行驶证原件<br />(副本)' . $confirmType . '</span><span class="see-exam blue" data-name="xingshi-behind">查看示例图</span>' .
                        '<input type="hidden" name="xsz_imgSecUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgSecUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'JQX') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgUrl . '"><input class="file" name="req_jqx_imgUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>交强险保单<br />(副本，有效期内)' . $confirmType . '</span><span class="see-exam blue" data-name="jiaoqiang">查看示例图</span>' .
                        '<input type="hidden" name="jqx_imgUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'CLDJZ') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgUrl . '"><input class="file" name="req_cldjz_imgUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>车辆登记证<br />复印件' . $confirmType . '</span><span class="see-exam blue" data-name="carid">查看示例图</span>' .
                        '<input type="hidden" name="cldjz_imgUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'SFZ') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgUrl . '"><input class="file" name="req_sfz_imgUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>身份证复印件<br />(正面)' . $confirmType . '</span><span class="see-exam blue" data-name="idcard-front">查看示例图</span>' .
                        '<input type="hidden" name="sfz_imgUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'SFZ') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgSecUrl . '"><input class="file" name="req_sfz_imgSecUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>身份证复印件<br />(反面)' . $confirmType . '</span><span class="see-exam blue" data-name="idcard-behind">查看示例图</span>' .
                        '<input type="hidden" name="sfz_imgSecUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgSecUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'CCS') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgUrl . '"><input class="file" name="req_ccs_imgUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>车船税发票<br />交强险缴税记录' . $confirmType . '</span><span class="see-exam blue" data-name="cheshui">查看示例图</span>' .
                        '<input type="hidden" name="ccs_imgUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'SCSFZ') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgUrl . '"><input class="file" name="req_scsfz_imgUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>手持身份证<br />正面照片' . $confirmType . '</span><span class="see-exam blue" data-name="benrenidcard">查看示例图</span>' .
                        '<input type="hidden" name="scsfz_imgUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgUrl'] . '"></li>';
                }
                if ($v['requirementType'] == 'XSZFYJ') {
                    $htmlStr .= '<li><div class="examples"><img src="' . $imgUrl . '"><input class="file" name="req_xszfyj_imgUrl" type="file" onchange="handleFiles(this)" />' .
                        $this->uploadStatus[$v['status']] .
                        '</div><span>行驶证<br />复印件' . $confirmType . '</span><span class="see-exam blue" data-name="benrenidcard">查看示例图</span>' .
                        '<input type="hidden" name="xszfyj_imgUrl" data-confirm="' . $v['confirmType'] . '" value="' . $v['imgUrl'] . '"></li>';
                }

                if ($v['status'] == 4) {
                    $i4++;
                } elseif ($v['status'] == 3) {
                    $i3++;
                } elseif ($v['status'] == 2) {
                    $i2++;
                }
            }
            if ($i4 > 0 && $i2 == 0) {
                $reqListStatus = 4;
            } elseif ($i2 > 0) {
                $reqListStatus = 2;
            } elseif ($i3 == count($requirementList)) {//全通过才是审核通过
                $reqListStatus = 3;
            }

            $tpl = 'uploadimg';
            if ($reqListStatus == 3) {
                $updata['id'] = $info['id'];
                $updata['status'] = ORDER_HANDLING;
                (new CarInsorder())->myUpdate($updata);
                return $this->redirect(['orderdetail', 'mid' => $mid]);
            }
        }

        return $this->render($tpl, [
            'carinfo' => $carInfo,
            'info' => $info,
            'useraddr' => $useraddr,
            'mailAddress' => $mailAddress,
            'requirementList' => $requirementList,
            'reqListStatus' => $reqListStatus,
            'htmlStr' => $htmlStr,
        ]);
    }

    /**
     * 【5-1】生成主订单
     * @param $uid
     * @param $coupon_id
     * @param $coupon_amount
     * @return array|bool
     */
    protected function main_order($uid, $coupon_id, $coupon_amount)
    {
        $obj = new Car_paternalor();
        $data = [
            'order_no' => $obj->create_order_no($uid, 'INS'),
            'uid' => $uid,
            'type' => INSPECTION,
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
     * 【6】确定寄出材料
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionSubmitorder()
    {
        $data = Yii::$app->request->post();
        $trans = Yii::$app->db->beginTransaction();
        $carArr = $this->userCar;
        try {
            $insorder = (new CarInsorder())->table()->where(['id' => $data['id']])->one();
            $carInfo = $carArr[$insorder['carid']];
            $useraddr = (new CarUseraddr())->getUserDefaultAddr($this->uid);
            $postdata = array(
                'exOrderId' => $insorder['orderid'],
                'carNumber' => $carInfo['card_province'] . $carInfo['card_char'] . $carInfo['card_no'],
                'registerDate' => date('Y-m-d', $carInfo['rg_time']),
                'checkDate' => $insorder['check_date'],
                'name' => $useraddr['name'],
                'phone' => $useraddr['mobile'],
                'callbackUrl' => $this->callbackurl,
                'transactType' => $insorder['transactType'],
                'payType' => 0,
            );
            $res = (new CheYiXingInspection())->orderPay($postdata);

            if (isset($res['code']) && $res['code'] == 0) {
                $data['errmsg'] = json_encode($res);
                $data['bizorderid'] = $res['data']['orderId'];
                $data['status'] = ORDER_HANDLING;
                (new CarInsorder())->myUpdate($data);
            } else {
                throw new \Exception($res['msg']);
            }
            $trans->commit();

            //使用优惠券回调通知。业务：不管用户办理成功与否，都不会返还优惠券，所以就在提交订单时就回调通知
//            (new EJin())->useCouponNotice($insorder);

            return $this->json(SUCCESS_STATUS, '操作成功');

        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    /**
     * 【6-1】填写单号
     */
    public function actionExpress()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            $trans = Yii::$app->db->beginTransaction();
            try {
//                $insorder = (new CarInsorder())->table()->where(['id' => $data['id']])->one();
//                $postdata['payMethod'] = DianDianInspection::$paytype['alipay'];
//                $postdata['orderId'] = $insorder['bizorderid'];
//                $postdata['payPrice'] = $insorder['amount'];
//                $res = (new DianDianInspection($this->uid))->orderPre($postdata);
//                if (isset($res['success']) && $res['success']) {
//                    $data['errmsg'] = json_encode($res);
//                    $data['status'] = ORDER_HANDLING;
//                    (new CarInsorder())->myUpdate($data);
//                } else {
//                    throw new \Exception($res['message']);
//                }
                $res = (new CarInsorder())->myUpdate($data);
                if ($res === false) {
                    throw new \Exception('操作失败');
                }
                $trans->commit();
                return $this->json(SUCCESS_STATUS, '操作成功');

            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->json(ERROR_STATUS, $e->getMessage());
            }
        } else {
            $id = $request->get('id', 0);
            $express = Juhe::deliver();
            $expressinfo = array();
            if ($express['resultcode'] == 200) {
                $expressinfo = $express['result'];
            }
            return $this->render('express', ['id' => $id, 'expressinfo' => $expressinfo]);
        }
    }

    /**
     * 【6-2】取消订单
     */
    public function actionCancelorder()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $orderinfo = (new CarInsorder())->table()->where(['id' => $id])->one();
        $trans = Yii::$app->db->beginTransaction();
        try {
            $updata['id'] = $id;
            $updata['status'] = ORDER_CANCEL;
            $res = (new CarInsorder())->myUpdate($updata);
            if ($res === false) {
                throw new \Exception('订单取消失败');
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

    /**
     * 【6-3】上传资料类型的取消订单
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionCancelorderimg()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $orderinfo = (new CarInsorder())->table()->where(['id' => $id])->one();
        $trans = Yii::$app->db->beginTransaction();
        try {
            $postdata = array(
                'orderId' => $orderinfo['bizorderid'],
                'remark' => '我就是要退款',
            );
            $res = (new CheYiXingInspection())->cancelOrder($postdata);
            if (isset($res['code']) && $res['code'] == 0) {
                if ($res['data']['resultFlag'] == 1) {
                    $updata['id'] = $id;
                    $updata['status'] = ORDER_CANCELING;
                    $res = (new CarInsorder())->myUpdate($updata);
                    if ($res === false) {
                        throw new \Exception('申请失败');
                    }
                } else {
                    throw new \Exception('申请失败');
                }
            } else {
                throw new \Exception($res['msg']);
            }
            $trans->commit();
            return $this->json(SUCCESS_STATUS, '申请成功');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    /**
     * 【7】订单详情
     */
    public function actionOrderdetail()
    {
        $this->menuActive = 'caruorder';
        $mid = Yii::$app->request->get('mid', 0);
        $info = (new CarInsorder())->table()->where(['uid' => $this->uid, 'm_id' => $mid])->one();
        if (!$info) {
            return '订单不存在';
        }
        $postdata = [
//            'orderId' =>'',
            'exOrderId' => $info['orderid'],
        ];
        $odetail['data'] = array();
        if ($info['status'] != ORDER_CANCEL) {
            $odetail = (new CheYiXingInspection())->getOrder($postdata);
            if ($odetail['code'] || !isset($odetail['data']) || !$odetail['data']) {
                return $odetail['msg'];
            }
        }
        $mainorder = (new Car_paternalor())->table()->where(['id' => $mid, 'type' => INSPECTION])->one();

        $info['couponprice'] = $mainorder['coupon_amount'];
        $info['inspectionType'] = $this->insType[$info['transactType'] <= CheYiXing::$limitFlag ? 1 : 2];
        $info['c_time'] = date('Y-m-d H:i:s', $info['c_time']);
        $info = ArrayHelper::merge($info, $odetail['data']);
        $useraddr = (new CarUseraddr())->getUserDefaultAddr($this->uid);
        //物流信息查询
//        $expressinfo = array();
//        if ($info['expresscom'] && $info['expressno']) {
//            $params['com'] = $info['expresscom'];
//            $params['no'] = $info['expressno'];
//            $resinfo = Juhe::deliver($params);
//            $expressinfo['reason'] = $resinfo['reason'];
//            $expressinfo['result'] = $resinfo['result'];
//            $expressinfo['result']['list'] = array_reverse($resinfo['result']['list']);
//        }
        return $this->render('orderdetail', ['info' => $info, 'useraddr' => $useraddr]);
    }

    public function actionUploadimg()
    {
        $picArr = ['png', 'jpg', 'jpeg'];
        $request = Yii::$app->request;
        $data = $request->post();
        $k = $data['attrName'];
        $v = $data['picValue'];
        $orderinfo = (new CarInsorder())->table()->where(['id' => $data['id']])->one();
        $karr = explode('_', $k);//$k="req_sfz_imgUrl";
        $ziliaoType = strtoupper($karr[1]);
        $requirementArr[$ziliaoType]['requirementType'] = $ziliaoType;
        if ($v) {
            $base64_string = explode(',', $v); //截取data:image/png;base64, 这个逗号后的字符
            $data = base64_decode($base64_string[1]);
            preg_match_all('/data:image\/(.*);base64/', $base64_string[0], $arr);
            if (in_array($arr[1][0], $picArr)) {
                $imgurl = '/frontend' . $this->uploadImg($orderinfo['orderid'], $data, $arr[1][0], $k);
                return $this->json(SUCCESS_STATUS, $request->getHostInfo() . $imgurl);
            } else {
                return $this->json(ERROR_STATUS, '不支持的格式');
            }
        } else {
            return $this->json(ERROR_STATUS, '错误操作');
        }
    }

    /**
     * 提交上传资料审核年检
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionUpdateimg()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', 0);
        $orderinfo = (new CarInsorder())->table()->where(['id' => $id])->one();
        $data = $_POST;
        $requirementArr = array();
        foreach ($data as $k => $v) {
            $karr = explode('_', $k);//$k="sfz_imgSecUrl";
            $ziliaoType = strtoupper($karr[0]);
            $requirementArr[$ziliaoType]['requirementType'] = $ziliaoType;
            $requirementArr[$ziliaoType][$karr[1]] = $v;
        }
        //把关联数据改成索引数组
        foreach ($requirementArr as $k => $v) {
            $requirements[] = $v;
        }

        $trans = Yii::$app->db->beginTransaction();
        try {
            $postdata = array(
                'orderId' => $orderinfo['bizorderid'],
                'exOrderId' => $orderinfo['orderid'],
                'requirements' => $requirements
            );
            $res = (new CheYiXingInspection())->updateOrderRequirement($postdata);
            if (isset($res['code']) && $res['code'] == 0) {
                if ($res['data']['resultFlag'] == 1) {
                    $updata['id'] = $id;
                    $updata['img_url_json'] = json_encode($requirementArr);
                    $res = (new CarInsorder())->myUpdate($updata);
                    if ($res === false) {
                        throw new \Exception('更新失败');
                    }
                } else {
                    throw new \Exception('请求失败');
                }
            } else {
                throw new \Exception($res['msg']);
            }
            $trans->commit();
            return $this->json(SUCCESS_STATUS, '提交成功');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
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
        $path = '/web/upload/cxy/' . date('Y-m') . '/' . $file . '/';
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
}