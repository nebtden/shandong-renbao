<?php

namespace frontend\controllers;

use common\components\AlxgBase;
use common\components\CarCateType;
use common\components\CarCouponAction;
use common\components\DianDianInspection;
use common\components\EJin;
use common\components\Helpper;
use common\components\W;
use common\models\Car_coupon_explain;
use common\models\CarBrand;
use common\models\CarBrandSeries;
use common\models\CarCoupon;
use common\models\CarCouponMeal;
use common\models\CarCouponPackage;
use common\models\CarDiandianModel;
use common\models\CarDiandianSeries;
use common\models\CarMeal;
use common\models\CarMobile;
use common\models\CarPcr;
use common\models\CarUserCarno;
use common\models\CarUseroilcard;
use common\models\ErrorLog;
use common\models\FansAccount;
use Yii;
use yii\helpers\Url;

class CaruserController extends CloudcarController
{
    protected $uid;
    protected $user;
    public $menuActive = 'caruser';
    public $layout = "cloudcarv2";
    public static $couponTypeCss = [
        1 => 'service-daijia',
        2 => 'service-rescue',
        3 => '',
        4 => 'service-wash-car',
        5 => 'service-jiayou',
        INSPECTION => 'service-year-testing',
        SEGWAY => 'service-daibu-car',
        10 => 'service-disinfect',
    ];

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);

        $this->user = $this->isLogin();
        $this->uid = (int)$this->user['uid'];
        return true;
    }

    public function actionIndex()
    {

        //查看我的油卡
        $user = $this->isLogin();
        $info = (new CarUseroilcard())->table()->where(['uid' => (int)$user['uid']])->one();
        $vip = $this->fans_account();
        $shoper = $this->is_shoper($user['uid']);
        return $this->render('index', ['user' => $user, 'vip' => $vip, 'shoper' => $shoper, 'car_id' => $info['id']
        ]);
    }


    /**
     * 手机号码绑定
     * @return string
     */
    public function actionBindmobile()
    {
        $this->layout = "cloudcarv2";
        $request = Yii::$app->request;
        $user = $this->isLogin();
        if ($request->isPost) {
            $user = $this->isLogin();
            $mobile = $request->post('mobile', null);
            $code = $request->post('code', null);
            if (!$mobile) return $this->json(0, '请输入手机号码！');
            $msg = 'ok';
            if (!Helpper::vali_mobile_code($mobile, $code, $msg)) return $this->json(0, $msg);
            $now = time();
            $data = [
                'uid' => $user['uid'],
                'realname' => $user['nickname'],
                'mobile' => $mobile,
                'u_time' => $now,
            ];
            $this->fans_account(true);
            $ac = $this->fans_account();
            $model = new AlxgBase('fans_account', 'id');
            if ($ac) {
                //更新
                $r = $model->myUpdate($data, ['id' => $ac['id']]);
                if ($r === false) return $this->json(0, '绑定失败，请重试！');
            } else {
                //新增
                $data['c_time'] = $now;
                $id = $model->myInsert($data);
                if (!$id) return $this->json(0, '绑定失败，请重试！');
            }
            $this->fans_account(true);
            $this->activePhonecoupoun();
            return $this->json(1, 'ok', [], Url::to(['index']));
        }
        return $this->render('bindmobile', ['user' => $user]);
    }

    /**
     * 发送验证码
     */
    public function actionSendsms()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $mobile = $request->post('mobile', null);
        if (!$mobile) return $this->json(0, '请输入手机号码');
        $user = $this->isLogin();
        //判断手机号码是否已绑定
        if($user['mobile'] == $mobile) return $this->json(0, '您已绑定该手机，无需重复操作！');
        if ($this->mobile_is_exist($mobile)) {
            return $this->json(0, '手机号码被占用，请更换手机号码！');
        }
        $f = W::sendSms($mobile);
        if (!$f) return $this->json(0, '验证码发送失败，请重试！');
        return $this->json(1, 'ok');
    }

    //绑定的车辆列表
    public function actionCarlist()
    {
        $user = $this->isLogin();
        $list = (new CarUserCarno())->get_user_bind_car($user['uid']);
        $this->layout = IS_V2 ? 'cloudcarv2' : 'cloudcar';
        $tpl = IS_V2 ? 'carlistv2' : 'carlist';
        return $this->render($tpl, ['list' => $list]);
    }

    //绑定车牌编辑页

    private function ajaxBindcar(){
        $request = Yii::$app->request;
        $data = $request->post();
        if (!$data['card_brand']) return $this->json(0, '请选择车型');
        if (!$data['card_no']) return $this->json(0, '请输入车牌号码');
        if (!$data['car_model_small_fullname']) return $this->json(0, '请选择车型');
        $data['uid'] = $this->uid;
        $data['car_model_id'] = intval($data['car_model_id']);
        //绑定添加到典典年检库
        $data['rg_time'] = $data['rg_time'] ? strtotime($data['rg_time']) : '';
        $rs = (new CarUserCarno())->update_card($data);
        if ($rs === false) return $this->json(0, '添加失败');
        return $this->json(1, '操作成功', [], Url::to(['carlist']));

    }
    public function actionBindcar()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
          return $this->ajaxBindcar();
        }
        $id = (int)$request->get('id', 0);
        $data = null;
        if ($id && is_numeric($id) && $id > 0) {
            $data = (new CarUserCarno())->table()->where(['id' => $id])->one();
            $data['rg_time'] = $data['rg_time'] ? date('Y-m-d', $data['rg_time']) : '';
        }
        //车型数据
        $brandsinfo = (new CarBrand())->select('id,name,initial',['parent_id'=>0])->all();
        $brands = [];
        foreach ($brandsinfo as $key=>$val){
            $brands[$val['initial']][$key]['id'] = $val['id'];
            $brands[$val['initial']][$key]['name'] = $val['name'];
        }
        $anchor = array_keys($brands);
        $car_types = CarUserCarno::$car_type;//车牌类型
        //获得省
        $pro = CarPcr::find()->where('pid=0')->asArray()->all();
        return $this->render('bindcar', ['data' => $data, 'pro' => $pro, 'brands' => $brands, 'anchors' => $anchor, 'car_types' => $car_types]);
    }

    /**
     * 通过品牌id获得其下的车系
     * @return array|string
     */
    public function actionGetcarseries()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            return $this->json(0, 'ok', '非法访问');
        }
        $brandId = (int)$request->post('brandId', 0);
        if (!$brandId) return $this->json(0, 'id=0', '参数不正确');
        $list_0 = (new CarBrand())->select('id',['parent_id'=>$brandId])->all();
        $modelid=array_column($list_0,'id');
        $list = (new CarBrand())->select('id,name,initial',['parent_id'=>$modelid])->all();
        $html = $this->renderPartial('carseries', ['list' => $list]);
        return $this->json(1, 'ok', $html);
    }
    /**
     * 通过系列id获取其下的车型,如果没存数据库就存数据库
     * @param $seriesId
     * @return array|string
     */
    protected  function  carSeries($seriesId){
        if(!$seriesId) return [];
        $carinfoModel= new CarCateType();
        $list = $carinfoModel->Info($seriesId);
        $data = [];
        foreach ($list as $key => $val ){
            unset($val['listdate'],$val['productionstate']);
            $val['parentid'] = $seriesId;
            $data[]=$val;
        }
        $fileds = ['id','name','logo','price','yeartype','salestate','sizetype','parentid'];
        $tablename='{{%car_brand_series}}';
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try{
            $db->createCommand()->batchInsert($tablename,$fileds,$data)->execute();
            $transaction->commit();
        }catch (\Exception $e){
            $list = false;
            $e->getMessage();
            $transaction->rollBack();
        }
        return $list;
    }
    /**
     * 通过系列id获取其下的车型
     * @return array|string
     */
    public function actionGetcarmodel()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $seriesId = (int)$request->post('seriesId', 0);
        if (!$seriesId) return $this->json(0, 'id=0', '参数不正确');
        $list = (new CarBrandSeries())->select('id,name',['parentid'=>$seriesId])->all();
        if(empty($list)){
            $list = $this->carSeries($seriesId);
            if(empty($list)) return $this->json(0,'访问人数过多请重试');
        }
        $html = $this->renderPartial('carmodel', ['list' => $list]);
        return $this->json(1, 'ok', $html);
    }

    public function actionGetcitydesc()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $id = $request->post('id', 0);
        if (!$id) return $this->json(0, 'id=0', '参数不正确');
        $des = CarPcr::find()->groupBy('desc')->where('pid=' . $id)->asArray()->all();
        $html = '';
        foreach ($des as $v) {
            $class = '';
            if ($v['desc'] == 'A') {
                $class = ' class ="active" ';
            }
            $html .= '<li ' . $class . '>' . $v['desc'] . '</li>';
        }
        return $this->json(1, 'ok', $html);
    }

    /**
     * 我的卡券
     */
    public function actionCoupon()
    {

        $this->activePhonecoupoun();
        $this->layout = 'cloudcarv2';
        $user = $this->isLogin();
        $list = (new CarCoupon())->get_user_bind_coupon_list($user['uid']);
        $use_text = (new Car_coupon_explain())->get_use_text();
        return $this->render('coupon', ['list' => $list, 'use_text' => $use_text]);
    }

    /**
     * 激活
     */
    private function activePhonecoupoun()
    {
        try {

            $user = $this->isLogin();
            $fans = $this->fans_account();
            if ($mobile = $fans['mobile']) {
                $carmobile = new CarMobile();
                $package = new CarCouponPackage();
                $list = $carmobile->table()->where(
                    [
                        'uid' => 0,
                        'mobile' => $mobile
                    ]
                )->all();

                //对这些卡包进行处理
                foreach ($list as $coupon) {
                    //对这个优惠券包，进行激活
                    $user['mobile'] = $fans['mobile'];

                    //对这个批次中的优惠券，取最近一条记录，进行激活
                    $package_info = $package->table()->where(
                        [
                            'uid' => 0,
                            'batch_nb' => $coupon['coupon_batch_no']
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
                    $carmobile->myUpdate($data, ['id' => $coupon['id']]);


                    $couponObj = new CarCouponAction($user);
                    $result = $couponObj->activateByPackage($package_info);
                    //对于没有成功使用优惠券的，记录相关日志
                    if (!$result) {
                        throw new \Exception('兑换失败！');
                    }


                }
            }

        } catch (\Exception $exception) {

            $error = new ErrorLog();
            $error->status = ErrorLog::$status['fail'];
            $error->type = ErrorLog::$type['coupon'];
            $error->content = '兑换优惠券失败,错误：' . $exception->getMessage();
            $user = $this->isLogin();
            $error->uid = $user['uid'];
            $error->c_time = time();
            $error->save();
        }

    }

    /**
     * 我的卡券
     */
    public function actionCouponv2()
    {
        $this->layout = 'cloudcarv2';
        $user = $this->isLogin();
        $list = (new CarCoupon())->get_user_bind_coupon_list($user['uid']);
        $use_text = (new Car_coupon_explain())->get_use_text();
        return $this->render('couponv2', ['list' => $list, 'use_text' => $use_text]);
    }

    /**
     * 卡券激活
     * @return string
     */
    public function actionAccoupon()
    {
        $this->menuActive = 'accoupon';
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        if ($request->isPost) {
            $pwd = $request->post('pwd', null);
            $pwd = trim($pwd);
            if (!$pwd) return $this->json(0, '请输入兑换码');
            //判断号码类型
            $type = CarCouponAction::switch_pwd($pwd);
            if ($type === 2) {
                Yii::$app->session['car_meal_pwd'] = $pwd;
                return $this->json(2, 'meal');
            }
            if ($type === 0) {
                return $this->json(0, '此兑换码不存在，请填写正确的兑换码');
            }
            if ($type === 3) {
                return $this->json(0, '兑换码已使用或失效');
            }
            $user = $this->isLogin();
            $fans = $this->fans_account();
            if (!$fans['mobile']) {
                return $this->json(0, '请先绑定手机号码');
            }
            $user['mobile'] = $fans['mobile'];

            $now = time();
            $map = [
                'uid' => $user['uid'],
                'status' => 2
            ];
            $map2 = ['between','use_time',strtotime(date('Y-m-01 00:00:00',$now)),strtotime(date('Y-m-t 23:59:59',$now))];
//查找所有当前用户本月激活的券包
            $packages = (new CarCouponPackage())->table()->select('meal_info')->where($map)->andWhere($map2)->all();
//筛选出包含洗车券的券包
            $washPackage = array_filter($packages,function($val,$key){
                return stripos($val['meal_info'],'"type":"4"') !== false;
            },ARRAY_FILTER_USE_BOTH);
//洗车券每月限制激活3张
            if(count($washPackage) >= 3){
                return $this->json(0,'本月已激活3次洗车卡券，本月无法继续激活');
            }
            $jinyong=(new FansAccount())->select('status',['status' => 0 , 'uid'=>$user['uid']])->one();
            if($jinyong){
                return $this->json(0,'鉴于之前的违规操作，此账号不可核销卡券');
            }

            $couponObj = new CarCouponAction($user);
            $coupons = $couponObj->activate($pwd);
            if ($coupons === false) {
                return $this->json(0, $couponObj->msg);
            }
            if (empty($coupons)) {
                return $this->json(0, '严重错误，请联系客服！');
            }
            $couponModel = new CarCoupon();
            foreach ($coupons as $k => $val) {
                $coupons[$k] = $couponModel->renderCouponList($val);
            }

            (new EJin())->chargeCouponNotice($coupons);//兑换券的通知

            return $this->json(1, 'ok', $coupons);
        }
        //如果用户没有激活手机号码，需要先激活后才可以激活卡券
        $fans = $this->fans_account();
        $isbindmobile = ($fans && $fans['mobile']) ? 1 : 0;
        return $this->render('accoupon', ['isbindmobile' => $isbindmobile]);
    }

    public function actionMeal()
    {
        $pwd = Yii::$app->session['car_meal_pwd'];
        if (!$pwd) {
            $this->redirect(['accoupon']);
            return false;
        }
        //获得套餐信息
        $cmeal = (new CarCouponMeal())->table()->where(['package_pwd' => $pwd])->one();
        if (!$cmeal || 1 !== (int)$cmeal['status']) {
            $this->redirect(['accoupon']);
            return false;
        }
        if (!$cmeal['meal_id']) {
            //无套餐信息
            $this->redirect(['accoupon']);
            return false;
        }
        $ids = explode(',', $cmeal['meal_id']);
        $meal = (new CarMeal())->table()->where(['id' => $ids])->all();
        foreach ($meal as $key => $val) {
            $meal[$key]['meal_info'] = json_decode($val['meal_info'], true);
        }
        return $this->render('meal', ['info' => $meal]);
    }

    public function actionDomeal()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $pwd = Yii::$app->session['car_meal_pwd'];
            if (!$pwd) {
                return $this->json(0, '兑换码丢失，请重新输入');
            }
            $id = $request->post('id');
            $user = $this->isLogin();
            $fans = $this->fans_account();
            $user['mobile'] = $fans['mobile'];
            $couponObj = new CarCouponAction($user);
            $coupons = $couponObj->active_meal($pwd, $id);
            Yii::$app->session['car_meal_pwd'] = null;
            if ($coupons === false) {
                return $this->json(0, $couponObj->msg);
            }
            return $this->json(1, 'ok');
        }
        return '非法访问';
    }
}