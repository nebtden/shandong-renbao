<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\9\28 0028
 * Time: 15:30
 */

namespace frontend\controllers;

use common\components\AlxgBase;
use common\components\BaseController;
use common\components\CarCouponAction;
use common\components\EJin;
use common\components\Helpper;
use common\components\W;
use common\models\CarCoupon;
use Yii;
use yii\helpers\Url;


class EarthinsController extends BaseController
{
    public $layout = 'earthins';
    public $site_title = '山东大地年检';
    const STATIC_PATH = '/frontend/web/earthins/';

    public function beforeAction($action = NULL)
    {
        Yii::$app->session['token'] = $token = 'dhcarcard';
        if (!Yii::$app->session['openid']) {
            W::getOpenid($token);
        }
        return true;
    }

    public function actionIndex()
    {
        $this->site_title = '山东大地年检';
        return $this->render('index');
    }

    /**
     * 兑换服务
     * @return string
     */
    public function actionActivatePage()
    {
        $this->site_title = '立即激活';
        $request = Yii::$app->request;
        if ($request->isPost) {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $openid = Yii::$app->session['openid'];
                $fans = (new AlxgBase('fans', 'id'))->table()->select('id,nickname')->where(['openid' => $openid])->one();
                if (!$fans) {
                    throw new \Exception('非法访问');
                }
                $mobile = $request->post('mobile', null);
                $code = $request->post('code', null);
                $realname = $request->post('realname', null);
                $pwd = $request->post('packagepwd', null);
                $sex = $request->post('sex', 1);
                if (!$mobile) {
                    throw new \Exception('请输入手机号码');
                }
                $pwd = trim($pwd);
                if (!$pwd) {
                    throw new \Exception('请输入兑换码');
                }
                $msg = 'ok';
                if (!Helpper::vali_mobile_code($mobile, $code, $msg)) {
                    throw new \Exception($msg);
                }
                $now = time();
                $data = [
                    'uid' => $fans['id'],
                    'realname' => $realname,
                    'mobile' => $mobile,
                    'u_time' => $now,
                ];
                $model = new AlxgBase('fans_account', 'id');
                $ac = $model->table()->select('id,uid,pid,realname,mobile,company,duties,email,is_vip,score,status')->where(['uid' => $fans['id']])->one();

                if ($ac) {
                    //更新
                    $r = $model->myUpdate($data, ['id' => $ac['id']]);
                    if ($r === false) {
                        throw new \Exception('绑定失败，请重试');
                    }
                } else {
                    //新增
                    $data['c_time'] = $now;
                    $id = $model->myInsert($data);
                    if (!$id) {
                        throw new \Exception('绑定失败，请重试');
                    }
                }

                (new AlxgBase('fans', 'id'))->myUpdate(['sex' => $sex], ['id' => $fans['id']]);

                //兑换优惠券部分start
                //判断号码类型
                $type = CarCouponAction::switch_pwd($pwd);
                if ($type === 2) {
                    Yii::$app->session['car_meal_pwd'] = $pwd;
                    return $this->json(2, 'meal');
                }
                if ($type === 0) {
                    throw new \Exception('兑换码不存在');
                }
                if ($type === 3) {
                    throw new \Exception('兑换码已使用或失效');
                }

                $user = array();
                $user['mobile'] = $mobile;
                $user['uid'] = $fans['id'];
                $couponObj = new CarCouponAction($user);
                $coupons = $couponObj->activate($pwd);
                if ($coupons === false) {
                    throw new \Exception($couponObj->msg);
                }
                if (empty($coupons)) {
                    throw new \Exception('严重错误，请联系客服');
                }
                $couponModel = new CarCoupon();
                foreach ($coupons as $k => $val) {
                    $coupons[$k] = $couponModel->renderCouponList($val);
                }
                (new EJin())->chargeCouponNotice($coupons);//兑换券的通知
                //end
                $trans->commit();
                return $this->json(SUCCESS_STATUS, 'ok', [], Url::to(['caruser/coupon']));
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->json(ERROR_STATUS, $e->getMessage());
            }
        }
        return $this->render('activatepage');
    }
    /**
     * 发送验证码
     */
    public function actionSendsms()
    {
        $request = Yii::$app->request;
        if (!$request->isPost){
            return $this->json(ERROR_STATUS, '非法访问');
        }
        $mobile = $request->post('mobile', null);
        if (!$mobile) {
            return $this->json(ERROR_STATUS, '请输入手机号码');
        }
        $f = W::sendSms($mobile);
        if (!$f) {
            return $this->json(ERROR_STATUS, '验证码发送失败，请重试！');
        }
        return $this->json(SUCCESS_STATUS, 'ok');
    }

    /**
     * 疑难解答
     * @return string
     */
    public function actionUserFlowPage()
    {
        $this->site_title = '使用流程';
        return $this->render('userflowpage');
    }
}