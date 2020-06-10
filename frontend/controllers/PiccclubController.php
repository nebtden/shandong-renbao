<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/20
 * Time: 17:22
 */
namespace frontend\controllers;



use common\models\CarDisinfectionOrder;
use Yii;
use frontend\util\PController;
use common\components\AESUtil;
use common\components\PiccInterface;
use common\components\AlxgBase;
use common\components\W;
use yii\web\Response;
use yii\helpers\Url;
use common\models\CarRenbaoOrder;
use common\models\WashOrder;
use common\models\CarCoupon;



class PiccclubController extends PController
{

    private $errdesc = [
        '1000000'  => 'SUCCESS',
        '2000001' => '请求数据不能为空',
        '2000002' => '数据无法解析',
        '2000003' => '订单不存在',
        '2000004' => '订单状态已同步，无需重复通知'


    ];

    public  function actionAuth()
    {
        $request = Yii::$app->request;
        $data  = $request->get();
        $aesObj = new AESUtil();
        $params = Yii::$app->params['PICC'];

        $aesKey = $params['weixinAeskey'];
        if(empty($data['orderNo']) || empty($data['cuponNo']) || empty($data['mobile']) || empty($data['productCode'])|| empty($data['orderCode'])|| empty($data['expirationTime'])|| empty($data['userName'])){
            exit('error');
        }

        $dedata['orderNo'] = $aesObj->decrypt($data['orderNo'],$aesKey);
        $dedata['cuponNo'] = $aesObj->decrypt($data['cuponNo'],$aesKey);
        $dedata['mobile'] = $aesObj->decrypt($data['mobile'],$aesKey);
        $dedata['productCode'] = $aesObj->decrypt($data['productCode'],$aesKey);
        $dedata['orderCode'] = $aesObj->decrypt($data['orderCode'],$aesKey);
        $dedata['expirationTime'] = $aesObj->decrypt($data['expirationTime'],$aesKey);
        $dedata['userName'] = $aesObj->decrypt($data['userName'],$aesKey);
        if(empty($dedata['orderNo']) || empty($dedata['cuponNo']) || empty($dedata['mobile']) || empty($dedata['productCode']) || empty($dedata['orderCode'])|| empty($dedata['expirationTime'])|| empty($dedata['userName'])){
            exit('非法请求！');
        }

        $lockingdata['orderNo']      = $dedata['orderNo'];
        $lockingdata['cuponNo']      = $dedata['cuponNo'];
        $lockingdata['outerOrderId'] = '2020042131122556';//date("YmdHis").$params['company_id'].rand(10000,99999);
        $lockingdata['connectPhone'] = $dedata['mobile'];
        $lockingdata['userName'] = $dedata['userName'];

        $mres = W::is_mobile($dedata['mobile']);
        if(!$mres) exit('参数错误！');
        $modelRenbao = new CarRenbaoOrder();
        $ordercheck = $modelRenbao->table()->select('*')->where(['order_no'=>$dedata['orderNo']])->one();

        if(! $ordercheck){//人保俱乐部锁定接口
            $piccObj = new PiccInterface();
            $res = $piccObj->cuponnoLock($lockingdata);
            if($res['code'] != '1000000' &&  $res['code'] != '2004024') exit('系统错误！');
        }

        $time = time();
        //检测用户 构造人保订单数据
        $dbdata['order_no'] = $dedata['orderNo'];
        $dbdata['cupon_no'] = $dedata['cuponNo'];
        $dbdata['company_id'] = $params['company_id'];
        $dbdata['order_id'] = $lockingdata['outerOrderId'];
        $dbdata['mobile'] = $dedata['mobile'];
        $dbdata['service_code'] = $dedata['productCode'];
        $dbdata['organization_code'] = $dedata['orderCode'];
        $dbdata['expiration_time'] = (int)$dedata['expirationTime']/1000;
        $dbdata['third_uname'] = $dedata['userName'];
        $dbdata['c_time'] = $time;
        $dbdata['u_time'] = $time;
        $userres = $this->add_apk_user($dbdata,$ordercheck);//添加用户或发券

        if($dedata['productCode'] == 'DHYCCJ001' || $dedata['productCode'] == 'DHYCXC001'){//臭氧杀菌或洗车
            $this->storeInfo($dedata['mobile']);//登陆操作
            $url = Url::to(['webcaruser/coupon','footer'=>'hidden']);
            if(!empty($ordercheck)){
                $info = CarRenbaoOrder::$service;
                $orderinfo  = (new AlxgBase($info[$dedata['productCode']]['tableName'], 'id'))
                    ->table()
                    ->select('*')
                    ->where([$info[$dedata['productCode']]['field']=>$ordercheck['coupon_id']])
                    ->one();
                if($orderinfo) $url = Url::to(['webcaruorder/Index','footer'=>'hidden']);
            }

            return $this->redirect($url);
        }elseif($dedata['productCode'] == 'DHSQJY001'){//神雀加油优惠权益
            $sqdata['soure_platform'] = $params['soure_platform'];
            $sqdata['yunche_order'] = $aesObj->encrypt($lockingdata['outerOrderId'],$params['aeskey']);
            $sqdata['mobile_encode'] = $aesObj->encrypt($dedata['mobile'],$params['aeskey']);;
            $sqstr = http_build_query($sqdata);
            $url =  $params['squrl'].'?'.$sqstr;

            Yii::$app->response->redirect($url)->send();
        }else{
            exit('系统错误！');
        }

    }

    /**
     * 产生用户
     * params $mobile
     * return $uid $mobile
     **/
    protected function add_apk_user($data,$ordercheck)
    {
        $now = time();
        $token = Yii::$app->session['token'];
        $params = Yii::$app->params['PICC'];
        $data_fans = [
            'mobile'         => $data['mobile'],
            'subscribe_time' => $now,
            'source'         => 'web端用户',
            'token'          => $token
        ];
        $data_ac = [
            'mobile'         => $data['mobile'],
            'c_time'         => $now,
            'u_time'         => $now,
            'is_web'         => '1'
        ];
        $source  = 'web端用户';
        $accountModel = new AlxgBase('fans_account', 'id');
        $fansModel = new AlxgBase('fans', 'id');
        $fansinfo=$fansModel->table()->select('*')->where(['mobile'=>$data['mobile'],'source' => $source, 'token' => $token])->one();

        $id = $fansinfo['id'];

        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try{
            //插入用户数据
            if(!$fansinfo) {
                $id = $fansModel->myInsert($data_fans);
                $data_ac['uid'] = $id;
                $accountModel->myInsert($data_ac);
            }
            //发放权益
            $data['uid'] = $id;
            if(!$ordercheck){//如果没有发放权益，就在这里发放
                if($data['service_code'] == 'DHYCCJ001' || $data['service_code'] == 'DHYCXC001'){
                    $coupnodata = $this->couponArray($id,$data);
                    $coupon_id = (new CarCoupon())->myInsert($coupnodata);

                    $data['coupon_id'] = $coupon_id;
                }
                (new CarRenbaoOrder())->myInsert($data);
            }else{//如果变更手机，就转移权益至新手机
                if($ordercheck['mobile'] != $data['mobile']){
                    (new CarRenbaoOrder())->myUpdate(['uid'=>$id,'mobile'=>$data['mobile'],'u_time'=>$now],['order_no'=>$data['order_no']]);
                    if($data['service_code'] == 'DHYCCJ001' || $data['service_code'] == 'DHYCXC001'){
                        (new CarCoupon())->myUpdate(['uid'=>$id,'mobile'=>$data['mobile']],['id'=>$data['coupon_id']]);
                        if($data['service_code'] == 'DHYCCJ001') (new CarDisinfectionOrder())->myUpdate(['uid'=>$id,'mobile'=>$data['mobile']],['coupon_id'=>$data['coupon_id']]);
                        if($data['service_code'] == 'DHYCXC001') (new WashOrder())->myUpdate(['uid'=>$id,'mobile'=>$data['mobile']],['couponId'=>$data['coupon_id']]);
                    }
                }
            }
        }catch (\Exception $e){
            $trans->rollBack();
            return false;
        }
        $trans->commit();

        $res = ['uid'=>$id,'mobile'=>$data['mobile']];
        return $res;
    }

    protected function storeInfo($mobile)
    {
        $token = Yii::$app->session['token'];
        $user = (new AlxgBase('fans', 'id'))->table()->select("id,nickname,headimgurl,sex,pid")->where(['mobile'=>$mobile,'source' => 'web端用户', 'token' => $token])->one();
        if ($user) {
            $user_auth = [
                'uid' => $user['id'],
                'nickname' => $mobile,
                'headimgurl' => $user['headimgurl'],
                'mobile' => $mobile,
                'sex' => $user['sex'],
                'pid' => $user['pid']
            ];
            Yii::$app->session['wx_user_auth_web'] = $user_auth;
            Yii::$app->session['xxz_mobile']=$mobile;
        }
    }

    /**
     * 神雀回调->云车回调->通知人保俱乐部
     **/
    public function actionNotice(){

        $postBody = yii::$app->getRequest()->getRawBody();
        $postParam = json_decode($postBody,true);

        //检验参数
        if(empty($postParam['mobile']) || empty($postParam['outerOrderId'])){
            return $this->response('2000001');
        }

        $aesObj = new AESUtil();
        $data = [];
        $params = Yii::$app->params['PICC'];
        $aesKey = $params['aeskey'];
        $data['mobile'] = $aesObj->decrypt($postParam['mobile'],$aesKey);
        $data['outerOrderId'] = $aesObj->decrypt($postParam['outerOrderId'],$aesKey);

        if(!$data['mobile'] || !$data['outerOrderId']){
            return $this->response('2000002','',$postParam);
        }

        //验证订单
        $renbaoModel = new CarRenbaoOrder();
        $res=$renbaoModel->table()->select('*')->where(['order_id'=>$data['outerOrderId']])->one();
        if(!$res) return $this->response('2000003','',$postParam);
        if($res['status'] == 2) return $this->response('2000004','',$postParam);

        //处理订单
        $status = '1000000';
        $msg = '';
        $trans = Yii::$app->db->beginTransaction();

        $rendata['orderNo']      = $res['order_no'];
        $rendata['cuponNo']      = $res['cupon_no'];
        $rendata['productCode']  = $res['service_code'];
        $rendata['status']       = 2;
        $rendata['connectPhone'] = $res['mobile'];

        try{
           $PiccObj = new PiccInterface();
           $appres = $PiccObj->syncOrderInfo($rendata);
           if($appres['code'] != '1000000') throw new \Exception('人保俱乐部订单状态通知失败！');
            $res = $renbaoModel->myUpdate(['status'=>2,'u_time'=>time()], ['order_id'=>$data['outerOrderId']]);
            if(!$res) throw new \Exception('云车订单状态通知失败！');
            $trans->commit();
        } catch (\Exception $e){
            $trans->rollBack();
            $msg = $e->getMessage();
            $status = '2000005';
        }
        return $this->response($status,$msg,$postParam);
    }


    protected function response($errno = 0,$msg,$param='')
    {
        $result = [
            'code' => $errno,
            'msg' => $msg?$msg:$this->errdesc[$errno],
        ];
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($param){
            $PiccObj = new PiccInterface();
            $PiccObj->noticelog(json_encode($param,true),json_encode($result,true));
        }
        return $result;
    }

    protected function couponArray($uid,$data)
    {
        $tmp = [];
        $model = new CarCoupon();
        $coupon_sn = $model -> generateCardNoxu(10);
        $couponInfo = CarRenbaoOrder::$service;
        $now = time();
        $tmp['uid'] = $uid;
        $tmp['coupon_type'] = $couponInfo[$data['service_code']]['type'];
        $tmp['name'] = $couponInfo[$data['service_code']]['name'];
        $tmp['amount'] = 1;
        $tmp['expire_days'] = '365';
        $tmp['c_time'] = $now;
        $tmp['coupon_sn'] = $coupon_sn;
        $tmp['mobile'] = $data['mobile'];
        $tmp['batch_no'] = $data['service_code'];
        $tmp['companyid'] = $data['company_id'];
        $tmp['active_time'] = $now;
        $tmp['company'] = 2;
        $tmp['is_mensal']  = 0;
        $tmp['status']  = 1;
        $tmp['use_limit_time'] = $data['expiration_time'];
        return $tmp;
    }
}