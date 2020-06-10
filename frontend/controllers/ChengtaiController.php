<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/11 0011
 * Time: 下午 3:29
 */
namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use common\models\CarCoupon;
use common\models\ErrorLog;
use common\components\CarCouponAction;
use common\models\CarCouponPackage;
use common\models\CarChengtaiCode;


class ChengtaiController extends WebcloudcarController {


    //卡券激活
    public function actionAccoupon()
    {
        $request = Yii::$app->request;
        $this->site_title = '诚泰洗车';
        if($request->isPost){

            $preson_name = trim($request->post('preson_name',null));
            $code = trim($request->post('code',null));
            $user = $this->isLogin();
            $time = time();
            $codeModle = new CarChengtaiCode();
            $packageObj = new CarCouponPackage();
            if(!$code){
                return $this->json(0,'兑换码不能为空');
            }

            if(!$user){
                return $this->json(0,'请登录后重试',[],Url::to(['index']));
            }
            $list = $codeModle->table()->select()->where(['customer_name' => $preson_name,'customer_code'=>$code])->one();
            if(!$list) return $this->json(0,'客户信息尚未录入系统，请稍后再试');
            if($list['status']==2)return $this->json(0,'您已兑换，请勿重复兑换');

            //对这个批次中的优惠券，取最近一条记录，进行激活
            $package_info = $packageObj->table()->where(
                [
                    'uid' => 0,
                    'batch_nb' => $list['package_batch_no'],
                    'status' => 1
                ]
            )->one();
            if (!$package_info) {
                return $this->json(0,'没有相应的优惠券！');
            }
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try {
                //兑换卡券
                $couponObj = new CarCouponAction($user);
                $result = $couponObj->activateByPackage($package_info);
                if(!$result){
                    throw new \Exception('兑换失败！');
                }
                //存入记录到Car_chengtai_code表
                $data = [
                    'uid' => $user['uid'],
                    'package_id' => $package_info['id'],
                    'customer_mobile' => $user['mobile'],
                    'u_time' => $time,
                    'status' => 2
                ];
                $res = $codeModle->myUpdate($data,['id'=>$list['id'],'status'=>1]);
                if(!$res){
                    throw new \Exception('兑换码已兑换，数据更新失败');
                }
                $trans->commit();
            } catch(\Exception $exception){
                $trans->rollBack();
                $error = [
                    'couponType' => 1,
                    'uid' => $user['uid'],
                    'content' => '兑换优惠券失败,错误：' . $exception->getMessage(),
                    'code' => $code
                ];
                $this->log(1,$error);
                return $this->json(0,$exception->getMessage());
            }
            return $this->json(1,'兑换成功');
        }
        return $this->renderPartial('accoupon');
    }

    private function log($type,$error)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/accoupon/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'couponType:' . $error['couponType'] . "\n");
        fwrite($f, 'uid:' . $error['uid'] . "\n");
        fwrite($f, 'content:' . $error['content'] . "\n");
        fwrite($f, 'code:' . $error['code'] . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }
}