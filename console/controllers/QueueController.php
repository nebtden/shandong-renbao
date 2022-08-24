<?php
/**
 * 聚合油卡充值
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;

use common\components\Eddriving;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\CarMobile;
use common\models\ErrorLog;
use common\models\FansAccount;
use yii\console\Controller;
use Yii;

/**
 * @package console\controllers
 */
class QueueController extends Controller
{
   public $CCmodel;
   public $EddrApi;


    public function actionActive(){
        $time = time();
        //过去二十分钟登录的，查一次，
        $old_time = $time-60*60*60*24;

        $users = FansAccount::find()
            ->select('tpy_fans_account.mobile,tpy_fans_account.uid,tpy_car_mobile.coupon_batch_no,tpy_car_mobile.id')
            ->rightJoin('tpy_car_mobile','tpy_car_mobile.mobile=tpy_fans_account.mobile')
			//->where(['tpy_car_mobile.uid'=>141157])->asArray()->all();
//             ->where(['>','tpy_fans_account.u_time', $old_time])
//             ->andWhere(['<=','tpy_fans_account.u_time', time()])
			 ->andWhere(['=','tpy_fans_account.is_web', 0])
             ->andWhere(['tpy_car_mobile.uid'=>0])
             ->andWhere(['>=','tpy_car_mobile.c_time',1628288000])

             ->asArray()->all();
        $carmobile = new CarMobile();
        $package = new CarCouponPackage();

        foreach ($users as $key=>$user){
            try{
                $mobile = $user['mobile'];
                echo $mobile;
                echo '\n';
                //对这个优惠券包，进行激活
                $user['mobile'] = $mobile;
                //对这个批次中的优惠券，取最近一条记录，进行激活
                $package_info = $package->table()->where(
                    [
                        'uid' => 0,
                        'status' => 1,
                        'batch_nb' => $user['coupon_batch_no']
                    ]
                )->one();
                if (!$package_info) {
                    throw new \Exception('没有相应的优惠券！');
                }

                //处理兑换的逻辑，更新用户id
                $data = [];
                $data['uid'] = $user['uid'];
                $data['u_time'] = time();
                $data['package_id'] = $package_info['id'];
                $data['remark'] = 'queue active'.$key;
                $carmobile->myUpdate($data, ['id' => $user['id']]);
                $result = $this->activateByPackage($user,$package_info);
                //对于没有成功使用优惠券的，记录相关日志
                if (!$result) {
                    throw new \Exception('兑换失败！,优惠券包的id为'.$package_info['id']);
                }
            }catch (\Exception $exception) {

                $error = new ErrorLog();
                $error->status = ErrorLog::$status['fail'];
                $error->type = ErrorLog::$type['coupon'];
                $error->content = '兑换优惠券失败,错误：' . $exception->getMessage();
                $error->uid = $user['uid'];
                $error->c_time = time();
                $error->save();
            }
        }

    }


    private function activateByPackage($user,$package_info){
        $this->CCmodel = new CarCoupon();
        $this->set_package_used($user,$package_info);
        $this->doCoupon($user,$package_info);
        return true;
    }


    protected function set_package_used($user,$package_info)
    {
        $data['status'] = 2;
        $data['use_time'] = time();
        $data['uid'] = $user['uid'];
        $data['id'] = $package_info['id'];
        $data['mobile'] = $user['mobile'];
        $data['companyid'] = $package_info['companyid'];
        $package_model = new CarCouponPackage();
        $r =  $package_model->myUpdate($data);
        if (!$r) {
            $this->msg = '激活失败，请重试！';
            return false;
        }
        return true;
    }

    protected function doCoupon($user,$package_info)
    {
        $coupons = json_decode($package_info['meal_info'], true);
        foreach ($coupons as $cp) {
            if ($cp['type'] == 6) {
                $cp['type'] = 4;
            }
            if ($cp['type'] == 1) {
                $this->activeEcarCoupon($cp,$user,$package_info);
                
            } elseif ($cp['type'] == 2) {
                $this->activeSaveCarCoupon($cp,$user,$package_info);
            } elseif ($cp['type'] == 3) {
                $this->activeScoreCoupon($cp,$user,$package_info);
            } elseif ($cp['type'] == 4) {
                $this->activeWashCarCoupon($cp,$user,$package_info);
            } elseif ($cp['type'] == 5) {
                $this->activeOilCoupon($cp,$user,$package_info);
            } elseif ($cp['type'] == INSPECTION) {
                $this->activeInsCoupon($cp,$user,$package_info);
            }elseif ($cp['type'] == SEGWAY) {
                $this->activeSegwayCoupon($cp,$user,$package_info);
            }
        }

    }

    /**
     * 激活积分券
     * @param array $coupon
     */
    protected function activeScoreCoupon($coupon = [],$user,$package_info)
    {
        return true;
    }

    /**
     * 年检券激活
     * @param array $coupon
     */
    protected function activeInsCoupon($coupon = [],$user,$package_info)
    {
        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) {
            $map['batch_no'] = $coupon['batch_no'];
        }

        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
//        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();

        $list = $db->createCommand($sql)->queryAll();
        if (!$list)
            return false;

        if (count($list) < $coupon['num']){
            return false;
        }

        foreach ($list as $val) {
            $val['status'] = 1;
            $val['uid'] = $user['uid'];
            $val['active_time'] = $now;
            $val['mobile'] = $user['mobile'];
//                $val['company'] = COMPANY_DIANDIAN;
            $val['package_id'] = $package_info['id'];
            $val['companyid'] = $package_info['companyid'];
            if ($val['expire_days'] > 0) {
                //如果这个字段大于0，表示过期时间为激活后多少天失效
                $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
            }
            $r = $this->CCmodel->myUpdate($val);
            if (!$r) {
                return false;
            }

        }
        return true;


    }

    /**
     * 油卡激活
     * @param array $coupon
     */
    protected function activeOilCoupon($coupon = [],$user,$package_info)
    {
        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) $map['batch_no'] = $coupon['batch_no'];

        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
//        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();


        $list = $db->createCommand($sql)->queryAll();
        if (!$list) return false;;
        if (count($list) < $coupon['num']){
            return false;
        }

        foreach ($list as $val) {
            $val['status'] = 1;
            $val['uid'] = $user['uid'];
            $val['active_time'] = $now;
            $val['mobile'] = $user['mobile'];
            $val['package_id'] = $package_info['id'];
            $val['companyid'] = $package_info['companyid'];
            if ($val['expire_days'] > 0) {
                //如果这个字段大于0，表示过期时间为激活后多少天失效
                $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
            }
            $r = $this->CCmodel->myUpdate($val);
            if (!$r) {
                return false;
            }
        }
        return true;

    }

    /**
     * 激活代驾券
     * @param array $coupon
     */
    protected function activeEcarCoupon($coupon = [],$user,$package_info)
    {

        $this->EddrApi = new Eddriving();
        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) $map['batch_no'] = $coupon['batch_no'];

        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
//        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();

        $list = $db->createCommand($sql)->queryAll();
        if (!$list) {
            return false;
        }

        if (count($list) < $coupon['num']) {
            return false;
        }
        //记录异常卡

        $abnormal = [];
        foreach ($list as $val) {
            if($val['company']==0){
                echo '$val[company]';
                echo $val['company'];

                echo '--coupon_id--';
                echo $val['id'];
                echo '--';
                //先更新卡券
                (new CarCoupon())->myUpdate(['status'=>4,'remark'=>'console'],['id'=>$val['id']]);

                $r = $this->EddrApi->coupon_bind($val['coupon_sn'], $user['mobile']);
                if ($r === false) {
                    return false;
                }
                $val['active_time'] = $now;
                $val['uid'] = $user['uid'];
                $val['bindid'] = $r['bindid'];
                $val['bonusid'] = $r['bonusid'];
                $val['mobile'] = $user['mobile'];
                $val['status'] = 1;//将卡券改为已激活状态
                $val['package_id'] = $package_info['id'];
                $val['companyid'] = $package_info['companyid'];

                (new CarCoupon())->myUpdate($val,['id'=>$val['id']]);
                /*$savePath = Yii::$app->getBasePath() . '/web/log/queue/' . date('Y-m') . '/';
                $f = fopen($savePath.'test' . '_' . date('Ymd') . ".txt", 'a+');
                fwrite($f, 'val:' . json_encode($val) . "\n");
                fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
                fwrite($f, "===========================================\n");
                fclose($f);*/
                //这里主要获得过期时间
                $r = $this->EddrApi->coupon_allinfo($val['coupon_sn'], $user['mobile']);
//                $val = [];
                if ($r === false) {
                    //没有查到的情况下，默认过期时间为绑定后20天
                    $expire = 0;
                    if (!$val['expire_days']) {
                        $val['expire_days'] = 20;
                    }
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                } else {
                    $val['bindsn'] = $r['bind_info']['sn'];
                    if($val['companyid']==10 && $val['c_time']>1556640000  ){

                    } elseif( $val['expire_days']!==0 && strtotime($r['endDate'])>(time()+$val['expire_days']*24*60*60)){
                        $val['use_limit_time'] = time()+$val['expire_days']*24*60*60;
                    }else{
                        $val['use_limit_time'] = strtotime($r['endDate']);
                    }
                }

                (new CarCoupon())->myUpdate($val,['id'=>$val['id']]);

            }
            if($val['company']==1){
                $val['active_time'] = $now;
                $val['uid'] = $user['uid'];
                $val['mobile'] = $user['mobile'];
                $val['status'] = 1;//将卡券改为已激活状态
                $val['package_id'] = $package_info['id'];
                $val['companyid'] = $package_info['companyid'];

                if (!$val['expire_days']) $val['expire_days'] = 365;
                $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    return false;
                }
            }

            //将成功的券加入

        }
        if ($abnormal) {
            //将异常卡券的状态修改掉
            $this->CCmodel->myUpdate(['status' => 4], ['id' => $abnormal]);
        }
        return true;



    }

    protected function activeSaveCarCoupon($coupon = [],$user,$package_info)
    {
        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'use_scene' => $coupon['scene'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) $map['batch_no'] = $coupon['batch_no'];

        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
//        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();


        $list = $db->createCommand($sql)->queryAll();
        if (!$list) return false;
        if (count($list) < $coupon['num']) return false;
        foreach ($list as $val) {
            $val['status'] = 1;
            $val['uid'] = $user['uid'];
            $val['active_time'] = $now;
            $val['mobile'] = $user['mobile'];
            if ($val['expire_days'] > 0) {
                //如果这个字段大于0，表示过期时间为激活后多少天失效
                $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
            }
            $val['package_id'] = $package_info['id'];
            $val['companyid'] = $package_info['companyid'];
            $r = $this->CCmodel->myUpdate($val);
            if (!$r) {
                return false;
            }

        }
        return true;

    }



    /**
     * 激活洗车券
     * @param array $coupon
     */
    protected function activeWashCarCoupon($coupon = [],$user,$package_info)
    {
        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) $map['batch_no'] = $coupon['batch_no'];

        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
//        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();


        $list = $db->createCommand($sql)->queryAll();
        if (!$list) return false;;
        if (count($list) < $coupon['num']) return false;
        foreach ($list as $val) {
            $val['status'] = 1;
            $val['uid'] = $user['uid'];
            $val['active_time'] = $now;
            $val['mobile'] = $user['mobile'];
            //如果不限制每月使用次数，则根据优惠券的exprie_days来设置过期时间
            if($val['is_mensal'] == 0){
                $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
            }else{
                //洗车券过期时间是按自然月计算，所以激活要根据amount设置过期时间
                $amount = (int)$val['amount'];
                $endDate = strtotime("+{$amount} month", strtotime(date("Ym") . '01000000')) - 1;
                $val['use_limit_time'] = $endDate;
            }
            $val['package_id'] = $package_info['id'];
            $val['companyid'] = $package_info['companyid'];
            $r = $this->CCmodel->myUpdate($val);
            if (!$r) {
                return false;
            }

        }
        return true;

    }


    /**
     * 代步券激活
     * @param array $coupon
     */
    protected function activeSegwayCoupon($coupon = [],$user,$package_info){
        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) {
            $map['batch_no'] = $coupon['batch_no'];
        }

        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
//        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();


        $list = $db->createCommand($sql)->queryAll();
        if (!$list) return false;
        if (count($list) < $coupon['num']) return false;
        foreach ($list as $val) {
            $val['status'] = 1;
            $val['uid'] = $user['uid'];
            $val['active_time'] = $now;
            $val['mobile'] = $user['mobile'];
            $val['package_id'] = $package_info['id'];
            $val['companyid'] = $package_info['companyid'];
            if ($val['expire_days'] > 0) {
                //如果这个字段大于0，表示过期时间为激活后多少天失效
                $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
            }
            $r = $this->CCmodel->myUpdate($val);
            if (!$r) {
                return false;
            }

        }
        return true;

    }


}