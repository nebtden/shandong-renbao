<?php
/*
 * 洗车商户中心
 * @time 2019/3/14
 */

namespace frontend\controllers;

use common\models\Area;
use common\models\Car_washarea;
use common\models\Fans;
use common\models\Wash_payee;
use common\models\Wash_shop;
use common\models\Wash_withdrawals;
use common\models\WashOrder;
use yii\helpers\Url;
use common\components\W;
use yii;
use common\components\Helpper;
use common\components\AlxgBase;

class CarwashshopController extends CloudcarController
{
    public $menuActive = 'caruser';
    public $layout = "cloudcarv2";
    public $site_title = '洗车商户中心';
    protected $user;
    protected $status = 1;
    protected $msg = 'Ok';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        $this->user = $this->isLogin();
        return  true;
    }

    private function log($type)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/washshop/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'status:' . $this->status . "\n");
        fwrite($f, 'post_body:' . yii::$app->getRequest()->getRawBody() . "\n");
        fwrite($f, 'return:' . $this->msg . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }

    private function selectShop($select,$where)
    {
        $washShop = (new Wash_shop())->select($select,$where)->one();
        if(!$washShop){
            header('Content-type: text/html;charset=utf-8');
            echo '<script language="JavaScript">;alert("您还没申请成为商户或申请未通过");location.href="/frontend/web/carwashshop/index.html";</script>;';
            exit;
        }
        return $washShop;
    }

    /**
     * 洗车商户中心
     * @return string|yii\web\Response
     */
    public function actionIndex()
    {
        //更新session中pid,避免删除的子账号还有权限
        $this->autoLogin();
        $this->user = $this->isLogin();

        $where = 'id ='.$this->user['sid'].'';
        $washShop = (new Wash_shop())
            ->select('id,uid,shop_name,shop_pic,shop_tel,shop_address,service_num,gross_income,amount,refuse_reason,shop_status',
            $where)->one();
        //如果当前用户属于该门店的子账号，直接跳转核销界面
        if($washShop  && $this->user['uid'] != $washShop['uid']){
            return $this->redirect(Url::to(['carwashshop/verification']));
        }
        return $this->render('index',['washShop' => $washShop]);
    }

    //获取验证码
    public function actionSmscode(){
        $request = Yii::$app->request;
        if($request->isPost){
            $mobile = $request->post('mobile',null);
            if(!$mobile){
                return $this->json(0,'请输入您的手机号码！');
            }
            $f = W::sendSms($mobile);

            if(!$f)return $this->json(0, '验证码发送失败，请重试！');
            return $this->json(1);
        }
        return '非法访问';
    }

    /**
     * 门店申请认证
     * @return array|string
     * @throws yii\db\Exception
     */
    public function actionApply()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $data = $request->post();
            $trans = Yii::$app->db->beginTransaction();
            try {
                //门店数据验证
                if(!$data['shop_pic']){
                    throw new \Exception('请上传门店图片');
                }

                if(!$data['shop_name'] || strlen($data['shop_name'])>50){
                    throw new \Exception('请输入合理的门店名称');
                }
                if(!$data['province'] || !$data['city'] || !$data['area']){
                    throw new \Exception('请选择省市区');
                }
                if(!$data['shop_address'] || strlen($data['shop_address'])>150){
                    throw new \Exception('请输入合理的详细地址');
                }
                if(!$data['lng'] || !$data['lat']){
                    throw new \Exception('请定位导航地址');
                }
                if(!$data['shop_credit_code'] || strlen($data['shop_credit_code'])>18){
                    throw new \Exception('请输入合理的统一社会信用代码');
                }
                $data['shop_register_time']=strtotime($data['shop_register_time']);
                if(!$data['shop_register_time'] || $data['shop_register_time'] > time()){
                    throw new \Exception('请选择正确的注册时间');
                }
                if(!$data['shop_tel'] || strlen($data['shop_tel'])>12){
                    throw new \Exception('请输入正确的联系电话');
                }
                if(!$data['start_time'] || strlen($data['start_time'])>20){
                    throw new \Exception('请输入正确的营业时间');
                }
                if(!$data['end_time'] || strlen($data['end_time'])>20){
                    throw new \Exception('请输入正确的营业时间');
                }
                if(!$data['mobile'] || !preg_match('/^1[0-9]\d{9}$/',$data['mobile'])){
                    throw new \Exception('请输入正确的手机号码');
                }
                //验证码
                $msg = 'ok';
                if(!Helpper::vali_mobile_code($data['mobile'], $data['code'], $msg)){
                    throw new \Exception($msg);
                }
                //银行账户信息
                $bankData = [
                    'bank_payee_name' =>$data['bank_payee_name'],
                    'bank_payee_account' => $data['bank_payee_account'],
                    'payee_bank' => $data['payee_bank']
                ];
                //支付宝账户信息
                $aipayData = [
                    'aipay_payee_account'=> $data['aipay_payee_account'],
                    'aipay_payee_name' => $data['aipay_payee_name']
                ];
                //删除账户信息，并将数据存入数据库
                unset($data['bank_payee_name'],
                    $data['bank_payee_account'],
                    $data['payee_bank'],
                    $data['aipay_payee_account'],
                    $data['aipay_payee_name'],
                    $data['code']
                );
                $data['uid'] = $this->user['uid'];
                $data['shop_apply_time'] = time();
                $data['shop_status'] = 1;

                $res = (new Wash_shop())->myInsert($data);
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
                //如果银行数据不为空，则验证数据并写入数据库
                if(!empty(implode('',array_values($bankData)))){
                    if(!$bankData['bank_payee_name'] || strlen($bankData['bank_payee_name'])>20){
                        throw new \Exception('请输入合理的开户人名称');
                    }
                    if(!$bankData['bank_payee_account'] || strlen($bankData['bank_payee_account'])>20 || !is_numeric($bankData['bank_payee_account'])){
                        throw new \Exception('请输入正确的银行账户');
                    }
                    if(!$bankData['payee_bank'] || strlen($bankData['payee_bank'])>25){
                        throw new \Exception('请输入正确的开户行');
                    }
                    $bankInsert = [
                        'uid' => $this->user['uid'],
                        'shop_id' => $res,
                        'payee_name' => $bankData['bank_payee_name'],
                        'payee_account' => $bankData['bank_payee_account'],
                        'payee_bank' => $bankData['payee_bank'],
                        'admin_mobile' => $data['mobile'],
                        'c_time' => time(),
                        'status' => 1,
                        'type' => 2  //1支付宝 2银行账户
                    ];
                    $result = (new Wash_payee())->myInsert($bankInsert);
                    if(!$result){
                        throw new \Exception('银行账户信息存入数据库失败');
                    }
                }
                //如果支付宝数据不为空，则验证数据并写入数据库
                if(!empty(implode('',array_values($aipayData)))){
                    if(!$aipayData['aipay_payee_account'] || strlen($aipayData['aipay_payee_account'])>50){
                        throw new \Exception('请输入正确的支付宝账户');
                    }
                    if(!$aipayData['aipay_payee_name'] || strlen($aipayData['aipay_payee_name'])>20){
                        throw new \Exception('请输入正确的真实姓名');
                    }
                    $aipayInsert = [
                        'uid' => $this->user['uid'],
                        'shop_id' => $res,
                        'payee_name' => $aipayData['aipay_payee_name'],
                        'payee_account' => $aipayData['aipay_payee_account'],
                        'payee_bank' => '支付宝',
                        'admin_mobile' => $data['mobile'],
                        'c_time' => time(),
                        'status' => 1,
                        'type' => 1  //1支付宝 2银行账户
                    ];
                    $result = (new Wash_payee())->myInsert($aipayInsert);
                    if(!$result){
                        throw new \Exception('银行账户信息存入数据库失败');
                    }
                }
                $fans = (new Fans())->myUpdate(['sid'=>$res,'mobile'=>$data['mobile']],'id = '.$this->user['uid']);
                if(!$fans){
                    throw new \Exception('账户信息存入数据库失败');
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            return $this->json($this->status,$this->msg);
        }
        $area=new Car_washarea();
        $province=$area->getProvince();
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);

        return $this->render('apply',[
            'province' => $province,
            'alxg_sign' => $alxg_sign
        ]);
    }

    /**
     * 获取省市区数据
     * @return array
     */
    public function actionGetdistrict()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $province = $request->post('province',null);
            $city = $request->post('city',null);
            $district = new Car_washarea();
            $cityList = $district->getCity($province);
            $areaList = $district->getArea($city);
            $data = [
                'city' => $cityList,
                'area' => $areaList
            ];
            return $this->json(1,'ok',$data);
        }

        return '非法请求';
    }

    /**
     * 门店审核记录
     * @return string
     */
    public function actionApplyrecord()
    {
        $washShop = $this->selectShop('id,shop_name,shop_status,shop_address,shop_tel,shop_credit_code,shop_pic,shop_register_time,start_time,end_time,mobile',
            'uid = '.$this->user['uid'].'  AND shop_status=1');
        $account = (new Wash_payee())->select('payee_name,payee_account,payee_bank,type',
            'uid ='.$this->user['uid'].' AND shop_id='.$washShop['id'].'')->all();
        return $this->render('applyrecord',[
            'washShop' => $washShop,
            'account' => $account,
        ]);
    }

    /**
     * 门店详情
     * @return string
     */
    public function actionShopdetail()
    {
        $washShop = $this->selectShop('id,shop_name,shop_status,province,city,area,shop_address,shop_tel,shop_credit_code,shop_pic,lng,lat,shop_register_time,start_time,end_time,mobile',
            'uid = '.$this->user['uid'].'  AND shop_status=2');
        $account = (new Wash_payee())->select('payee_name,payee_account,payee_bank,type',
            'uid ='.$this->user['uid'].' AND shop_id='.$washShop['id'].'')->all();

        //从数组中查询指定的账户
        foreach ($account as $val){
            if($val['type'] == 1){
                $aipay = $val;
            }
            if($val['type'] == 2){
                $bank = $val;
            }
        }
        $district = (new Car_washarea())->getLocationByPid(['province'=>$washShop['province'],'city'=>$washShop['city'],'district'=>$washShop['area']]);
        $district = $district['province']['name'].$district['city']['name'].$district['area']['name'];
        $washShop['district'] = $district;
        return $this->render('shopdetail',[
            'washShop' => $washShop,
            'aipay' => $aipay,
            'bank' => $bank,
        ]);
    }

    /**
     * 修改头像
     * @return array|string
     */
    public function actionChangepic()
    {
        $washShop = $this->selectShop('id,shop_pic','uid = '.$this->user['uid'].'  AND shop_status=2');
        $request = Yii::$app->request;
        if($request->isPost){
            $postData = $request->post();
            try {
                if(!$postData['shop_pic'] || strlen($postData['shop_pic'])>255){
                    throw new \Exception('请上传图片');
                }
                $res = (new Wash_shop())->myUpdate($postData,'id = '.$washShop['id'].'');
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            return $this->json($this->status,$this->msg);
        }
        return $this->render('changepic',[
            'washShop' => $washShop
        ]);
    }

    /**
     * 修改门店地址信息
     * @return array|string
     */
    public function actionChangeaddr()
    {
        $washShop = $this->selectShop('id,shop_address,province,city,area,lng,lat','uid = '.$this->user['uid'].'  AND shop_status=2');
        $request = Yii::$app->request;
        if($request->isPost){
            $data = $request->post();
            try {
                if(!$data['province'] || !$data['city'] || !$data['area']){
                    throw new \Exception('请选择省市区');
                }
                if(!$data['shop_address'] || strlen($data['shop_address'])>150){
                    throw new \Exception('请输入合理的详细地址');
                }
                if(!$data['lng'] || !$data['lat']) {
                    throw new \Exception('请定位导航地址');
                }
                $res = (new Wash_shop())->myUpdate($data,'id = '.$washShop['id'].'');
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            return $this->json($this->status,$this->msg);
        }
        $province = (new Car_washarea())->getProvince();
        $location = (new Car_washarea())->getLocationByPid(['province'=>$washShop['province'],'city'=>$washShop['city'],'district'=>$washShop['area']]);
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        return $this->render('changeaddr',[
            'washShop' => $washShop,
            'province' => $province,
            'location' => $location,
            'alxg_sign' => $alxg_sign
        ]);
    }

    /**
     * 修改电话号码
     * @return array|string
     */
    public function actionChangetel()
    {
        $washShop = $this->selectShop('id,shop_tel,shop_pic','uid = '.$this->user['uid'].'  AND shop_status=2');
        $request = Yii::$app->request;
        if($request->isPost){
            $postData = $request->post();
            try {
                if(!$postData['shop_tel'] || strlen($postData['shop_tel'])>12){
                    throw new \Exception('请输入正确的电话号码');
                }
                if($washShop['shop_tel'] == $postData['shop_tel']){
                    throw new \Exception('输入的号码与当前电话号码重复');
                }
                $res = (new Wash_shop())->myUpdate($postData,'id = '.$washShop['id'].'');
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            return $this->json($this->status,$this->msg);
        }
        return $this->render('changetel',[
            'washShop' => $washShop
        ]);
    }

    /**
     * 修改营业时间
     * @return array|string
     */
    public function actionChangeservicetime()
    {
        $washShop = $this->selectShop('id,start_time,,end_time,shop_pic','uid = '.$this->user['uid'].'  AND shop_status=2');

        $request = Yii::$app->request;
        if($request->isPost){
            $postData = $request->post();
            try {
                if(!$postData['start_time'] || strlen($postData['start_time'])>20){
                    throw new \Exception('请输入正确的开始营业时间');
                }
                if(!$postData['end_time'] || strlen($postData['end_time'])>20){
                    throw new \Exception('请输入正确的结束营业时间');
                }
                if($washShop['start_time'] == $postData['start_time'] && $washShop['end_time'] == $postData['end_time']){
                    throw new \Exception('输入的数据与当前营业时间重复');
                }
                $res = (new Wash_shop())->myUpdate($postData,'id = '.$washShop['id'].'');
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            return $this->json($this->status,$this->msg);
        }
        return $this->render('changeservicetime',[
            'washShop' => $washShop
        ]);
    }

    /**
     * 修改手机号码
     * @return array|string
     */
    public function actionChangemobile()
    {
        $washShop = $this->selectshop('id,mobile,shop_pic','uid = '.$this->user['uid'].'  AND shop_status=2');
        $request = Yii::$app->request;
        if($request->isPost){
            $postData = $request->post();
            try {
                //从session中判断是否验证过旧手机
                if(!Yii::$app->session['checkmobile']){
                    throw new \Exception('请先验证旧手机号码');
                }

                if(!$postData['code'] || !is_numeric($postData['code'])){
                    throw new \Exception('请输入正确的验证码');
                }
                if(!$postData['mobile'] || strlen($postData['mobile'])>20){
                    throw new \Exception('请输入正确的手机号码');
                }
                if($washShop['mobile'] == $postData['mobile']){
                    throw new \Exception('输入的数据与当前手机号码重复');
                }
                $msg = 'ok';
                if(!Helpper::vali_mobile_code($postData['mobile'],$postData['code'],$msg)){
                    throw  new \Exception($msg);
                }
                //删除验证码，存入数据
                unset($postData['code']);
                $res = (new Wash_shop())->myUpdate($postData,'id = '.$washShop['id'].'');
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            unset(Yii::$app->session['checkmobile']);
            return $this->json($this->status,$this->msg);
        }
        return $this->render('changemobile',[
            'washShop' => $washShop
        ]);

    }

    /**
     * 修改账户
     * @return array|string
     */
    public function actionChangeaccount()
    {
        $washShop = $this->selectShop('id,mobile,shop_pic','uid = '.$this->user['uid'].'  AND shop_status=2');

        $request = Yii::$app->request;
        if($request->isPost){
            $postData = $request->post();
            try {
                //从session中判断是否验证过手机
                if(!Yii::$app->session['checkmobile']){
                    throw new \Exception('请先验证手机号码');
                }
                if(!$postData['payee_name'] || strlen($postData['payee_name'])>20){
                    throw new \Exception('请输入合理的开户人名称');
                }
                if($postData['type'] == 2){
                    if(!$postData['payee_account'] || strlen($postData['payee_account'])>20 || !is_numeric($postData['payee_account'])){
                        throw new \Exception('请输入正确的银行账户');
                    }
                }else{
                    if(!$postData['payee_account'] || strlen($postData['payee_account'])>20){
                        throw new \Exception('请输入正确的支付宝账户');
                    }
                }

                if(!$postData['payee_bank'] || strlen($postData['payee_bank'])>25){
                    throw new \Exception('请输入正确的开户行');
                }
                $sql = "uid = ".$this->user['uid']." AND shop_id=".$washShop['id']." AND type=".$postData['type']."";
                $account = (new Wash_payee())->select('id,payee_name,payee_account,payee_bank,type',$sql)->one();
                $postData['u_time'] = time();
                $postData['up_type'] = 1; //前台修改
                if($account){
                    $res = (new Wash_payee())->myUpdate($postData,'id = '.$account['id'].'');
                }else {
                    $postData['uid'] = $this->user['uid'];
                    $postData['shop_id'] = $washShop['id'];
                    $postData['admin_mobile'] = $washShop['mobile'];
                    $postData['c_time'] = time();
                    $postData['u_time'] = time();
                    $postData['status'] = 1;
                    $res = (new Wash_payee())->myInsert($postData);
                }
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            unset(Yii::$app->session['checkmobile']);
            return $this->json($this->status,$this->msg);
        }
        $type = $request->get('type',null);
        $sql = "uid = ".$this->user['uid']." AND shop_id=".$washShop['id']." AND type=".$type."";
        $account = (new Wash_payee())->select('payee_name,payee_account,payee_bank,type',$sql)->one();
        return $this->render('changeaccount',[
            'washShop' => $washShop,
            'type' => $type,
            'account' => $account
        ]);
    }

    /**
     * 修改提现密码
     * @return string
     */
    public function actionChangewithdraw()
    {
        $washShop = $this->selectShop('id,mobile,shop_pic,w_password','uid = '.$this->user['uid'].'  AND shop_status=2');
        $request = Yii::$app->request;
        if($request->isAjax){
            $postData = $request->post();
            try {
                //从session中判断是否验证过手机
                if(!Yii::$app->session['checkmobile']){
                    throw new \Exception('请先验证手机号码');
                }
                if(!preg_match('/^[_0-9a-z]{6,16}$/i',$postData['w_password']) || !$postData['w_password']){
                    throw new \Exception('密码为空或者密码格式不正确');
                }
                if(!$postData['s_password']){
                    throw new \Exception('确认密码不能为空');
                }
                if($postData['w_password'] != $postData['s_password']){
                    throw new \Exception('二次密码输入不一致');
                }
                $postData['w_password']=md5(md5($postData['w_password']).'dhxxz');
                if($postData['w_password'] == $washShop['w_password']){
                    throw new \Exception('输入的密码与旧密码重复');
                }
                unset($postData['s_password'],Yii::$app->session['checkmobile']);
                $res = (new Wash_shop())->myUpdate($postData,'id = '.$washShop['id'].'');
                if(!$res){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);
        }
        return $this->render('changewithdraw',[
            'washShop' => $washShop
        ]);
    }


    //验证手机号码
    public function actionCheckmobile()
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            $postData = $request->post();
            try {
                if(empty($postData['code']) || !is_numeric($postData['code'])){
                    throw new \Exception('请输入正确的验证码');
                }
                //验证码
                $msg = 'ok';
                if(!Helpper::vali_mobile_code($postData['mobile'], $postData['code'], $msg)){
                    throw new \Exception($msg);
                }
            } catch ( \Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            //验证状态存入session
            Yii::$app->session['checkmobile'] = true;
            return $this->json($this->status,$this->msg);
        }
        return '非法请求';
    }

    /**
     * 门店核销功能
     * @return string
     */
    public function actionVerification()
    {
        $this->autoLogin();

        $washShop = $this->selectShop('id,shop_pic','id = '.$this->user['sid'].'');
        $request = Yii::$app->request;
        if($request->isAjax){
            $postData = $request->post();
            $trans = Yii::$app->db->beginTransaction();
            try {
                if(!$this->user['sid']){
                    throw new \Exception('您没有权限核销卡券');
                }
                if(!$postData['consumerCode']){
                    throw new \Exception('服务码不能为空');
                }
                if(!$postData['car_pic']){
                    throw new \Exception('车牌照片不能为空');
                }
                $res = (new WashOrder())->table()->select('id,consumerCode,promotion_price,amount,status,shopId')->where([
                    'consumerCode' => $postData['consumerCode'],
                    'shopId'=>$this->user['sid'],
                    'company_id' => 3
                ])->one();
                if(!$res){
                    throw new \Exception('服务码错误');
                }
                if($res['status'] != 1){
                    throw new \Exception('该服务码已使用或者已取消');
                }
                $res['status'] = 2;
                $res['s_time'] = time();
                $res['car_pic'] = $postData['car_pic'];
                $result = (new WashOrder())->table()->myUpdate($res,['id' => $res['id']]);
                if(!$result){
                    throw new \Exception('订单状态写入失败');
                }
                $result1 = (new Wash_shop())->recovery($res);
                if(!$result1){
                    throw new \Exception('门店数据写入失败');
                }
                $trans->commit();
            } catch (\Exception $e){
                $trans->rollBack();
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            //核销数据写入日志
            $this->log('verification');
            return $this->json($this->status,$this->msg);
        }
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);

        return $this->render('verification',[
            'alxg_sign' => $alxg_sign,
            'washShop' => $washShop
            ]);
    }

    /**
     * 申请提现
     * @return array|string
     * @throws yii\db\Exception
     */
    public function actionWithdraw()
    {
        $washShop = $this->selectShop('id,mobile,w_password,shop_pic,amount,uid',
            'uid = '.$this->user['uid'].'  AND shop_status=2');
        $password = $washShop['w_password'];
        unset($washShop['w_password']);
        $account = (new Wash_payee())->select('id,payee_name,payee_account,payee_bank,type',
            'uid ='.$this->user['uid'].' AND shop_id='.$washShop['id'].'')->all();
        //从数组中查询指定的账户
        foreach ($account as $val){
            if($val['type'] == 1){
                $aipay = $val;
            }
            if($val['type'] == 2){
                $bank = $val;
            }
        }
        $request = Yii::$app->request;
        if($request->isAjax){
            $postData = $request->post();
            $trans = Yii::$app->db->beginTransaction();
            try {
                if(!$postData['code']){
                    throw new \Exception('验证码不能为空');
                }
                if(!preg_match('/^[_0-9a-z]{6,16}$/i',$postData['w_password']) || !$postData['w_password']){
                    throw new \Exception('提现密码为空或者密码格式不正确');
                }
                $postData['w_password']=md5(md5($postData['w_password']).'dhxxz');
                if($postData['w_password'] != $password){
                    throw new \Exception('密码不正确');
                }
                //验证码
                $msg = 'ok';
                if(!Helpper::vali_mobile_code($postData['mobile'], $postData['code'], $msg)){
                    throw new \Exception($msg);
                }
                if($postData['amount']>$washShop['amount']){
                    throw new \Exception('申请金额大于可提现金额');
                }
                if($postData['account'] == 1){
                    $postData['account'] = $aipay;
                } else {
                    $postData['account'] = $bank;
                }
                if(!$postData['account']){
                    throw new \Exception('请完善账户信息后提交');
                }
                $wxuser = (new AlxgBase('fans', 'id'))->table()->select("id,openid")->where(['id'=>$this->user['uid']])->one();
                $data = [
                    'uid' => $this->user['uid'],
                    'payee' => $postData['account']['payee_name'],
                    'withdrawals_no' => time().$this->user['uid'],
                    'amount' => $postData['amount'],
                    'openid' => $wxuser['openid'],
                    'account' => $postData['account']['payee_account'],
                    'account_bank' => $postData['account']['payee_bank'],
                    'apply_time' => time(),
                    'status' => 1,
                    'shop_id' => $washShop['id'],
                    'payee_id' => $postData['account']['id'],
                    'ac_type' => $postData['account']['type'],
                ];
                $res = (new Wash_withdrawals())->myInsert($data);
                if(!$res){
                    throw new \Exception('提现记录写入失败');
                }
                $result = (new Wash_shop())->withdraw($postData['amount'],$washShop['id']);
                if(!$result){
                    throw new \Exception('提现金额写入失败');
                }
                $trans->commit();
            } catch (\Exception $e){
                $trans->rollBack();
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);
        }
        return $this->render('withdraw',[
            'washShop' => $washShop,
            'aipay' => $aipay,
            'bank' =>$bank
        ]);
    }

    /**
     * 提现记录
     * @return string
     */
    public function actionWithdrawals()
    {
        $washShop = $this->selectShop('id,service_num,gross_income,amount,promotion_price',
            'uid = '.$this->user['uid'].'  AND shop_status=2');

        $withdrawals = (new Wash_withdrawals())->select('amount,withdrawals_no,status,apply_time',
            'uid = '.$this->user['uid'].'')->all();
        return $this->render('withdrawals',[
            'washShop' => $washShop,
            'withdrawals' => $withdrawals
        ]);
    }

    /**
     * 收入明细
     * @return string
     */
    public function actionIncomedetail()
    {
        $washShop = $this->selectShop('id,service_num,gross_income,amount,promotion_price',
            'uid = '.$this->user['uid'].'  AND shop_status=2');

        $incomedetail = (new WashOrder())->table()->select('id,shopName,mainOrderSn,consumerCode,amount,promotion_price,s_time')->where([
            'uid' =>$this->user['uid'],'shopId'=>$washShop['id'],'status' => 2])->all();

        return $this->render('incomedetail',[
            'washShop' => $washShop,
            'incomedetail' =>$incomedetail
        ]);

    }

    /**
     * 子账号管理
     * @return string
     */
    public function actionChildaccount()
    {
        $washShop = $this->selectShop('id,uid','uid = '.$this->user['uid'].'  AND shop_status=2');
        //查询所有账号
        $account = (new AlxgBase('fans', 'id'))->table()->select("id,pid,openid,nickname,mobile,realname")
            ->where(['sid'=>$washShop['id']])->all();

        //区分主账号和子账号
        foreach($account as $key=>$val){
            if($val['id'] == $washShop['uid']){
                $masterAccount = $val;
                unset($account[$key]);
                break;
            }
        }

        return $this->render('childaccount',[
            'washShop' => $washShop,
            'masterAccount' => $masterAccount,
            'childAccount' => $account
        ]);

    }

    /**
     * 子账号激活链接
     * @return array|string
     */
    public function actionChildurl()
    {
        $washShop = $this->selectShop('id,uid','uid = '.$this->user['uid'].'  AND shop_status=2');
        $request = Yii::$app->request;
        if($request->isPost){
            $num = $request->post('num');
            try {
                if(!is_numeric($num) || $num >20){
                    throw new \Exception('有限次数只能为数字且不能大于20');
                }
                $data = [
                    'uid' => $this->user['uid'],
                    'sid' => $washShop['id']
                ];
                ksort($data);
                $jsonData = json_encode($data);
                $sign = md5(md5($jsonData).'active-childAccount-url');
                $url = $request->hostInfo.Url::to(['carwashshop/childactive','sign'=>$sign,'uid'=>$data['uid'],'sid'=>$data['sid']]);
                $urlNum = [
                    'num' => $num,
                    'c_time' => time()
                ];
                $cache = Yii::$app->cache;
                //cache过期时间为3天
                $cache->set($sign,$urlNum,259200);
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }
            return $this->json($this->status,$this->msg,$url);
        }
        return '非法请求';
    }

    /**
     * 激活子账号
     * @return array|string
     */
    public function actionChildactive()
    {
        $this->isLogin();
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        if($request->isPost){
            $mobile = $request->post('mobile', null);
            $realname = $request->post('realname', null);
            $code = $request->post('code', null);
            $sid = $request->post('sid', null);
            $uid = $request->post('uid', null);
            try {
                if(!$mobile){
                    throw new \Exception('请输入手机号');
                }
                if(!$realname || strlen($realname)>25){
                    throw new \Exception('请输入正确的姓名');
                }
                $msg = 'ok';
                if (!Helpper::vali_mobile_code($mobile, $code, $msg)){
                    throw new \Exception($msg);
                }
                $washShop = (new Wash_shop())->select('id,uid','id = '.$sid.' AND uid='.$uid.'')->one();
                if(!$washShop){
                    throw new \Exception('找不到门店数据');
                }
                $data = [
                    'mobile' => $mobile,
                    'realname' => $realname,
                    'sid' => $washShop['id']
                ];
                $fans = (new AlxgBase('fans','id'))->myUpdate($data,['id' => $this->user['uid']]);
                if(!$fans){
                    throw new \Exception('数据写入失败');
                }
                //更新session中pid
                $this->autoLogin();
                $sign = Yii::$app->session['child_sign'];
                unset(Yii::$app->session['child_sign']);
                $urlNum = $cache->get($sign);
                //根据cache创建时间计算剩余过期时间
                $timeOut = 259200-(time()-$urlNum['c_time']);
                $urlNum['num'] = $urlNum['num']-1;
                if($urlNum['num']<1){
                    $cache->delete($sign);
                }else {
                    $cache->set($sign,$urlNum,$timeOut);
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);

        }

        //判断sign是否一致
        $data = $request->get();
        $sign = $data['sign'];
        Yii::$app->session->set('child_sign',$sign);
        $dataEn = [
            'uid' => $data['uid'],
            'sid' => $data['sid']
        ];
        ksort($dataEn);
        $dataEn = json_encode($dataEn);
        $newSign = md5(md5($dataEn).'active-childAccount-url');
        try {

            if($sign != $newSign){
                throw new \Exception('sign验证失败');
            }

            $urlNum = $cache->get($sign);
            if(!$urlNum){
                throw new \Exception('邀请链接已经失效');
            }
            $washShop = (new Wash_shop())->select('id,uid','uid = '.$data['uid'].' AND id='.$data['sid'].'')->one();
            if(!$washShop){
                throw new \Exception('找不到门店数据');
            }

        } catch (\Exception $e){
            $msg = $e->getMessage();
            header('Content-type: text/html;charset=utf-8');
            echo '<script language="JavaScript">;alert("'.$msg.'");location.href="/frontend/web/carwashshop/index.html";</script>;';
            exit;
        }

        return $this->render('childactive',[
            'user' => $this->user,
            'data' => $data,
        ]);

    }

    /**
     * 删除子账号
     * @return array|string
     */
    public function actionChilddel()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $uid = $request->post('child',null);
            try {
                if(!$uid || !is_numeric($uid)){
                    throw new \Exception('参数错误');
                }
                $childAc= (new Fans())->select('id,sid','id='.$uid.' AND sid='.$this->user['sid'].'')->one();
                if(!$childAc){
                    throw new \Exception('没有该子账号');
                }
                $result = (new Fans())->myUpdate(['sid' => 0],'id = '.$uid.'');
                if(!$result){
                    throw new \Exception('数据写入失败');
                }
            } catch (\Exception $e){
                $this->status = 0;
                $this->msg = $e->getMessage();
            }

            return $this->json($this->status,$this->msg);
        }

        return '非法请求';
    }

}