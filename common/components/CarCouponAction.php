<?php

namespace common\components;

use common\models\CarCouponMeal;
use common\models\CarMeal;
use Yii;
use yii\base\Component;
use common\models\CarCouponPackage;
use common\models\CarCoupon;
use common\components\Eddriving;
use yii\db\Expression;

/**
 * Class CarCoupon
 * 优惠券激活与使用
 * 从卡包开始
 * @package common\components
 */
class CarCouponAction extends Component
{
    private $package = null;
    private $package_sn = null;
    private $package_pwd = null;
    private $companyid = null;
    private $CCPmodel = null;//卡包模型
    private $CCmodel = null;//卡券模型

    private $user = null;//[uid,mobile,nickname,headimgurl]

    private $EddrApi = null;//代驾api

    private $log = ['status' => 1];

    public $msg = 'ok';

    private $coupons = [];//激活的券


    /**
     * 判断卡券类型
     * 暂时只有两种卡券，所以只是做了简单的判断
     * 如果以后有其他的卡券，更新这个方法
     * @param $pwd
     * @return int 1:普通卡券，2:套餐卡券
     */
    public static function switch_pwd($pwd)
    {
        if (strlen($pwd) > 8 && strtolower(substr($pwd, 0, 2)) == 'me') {
            $cmeal = (new CarCouponMeal())->table()->where(['package_pwd' => $pwd])->one();
            if (!$cmeal) {
                return 0;
            }
            if (1 !== (int)$cmeal['status']) {
                return 3;
            }
            return 2;
        }
        return 1;
    }

    /**
     * 传入当前登录用户信息或其他用户的信息
     * CarCouponAction constructor.
     * @param array $user
     */
    public function __construct($user = [])
    {
        $this->user = $user;
    }

    /**
     * 重置日志
     */
    protected function render_log()
    {
        $this->log = ['status' => 1];
    }

    /**
     * 返回当前日志数组
     * @return array
     */
    public function get_log()
    {
        return $this->log;
    }

    /**
     * 设置日志
     * @param $key
     * @param $msg
     */
    protected function set_log($key, $msg)
    {
        $this->log['status'] = 0;
        $this->log[$key][] = $msg;
    }

    /**
     * 设置日志头
     */
    protected function set_log_header()
    {
        $this->log['package'] = [
            'pwd' => $this->package_pwd,
            'uid' => $this->user['uid'],
            'mobile' => $this->user['mobile']
        ];
    }

    /**
     * 写日志
     */
    protected function write_log()
    {
        if ($this->log['status']) return false;
        $content = "--------" . date("Y-m-d H:i:s") . "-------" . PHP_EOL;
        $content .= json_encode($this->log, JSON_UNESCAPED_UNICODE);
        $content .= PHP_EOL;
        $path = "log/coupon_" . date("Y_m_d") . '.log';
        error_log($content, 3, $path);
    }

    /**
     * 激活套餐
     * @param string $pwd 兑换码
     * @param int $id 套餐id
     * @return array|bool
     */
    public function active_meal($pwd, $id)
    {
        //重置日志
        $this->render_log();
        if (!$pwd) {
            $this->msg = '请提供卡包兑换码';
            return false;
        }
        $this->package_pwd = $pwd;

        $this->set_log_header();
        if (!$this->is_valid_coupon_meal($id)) return false;
        //获得套餐
        $meal = (new CarMeal())->table()->where(['id' => $id])->one();
        if (!$meal) {
            $this->msg = '套餐不存在';
            return false;
        }
        if (!$meal['status']) {
            $this->msg = '套餐已被禁用';
            return false;
        }
        $this->package = $meal;
        return $this->doCoupon();
    }

    protected function is_valid_coupon_meal($id)
    {
        $map['package_pwd'] = $this->package_pwd;
        $obj = (new CarCouponMeal());
        $package = $obj->table()->where($map)->one();
        if (empty($package)) {
            $this->msg = '卡包不存在';
            return false;
        } elseif ($package['status'] != 1) {
            $msg = '';
            if ($package['status'] == 0) $msg = '卡包已被禁用';
            if ($package['status'] == 2) $msg = '卡包已被使用';
            if ($package['status'] == 3) $msg = '卡包已过期';
            $this->msg = $msg;
            return false;
        } else {
            //判断是否过期
            $use_limit_time = $package['use_limit_time'];
            if ($use_limit_time && $use_limit_time < time()) {
                $this->msg = '卡包已过期';
                return false;
            }
        }
        //设置为已使用
        $data['status'] = 2;
        $data['use_time'] = time();
        $data['uid'] = $this->user['uid'];
        $data['id'] = $package['id'];
        $data['choose_meal_id'] = $id;
        $r = $obj->myUpdate($data);
        if ($r === false) {
            $this->msg = '操作失败';
            return false;
        }
        return true;
    }

    /**
     * 激活卡包
     * 激活不成功返回false，激活成功返回coupons数组
     * @param string $pwd
     * @param int $companyid
     * @return bool|array
     */
    public function activate($pwd = '', $companyid = 0)
    {
        //重置日志
        $this->render_log();
        if (!$pwd) {
            $this->msg = '请提供卡包兑换码';
            return false;
        }
        $this->package_pwd = $pwd;
        $this->companyid = $companyid;

        $this->set_log_header();

        $this->get_package();

        if (!$this->is_valid_package()) return false;

        if (!$this->set_package_used()) return false;
        return $this->doCoupon();
    }

    public function activateByPackage($package)
    {
        $this->CCPmodel = new CarCouponPackage();
        $this->package = $package;
        $this->set_package_used();
        $this->doCoupon();
        return true;
    }


    protected function get_package()
    {
        $map['package_pwd'] = $this->package_pwd;
        if ($this->companyid) {
            $map['companyid'] = $this->companyid;
        }
        $this->CCPmodel = new CarCouponPackage();
        $this->package = $this->CCPmodel->table()->where($map)->one();
    }

    /**
     * 判断卡包是否可用
     * @param null $package
     * @return bool
     */
    public function is_valid_package($package = null)
    {
        if (!$package) {
            $package = $this->package;
        }
        if (empty($package)) {
            $this->msg = '卡包不存在';
            return false;
        } elseif ($package['status'] != 1) {
            $msg = '';
            if ($package['status'] == 0) $msg = '卡包已被禁用';
            if ($package['status'] == 2) $msg = '卡包已被使用';
            if ($package['status'] == 3) $msg = '卡包已过期';
            $this->msg = $msg;
            return false;
        } else {
            //判断是否过期
            $use_limit_time = $package['use_limit_time'];
            if ($use_limit_time && $use_limit_time < time()) {
                $this->msg = '卡包已过期';
                return false;
            }
        }
        return true;
    }

    /**
     * 将卡包设置为已使用状态
     */
    protected function set_package_used()
    {
        $data['status'] = 2;
        $data['use_time'] = time();
        $data['uid'] = $this->user['uid'];
        $data['id'] = $this->package['id'];
        $data['mobile'] = $this->user['mobile'];
        $data['companyid'] = $this->package['companyid'];
        $r = $this->CCPmodel->myUpdate($data);
        if (!$r) {
            $this->msg = '激活失败，请重试！';
            return false;
        }
        return true;
    }

    /**
     *  读取卡包信息，再做解析
     */
    protected function doCoupon()
    {
        $coupons = json_decode($this->package['meal_info'], true);
        $this->CCmodel = new CarCoupon();
        foreach ($coupons as $cp) {
            if ($cp['type'] == 6) {
                $cp['type'] = 4;
            }
            if ($cp['type'] == 1) {
                $this->activeEcarCoupon($cp);
            } elseif ($cp['type'] == 2) {
                $this->activeSaveCarCoupon($cp);
            } elseif ($cp['type'] == 3) {
                $this->activeScoreCoupon($cp);
            } elseif ($cp['type'] == 4) {
                $this->activeWashCarCoupon($cp);
            } elseif ($cp['type'] == 5) {
                $this->activeOilCoupon($cp);
            } elseif ($cp['type'] == INSPECTION) {
                $this->activeInsCoupon($cp);
            }elseif ($cp['type'] == SEGWAY) {
                $this->activeSegwayCoupon($cp);
            }elseif ($cp['type'] == 9) {
                $this->activeEtcCoupon($cp);
            }elseif ($cp['type'] == 10) {
                $this->activeWashCarCoupon($cp);
            }elseif ($cp['type'] == 11) {
                $this->activeAiqiyiCoupon($cp);
            }
        }
        $this->write_log();
        return $this->coupons;
    }

    /**
     * 年检券激活
     * @param array $coupon
     */
    protected function activeInsCoupon($coupon = [])
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
        $trans = $db->beginTransaction();
        try {
            $list = $db->createCommand($sql)->queryAll();
            if (!$list) $this->set_log('InsCoupon', '未找到相应的未激活年检卡券');
            if (count($list) < $coupon['num']) $this->set_log('InsCoupon', '年检卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
            foreach ($list as $val) {
                $val['status'] = 1;
                $val['uid'] = $this->user['uid'];
                $val['active_time'] = $now;
                $val['mobile'] = $this->user['mobile'];
//                $val['company'] = COMPANY_DIANDIAN;
                $val['package_id'] = $this->package['id'];
                $val['companyid'] = $this->package['companyid'];
                if ($val['expire_days'] > 0) {
                    //如果这个字段大于0，表示过期时间为激活后多少天失效
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                }
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    $this->set_log('InsCoupon', $val);
                    continue;
                }
                $this->coupons[] = $val;
            }
        } catch (\Exception $e) {
            $trans->rollBack();
        }
        $trans->commit();
    }

    /**
     * 代步券激活
     * @param array $coupon
     */
    protected function activeSegwayCoupon($coupon = []){
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
        $trans = $db->beginTransaction();
        try {
            $list = $db->createCommand($sql)->queryAll();
            if (!$list) $this->set_log('SegwayCoupon', '未找到相应的未激活代步卡券');
            if (count($list) < $coupon['num']) $this->set_log('SegwayCoupon', '代步卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
            foreach ($list as $val) {
                $val['status'] = 1;
                $val['uid'] = $this->user['uid'];
                $val['active_time'] = $now;
                $val['mobile'] = $this->user['mobile'];
                $val['package_id'] = $this->package['id'];
                $val['companyid'] = $this->package['companyid'];
                if ($val['expire_days'] > 0) {
                    //如果这个字段大于0，表示过期时间为激活后多少天失效
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                }
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    $this->set_log('InsCoupon', $val);
                    continue;
                }
                $this->coupons[] = $val;
            }
        } catch (\Exception $e) {
            $trans->rollBack();
        }
        $trans->commit();
    }

    /**
     * 油卡激活
     * @param array $coupon
     */
    protected function activeOilCoupon($coupon = [])
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
        $trans = $db->beginTransaction();
        try {
            $list = $db->createCommand($sql)->queryAll();
            if (!$list) $this->set_log('OilCoupon', '未找到相应的未激活卡券');
            if (count($list) < $coupon['num']) $this->set_log('OilCoupon', '卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
            foreach ($list as $val) {
                $val['status'] = 1;
                $val['uid'] = $this->user['uid'];
                $val['active_time'] = $now;
                $val['mobile'] = $this->user['mobile'];
                $val['package_id'] = $this->package['id'];
                $val['companyid'] = $this->package['companyid'];
                if ($val['expire_days'] > 0) {
                    //如果这个字段大于0，表示过期时间为激活后多少天失效
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                }
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    $this->set_log('OilCoupon', $val);
                    continue;
                }
                $this->coupons[] = $val;
            }
        } catch (\Exception $e) {
            $trans->rollBack();
        }
        $trans->commit();
    }


    /**
     * 油卡激活
     * @param array $coupon
     */
    protected function activeAiqiyiCoupon($coupon = [])
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
        $trans = $db->beginTransaction();
        try {
            $list = $db->createCommand($sql)->queryAll();
            if (!$list) $this->set_log('AiqiyiCoupon', '未找到相应的未激活卡券');
            if (count($list) < $coupon['num']) $this->set_log('AiqiyiCoupon', '卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
            foreach ($list as $val) {
                $val['status'] = 1;
                $val['uid'] = $this->user['uid'];
                $val['active_time'] = $now;
                $val['mobile'] = $this->user['mobile'];
                $val['package_id'] = $this->package['id'];
                $val['companyid'] = $this->package['companyid'];
                if ($val['expire_days'] > 0) {
                    //如果这个字段大于0，表示过期时间为激活后多少天失效
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                }
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    $this->set_log('AiqiyiCoupon', $val);
                    continue;
                }
                $this->coupons[] = $val;
            }
        } catch (\Exception $e) {
            $trans->rollBack();
        }
        $trans->commit();
    }

    /**
     * 激活代驾券
     * @param array $coupon
     */
    protected function activeEcarCoupon($coupon = [])
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
//            $sql .= ' FOR UPDATE';
            $db = Yii::$app->db;
            $now = time();
            $trans = $db->beginTransaction();

            try {
                $list = $db->createCommand($sql)->queryAll();
                if (!$list) $this->set_log('Ecar', '未找到相应的未激活卡券');
                if (count($list) < $coupon['num']) $this->set_log('Ecar', '卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
                //记录异常卡

                $abnormal = [];
                foreach ($list as $val) {
                    if($val['company']==0){
                        $r = $this->EddrApi->coupon_bind($val['coupon_sn'], $this->user['mobile']);
                        if ($r === false) {
                            $abnormal[] = $val['id'];
                            $this->set_log('Ecar', $val['coupon_sn'] . '与' . $this->user['mobile'] . '绑定发生错误:' . $this->EddrApi->errMsg);
                            continue;
                        }
                        $val['active_time'] = $now;
                        $val['uid'] = $this->user['uid'];
                        $val['bindid'] = $r['bindid'];
                        $val['bonusid'] = $r['bonusid'];
                        $val['mobile'] = $this->user['mobile'];
                        $val['status'] = 1;//将卡券改为已激活状态
                        //这里主要获得过期时间
                        $r = $this->EddrApi->coupon_allinfo($val['coupon_sn'], $this->user['mobile']);
                        if ($r === false) {
                            //没有查到的情况下，默认过期时间为绑定后20天
                            $expire = 0;
                            if (!$val['expire_days']) $val['expire_days'] = 20;
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
                        $val['package_id'] = $this->package['id'];
                        $val['companyid'] = $this->package['companyid'];
                        $r = $this->CCmodel->myUpdate($val);
                        if (!$r) {
                            $this->set_log('Ecar', $val);
                            continue;
                        }
                    }
                    if($val['company']==1){
                        $val['active_time'] = $now;
                        $val['uid'] = $this->user['uid'];
                        $val['mobile'] = $this->user['mobile'];
                        $val['status'] = 1;//将卡券改为已激活状态
                        $val['package_id'] = $this->package['id'];
                        $val['companyid'] = $this->package['companyid'];

                        if (!$val['expire_days']) $val['expire_days'] = 365;
                        $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                        $r = $this->CCmodel->myUpdate($val);
                        if (!$r) {
                            $this->set_log('Ecar', $val);
                            continue;
                        }
                    }

                    //将成功的券加入
                    $this->coupons[] = $val;
                }
                if ($abnormal) {
                    //将异常卡券的状态修改掉
                    $this->CCmodel->myUpdate(['status' => 4], ['id' => $abnormal]);
                }
            } catch (\Exception $e) {
                $trans->rollBack();
            }
            $trans->commit();


    }





    /**
     * 激活救援券
     * @param array $coupon
     */
    protected function activeSaveCarCoupon($coupon = [])
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
        $trans = $db->beginTransaction();
        try {
            $list = $db->createCommand($sql)->queryAll();
            if (!$list) $this->set_log('SaveCar', '未找到相应的未激活卡券');
            if (count($list) < $coupon['num']) $this->set_log('SaveCar', '卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
            foreach ($list as $val) {
                $val['status'] = 1;
                $val['uid'] = $this->user['uid'];
                $val['active_time'] = $now;
                $val['mobile'] = $this->user['mobile'];
                if ($val['expire_days'] > 0) {
                    //如果这个字段大于0，表示过期时间为激活后多少天失效
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                }
                $val['package_id'] = $this->package['id'];
                $val['companyid'] = $this->package['companyid'];
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    $this->set_log('SaveCar', $val);
                    continue;
                }
                $this->coupons[] = $val;
            }
        } catch (\Exception $e) {
            $trans->rollBack();
        }
        $trans->commit();
    }

    /**
     * 激活积分券
     * @param array $coupon
     */
    protected function activeScoreCoupon($coupon = [])
    {

    }

    /**
     * 激活洗车券或臭氧消毒券
     * @param array $coupon
     */
    protected function activeWashCarCoupon($coupon = [])
    {
        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) $map['batch_no'] = $coupon['batch_no'];
        $logtype = $coupon['type'] == 10 ? 'DisinfectCar' : 'WashCar';
        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
//        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();
        $trans = $db->beginTransaction();
        try {
            $list = $db->createCommand($sql)->queryAll();
            if (!$list) $this->set_log($logtype, '未找到相应的未激活卡券');
            if (count($list) < $coupon['num']) $this->set_log($logtype, '卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
            foreach ($list as $val) {
                $val['status'] = 1;
                $val['uid'] = $this->user['uid'];
                $val['active_time'] = $now;
                $val['mobile'] = $this->user['mobile'];
                //如果不限制每月使用次数，则根据优惠券的exprie_days来设置过期时间
                if($val['is_mensal'] == 0){
                    $ac_end_time= $now + $val['expire_days'] * 24 * 3600;
                    //激活日期结束时间戳 20201225 许雄泽
                    $val['use_limit_time'] = mktime(23,59,59,date("m",$ac_end_time),date("d",$ac_end_time),date("Y",$ac_end_time));
                }else{
                    //洗车券过期时间是按自然月计算，所以激活要根据amount设置过期时间
                    $amount = (int)$val['amount'];
                    $endDate = strtotime("+{$amount} month", strtotime(date("Ym") . '01000000')) - 1;
                    $val['use_limit_time'] = $endDate;
                }
                $val['package_id'] = $this->package['id'];
                $val['companyid'] = $this->package['companyid'];
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    $this->set_log($logtype, $val);
                    continue;
                }
                $this->coupons[] = $val;
            }
            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            $this->set_log($logtype, $e->getMessage());
        }

    }

    protected function activeEtcCoupon($coupon =[])
    {

        $map = [
            'uid' => 0,
            'coupon_type' => $coupon['type'],
            'amount' => $coupon['amount'],
            'status' => 0,
        ];
        if (isset($coupon['batch_no']) && $coupon['batch_no']) $map['batch_no'] = $coupon['batch_no'];

        $sql = $this->CCmodel->table()->where($map)->limit((int)$coupon['num'])->getLastSql();
        $sql .= ' FOR UPDATE';
        $db = Yii::$app->db;
        $now = time();
        $trans = $db->beginTransaction();
        try {
            $list = $db->createCommand($sql)->queryAll();
            if (!$list) $this->set_log('EtcCar', '未找到相应的未激活卡券');
            if (count($list) < $coupon['num']) $this->set_log('EtcCar', '卡券不足，缺' . ($coupon['num'] - count($list)) . '张');
            foreach ($list as $val) {
                $val['status'] = 1;
                $val['uid'] = $this->user['uid'];
                $val['active_time'] = $now;
                $val['mobile'] = $this->user['mobile'];
                //如果不限制每月使用次数，则根据优惠券的exprie_days来设置过期时间
                if($val['is_mensal'] == 0){
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                }else{
                    //洗车券过期时间是按自然月计算，所以激活要根据amount设置过期时间
                    $amount = (int)$val['amount'];
                    $endDate = strtotime("+{$amount} month", strtotime(date("Ym") . '01000000')) - 1;
                    $val['use_limit_time'] = $endDate;
                }
                $val['package_id'] = $this->package['id'];
                $val['companyid'] = $this->package['companyid'];
                $r = $this->CCmodel->myUpdate($val);
                if (!$r) {
                    $this->set_log('WashCar', $val);
                    continue;
                }
                $this->coupons[] = $val;
            }
            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
        }

    }

    /**
     * 使用卡券
     */
    public function useCoupon($id = 0)
    {
        $this->CCmodel = new CarCoupon();
        $coupon = $this->CCmodel->table()->where(['id' => $id])->one();
        if (!$this->checkCoupon($coupon)) return false;
        $rs = false;
        switch ($coupon['coupon_type']) {
            case 1:
                $rs = $this->useEcarCoupon($coupon);
                break;
            case 2:
                $rs = $this->useRescueCoupon($coupon);
                break;
            case 3:
                $rs = $this->useScoreCoupon($coupon);
                break;
            case 4:
                $rs = $this->useWashCoupon($coupon);
                break;
            case 5:
                $rs = $this->useOilCoupon($coupon);
                break;
            case SEGWAY:
            case INSPECTION:
                $rs = $this->useInsCoupon($coupon);
                break;
            case 9:
                $rs = $this->useEtcCoupon($coupon);
                break;
            case 10:
                $rs = $this->useWashCoupon($coupon);
                break;

        }
        return $rs;
    }

    protected function checkCoupon($coupon)
    {
        if (empty($coupon)) {
            $this->msg = '卡券不存在';
            return false;
        } elseif ($coupon['status'] != 1) {
            $msg = '';
            if ($coupon['status'] == 0) $msg = '卡券已被禁用';
            if ($coupon['status'] == 2) $msg = '卡券已被使用';
            if ($coupon['status'] == 3) $msg = '卡券已过期';
            $this->msg = $msg;
            return false;
        } else {
            //判断是否过期
            $use_limit_time = $coupon['use_limit_time'];
            if ($use_limit_time && $use_limit_time < time()) {
                $this->msg = '卡券已过期';
                return false;
            }
            if ($coupon['uid'] != $this->user['uid']) {
                $this->msg = '非用户卡券';
                return false;
            }
        }
        return true;
    }

    protected function useEcarCoupon($coupon)
    {
        $coupon['used_num'] += 1;
        $coupon['use_time'] = time();
        $coupon['status'] = 2;
        $r = $this->CCmodel->myUpdate($coupon);
        if (!$r) {
            $this->msg = '卡券使用失败';
            return false;
        }
        return $coupon;
    }

    protected function useRescueCoupon($coupon)
    {
        if ($coupon['amount'] < 0) {
            //不限次数
        } else {
            if ($coupon['amount'] - $coupon['used_num'] <= 0) {
                $this->msg = '卡券已被使用';
                return false;
            }
        }

        $coupon['used_num'] += 1;
        $coupon['use_time'] = time();
        if ($coupon['amount'] < 0) {

        } else {
            if ($coupon['used_num'] >= $coupon['amount']) $coupon['status'] = 2;
        }
        $r = $this->CCmodel->myUpdate($coupon);
        if (!$r) {
            $this->msg = '卡券使用失败';
            return false;
        }
        return $coupon;
    }

    protected function useScoreCoupon($coupon)
    {

    }

    protected function useWashCoupon($coupon)
    {
        $coupon['used_num'] += 1;
        $coupon['use_time'] = time();
        if($coupon['used_num'] >= $coupon['amount']){
            $coupon['status'] = ORDER_SUCCESS;
            $coupon['use_time'] = time();
        }
        $r = $this->CCmodel->myUpdate($coupon);
        if (!$r) {
            $this->msg = '卡券使用失败';
            return false;
        }
        return $coupon;
    }

    /**
     * 使用油卡
     * @param $coupon
     * @return bool
     */
    protected function useOilCoupon($coupon)
    {

        $coupon['used_num'] += 1;
        $coupon['use_time'] = time();
        $coupon['status'] = 2;
        $r = $this->CCmodel->myUpdate($coupon);
        if (!$r) {
            $this->msg = '卡券使用失败';
            return false;
        }
        return $coupon;
    }

    /**
     * 使用年检券，代步券
     * @param $coupon
     * @return bool
     */
    protected function useInsCoupon($coupon)
    {

        $coupon['used_num'] += 1;
        $coupon['use_time'] = time();
        $coupon['status'] = 2;
        $r = $this->CCmodel->myUpdate($coupon);
        if (!$r) {
            $this->msg = '卡券使用失败';
            return false;
        }
        return $coupon;
    }

    /**
     * 使用ETC券
     * @param $coupon
     * @return bool
     */
    protected function useEtcCoupon($coupon)
    {
        $coupon['used_num'] += 1;
        $coupon['use_time'] = time();
        $coupon['status'] = 2;
        $r = $this->CCmodel->myUpdate($coupon);
        if (!$r) {
            $this->msg = '卡券使用失败';
            return false;
        }
        return $coupon;
    }

    /**
     * 优惠券恢复
     * @param int $id
     * @return bool
     */
    public function unuseCoupon($id = 0,$remark='')
    {
        $this->CCmodel = new CarCoupon();
        $coupon = $this->CCmodel->table()->where(['id' => $id])->one();
        $data = ['status' => 1];
        if ($coupon['use_limit_time'] && $coupon['use_limit_time'] < time()) {
            $data['status'] = 3;
        }
        if($remark){
            $data['remark']= $coupon['remark'].'-'.$remark;
        }
        $data['used_num'] = new Expression('used_num - 1');
        $rs = $this->CCmodel->myUpdate($data, ['id' => $id]);
        if (!$rs) return false;
        return true;
    }

    public function __destruct()
    {
        unset($this->package);
        unset($this->CCmodel);
        unset($this->CCPmodel);
        unset($this->EddrApi);
    }
}