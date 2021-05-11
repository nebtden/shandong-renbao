<?php

namespace backend\controllers;

use common\components\Aiqiyi;
use common\models\CarLifeCode;
use yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use backend\util\BController;
use common\models\Car_bank_code;
use common\models\Car_tuhunotice;
use common\models\CarCompany;
use common\models\CarCouponMeal;
use common\models\CarDemand;
use common\models\CarEtcCode;
use common\models\FansAccount;
use common\components\W;
use common\components\CExcel;
use common\models\Fans;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\Car_wash_coupon;
use common\models\Car_coupon_explain;
use common\models\CarMeal;
use common\models\Area;
use common\models\CarMobile;
use common\models\CarChengtaiCode;
use common\models\CarCouponPackageExamine;
use backend\models\Admin;
use common\components\Eddriving;

class CouponController extends BController {

    private  $couponMpagesize=10000;
    private  $cacheTime=7200;
    private  $maxInsertNum=1000;
    public $title = '优惠券';
    public $enableCsrfValidation = false;
    /**
     * 优惠券查询条件
     * @return string
     */

    protected function setCouponWhere(){
        $request = Yii::$app->request;
        $coupon_type = $request->get('coupon_type',0);
        $coupon_sn = trim($request->get('coupon_sn',null));
        $mobile = trim($request->get('mobile',null));
        $coupon_status = $request->get('coupon_status',null);
        $coupon_name = trim($request->get('coupon_name',null));
        $batch_no = trim($request->get('batch_no',null));
        $amount = $request->get('amount',0);
        $coupon_id = intval($request->get('coupon_id'));
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $acs_time = $request->get('acs_time',null);
        $ace_time = $request->get('ace_time',null);

        $where=' id<>0 ';
        if(!empty($coupon_type)){
            $where.=' AND  coupon_type ="'.$coupon_type.'"';
        }

        if(!empty($coupon_sn)){
            $where.=' AND  coupon_sn ="'.$coupon_sn.'"';
        }

        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }

        if(!empty($coupon_status) || $coupon_status=='0'){
            $where.=' AND  status ="'.$coupon_status.'"';
        }

        if(!empty($coupon_name)){
            $where.=' AND  name ="'.$coupon_name.'"';
        }
        if(!empty($amount)){
            $where.=' AND  amount ="'.$amount.'"';
        }
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }
        if(! empty($coupon_id)){
            $where.=' AND  id ="'.$coupon_id.'"';
        }
        if(! empty($companyid)){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        $where=$this->getTimeWhere($where,'use_time',$start_time,$end_time);
        $where=$this->getTimeWhere($where,'c_time',$s_time,$e_time);
        $where=$this->getTimeWhere($where,'active_time',$acs_time,$ace_time);
        return $where;

    }


    public function actionIndex() {
        $coupon_status=CarCoupon::$coupon_status;
        $coupon_type=CarCoupon::$coupon_type;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $allcompany = CarCoupon::$coupon_company_info;
        $companys=(new CarCompany)->getCompany(['id','name']);
        if (Yii::$app->request->isAjax) {
            $couponmodel = new CarCoupon();
            $where=$this->setCouponWhere();
            $res=$couponmodel->page_list('*',$where, 'c_time DESC , id ASC');
            $listrows=$res['rows'];
            $uid=array_column($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname,source',['id'=>$uid])->all();
            $sourceall= array_column($nickname,'source','id');
            $nicknamearr=array_column($nickname,'nickname','id');
            $companynames=array_column($companys,'name','id');

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['active_time'] = !empty($val['active_time'])?date("Y-m-d H:i:s",$val['active_time']):'--';
                $listrows[$k]['use_limit_time'] = !empty($val['use_limit_time'])?date("Y-m-d H:i:s",$val['use_limit_time']):'--';
                $listrows[$k]['use_time'] = !empty($val['use_time'])?date("Y-m-d H:i:s",$val['use_time']):'--';
                $listrows[$k]['c_time'] = date("Y-m-d H:i:s",$val['c_time']);
                $listrows[$k]['status'] = $coupon_status[$val['status']];
                $listrows[$k]['coupon_type'] = $coupon_type[$val['coupon_type']];
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['use_scene'] = $val['coupon_type']==2?$coupon_faulttype[$val['use_scene']]:'--';
                $listrows[$k]['used_num'] = $val['coupon_type']==2||$val['coupon_type']==4||$val['coupon_type']==8 ? $val['used_num'] : '--';
                $listrows[$k]['is_mensal'] =$allcompany[$val['coupon_type']]['is_mensal'][$val['is_mensal']];
                $listrows[$k]['company'] =   $allcompany[$val['coupon_type']]['company'][$val['company']];
                $listrows[$k]['companyid'] = !empty($listrows[$k]['companyid'])?$companynames[$listrows[$k]['companyid']]:'--';
                if($val['uid']){
                    $listrows[$k]['source'] = $sourceall[$val['uid']]=='web端用户'?$sourceall[$val['uid']]:'微信';
                }else{
                    $listrows[$k]['source'] = '--';
                }

            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('index',['coupon_type'=>$coupon_type,'coupon_status'=>$coupon_status,'companys'=>$companys]);
    }

    public function actionDownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $coupon_type = $request->get('coupon_type',0);

        $coupon_sn = trim($request->get('coupon_sn',null));
        $mobile = trim($request->get('mobile',null));
        $coupon_status = $request->get('coupon_status');
        $coupon_name = trim($request->get('coupon_name',null));
        $batch_no = trim($request->get('batch_no',null));
        $amount = $request->get('amount',0);
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $acs_time = $request->get('acs_time',null);
        $ace_time = $request->get('ace_time',null);
        $where = $this->setCouponWhere();
        $cot = (new CarCoupon())->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '优惠券列表-';
        $pagesize = $this->couponMpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'coupon/coupondown',
                    'page'=>1,
                    'coupon_type'=> $coupon_type,
                    'coupon_sn' => $coupon_sn,
                    'mobile' => $mobile,
                    'coupon_status' => $coupon_status,
                    'coupon_name' => $coupon_name,
                    'amount' => $amount,
                    'batch_no' =>$batch_no,
                    'companyid'=>$companyid,
                    'start_time' =>$start_time,
                    'end_time'=>$end_time,
                    's_time' =>$s_time,
                    'e_time'=>$e_time,
                    'acs_time' =>$acs_time,
                    'ace_time'=>$ace_time


                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'coupon/coupondown',
                        'page'=>$i,
                        'coupon_type'=> $coupon_type,
                        'coupon_sn' => $coupon_sn,
                        'mobile' => $mobile,
                        'coupon_status' => $coupon_status,
                        'coupon_name' => $coupon_name,
                        'amount' => $amount,
                        'batch_no' =>$batch_no,
                        'companyid'=>$companyid,
                        'start_time' =>$start_time,
                        'end_time'=>$end_time,
                        's_time' =>$s_time,
                        'e_time'=>$e_time,
                        'acs_time' =>$acs_time,
                        'ace_time'=>$ace_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }
    public function actionCoupondown(){
        $request = Yii::$app->request;
        $where = $this->setCouponWhere();
        $pagesize = $this->couponMpagesize;
        $page = $request->get('page',1);
        $allcompany = CarCoupon::$coupon_company_info;
        $field = ['id','coupon_type','name','amount','coupon_sn','expire_days','status','c_time','active_time','use_limit_time','use_time','mobile','used_num','use_scene','uid','batch_no','companyid','company'];
        $couponlist = (new CarCoupon())->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=array_column($couponlist,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=array_column($nickname,'nickname','id');

        $companys=(new CarCompany)->getCompany(['id','name']);
        $companynames=array_column($companys,'name','id');

        $field = ['id','coupon_type','name','amount','coupon_sn','expire_days','status','c_time','active_time','use_limit_time','use_time','mobile','used_num','use_scene','nickname','batch_no','companyid','company'];
        $str   = ['序号','优惠券类型',['优惠券名称','string'],'面值',['优惠券号码','string'],'多少天后过期','状态','生成时间','激活时间','过期时间','使用时间',['手机号','string'],'已用次数','道路救援类型',['使用人微信昵称','string'],'批号','客户公司名称','供应商'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        $coupon_status=CarCoupon::$coupon_status;
        $coupon_type=CarCoupon::$coupon_type;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        foreach ($couponlist as $e){
            $tmp['id'] = $index;
            $tmp['coupon_type'] = $coupon_type[$e['coupon_type']];
            $tmp['name'] = $e['name'];
            $tmp['amount'] = $e['amount'];
            $tmp['coupon_sn'] = $e['coupon_sn'] ;
            $tmp['expire_days'] = $e['expire_days'];
            $tmp['status']=$coupon_status[$e['status']];
            $tmp['c_time'] = ! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['active_time'] = ! empty($e['active_time']) ? date("Y-m-d H:i:s",$e['active_time']) : '--';
            $tmp['use_limit_time'] = ! empty($e['use_limit_time']) ? date("Y-m-d H:i:s",$e['use_limit_time']) : '--';
            $tmp['use_time'] = ! empty($e['use_time']) ? date("Y-m-d H:i:s",$e['use_time']) : '--';
            $tmp['mobile']=$e['mobile'];
            $tmp['used_num']=$e['coupon_type']=='2' || $e['coupon_type']=='4' ?$e['used_num']:'--' ;
            $tmp['use_scene']=$e['coupon_type']=='2'?$coupon_faulttype[$e['use_scene']]:'--' ;
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['batch_no']=$e['batch_no'];
            $tmp['companyid']=$e['companyid']?$companynames[$e['companyid']]:'--';
            $tmp['company'] =   $allcompany[$e['coupon_type']]['company'][$e['company']];
            $data[] = $tmp;
            $index++;
        }



        $headArr = array_combine($field,$str);
        $fileName = '优惠券列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );

        unset($couponlist,$tmp,$data,$nickname,$nicknamearr,$companys,$companynames);

    }


    public function actionGenerate() {
        $request = Yii::$app->request;
        $coupon_type=CarCoupon::$coupon_type;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $oil_type=CarCoupon::$oil_xxz;
        $oil_company=CarCoupon::$oil_company;
        $wash_company=CarCoupon::$wash_company;
        $aiqiyi_coupon_types = Aiqiyi::$types;

        $driving_coupon_type=Yii::$app->params['driving_coupon_type'] ;
        $driving_coupon_amount=Yii::$app->params['driving_coupon_amount'];
        $ins_company=CarCoupon::$ins_company;
        if($request->isPost) {
            if(!Yii::$app->session ['UID'])return $this->json(0, '登录超时，请重新登陆');
            $authority = $this->getUserAuthority(Yii::$app->session ['UID']) ;
            if(!$authority)return $this->json(0, '您暂无权限发券');
            $data = $request->post();
            $num = intval($data['generate_num']);
            unset($data['generate_num']);
            $model = new CarCoupon();
            //验证
            if (!$num || !is_numeric($num)) return $this->json(0,'请确认输入的是一个正整数') ;
            //验证批号是否的重复
            $batch=W::createNonceCapitalStr(10);
            $res=$model->table()->select('batch_no')->where(['batch_no'=>$batch])->one();
            if($res) return $this->json(0,'批号重复请重新提交');
            if (empty($data['name'])) return $this->json(0,'请填写卡的名称') ;

            if($data['coupon_type']=='5'){
                $checkoil=array_flip($oil_type);
                if(!in_array($data['oil_type'],$checkoil)) return '非法请求';
            }

            //数据处理
            if($data['coupon_type']=='2' && (int)$data['amount'] > 366) return $this->json(0,'道路救援券使用次数不能超过366次');
            if($data['coupon_type']=='4' && (int)$data['amount'] > 12) return $this->json(0,'洗车券使用次数不能超过12次');
            $amount=$data['amount'];
            $use_scene='0';
            $xxz_company='0';
            $bindid='';
            //供应商添加
            $is_mensal=$data['is_mensal'];
            if($data['coupon_type']=='1'){
                $amount=$driving_coupon_amount[$data['bindid']];
                $bindid=$data['bindid'];
                $xxz_company='1';
            }else if($data['coupon_type']=='5'){
                $amount=$data['oil_type'];
                $xxz_company=$data['oil_company'];
            }else if($data['coupon_type']=='2'){
                $xxz_company='1';
                $use_scene=$data['scene'];
            }else if($data['coupon_type']=='4'){
                $xxz_company=$data['wash_company'];
                if($data['wash_company'] == '0')$is_mensal=1;
            }else if($data['coupon_type']=='7'){
                $xxz_company=$data['ins_company'];
            }else if($data['coupon_type']=='10'){
                $xxz_company=2;
            }

            $data['c_time'] = time();
            $fData = null;
            for ($i = 0; $i < $num; $i++) {
                $tmp = [];
                $coupon_sn = $model->generateCardNoxu(8);
                $tmp['coupon_type'] = $data['coupon_type'];
                $tmp['name'] = $data['name'];
                $tmp['amount'] = $amount;
                $tmp['expire_days'] = $data['expire_days'];
                $tmp['use_scene'] = '0';
                $tmp['use_scene'] = $use_scene;
                $tmp['c_time'] = $data['c_time'];
                $tmp['coupon_sn'] = $coupon_sn;
                $tmp['coupon_pwd'] = '';
                $tmp['batch_no'] = $batch;
                $tmp['company'] = $xxz_company;
                $tmp['bindid'] = $bindid;
                $tmp['is_mensal']  = $is_mensal;
                ;
                $fData [] = $tmp;
            }


            //分割数组并插入缓存
            $this->maxInsertNum = 800;
            $result=$this->segmentationArr($fData);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            unset($fData,$tmp);
            return $this->json(1,'成功',$result);
        }

        return $this->render('generate',
            [
                'coupon_type'=>$coupon_type,
                'coupon_faulttype'=>$coupon_faulttype,
                'driving_coupon_type'=>$driving_coupon_type,
                'aiqiyi_coupon_types'=>$aiqiyi_coupon_types,
                'oil_type'=>$oil_type,
                'oil_company'=>$oil_company,
                'wash_company'=>$wash_company,
                'ins_company'=>$ins_company
            ]
        );
    }

    /**
     * 优惠券批量插入数据库
     * @return string
     */
    public function actionBatchcoupon(){
        set_time_limit(0);
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
            }else{
                $fields = ['coupon_type', 'name', 'amount', 'expire_days','use_scene','c_time','coupon_sn', 'coupon_pwd','batch_no','company','bindid','is_mensal'];
                $tablename='{{%car_coupon}}';
                $msg=$this->insertDb($data,$fields,$tablename);
            }
            $msg['url'] = Url::to(['coupon/index']);

            return $msg;
        }
        return '非法访问';
    }


    /**
     * 券包查询条件
     * @return string
     */

    protected function setPackageWhere(){
        $request = Yii::$app->request;
        $status = $request->get('status');
        $batch_nb = trim($request->get('batch_nb',null));
        $package_sn = trim($request->get('package_sn',null));
        $user_id = intval($request->get('user_id',0));
        $companyid = intval($request->get('companyid',0));
        $mobile=$request->get('mobile',0);
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $c_batch_no = trim($request->get('c_batch_no',null));
        $package_pwd = trim($request->get('package_pwd',null));
        $where=' id<>0 ';

        if(!empty($status) || $status=='0' ){
            $where.=' AND  status ="'.$status.'"';
        }

        if(!empty($batch_nb)){
            $where.=' AND  batch_nb ="'.$batch_nb.'"';
        }

        if(!empty($package_sn)){
            $where.=' AND  package_sn ="'.$package_sn.'"';
        }

        if(!empty($user_id)){
            $where.=' AND  uid ="'.$user_id.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        if(!empty($c_batch_no)){
            $where.=' AND  meal_info  LIKE "%'.$c_batch_no.'%"';
        }
        if(!empty($package_pwd)){
            $where.=' AND  package_pwd ="'.$package_pwd.'"';
        }

        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        $where=$this->getTimeWhere($where,'use_time',$s_time,$e_time);

        return $where;

    }
    //获取客户公司
    public function actionGetNewCompany() {

        $request = Yii::$app->request;
        $keyword = $request->post('keyword',null);
        $model = new CarCompany;
        $where = '';
        if(!empty($keyword)) {
            $where =' `name` LIKE "%'.trim($keyword).'%" ';
            $companys=  $model->table()->select('id,name')->where($where)->orderBy('browse_num DESC , id DESC ')->all();
        }else{
            $companys=  $model->table()->select('id,name')->where($where)->orderBy('browse_num DESC , id DESC ')->page(1, 10)->all();
        }
        foreach($companys as $key=>$val){
            $companys[$key]['title'] =  $val['name'];
        }

        return json_encode(['data'=>$companys]);
    }


    public function actionPackagelist() {
        $status=CarCouponPackage::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $allcompany=CarCoupon::$coupon_company_info;
        $companys=(new CarCompany)->getCompany(['id','name']);

        if (Yii::$app->request->isAjax) {
            $model= new CarCouponPackage();
            $where=$this->setPackageWhere();
            $res=$model->page_list('*',$where, ' c_time DESC,id ASC ');
            $listrows=$res['rows'];

            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname,source',['id'=>$uid])->all();
            $nicknamearr= array_column($nickname,'nickname','id');
            $sourceall= array_column($nickname,'source','id');
            $companynames=[];
            if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['uid'] = !empty($val['uid'])?$val['uid']:'--';
                $listrows[$k]['use_time'] = !empty($val['use_time'])?date("Y-m-d H:i:s",$val['use_time']):'--';
                $listrows[$k]['use_limit_time'] = !empty($val['use_limit_time'])?date("Y-m-d H:i:s",$val['use_limit_time']):'--';
                $listrows[$k]['status'] = $status[$val['status']];
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['c_time'] = date("Y-m-d H:i:s",$val['c_time']);
                $listrows[$k]['mobile'] = !empty($val['mobile'])?$val['mobile']:'--';
                if($val['uid']){
                    $listrows[$k]['source'] = $sourceall[$val['uid']]=='web端用户'?$sourceall[$val['uid']]:'微信';
                }else{
                    $listrows[$k]['source'] = '--';
                }
                $info=json_decode($val['meal_info'],true);
                foreach ($info as $v){
                    $listrows[$k]['info'].=$allcompany[$v['type']]['name'].'（'.$v['amount'].'，'.$v['num'].'，'.$v['batch_no'];
                    if($v['type']=='2')$listrows[$k]['info'].='，'.$coupon_faulttype[$v['scene']];
                    $listrows[$k]['info'].='）<br>';
                }
                if(empty($listrows[$k]['roadrescue']))$listrows[$k]['roadrescue']='--' ;
                $listrows[$k]['companyid'] = !empty($listrows[$k]['companyid'])?$companynames[$listrows[$k]['companyid']]:'--';

            }
            $res['rows']=$listrows;
            $res['where']=$where;
            return json_encode($res);
        }
        return $this->render('packagelist',['status'=>$status,'companys'=>$companys]);
    }

    public function actionPackagedownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $status = $request->get('status');
        $batch_nb = $request->get('batch_nb',null);
        $package_sn = $request->get('package_sn',null);
        $user_id = intval($request->get('user_id',0));
        $companyid = intval($request->get('companyid',0));
        $mobile=$request->get('mobile',0);
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $c_batch_no = trim($request->get('c_batch_no',null));
        $package_pwd = trim($request->get('package_pwd',null));
        $where = $this->setPackageWhere();
        $cot = (new CarCouponPackage())->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '券包列表-';
        $pagesize = 20000;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'coupon/packagedown',
                    'page'=>1,
                    'status'=> $status,
                    'batch_nb' => $batch_nb,
                    'package_sn' => $package_sn,
                    'user_id' => $user_id,
                    'companyid' => $companyid,
                    'mobile' => $mobile,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    's_time' => $s_time,
                    'e_time' => $e_time,
                    'c_batch_no'=>$c_batch_no,
                    'package_pwd'=>$package_pwd
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'coupon/packagedown',
                        'page'=>$i,
                        'status'=> $status,
                        'batch_nb' => $batch_nb,
                        'package_sn' => $package_sn,
                        'user_id' => $user_id,
                        'companyid' => $companyid,
                        'mobile' => $mobile,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        's_time' => $s_time,
                        'e_time' => $e_time,
                        'c_batch_no'=>$c_batch_no,
                        'package_pwd'=>$package_pwd
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }
    public function actionPackagedown(){
        $request = Yii::$app->request;
        $where = $this->setPackageWhere();
        $pagesize = 20000;
        $page = $request->get('page',1);
        $allcompany=CarCoupon::$coupon_company_info;

        $pfield=['id','package_sn','package_pwd','c_time','use_limit_time','use_time','status','mobile','meal_info','companyid','batch_nb','is_redeem','uid','batch_nb'];
        $couponlist = (new CarCouponPackage())->table()->select($pfield)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($couponlist,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

        $companys=(new CarCompany)->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

        $field = ['id','package_sn','package_pwd','c_time','use_limit_time','use_time','status','nickname','mobile','companyid','batch_nb'];
        $packey = array_column($allcompany,'showkey');
        $field = array_merge($field,$packey);
        $str   = ['序号',['券包号码','string'],'卡包兑换码','券包生成时间','过期时间','使用时间','状态',['使用人微信昵称','string'],'激活手机','客户公司名称','批号'];
        $pactext = array_column($allcompany,'showtext');
        $str = array_merge($str,$pactext);
        $data  = null;
        $tmp   = null;
        $index = 1;
        $status=CarCouponPackage::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        foreach ($couponlist as $e){
            $tmp['id'] = $index;
            $tmp['package_sn'] = $e['package_sn'];
            $tmp['package_pwd'] = $e['package_pwd'];
            $tmp['c_time'] = ! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['use_limit_time'] = ! empty($e['use_limit_time']) ? date("Y-m-d H:i:s",$e['use_limit_time']) : '--';
            $tmp['use_time'] = ! empty($e['use_time']) ? date("Y-m-d H:i:s",$e['use_time']) : '--';
            $tmp['status']=$status[$e['status']];
            $tmp['nickname']=preg_quote($nicknamearr[$e['uid']]);
            $tmp['mobile'] = !empty($e['mobile'])?$e['mobile']:'--';
            $tmp['companyid']=$companynames[$e['companyid']];
            $tmp['batch_nb'] = $e['batch_nb'];
            $info=json_decode($e['meal_info'],true);
            foreach ($allcompany as $a){
                $tmp[$a['showkey']]='--';
            }
            foreach ($info as $val){
                $str1='（'.$val['amount'].'，'.$val['num'].'）';
                $tmp[$allcompany[$val['type']]['showkey']]=$str1;
                if ($val['type']=='2')$tmp[$allcompany['2']['showkey']]='（'.$val['amount'].'，'.$val['num'].'，'.$coupon_faulttype[$val['scene']].'）';
            }
            $data[] = $tmp;
            $index++;
        }

        unset($orderlist);

        $headArr = array_combine($field,$str);
        $fileName = '券包列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
    }

    /**
     * 券包或套餐券批量禁用
     * 需要参数批号 @param  $batch_no
     * 返回 列表页
     */

    public function actionForbidden() {

        $request = Yii::$app->request;
        $status=CarCouponPackage::$status;
        unset($status['2'],$status['3']);
        if($request->isPost) {
            $data = $request->post();
            if(empty($data['batch_no'])) return '批号不能为空';
            $data['batch_no']=trim($data['batch_no']);
            if($data['type'] == 0){
                $Model= new CarCouponPackage();
                $field='batch_nb';
                $url=['coupon/packagelist'];
            }elseif($data['type'] == 1){
                $Model= new CarCouponMeal();
                $field='batch_no';
                $url=['coupon/couponmeallist'];
            }else{
                return '参数错误';
            }

            $wherestatus= $data['status']=='0' ? 1 : 0 ;

            $res=$Model->table()->select("id")->where([$field=>$data['batch_no'],'status' => $wherestatus])->one();
            if(!$res) return '无操作数据';
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $Model->myUpdate(['status' => $data['status']],[$field=>$data['batch_no'],'status' => $wherestatus]);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $e->getMessage();
            }
            return $this->redirect($url);
        }

        return $this->render('forbidden',['status'=>$status]);
    }
    public function actionMealforbidden() {

        $status=CarCouponPackage::$status;
        unset($status['2'],$status['3']);
        return $this->render('mealforbidden',['status'=>$status]);
    }



    public function actionPgenerate() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $data = $request->post();
            $num = intval($data['generate_num']);
            unset($data['generate_num']);
            $model = new CarCouponPackage();
            //验证
            if (!$num || !is_numeric($num)) return '请确认输入的是一个正整数';

            //验证卡的有效性
            $time=time();
            $gouqitime='0';
            if(!empty($data['use_limit_time'])){
                $gouqitime=strtotime($data['use_limit_time']);
                if ($gouqitime<=$time) return '过期时间必须大于当前时间';
            }
            $fData = null;
            $arr=[];
            for ($i = 0; $i < intval($data['arrnum']); $i++) {
                if($i == 0){
                    $arr[$i]['amount']=$data['amount'];
                    $arr[$i]['type']=$data['coupon_type'];
                    $arr[$i]['num']=$data['num'];
                    if($data['coupon_type']=='2') $arr[$i]['scene']=$data['scene'];
                }else{
                    $arr[$i]['amount']=$data['amount'.$i];
                    $arr[$i]['type']=$data['coupon_type'.$i];
                    $arr[$i]['num']=$data['num'.$i];
                    if($data['coupon_type'.$i]=='2') $arr[$i]['scene']=$data['scene'.$i];
                }
            }
            $jsonstr=json_encode($arr);
            $batch_nb=W::createNonceCapitalStr(8);
            for ($i = 0; $i < $num; $i++) {
                $tmp = [];
                $flowcode = $model->generateCardNo(8, 8);
                $tmp['meal_info']=$jsonstr;
                $tmp['package_sn'] = $flowcode['package_sn'];
                $tmp['package_pwd'] = $flowcode['package_pwd'];
                $tmp['batch_nb'] = $batch_nb;
                $tmp['use_limit_time'] = $gouqitime;
                $tmp['c_time'] =$time;

                $fData [] = $tmp;
            }
            $fields = [ 'meal_info','package_sn', 'package_pwd','batch_nb','use_limit_time','c_time'];
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $db->createCommand()->batchInsert('{{%car_coupon_package}}', $fields, $fData)->execute();
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $e->getMessage();
            }
            return $this->redirect(['coupon/packagelist']);
        }
        $coupon_type=CarCoupon::$coupon_type;//$coupon_faulttype
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        return $this->render('pgenerate',['coupon_type'=>$coupon_type,'coupon_faulttype'=>$coupon_faulttype,]);
    }

    public function actionCouponedit() {
        $couponmodel = new CarCoupon();
        $where = ['id'=>intval($_REQUEST ['id'])];
        $data = $couponmodel->table()->select('*')->where($where)->one();
        if (Yii::$app->request->isPost) {
            $arr = Yii::$app->request->post();
            $arr['use_limit_time']=strtotime($arr['use_limit_time']);
            if(!empty($arr['mobile'])){
                $res_m=W::is_mobile($arr['mobile']);
                if(!$res_m) return '手机号码错误';
            }
            $arr['use_scene'] = $arr['coupon_type']=='2'?$arr['use_scene']:'0';
            $arr['u_time'] = time();
            $couponmodel->myUpdate($arr, $where);
            $this->redirect('index.html');
        }
        $coupon_type=CarCoupon::$coupon_type;
        $coupon_status=CarCoupon::$coupon_status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        return $this->render('couponedit',
            [
                'data' => $data,
                'coupon_status' => $coupon_status,
                'coupon_faulttype' => $coupon_faulttype,
                'coupon_type' => $coupon_type,
                'act' => 'edit'
            ]
        );
    }

    public function actionPackageedit() {
        $couponmodel = new CarCouponPackage();
        $where = ['id'=>intval($_REQUEST ['id'])];
        $data = $couponmodel->table()->select('*')->where($where)->one();

        if (Yii::$app->request->isPost) {
            $postarr = Yii::$app->request->post();
            $postarr['use_limit_time']=strtotime($postarr['use_limit_time']);
            $arr=[];
            for ($i = 0; $i < intval($postarr['arrnum']); $i++) {
                $arr[$i]['amount']=$postarr['amount'.$i];
                $arr[$i]['type']=$postarr['coupon_type'.$i];
                $arr[$i]['num']=$postarr['num'.$i];
                $arr[$i]['batch_no']=$postarr['batch_no'.$i];
                if($postarr['coupon_type'.$i]=='2') $arr[$i]['scene']=$postarr['scene'.$i];
            }
//            $isnull = $couponmodel->table()->select('id')->where(['batch_nb'=>$postarr['batch_nb']])->one();
//            if($isnull) exit('批号已存在！');
            if($data['status'] != 1 ) exit('此券包为非正常状态，不可修改');
            $jsonstr=json_encode($arr);
            $updata['use_limit_time']=$postarr['use_limit_time'];
            $updata['package_sn']=$postarr['package_sn'];
            $updata['package_pwd']=$postarr['package_pwd'];
            $updata['batch_nb']=$postarr['batch_nb'];
            $updata['status']=$postarr['status'];
            $updata['meal_info']=$jsonstr;

            $re=$couponmodel->myUpdate($updata, $where);

            $this->redirect('packagelist.html');
        }


        $coupon_type=CarCoupon::$coupon_type;
        $status=CarCouponPackage::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $info=json_decode($data['meal_info'],true);
        return $this->render('packageedit',
            [
                'data' => $data,
                'pckage_status' => $status,
                'coupon_faulttype' => $coupon_faulttype,
                'coupon_type' => $coupon_type,
                'info' => $info,
                'act' => 'edit'
            ]
        );
    }





//批量导入优惠券
    public function actionLeadin() {
        $coupon_type=CarCoupon::$coupon_type;
        $coupon_amount=CarCoupon::$coupon_amount;

        return $this->render('leadin',
            [
                'coupon_type' => $coupon_type,
                'coupon_amount' => $coupon_amount
            ]
        );
    }

    //批量导入优惠券，插入数据库

    public function actionExcutexls(){
        set_time_limit(0);
        $msg['status'] = 1;
        $msg['error'] = 'ok';
        $msg['data'] = [];
        $request=Yii::$app->request;
        if($request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $post_data=$request->post();
            $path = $post_data['path'];
            $coupon_type = intval($post_data['coupon_type']);
            $coupon_name = trim($post_data['coupon_name']);
            $coupon_amount = trim($post_data['coupon_amount']);
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)){
                $msg['status'] = 0;
                $msg['error'] = '文件不存在';
                unlink($path);
            }

            $expire_days = 365;
            if(!empty($post_data['expire_days']) || $post_data['expire_days'] == 0){
                $expire_days = intval($post_data['expire_days']);
            }
            $batch_no=W::createNonceCapitalStr(10);
            $coupon_all=(new CarCoupon())->table()->select('id')->where(['batch_no'=>$batch_no])->one();
            if($coupon_all){
                $msg['status'] = 0;
                $msg['error'] = '批号已存在请重新提交';
                unlink($path);
            }
            $coupon_all=(new CarCoupon())->table()->select('coupon_sn')->where(['coupon_type'=>$coupon_type])->all();
            $couponall=ArrayHelper::getColumn($coupon_all,'coupon_sn');
            $file=substr($path, strrpos($path, '.')+1);

            $data = CExcel::getExcel($file)->importExcel($path);

            $c_time = time();
            $data_1=[];
            $data_2=[];
            $company=0;
            if($coupon_type === 8 )$company=1;
            foreach ($data as $key=>$val){
                array_push($val,$coupon_amount,$expire_days,$coupon_name,$coupon_type,$c_time,$batch_no,$company);
                if(!empty($val[0])){
                    if(! in_array($val[0],$data_1)){
                        array_push($data_1,$val[0]);
                        if(! in_array($val[0],$couponall)){
                            $data_2[] = $val;
                        }
                    }
                }
            }
            $arrlength=count($data_2);
            if($arrlength == 0 && empty($data_2)){
                $msg['status'] = 0;
                $msg['error'] = '数据不存在或全为重复数据';
                unlink($path);
            }

            //分割数组并插入缓存
            if($msg['status'] == 1){
                $result=$this->segmentationArr($data_2);
                if($result){
                    $msg['data'] = $result;
                }else{
                    $msg['status'] = 0;
                    $msg['error'] = '数据写入缓存失败';
                }
                unset($data,$data_1,$data_2,$coupon_all,$couponall);
                unlink($path);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $msg;
        }
        return '非法访问';
    }
//批量轮询
    public function actionBatchpolling(){
        set_time_limit(0);
        $msg['status'] = 2;
        $msg['error'] = 'ok';
        $msg['data'] = [];
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
                return $msg;
            }else{
                $fileds = ['coupon_sn','amount','expire_days','name','coupon_type','c_time','batch_no','company'];
                $tablename='{{%car_coupon}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/index']);

            return $msg;
        }
        return '非法访问';
    }

    //批量操作数组分割并存入缓存
    protected function segmentationArr($data)
    {

        $chennum=$this->maxInsertNum;
        $arrlength=count($data);
        $xxz_i=ceil($arrlength/$chennum);

        $xxz_key=time().W::createNonceCapitalStr(6);
        $result=['xxzkey'=>$xxz_key,'xxzmaxnum'=>$xxz_i];
        for($i=0;$i<$xxz_i;$i++){
            $res=Yii::$app->cache->set($xxz_key.$i, array_slice($data,($i*$chennum),$chennum), $this->cacheTime);
            if(!$res){
                $result=false;
                break;
            }
        }
        return $result;
    }



    //批量操作插入数据库
    protected function insertDb($data,$fileds,$tablename){

        $msg['status'] = 2;
        $msg['error'] = 'ok';
        $msg['data'] = [];

        $res = Yii::$app->cache->get($data['key'].$data['num']);
        if(!$res){
            $msg['status'] = 0;
            $msg['error'] = '获取缓存失败';
            return $msg;
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try{
            $db->createCommand()->batchInsert($tablename,$fileds,$res)->execute();
            if($data['num'] == ($data['xxzmaxnum']-1)){
                $msg['status'] = 1;
                $msg['error'] = 'yes';
            }else{
                $msg['data'] = ['xxzkey'=>$data['key'],'xxzmaxnum'=>$data['xxzmaxnum']];
            }
            Yii::$app->cache->delete($data['key'].$data['num']);
        }catch (\Exception $e){
            $transaction->rollBack();
            $msg['status'] = 0;
            $msg['error'] = $e->getMessage();
        }
        $transaction->commit();
        return $msg;
    }
    //券批号合并

    public function actionBatchnomerge(){

        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();
            $data['oldbatch_no']=trim($data['oldbatch_no']);
            $couponModel=new CarCoupon();
            if(empty($data['oldbatch_no']) || empty($data['newbatch_no']))return '该主批号或副批号不存在，不可合并';
            $oldcoupon=$couponModel->table()->select('coupon_type,name,amount,expire_days,companyid,use_limit_time')->where(['batch_no'=>$data['oldbatch_no']])->one();
            if(!$oldcoupon)return '该主批号不存在，不可合并';
//            $time=time();
//            if($oldcoupon['use_limit_time'] < $time)return '该主券已过期，不可合并';
            $newbatch_no=array_unique($data['newbatch_no']);
            $length=count($newbatch_no);
            for($i=0;$i<$length;$i++){
                $newcoupon=$couponModel->table()->select('coupon_type,name,amount,expire_days,companyid')->where(['batch_no'=>trim($newbatch_no[$i])])->one();
                if(!$newcoupon)return '第'.($i+1).'个副批号不存在，不可合并';
                if($newcoupon['companyid'] != 0 )return '第'.($i+1).'个副批号已绑其他公司';
                if($newcoupon['coupon_type'] !== $oldcoupon['coupon_type'])return '第'.($i+1).'个副批号与主批号类型不一样，不可合并';
                //if($newcoupon['name'] !== $oldcoupon['name'])return '第'.($i+1).'个副批号与主批号名称不一样，不可合并';
                if($newcoupon['amount'] !== $oldcoupon['amount'])return '第'.($i+1).'个副批号与主批号面值不一样，不可合并';
                if($newcoupon['expire_days'] !== $oldcoupon['expire_days'])return '第'.($i+1).'个副批号与主批号券激活后多少天有效不一样，不可合并';
            }

            $wherecoupon=$data['newbatch_no'];
            $wherestatus=['1','2','3','4'];
            $couponstatus=$couponModel->table()->select('id')->where(['batch_no'=>$wherecoupon,'status'=>$wherestatus])->one();
            if($couponstatus)return '该副券状态不全是未激活状态，不可合并';

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                $couponModel->myUpdate(['batch_no'=>$data['oldbatch_no'],'companyid'=>$oldcoupon['companyid']],['batch_no'=>$data['newbatch_no']]);
            }catch (\Exception $e){
                $transaction->rollBack();
                $msg['status'] = 0;
                $msg['error'] = $e->getMessage();
            }
            $transaction->commit();

            $this->redirect('index.html');
        }
        return $this->render('batchnomerge');
    }

    //券包批号合并
    public function actionPackbatchnomerge(){

        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();
            $data['oldbatch_no']=trim($data['oldbatch_no']);
            $couponModel=new CarCouponPackage();
            if(empty($data['oldbatch_no']) || empty($data['newbatch_no']))return '该主批号或副批号不存在，不可合并';
            $oldcoupon=$couponModel->table()->select('meal_info,companyid,status,is_redeem')->where(['batch_nb'=>$data['oldbatch_no']])->one();
            if(!$oldcoupon)return '该主批号不存在，不可合并';
            $infoold=json_decode($oldcoupon['meal_info'],true);
            $newbatch_no=array_unique($data['newbatch_no']);
            $length=count($newbatch_no);
            for($i=0;$i<$length;$i++){
                $newcoupon=$couponModel->table()->select('meal_info,companyid,status,is_redeem')->where(['batch_nb'=>trim($newbatch_no[$i])])->one();
                if(!$newcoupon)return '第'.($i+1).'个副批号不存在，不可合并';
                if($newcoupon['companyid'] != $oldcoupon['companyid'] )return '第'.($i+1).'个副批号已绑其他公司';
                if($newcoupon['is_redeem'] != $oldcoupon['is_redeem'] )return '第'.($i+1).'个副批号免兑换状态不一致，不可合并';
                if($newcoupon['status'] != 1)return '第'.($i+1).'个副批号非正常状态，不可合并';
                $infonew=json_decode($newcoupon['meal_info'],true);
                if($infoold[0]['amount'] != $infonew[0]['amount'] || $infoold[0]['type'] != $infonew[0]['type'] || $infoold[0]['num'] != $infonew[0]['num']) return '第'.($i+1).'个副批号券信息不一致，不可合并';
            }
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                $couponModel->myUpdate(['batch_nb'=>$data['oldbatch_no'],'companyid'=>$oldcoupon['companyid']],['batch_nb'=>$data['newbatch_no']]);
            }catch (\Exception $e){
                $transaction->rollBack();
                $msg['status'] = 0;
                $msg['error'] = $e->getMessage();
            }
            $transaction->commit();

            $this->redirect('packagelist.html');
        }
        return $this->render('packbatchnomerge');
    }
    //批量导入九天汽车救援券
    public function actionLeadinjiutian() {

        return $this->render('leadinjiutian');
    }

    //批量导入九天汽车救援券，插入数据库
    public function actionExcutexlsjiutian(){
        $msg['status'] = 1;
        $msg['error'] = 'ok';
        $msg['data'] = '';
        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $coupon_name = trim(Yii::$app->request->post('coupon_name'));
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)){
                $msg['status'] = 0;
                $msg['error'] = '文件不存在';
            }
            $batch_no=W::createNonceCapitalStr(10);

            $coupon_all=(new CarCoupon())->table()->select('id')->where(['batch_no'=>$batch_no])->one();
            if($coupon_all){
                $msg['status'] = 0;
                $msg['error'] = '批号已存在请重新提交';
            }

            $coupon_all=(new CarCoupon())->table()->select('coupon_sn')->where(['coupon_type'=>'2'])->all();
            $couponall=ArrayHelper::getColumn($coupon_all,'coupon_sn');
            $file=substr($path, strrpos($path, '.')+1);
            $data = CExcel::getExcel($file)->importExcel($path);
            $c_time = time();
            $data_1=[];
            $data_2=[];
            $coupon_type='2';
            foreach ($data as $key=>$val){
                array_push($val,'-1',$coupon_name,$coupon_type,$c_time,$batch_no,'2','5');
                if(!empty($val[0]) && !empty($val[1]) && !empty($val[2])){
                    if(! in_array($val[0],$data_1)){
                        array_push($data_1,$val[0]);
                        if(! in_array($val[0],$couponall)){
                            $data_2[] = $val;
                        }
                    }
                }
            }
            if(count($data_2) == 0 && empty($data_2)){
                $msg['status'] = 0;
                $msg['error'] = '数据不存在或全为重复数据';
            }
            //分割数组并插入缓存
            if($msg['status'] == 1){
                $result=$this->segmentationArr($data_2);
                if($result){
                    $msg['data'] = $result;
                }else{
                    $msg['status'] = 0;
                    $msg['error'] = '数据写入缓存失败';
                }
                unset($data,$data_1,$data_2,$coupon_all,$couponall);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            $msg['url'] = Url::to(['coupon/index']);
            return $msg;
        }
        return '非法访问';
    }
    public function actionBatchpollingjiu(){
        set_time_limit(0);
        $msg['status'] = 2;
        $msg['error'] = 'ok';
        $msg['data'] = [];
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
                return $msg;
            }else{
                $fileds = ['coupon_sn','coupon_pwd','expire_days','amount','name','coupon_type','c_time','batch_no','company','use_scene'];
                $tablename='{{%car_coupon}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/index']);

            return $msg;
        }
        return '非法访问';
    }

    protected function setCarwashWhere(){
        $request = Yii::$app->request;
        $servicecode = trim($request->get('servicecode',null));
        $batch_no = trim($request->get('batch_no',null));
        $status= $request->get('status');
        $month = trim($request->get('month',null));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);

        $where=' id<>0 ';
        if(!empty($servicecode)){
            $where.=' AND  servicecode ="'.$servicecode.'"';
        }

        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }

        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        if(!empty($month) || strtotime($month)){
            $where.=' AND  month ="'.date('Ym',strtotime($month)).'"';
        }



        if(! empty($start_time) && empty($end_time)){
            $s_time=strtotime($start_time);
            $where.=' AND  use_time >"'.$s_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $e_time=strtotime($end_time)+86400;
            $where.=' AND  use_time > 0 AND  use_time < "'.$e_time.'"';
        }elseif (! empty($start_time) && ! empty($end_time)){
            $st_time=strtotime($start_time);
            $en_time=strtotime($end_time);
            if($st_time > $en_time) {
                $s_time = $en_time;
                $e_time = $st_time;
            }else{
                $s_time = $st_time;
                $e_time = $en_time;
            }
            $e_time=$e_time+86400;
            $where.=' AND  use_time >"'.$s_time.'" AND  use_time < "'.$e_time.'"';
        }

        return $where;

    }

    //洗车券列表
    public function actionCarwashlist() {
        $status=Car_wash_coupon::$status;
        if (Yii::$app->request->isAjax) {
            $model = new Car_wash_coupon();
            $where=$this->setCarwashWhere();
            $res=$model->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];
            //查微信昵称
            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
            //兑换手机
            $coupon_id=ArrayHelper::getColumn($listrows,'coupon_id');
            $couponlist=[];
            $coupon=(new CarCoupon())->table()->select('id,mobile')->where(['id'=>$coupon_id])->all();
            if($coupon)foreach ($coupon as $key=>$val){$couponlist[$val['id']]=$val['mobile'];}
            //核销门店
            $servicecode=ArrayHelper::getColumn($listrows,'servicecode');
            $servicecodelist=[];
            $servicecoderes=(new Car_tuhunotice())->table()->select('servicecode,shopname')->where(['servicecode'=>$servicecode])->all();
            if($servicecoderes)foreach ($servicecoderes as $key=>$val){$servicecodelist[$val['servicecode']]=$val['shopname'];}
            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['status'] = $status[$val['status']];
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['use_time'] = !empty($val['use_time'])?date("Y-m-d H:i:s",$val['use_time']):'--';
                $listrows[$k]['mobile'] = !empty($couponlist[$val['coupon_id']])?$couponlist[$val['coupon_id']]:'--';
                $listrows[$k]['shopname'] = !empty($servicecodelist[$val['servicecode']])?$servicecodelist[$val['servicecode']]:'--';

            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('carwashlist',['status'=>$status]);
    }

    public function actionLeadinwash() {
        return $this->render('leadinwash');
    }

    //批量导入洗车券，插入数据库
    public function actionExcutexlswash(){
        set_time_limit(0);
        $msg['status'] = 1;
        $msg['error'] = 'ok';
        $msg['data'] = '';
        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)){
                $msg['status'] = 0;
                $msg['error'] = '文件不存在';
                return $msg;
            }
            $coupon_all=(new Car_wash_coupon())->table()->select('servicecode')->where(['status'=>'0'])->all();
            $couponall=ArrayHelper::getColumn($coupon_all,'servicecode');
            $file=substr($path, strrpos($path, '.')+1);
            $data = CExcel::getExcel($file)->importExcel($path);
            $c_time = time();
            $data_1=[];
            $data_2=[];
            $month=date("Ym",$c_time);
            $batch_no=W::createNonceCapitalStr(8);
            foreach ($data as $key=>$val){
                array_push($val,$month,$c_time,$batch_no);
                if(!empty($val[0]) && !empty($val[1]) && !empty($val[2])){
                    if(! in_array($val[0],$data_1)){
                        array_push($data_1,$val[0]);
                        if(! in_array($val[0],$couponall)){
                            $val[3]=substr($val[1],0,6);
                            $data_2[] = $val;
                        }
                    }
                }
            }
            if(count($data_2) == 0 && empty($data_2)){
                $msg['status'] = 0;
                $msg['error'] = '数据不存在或全为重复数据';
            }
            //分割数组并插入缓存
            if($msg['status'] == 1){
                $result=$this->segmentationArr($data_2);
                if($result){
                    $msg['data'] = $result;
                }else{
                    $msg['status'] = 0;
                    $msg['error'] = '数据写入缓存失败';
                }
                unset($data,$data_1,$data_2,$coupon_all,$couponall);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            $msg['url'] = Url::to(['coupon/carwashlist']);
            return $msg;
        }
        return '非法访问';
    }
//批量轮询
    public function actionBatchpollingwash(){
        set_time_limit(0);
        $msg['status'] = 2;
        $msg['error'] = 'ok';
        $msg['data'] = [];
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
                return $msg;
            }else{
                $fileds = ['servicecode','startdate','enddate','month','c_time','batch_no'];
                $tablename='{{%car_wash_coupon}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/carwashlist']);

            return $msg;
        }
        return '非法访问';
    }


    //优惠券说明模板搜索条件
    protected function setTemplateWhere(){
        $where=' id<>0 ';
        return $where;
    }
    //优惠券说明模板列表
    public function actionTemplatelist() {
        $coupon_type=CarCoupon::$coupon_type;
        $company=CarCoupon::$coupon_company_info;
        if (Yii::$app->request->isAjax) {
            $couponmodel = new Car_coupon_explain();
            $where=$this->setTemplateWhere();
            $res=$couponmodel->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];
            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['coupon_type'] = $coupon_type[$val['coupon_type']];
                $listrows[$k]['company'] = $company[$val['coupon_type']]['company'][$val['company']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('templatelist',['coupon_type'=>$coupon_type]);
    }

    public function actionTemplateedit() {
        $coupon_type=CarCoupon::$coupon_type;
        $company=CarCoupon::$coupon_company_info;
        $templateModel = new Car_coupon_explain();
        $id=intval($_REQUEST ['id']);
        $where = ['id'=>$id];
        $data = $templateModel->table()->select('*')->where($where)->one();
        $companyxu= $company['1']['company'];
        if(!empty($id)) $companyxu = $company[$data['coupon_type']]['company'];
        if (Yii::$app->request->isPost) {
            if(empty($_POST['content']) || empty($_POST['coupon_type'])) return $this->render ('templateedit',['coupon_type'=>$coupon_type,'data'=>$data]);
            $arr = [
                'content' => $_POST['content'],
                'coupon_type' => $_POST['coupon_type'],
                'company' => $_POST['company'],
            ];

            if(! empty($id)) $arr['id'] = $id;
            $templateModel->edit_ad($arr);
            $this->redirect('templatelist.html');
        }
        return $this->render('templateedit',['coupon_type'=>$coupon_type,'data'=>$data,'company'=>$companyxu]);
    }
    public function actionGetcompany() {
        $request = Yii::$app->request;
        if(! $request->isAjax)  return $this->json(0,'非法请求');
        $data = $request->post();
        $company=CarCoupon::$coupon_company_info;
        return $this->json(1,'成功',$company[$data['company']]['company']);
    }

    /*
     * checkbatchno
     * 验证优惠券批号
     * 数据 @param  $batch_no
     * 返回 @param json
     *
     * */

    public function actionCheckbatchno(){
        $request = Yii::$app->request;
        if(! $request->isAjax)  return $this->json(0,'非法请求');
        if(!Yii::$app->session ['UID'])return $this->json(0, '登录超时，请重新登陆');
        $authority = $this->getUserAuthority(Yii::$app->session ['UID']) ;
        if(!$authority)return $this->json(0, '您暂无权限发券');
        $data = $request->post();
        if(empty($data['company'])) return $this->json(0,'请选选择公司');
        if(empty($data['batch_no'])) return $this->json(0,'批号不能为空');
        if(empty($data['use_limit_time'])) return $this->json(0,'请选择过期时间');


        $reswhere=' batch_no ="'.$data['batch_no'].'"';
        $res = (new CarCoupon())->table()->select('*')->where($reswhere)->one();
        if(! empty($res['companyid']) && $res['companyid'] != $data['company']) return $this->json(0,'此批优惠券已被其他公司占用');

        //检测e代驾券的过期时间
        if($res['coupon_type'] == 1 && $res['company'] == 0){
            $edd = new Eddriving();
            $gouqitime=strtotime($data['use_limit_time']);
            $couponinfo = $edd->coupon_allinfo($res['coupon_sn'],'15802657270');
            if(!$couponinfo)return $this->json(0,'系统出错！');
            if(strtotime($couponinfo['binding_deadline']) < $gouqitime){
                return $this->json(0,'此批代驾券的最后过期时间为'.$couponinfo['binding_deadline'].',无法满足要求');
            }
        }

        $where['batch_no']=$data['batch_no'];
        $where['coupon_type'] = $data['coupon_type']=='6'?'4':$data['coupon_type'];
        $where['amount']=$data['amount'];
        $where['status']='0';
        if($where['coupon_type'] == '2') $where['use_scene']=$data['scene'];

        $maxnum = (new CarCoupon())->table()->select('id')->where($where)->count();

        if(!$maxnum)return $this->json(0,'没有符合条件的数据');
        $checknum=intval($data['coupon_num'])*intval($data['generate_num']);
        if($maxnum < $checknum && $data['coupon_type'] != '2')return $this->json(0,'此类优惠券不足');
        return $this->json(1,'成功');

    }

    public function actionPackagegenerate(){
        $request = Yii::$app->request;
        $coupon_type=CarCoupon::$coupon_new_type;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $oil_xxz=CarCoupon::$oil_xxz;

        $areaModel=new Area();
        $provinces=$areaModel->getProvince();

        $companyModel=new CarCompany();
        $companys=  $companyModel->table()->select(['id','name','maxnum'])->where([])->all();

        if($request->isPost) {
            $data = $request->post();
            $num = intval($data['generate_num']);
            unset($data['generate_num']);
            $model = new CarCouponPackage();
            //验证
            if (!$num || !is_numeric($num)) return $this->json(0, '请确认输入的是一个正整数');
            if(!Yii::$app->session ['UID'])return $this->json(0, '登录超时，请重新登陆');
            $authority = $this->getUserAuthority(Yii::$app->session ['UID']) ;
            if(!$authority)return $this->json(0, '您暂无权限发券');
            $province = substr($data['province'],-2);
            $company_id=$data['company'];
            if($company_id)(new CarCompany)->browseNum($company_id);
            $gu_amount=$data['gu_amount'];

            $batch_nb=W::createNonceCapitalStr(8);
            $checkbatch_nb=  $model->table()->select(['id'])->where(['batch_nb'=>$batch_nb])->one();
            if($checkbatch_nb)return $this->json(0, '批号重复');

            if(! is_numeric($gu_amount))return $this->json(0, '套餐券估值填写不合格');
            if(strlen($gu_amount) != 4) return $this->json(0, '套餐券估值填写不合格');
            if($company_id<10 && $company_id>0) $company_id=str_pad($company_id,2,"0",STR_PAD_LEFT);
            $str=$province.$company_id.$gu_amount;
            $xxzmaxnum=[];
            foreach($companys as $v){
                $xxzmaxnum[$v['id']]=$v['maxnum'];
            }

            //验证卡的有效性
            $time=time();
            $gouqitime='0';
            if(!empty($data['use_limit_time'])){
                $gouqitime=strtotime($data['use_limit_time']);
                if ($gouqitime<=$time) return $this->json(0, '过期时间必须大于当前时间');
            }
            $fData = null;
            $j=0;
            $is_pay=0;
            $couponinfo=$data['couponinfo'];
            $x_sum=count($couponinfo);
            $coupon_batch_nos=[];
            for ($i = 0; $i < $x_sum; $i++) {
                if(!empty($couponinfo[$i])){
                    $arr_1=explode(',',$couponinfo[$i]);
                    if(! empty($arr_1)){
                        if($arr_1[1]=='2'){
                            $arr[$j]['amount']=$arr_1[0];
                            $arr[$j]['type']=$arr_1[1];
                            $arr[$j]['num']=$arr_1[2];
                            $arr[$j]['batch_no']=$arr_1[3];
                            $arr[$j]['scene']=$arr_1[4];
                            $coupon_batch_nos[]=$arr_1[3];
                        }else{
                            $arr[$j]['amount']=$arr_1[0];
                            $arr[$j]['type']=$arr_1[1];
                            $arr[$j]['num']=$arr_1[2];
                            $arr[$j]['batch_no']=$arr_1[3];
                            $coupon_batch_nos[]=$arr_1[3];
                        }
                        if($arr_1[1]=='6') $is_pay=1;
                        $j++;
                    }
                }
            }

            if(empty($arr)) return $this->json(0, '你没有填写任何优惠券信息');
            $jsonstr=json_encode($arr);

            $data['is_redeem']=intval($data['is_redeem']);
            for ($i = 0; $i < $num; $i++) {
                $tmp = [];
                $flowcode = $model->generateCardNo(8, 8);
                $tmp['meal_info']= $jsonstr;
                $sourceNumber = $xxzmaxnum[$data['company']]+$i+1;
                $newNumber = substr(strval($sourceNumber+1000000),1,6);
                $tmp['package_sn'] =$str.$newNumber;
                $tmp['package_pwd'] = $data['is_redeem']==1 ? '' : $flowcode['package_pwd'];
                $tmp['batch_nb'] = $batch_nb;
                $tmp['use_limit_time'] = $gouqitime;
                $tmp['c_time'] = $time;
                $tmp['is_pay'] = $is_pay;
                $tmp['is_redeem'] = $data['is_redeem'];
                $tmp['companyid'] = $company_id;
                $fData [] = $tmp;
            }
            //分割数组并插入缓存
            $this->maxInsertNum = 800;
            $result=$this->segmentationArr($fData);
            if($result){
                $jsondata = $result;
                $jsondata['maxnum']=$num;
                $jsondata['companyid']=$company_id;
                $jsondata['batch_no']=$coupon_batch_nos;
                $jsondata['use_limit_time']=$gouqitime;
            }else{
                return $this->json(0, '操作失败');
            }

            unset($fData,$tmp);
            return $this->json(1,'操作成功',$jsondata);
        }
        return $this->render('packagegenerate',
            [
                'coupon_type'=>$coupon_type,
                'coupon_faulttype'=>$coupon_faulttype,
                'oil_type'=>$oil_xxz,
                'provinces'=>$provinces,
                'companys'=>$companys,
                'fornum'=>count($coupon_type)
            ]);
    }

    /*
     * 申请提交
     * 入参 @param  $data
     * 出参 @param  json
     * */
    public function actionApplyPackage(){

        $request = Yii::$app->request;
        $coupon_type=CarCoupon::$coupon_new_type;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $oil_xxz=CarCoupon::$oil_xxz;
        $areaModel=new Area();
        $provinces=$areaModel->getProvince();
        $companyModel=new CarCompany();
        $companys=  $companyModel->table()->select(['id','name','maxnum'])->where([])->all();
        if($request->isPost) {
            $data = $request->post();
            $num = intval($data['generate_num']);
            $model = new CarCouponPackage();
            //验证
            if (!$num || !is_numeric($num)) return $this->json(0, '请确认输入的是一个正整数');

            $province = substr($data['province'],-2);
            $company_id=$data['company'];
            $gu_amount=$data['gu_amount'];

            $batch_nb=W::createNonceCapitalStr(8);
            $checkbatch_nb=  $model->table()->select(['id'])->where(['batch_nb'=>$batch_nb])->one();
            if($checkbatch_nb)return $this->json(0, '批号重复');

            if(! is_numeric($gu_amount))return $this->json(0, '套餐券估值填写不合格');
            if(strlen($gu_amount) != 4) return $this->json(0, '套餐券估值填写不合格');
            if($company_id<10 && $company_id>0) $company_id=str_pad($company_id,2,"0",STR_PAD_LEFT);
            $str=$province.$company_id.$gu_amount;
            $xxzmaxnum=[];
            foreach($companys as $v){
                $xxzmaxnum[$v['id']]=$v['maxnum'];
            }

            //验证卡的有效性
            $time=time();
            $gouqitime='0';
            if(!empty($data['use_limit_time'])){
                $gouqitime=strtotime($data['use_limit_time']);
                if ($gouqitime<=$time) return $this->json(0, '过期时间必须大于当前时间');
            }
            $fData = null;
            $j=0;
            $is_pay=0;
            $couponinfo=$data['couponinfo'];
            $x_sum=count($couponinfo);
            $coupon_batch_nos=[];
            for ($i = 0; $i < $x_sum; $i++) {
                if(!empty($couponinfo[$i])){
                    $arr_1=explode(',',$couponinfo[$i]);
                    if(! empty($arr_1)){
                        if($arr_1[1]=='2'){
                            $arr[$j]['amount']=$arr_1[0];
                            $arr[$j]['type']=$arr_1[1];
                            $arr[$j]['num']=$arr_1[2];
                            $arr[$j]['batch_no']=$arr_1[3];
                            $arr[$j]['scene']=$arr_1[4];
                            $coupon_batch_nos[]=$arr_1[3];
                        }else{
                            $arr[$j]['amount']=$arr_1[0];
                            $arr[$j]['type']=$arr_1[1];
                            $arr[$j]['num']=$arr_1[2];
                            $arr[$j]['batch_no']=$arr_1[3];
                            $coupon_batch_nos[]=$arr_1[3];
                        }
                        if($arr_1[1]=='6') $is_pay=1;
                        $j++;
                    }
                }
            }

            if(empty($arr)) return $this->json(0, '你没有填写任何优惠券信息');
            $jsonstr=json_encode($arr);
            $data['is_redeem']=intval($data['is_redeem']);
            $tmp = [];
            $tmp['meal_info']= $jsonstr;
            $tmp['use_limit_time'] = $gouqitime;
            $tmp['is_pay'] = $is_pay;
            $tmp['is_redeem'] = $data['is_redeem'];
            $tmp['companyid'] = $company_id;
            $tmp['number'] = $num;
            $examineModel = new CarCouponPackageExamine();
            $res = $examineModel->table()->select('id')->where($tmp)->one();
            if($res){
                return $this->json(0,'请勿重复提交');
            }
            $tmp['sn_prefix'] =$str;
            $tmp['apply_desc'] = $data['apply_desc'];
            $tmp['liaison_pic'] = $data['liaison_pic'];
            $tmp['c_time'] = $time;
            $tmp['uid'] = Yii::$app->session ['UID'];
            $tmp['batch_nb'] = $batch_nb;
            if(! $tmp['uid'])return $this->json(0,'登陆已超时，请重新登陆！');
            $examine_id = $examineModel->myInsert($tmp);
            if(! $examine_id)return $this->json(0,'系统错误！');
            return $this->json(1,'操作成功');
        }
        return $this->render('apply-package',
            [
                'coupon_type'=>$coupon_type,
                'coupon_faulttype'=>$coupon_faulttype,
                'oil_type'=>$oil_xxz,
                'provinces'=>$provinces,
                'companys'=>$companys,
                'fornum'=>count($coupon_type)
            ]);
    }
    /**
     * 券包查询条件
     * @return string
     */

    protected function setApplyWhere(){
        $request = Yii::$app->request;
        $status = $request->get('status');
        $batch_nb = trim($request->get('batch_nb',null));
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $where=' status > 0 ';
        if(!empty($status)){
            $where.=' AND  status ="'.$status.'"';
        }

        if(!empty($batch_nb)){
            $where.=' AND  batch_nb ="'.$batch_nb.'"';
        }

        if(!empty($companyid)){
            $where.=' AND  companyid ="'.$companyid.'"';
        }

        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        $where=$this->getTimeWhere($where,'u_time',$s_time,$e_time);

        return $where;

    }
    /*
      * 申请列表
      * 入参 @param  $where
      * 出参 @param  json
      * */
    public function actionApplyList() {
        $status=CarCouponPackageExamine::$status;
        $allcompany=CarCoupon::$coupon_company_info;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $companys=(new CarCompany)->getCompany(['id','name']);

        if (Yii::$app->request->isAjax) {
            $res = $this->getApplyList($status,$allcompany,$coupon_faulttype,$companys);
            return json_encode($res);
        }
        return $this->render('apply-list',['status'=>$status,'companys'=>$companys]);
    }
    /*
      * 审核列表
      * 入参 @param  $where
      * 出参 @param  json
      * */
    public function actionExamineList() {
        $status=CarCouponPackageExamine::$status;
        $allcompany=CarCoupon::$coupon_company_info;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $companys=(new CarCompany)->getCompany(['id','name']);

        if (Yii::$app->request->isAjax) {
            $res = $this->getApplyList($status,$allcompany,$coupon_faulttype,$companys);
            return json_encode($res);
        }
        return $this->render('examine-list',['status'=>$status,'companys'=>$companys]);
    }
    protected function getApplyList($status,$allcompany,$coupon_faulttype,$companys){
        $model= new CarCouponPackageExamine();
        $where=$this->setApplyWhere();
        $res=$model->page_list('*',$where, ' c_time DESC');
        $listrows=$res['rows'];
        $companynames = array_column($companys,'name','id');
        $adlist =  (new Admin())->select(['id','contact'],[])->all();
        $aduser = array_column($adlist,'contact','id');
        foreach ($res['rows'] as $k => $val) {
            $listrows[$k]['uid'] = $aduser[$val['uid']];
            $listrows[$k]['u_time'] = !empty($val['u_time'])?date("Y-m-d H:i:s",$val['u_time']):'--';
            $listrows[$k]['status'] = $status[$val['status']];
            $listrows[$k]['c_time'] = date("Y-m-d H:i:s",$val['c_time']);
            $info=json_decode($val['meal_info'],true);
            foreach ($info as $v){
                $listrows[$k]['info'].=$allcompany[$v['type']]['name'].'（'.$v['amount'].'，'.$v['num'].'，'.$v['batch_no'];
                if($v['type']=='2')$listrows[$k]['info'].='，'.$coupon_faulttype[$v['scene']];
                $listrows[$k]['info'].='）<br>';
            }
            $listrows[$k]['companyid'] = !empty($listrows[$k]['companyid'])?$companynames[$listrows[$k]['companyid']]:'--';

        }
        $res['rows']=$listrows;
        return $res;
    }

    public function actionApplyEdit() {
        $data = $this->getApplyInfo();
        return $this->render('apply-edit',
            ['data' => $data]
        );
    }
    public function actionExamineEdit() {
        $data = $this->getApplyInfo();
        return $this->render('examine-edit',
            ['data' => $data]
        );
    }
    protected function getApplyInfo(){
        $model = new CarCouponPackageExamine();
        $allcompany=CarCoupon::$coupon_company_info;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $companys=(new CarCompany)->getCompany(['id','name']);
        $companynames = array_column($companys,'name','id');

        $request = Yii::$app->request;
        $where = ['id'=>intval($request->get('id'))];
        $data = $model->table()->select('*')->where($where)->one();
        $info=json_decode($data['meal_info'],true);
        $data['info'] = '';
        foreach ($info as $v){
            $data['info'].=$allcompany[$v['type']]['name'].'（'.$v['amount'].'，'.$v['num'].'，'.$v['batch_no'];
            if($v['type']=='2')$data['info'].='，'.$coupon_faulttype[$v['scene']];
            $data['info'].='）<br>';
        }
        $adlist =  (new Admin())->select(['id','contact'],[])->all();
        $aduser = array_column($adlist,'contact','id');
        $data['uid'] = $aduser[$data['uid']];
        $data['ad_uid'] = $data['ad_uid']?$aduser[$data['ad_uid']]:'--';
        $data['companyid'] = $companynames[$data['companyid']];
        $data['c_time'] = !empty($data['c_time'])?date("Y-m-d H:i:s",$data['c_time']):'--';
        $data['u_time'] = !empty($data['u_time'])?date("Y-m-d H:i:s",$data['u_time']):'--';
        return $data;
    }
    public function actionApplyAjax() {
        if (Yii::$app->request->isPost) {
            $model = new CarCouponPackageExamine();
            $data = Yii::$app->request->post();
            $data['id'] = intval($data['id']);
            $where = ' id = '. $data['id'] .' AND status <> -1 AND status <> 0 ';
            $info = $model->table()->select('*')->where($where)->one();
            if(empty($info)) return $this->json(0,'此单已删除！');
            if($info['status'] == 2) return $this->json(0,'审核通过无需修改！');
            if($info['status'] == 4) return $this->json(0,'券码已生成不可修改！');
            $time = time();
            $uid = Yii::$app->session ['UID'];
            if($data['is_apply'] == 1){
                $datadb['uid'] = $uid;
                $datadb['liaison_pic'] = $data['liaison_pic'];
                $datadb['apply_desc'] = $data['apply_desc'];
                $datadb['c_time'] = $time;
                $datadb['status'] = 1;
                $url = Url::to(["coupon/apply-list"]);
            }elseif($data['is_apply'] == 2){
                if($info['status'] == 3) return $this->json(0,'您已操作，无需重复！');
                $datadb['ad_uid'] = $uid;
                $datadb['examine_desc'] = $data['examine_desc'];
                $datadb['status'] = $data['status'];
                $datadb['u_time'] = $time;
                $url = Url::to(["coupon/examine-list"]);
            }else{
                return $this->json(0,'参数错误！');
            }
            $res = $model->myUpdate($datadb,['id'=>$data['id']]);
            if(! $res) return $this->json(0,'系统错误！');
            return $this->json(1,'操作成功！',[],$url);

        }
    }
    public function actionPackagecreate(){
        $request = Yii::$app->request;
        if($request->isPost) {
            $data = $request->post();
            $id = intval($data['id']);
            $model = new CarCouponPackageExamine();
            //验证
            if(!Yii::$app->session ['UID'])return $this->json(0, '登录超时，请重新登陆');
            $authority = $this->getUserAuthority(Yii::$app->session ['UID']) ;
            if(!$authority)return $this->json(0, '您暂无权限发券');
            $info = $model->table()->select()->where(['id'=>$id])->one();
            if(!$info ) return $this->json(0, '无此提交信息！');
            if($info['status']==1 || $info['status']==3 ) return $this->json(0, '审核未通过不可生成券包');
            if($info['status']==4 ) return $this->json(0, '券包已生成不可重复');
            if($info['status']==5 ) return $this->json(0, '券包正在生成，请稍候');
            $companyModel=new CarCompany();
            $companys=  $companyModel->table()->select(['id','name','maxnum'])->where([])->all();
            $company_id = $info['companyid'];
            if($company_id<10 && $company_id>0) $company_id=str_pad($company_id,2,"0",STR_PAD_LEFT);
            $xxzmaxnum=[];
            foreach($companys as $v){
                $xxzmaxnum[$v['id']]=$v['maxnum'];
            }
            $time=time();
            $fData = null;
            $is_pay=0;
            $packModel = new CarCouponPackage();
            for ($i = 0; $i < $info['number']; $i++) {
                $tmp = [];
                $flowcode = $packModel->generateCardNo(8, 8);
                $tmp['meal_info']= $info['meal_info'];
                $sourceNumber = $xxzmaxnum[$data['company']]+$i+1;
                $newNumber = substr(strval($sourceNumber+1000000),1,6);
                $tmp['package_sn'] =$info['sn_prefix'].$newNumber;
                $tmp['package_pwd'] = $data['is_redeem']==1 ? '' : $flowcode['package_pwd'];
                $tmp['batch_nb'] = $info['batch_nb'];
                $tmp['use_limit_time'] = $info['use_limit_time'];
                $tmp['c_time'] = $time;
                $tmp['is_pay'] = $is_pay;
                $tmp['is_redeem'] = $data['is_redeem'];
                $tmp['companyid'] = $company_id;
                $fData [] = $tmp;
            }
            //分割数组并插入缓存
            $this->maxInsertNum = 800;
            $result=$this->segmentationArr($fData);
            $infoarr = json_decode($info['meal_info'],true);
            $coupon_batch_nos = array_column($infoarr,'batch_no');
            if($result){
                $jsondata = $result;
                $jsondata['maxnum']=$info['number'];
                $jsondata['companyid']=$company_id;
                $jsondata['batch_no']=$coupon_batch_nos;
                $jsondata['use_limit_time']=$info['use_limit_time'];
                $jsondata['examine_id'] = $id;
            }else{
                return $this->json(0, '操作失败');
            }

            unset($fData,$tmp);
            $model->myUpdate(['status'=>5],['id'=>$id]);
            return $this->json(1,'操作成功',$jsondata);
        }

    }




    public function actionPackageleadingtwo(){
        set_time_limit(0);
        $msg['status'] = 2;
        $msg['error'] = 'ok';
        $msg['data'] = [];
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
                return $msg;
            }else{
                $fileds = [ 'meal_info','package_sn', 'package_pwd','batch_nb','use_limit_time','c_time','is_pay','is_redeem','companyid'];
                $tablename='{{%car_coupon_package}}';
                $msg=$this->insertDbPackage($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/packagelist']);

            return $msg;
        }
        return '非法访问';
    }
    //批量操作插入数据库
    protected function insertDbPackage($data,$fileds,$tablename){
        $msg['status'] = 2;
        $msg['error'] = 'ok';
        $msg['data'] = [];

        $res = Yii::$app->cache->get($data['xxzkey'].$data['num']);
        if(!$res){
            $msg['status'] = 0;
            $msg['error'] = '获取缓存失败';
            return $msg;
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try{
            $db->createCommand()->batchInsert($tablename,$fileds,$res)->execute();
            if($data['num'] == ($data['xxzmaxnum']-1)){
                (new CarCompany())->myUpdate(['maxnum' => new Expression("maxnum + ".$data['maxnum']),'packagenum' => new Expression("packagenum + ".$data['maxnum']),],['id'=>$data['companyid']]);
                $couponModel= new CarCoupon();
                $res = $couponModel->table()->select('companyid,coupon_type')->where(['batch_no'=>$data['batch_no']])->one();
                $coupondata['companyid']=$data['companyid'];
                $wheredata['batch_no']=$data['batch_no'];
                if($data['companyid'] == 10 &&  $res['coupon_type'] == 1){
                    $coupondata['use_limit_time']=$data['use_limit_time'];
                }
                $couponModel->myUpdate($coupondata,$wheredata);
                if($data['examine_id']) (new CarCouponPackageExamine())->myUpdate(['status'=>4],['id'=>$data['examine_id']]);
                $msg['status'] = 1;
                $msg['error'] = 'yes';
            }else{
                $msg['data'] = $data;
            }
            Yii::$app->cache->delete($data['xxzkey'].$data['num']);
        }catch (\Exception $e){
            $transaction->rollBack();
            $msg['status'] = 0;
            $msg['error'] = $e->getMessage();
        }
        $transaction->commit();
        return $msg;
    }

    /*
     * meallist
     * 套餐列表
     * 数据 @param
     * 返回 @param
     *
     * */


    protected function setMealWhere(){
        $request = Yii::$app->request;
        $meal_name = trim($request->get('meal_name',null));
        $status= $request->get('status');

        $where=' id<>0 ';
        if(!empty($meal_name)){
            $where.=' AND  name ="'.$meal_name.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        return $where;

    }


    function actionMeallist() {
        $status=CarMeal::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        if (Yii::$app->request->isAjax) {
            $model= new CarMeal();
            $where=$this->setMealWhere();
            $res=$model->page_list('*',$where, 'id ASC');
            $listrows=$res['rows'];

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['uid'] = !empty($val['uid'])?$val['uid']:'--';
                $listrows[$k]['status'] = $status[$val['status']];
                $listrows[$k]['edaicar']='--' ;
                $listrows[$k]['roadrescue']='' ;
                $listrows[$k]['integral']='--' ;
                $listrows[$k]['carwash']='--' ;
                $listrows[$k]['oilinfo']='--' ;
                $info=json_decode($val['meal_info'],true);
                foreach ($info as $val){
                    if($val['type']=='1'){
                        $listrows[$k]['edaicar']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }elseif ($val['type']=='2'){
                        $listrows[$k]['roadrescue'].='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'，'.$coupon_faulttype[$val['scene']].'）';
                    }elseif ($val['type']=='3'){
                        $listrows[$k]['integral']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }elseif ($val['type']=='4'){
                        $listrows[$k]['carwash']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }elseif ($val['type']=='5'){
                        $listrows[$k]['oilinfo']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }
                }
                foreach ($info as $val){
                    if($val['type']=='1'){
                        $listrows[$k]['edaicar']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }elseif ($val['type']=='2'){
                        $listrows[$k]['roadrescue'].='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'，'.$coupon_faulttype[$val['scene']].'）';
                    }elseif ($val['type']=='3'){
                        $listrows[$k]['integral']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }elseif ($val['type']=='4'){
                        $listrows[$k]['carwash']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }elseif ($val['type']=='5'){
                        $listrows[$k]['oilinfo']='（'.$val['amount'].'，'.$val['num'].'，'.$val['batch_no'].'）';
                    }
                }
                if(empty($listrows[$k]['roadrescue']))$listrows[$k]['roadrescue']='--' ;
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('meallist',['status'=>$status]);
    }

    /*
     * mealedit
     * 套餐添加修改
     * 数据 @param
     * 返回 @param
     *
     * */
    function actionMealedit() {
        $couponmodel = new CarMeal();
        $coupon_type=CarCoupon::$coupon_type;
        $status=CarMeal::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $oil_xxz=CarCoupon::$oil_xxz;
        $mealid=intval(Yii::$app->request->get('id'));
        $data=$couponmodel->table()->select('*')->where(['id'=>$mealid])->one();
        if (Yii::$app->request->isPost) {
            $checkoil=array_keys($oil_xxz) ;
            $postarr = Yii::$app->request->post();
            $arr=[];
            $num=intval($postarr['arrnum']);
            for ($i = 0; $i < $num; $i++) {
                if($i == 0 && empty($postarr['id'])){
                    $arr[$i]['amount']=intval($postarr['amount']);
                    if($postarr['coupon_type']=='5'){
                        if( ! in_array($postarr['amount'],$checkoil)) return '油券面额不合法';
                    }
                    $arr[$i]['type']=$postarr['coupon_type'];
                    $arr[$i]['num']=$postarr['num'];
                    $arr[$i]['batch_no']=$postarr['couponbatchno'];
                    if($postarr['coupon_type']=='2') $arr[$i]['scene']=$postarr['scene'];
                }else{
                    $arr[$i]['amount']=intval($postarr['amount'.$i]);
                    if($postarr['coupon_type'.$i]=='5'){
                        if( ! in_array($postarr['amount'.$i],$checkoil)) return '油券面额不合法';
                    }
                    $arr[$i]['type']=$postarr['coupon_type'.$i];
                    $arr[$i]['num']=$postarr['num'.$i];
                    $arr[$i]['batch_no']=$postarr['couponbatchno'.$i];
                    if($postarr['coupon_type'.$i]=='2') $arr[$i]['scene']=$postarr['scene'.$i];
                }

            }

            $jsonstr=json_encode($arr);

            $adddata['name']=$postarr['mealname'];
            $adddata['meal_info']=$jsonstr;
            $adddata['meal_pic']=$postarr['meal_pic'];
            $adddata['remarks']=$postarr['remarks']?$postarr['remarks']:'';
            $adddata['desc']=$postarr['desc']?$postarr['desc']:'';
            $adddata['c_time']=time();

            if(empty($postarr['id'])){
                $re=$couponmodel->myInsert($adddata);
            }else{
                $adddata['status']=$postarr['status'];
                $re=$couponmodel->myUpdate($adddata,['id'=>$postarr['id']]);
            }
            if(! $re)  return '系统错误，请重新提交';
            $this->redirect('meallist.html');
        }

        $info=json_decode($data['meal_info'],true);
        return $this->render('mealedit',
            [
                'status' => $status,
                'coupon_faulttype' => $coupon_faulttype,
                'coupon_type' => $coupon_type,
                'data'=>$data,
                'info'=>$info,
                'act' => 'edit'
            ]
        );
    }

    /*
     * couponmeallist
     * 套餐添加修改
     * 数据 @param
     * 返回 @param
     *
     * */

    protected function setCouponMealWhere(){
        $request = Yii::$app->request;
        $status = $request->get('status');
        $batch_no = trim($request->get('batch_no',null));
        $package_sn = trim($request->get('package_sn',null));
        $user_id = intval($request->get('user_id',0));
        $companyid = intval($request->get('companyid',0));

        $where=' id<>0 ';
        if(!empty($status) || $status=='0' ){
            $where.=' AND  status ="'.$status.'"';
        }

        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }

        if(!empty($package_sn)){
            $where.=' AND  package_sn ="'.$package_sn.'"';
        }

        if(!empty($user_id)){
            $where.=' AND  uid ="'.$user_id.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  companyid="'.$companyid.'"';
        }


        return $where;

    }
    function actionCouponmeallist() {
        $status=CarCouponPackage::$status;
        $companys=(new CarCompany())->getCompany(['id','name']);
        if (Yii::$app->request->isAjax) {
            $model= new CarCouponMeal();
            $where=$this->setCouponMealWhere();
            $res=$model->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];

            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

            $companys=  (new CarCompany())->table()->select(['id','name','maxnum'])->where([])->all();
            $companyname=[];
            foreach($companys as $v){$companyname[$v['id']]=$v['name'];}

            $meals=  (new CarMeal())->table()->select(['id','name'])->where([])->all();
            $mealsname=[];
            foreach($meals as $v){$mealsname[$v['id']]=$v['name'];}

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['uid'] = !empty($val['uid'])?$val['uid']:'--';
                $listrows[$k]['use_time'] = !empty($val['use_time'])?date("Y-m-d H:i:s",$val['use_time']):'--';
                $listrows[$k]['use_limit_time'] = !empty($val['use_limit_time'])?date("Y-m-d H:i:s",$val['use_limit_time']):'--';
                $listrows[$k]['status'] = $status[$val['status']];
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['companyname']=$companyname[$val['companyid']];
                $listrows[$k]['mealsname']=!empty($val['choose_meal_id'])?$mealsname[$val['choose_meal_id']]:'--';

            }
            $res['rows']=$listrows;
            $res['where']=$where;
            return json_encode($res);
        }
        return $this->render('couponmeallist',['status'=>$status,'companys'=>$companys]);
    }

    public function actionMealdownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $status = $request->get('status');
        $batch_no = trim($request->get('batch_no',null));
        $package_sn = trim($request->get('package_sn',null));
        $user_id = intval($request->get('user_id',0));
        $companyid = intval($request->get('companyid',0));
        $where = $this->setCouponMealWhere();
        $cot = (new CarCouponMeal())->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '套餐券列表-';
        $pagesize = 5000;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'coupon/mealdown',
                    'page'=>1,
                    'status'=> $status,
                    'batch_no' => $batch_no,
                    'package_sn' => $package_sn,
                    'user_id' => $user_id,
                    'companyid'=>$companyid
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'coupon/mealdown',
                        'page'=>$i,
                        'status'=> $status,
                        'batch_no' => $batch_no,
                        'package_sn' => $package_sn,
                        'user_id' => $user_id,
                        'companyid'=>$companyid
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }
    public function actionMealdown(){
        $request = Yii::$app->request;
        $where = $this->setCouponMealWhere();
        $pagesize = 5000;
        $page = $request->get('page',1);
        $field = ['id','name','package_sn','package_pwd','companyid','remarks','batch_no','choose_meal_id'];
        $couponlist = (new CarCouponMeal())->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $companys=  (new CarCompany())->table()->select(['id','name','maxnum'])->where([])->all();
        $companyname=[];
        foreach($companys as $v){$companyname[$v['id']]=$v['name'];}
        $meals=  (new CarMeal())->table()->select(['id','name'])->where([])->all();
        $mealsname= array_column($meals,'name','id');

        $str   = ['编号','套餐券名称',['套餐券号码','string'],['套餐券兑换码','string'],'所属公司','备注','批号','用户所选套餐'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($couponlist as $e){
            $tmp['id'] = $index;
            $tmp['name'] = $e['name'];
            $tmp['package_sn'] = $e['package_sn'] ;
            $tmp['package_pwd'] = $e['package_pwd'];
            $tmp['company']=$companyname[$e['companyid']];
            $tmp['remarks']=$e['remarks'];
            $tmp['batch_no']=$e['batch_no'];
            $tmp['choose_meal_id']=$mealsname[$e['choose_meal_id']];
            $data[] = $tmp;
            $index++;
        }

        unset($couponlist);
        $headArr = array_combine($field,$str);
        $fileName = '套餐券列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
    }




    function actionMealgenerate() {

        $request = Yii::$app->request;
        $coupon_type=CarCoupon::$coupon_type;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $oil_xxz=CarCoupon::$oil_xxz;

        $areaModel=new Area();
        $provinces=$areaModel->getProvince();

        $companyModel=new CarCompany();
        $companys=  $companyModel->table()->select(['id','name','maxnum'])->where([])->all();

        $mealModel=new CarMeal();
        $meals=  $mealModel->table()->select(['id','name','remarks'])->where(['status'=>1])->all();
        $mealids=[];
        foreach($meals as $k=>$v ){
            $mealids[$v['remarks']][]=$v['id'];
        }
        $remarks=ArrayHelper::getColumn($meals,'remarks');
        $remarksall=array_unique($remarks);

        if($request->isPost) {
            $data = $request->post();
            $num = intval($data['generate_num']);
            unset($data['generate_num']);
            $model = new CarCouponMeal();
            //验证
            if (!$num || !is_numeric($num)) return '请确认输入的是一个正整数';
            if(empty($data['name']))return '请填写套餐券名';
            //验证卡的有效性
            $time=time();
            $gouqitime='0';
            if(!empty($data['use_limit_time'])){
                $gouqitime=strtotime($data['use_limit_time']);
                if ($gouqitime<=$time) return '过期时间必须大于当前时间';
            }

            $fData = null;
            $province = substr($data['province'],-2);
            $company=$data['company'];
            $amount=$data['amount'];

            if(! is_numeric($amount))return '套餐券估值填写不合格';
            if(strlen($amount) != 4)return '套餐券估值填写不合格';
            if($company<10 && $company>0) $company=str_pad($company,2,"0",STR_PAD_LEFT);
            $str=$province.$company.$amount;
            $xxzmaxnum=[];
            foreach($companys as $v){
                $xxzmaxnum[$v['id']]=$v['maxnum'];
            }
            if(empty($data['remarks'])) return '你没选任何套餐';
            $meal_ids=implode(',',$mealids[$data['remarks']]);
            $batch_no=W::createNonceCapitalStr(8);
            $checkrs=  $model->table()->select(['id'])->where(['batch_no'=>$batch_no])->one();
            if($checkrs)return '批号重复请重新提交';
            for ($i = 1; $i < ($num+1); $i++) {
                $tmp = [];
                $changecode = $model->generateCardNo();
                $tmp['name']= $data['name'];
                $tmp['meal_id']= $meal_ids;
                $sourceNumber = $xxzmaxnum[$data['company']]+$i;
                $newNumber = substr(strval($sourceNumber+1000000),1,6);
                $tmp['package_sn'] =$str.$newNumber;
                $tmp['package_pwd'] = 'ME'.$changecode;
                $tmp['batch_no'] = $batch_no;
                $tmp['use_limit_time'] = $gouqitime;
                $tmp['c_time'] = $time;
                $tmp['companyid'] = $data['company'];
                $tmp['remarks'] = $data['remarks'];
                $fData [] = $tmp;
            }

            $fields = [ 'name','meal_id','package_sn', 'package_pwd','batch_no','use_limit_time','c_time','companyid','remarks'];
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            //['rest_mlimit' => new Expression("rest_mlimit + ".$summoney)]
            try {
                $db->createCommand()->batchInsert('{{%car_coupon_meal}}', $fields, $fData)->execute();
                $companyModel->myUpdate(['maxnum' => new Expression("maxnum + ".$num),'mealnum' => new Expression("mealnum + ".$num),],['id'=>$data['company']]);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $e->getMessage();
            }
            $this->redirect('couponmeallist.html');
        }
        return $this->render('mealgenerate',[
            'provinces'      => $provinces,
            'meals'          => $meals,
            'companys'       => $companys,
            'remarksall'     =>$remarksall
        ]);
    }


//地址显示

    function actionArealist() {
        $request = Yii::$app->request;

        if (Yii::$app->request->isAjax) {
            $model= new Area();
            $where='LENGTH(code) = 3';
            $code= intval($request->get("code"));
            $page = $request->get("pageNumber",1);
            $pagesize = $request->get("pageSize",10);
            if(!empty($code)) $where.=' AND code ='.$code;
            $cot = $model->select("count(id) as cot",$where)->one();
            $arealist=$model->select('id,code,name',$where)->page($page,$pagesize)->all();
            foreach ($arealist as $key=>$val){
                $arealist[$key]['code'] = substr($val['code'],1);
            }
            $res = ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$arealist];
            return json_encode($res);
        }
        return $this->render('arealist');
    }


//公司管理
    function actionCompanylist() {
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $companyModel=new CarCompany();
            $where='';
            $name = $request->get('name',null);
            if(!empty($name))$where=' `name` LIKE "%'.trim($name).'%" ';
            $res=$companyModel->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['is_pnotice'] = $val['is_pnotice']==1 ? '是' : '否' ;
                $listrows[$k]['is_cnotice'] = $val['is_cnotice']==1 ? '是' : '否' ;
                $listrows[$k]['is_anotice'] = $val['is_anotice']==1 ? '是' : '否' ;
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('companylist');
    }
    function actionCompanyedit() {
        $request = Yii::$app->request;
        $companyModel=new CarCompany();
        $companyid=intval($request->get('id'));
        if($companyid)$info=$companyModel->table()->select()->where(['id'=>$companyid])->one();

        if($request->isPost) {
            $data = $request->post();
            $time = time();
            if(empty($data['name']))return '请填写公司名称';
            $dbdata=[];
            $dbdata['name']=trim($data['name']);
            $dbdata['c_time'] = $time;

            $pattern="/(\\w+(-\\w+)*)(\\.(\\w+(-\\w+)*))*(\\?\\S*)?/";//正则表达式

            if($data['is_pnotice']==1){
                if(!preg_match($pattern,$data['package_url'])) return '券包使用回调地址不合法';
                $dbdata['is_pnotice']==1;
                $dbdata['package_url']=$data['package_url'];
            }
            if($data['is_cnotice']==1){
                if(!preg_match($pattern,$data['coupon_url'])) return '券使用回调地址不合法';
                $dbdata['is_cnotice']==1;
                $dbdata['coupon_url']=$data['coupon_url'];
            }
            if($data['is_anotice']==1){
                if(!preg_match($pattern,$data['admin_url'])) return '发包通知地址不合法';
                $dbdata['is_anotice']==1;
                $dbdata['admin_url']=$data['admin_url'];
            }

            if(empty($data['id'])){
                if($data['is_pnotice']==1 || $data['is_cnotice']==1 || $data['is_anotice']==1){
                    $str1=W::createNonceCapitalStr(6);
                    $str2=W::createNonceCapitalStr(4);
                    $dbdata['appkey']=substr(md5($dbdata['name'].$time.$str1),0,16);
                    $dbdata['secret'] = md5($dbdata['name'].$time.$str2);
                }
                if($companyModel->table()->select()->where(['name'=>$data['name']])->one())return '公司名称重复';
                $re=$companyModel->myInsert($dbdata);
            }else{
                $id=$data['id'];
                unset($data['id']);
                $re=$companyModel->myUpdate($data,['id'=>$id]);
            }

            $this->redirect('companylist.html');
        }

        return $this->render('companyedit',['info'=>$info]);
    }


// 手机号码搜索条件
    protected function setMobileWhere(){
        $request = Yii::$app->request;
        $mobile = trim($request->get('mobile'));
        $coupon_batch_no = trim($request->get('coupon_batch_no'));
        $batch_no = trim($request->get('batch_no'));
        $company_id = intval($request->get('company_id'));
        $city = trim($request->get('city'));
        $where=[];

        if(! empty($mobile))            $where['mobile'] = $mobile;
        if(! empty($coupon_batch_no))   $where['coupon_batch_no'] = $coupon_batch_no;
        if(! empty($batch_no))          $where['batch_no'] = $batch_no;
        if(! empty($company_id)){
            $where['company_id'] = $company_id;
            (new CarCompany)->browseNum($company_id);
        }
        if(! empty($city))              $where['city'] = $city;
        Yii::$app->session['sqlwhere'] = $request->get();
        return $where;

    }
//免兑换手机号管理
    function actionMobilelist() {
        $companys =  (new CarCompany())->getCompany();
        $companyname = array_column($companys,'name','id');
        if (Yii::$app->request->isAjax) {
            $Model=new CarMobile();
            $where = $this->setMobileWhere();
            $res=$Model->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];
            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['company_id'] = $companyname[$val['company_id']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('mobilelist',[
            'companylist'=>$companys,
            'getparams' => Yii::$app->request->get()
        ]);
    }

//批量导入手机号
    public function actionLeadinmobile() {

        $companyModel=new CarCompany();
        $companylist=$companyModel->table()->select(['id','name'])->where([])->all();
        return $this->render('leadinmobile',['companylist'=>$companylist]);
    }

    public function actionExcutexlsmobile() {
        set_time_limit(0);

        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $coupon_batch_no = Yii::$app->request->post('batch_no');
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)) return $this->json(0,'文件不存在');
            $batch_no=W::createNonceCapitalStr(8);
            $carMobile        = new CarMobile();
            $carCouponPackage = new CarCouponPackage();
            $is_you=$carMobile->table()->select('id')->where(['batch_no'=>$batch_no])->one();
            if($is_you){
                unlink($path);
                return $this->json(0,'批号重复');
            }
            $is_coupon=$carMobile->table()->select('id')->where(['coupon_batch_no'=>$coupon_batch_no])->one();
            if($is_coupon){
                unlink($path);
                return $this->json(0,'此批优惠券已绑定');
            }
            $couponnum=$carCouponPackage->table()->select('id')->where(['batch_nb'=>$coupon_batch_no,'status'=>1,'is_redeem'=>1])->count();
            if($couponnum==0){
                unlink($path);
                return $this->json(0,'不存在此批券包或此批券包已用完');
            }
            $company_id=$carCouponPackage->table()->select('companyid')->where(['batch_nb'=>$coupon_batch_no,'status'=>1,'is_redeem'=>1])->one();
            $company_id=$company_id['companyid'];
            $file=substr($path, strrpos($path, '.')+1);

            $data = CExcel::getExcel($file)->importExcel($path);
            $changenum = count($data);
            if($couponnum != $changenum){
                unlink($path);
                return $this->json(0,'此批券包数量或此批手机号数量不相等，仔细检查后再导入');
            }
            $c_time = time();
            $data_1=[];
            $data_2=[];
            $tmp = null;
            foreach ($data as $key=>$val){
                $val[1] = $val[1] ? $val[1] : '' ;
                array_push($val,$coupon_batch_no,$company_id,$c_time,$batch_no);
                $val[0]=trim($val[0]);
                $res=W::is_mobile($val[0]);
                if(!empty($val[0])){
                    if($res === true){
                        $data_2[]=$val;
                    }else{
                        $tmp['mobile'] = $val[0];
                        $tmp['key']    = (int)$key+1;
                        $data_1[]=$tmp;
                    }
                }
            }
            $arrlength=count($data_2);
            $mobilearr=array_column($data_2,0);
            if(! empty($tmp)) Yii::$app->cache->set('xxzmobile',$data_1, $this->cacheTime);
            if($arrlength == 0 && empty($data_2)){
                unlink($path);
                return $this->json(0,'数据不存在');

            }
            if($arrlength > $couponnum){
                unlink($path);
                return $this->json(0,'此批券包过少');
            }

            //分割数组并插入缓存
            $this->maxInsertNum = 2000;
            $result=$this->segmentationArr($data_2);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            (new FansAccount())->myUpdate(['u_time'=>$c_time],['mobile'=>$mobilearr]);
            unset($data,$data_1,$data_2);
            return $this->json(1,'成功',$result);
        }
        return '非法访问';
    }
//导出异常手机号
    public function  actionMobiledown(){
        $list =  Yii::$app->cache->get('xxzmobile');
        if(empty($list)) $this->redirect('mobilelist.html');;
        $field = ['mobile','key'];
        $str   = [['异常手机号','string'],'行'];
        $headArr = array_combine($field,$str);
        $fileName = '异常手机号';
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $list );
        Yii::$app->cache->delete('xxzmobile');
        unset($list);
    }

    public function actionBatchmobile(){
        set_time_limit(0);
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
            }else{
                $fileds = ['mobile','city','coupon_batch_no','company_id','c_time','batch_no',];
                $tablename='{{%car_mobile}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/mobilelist']);
            $res =  Yii::$app->cache->get('xxzmobile');
            if(! empty($res) && $msg['status'] === 1){
                $msg['status'] = 3;
                $msg['error'] = '有异常手机号';
                $msg['url'] = Url::to(['coupon/mobiledown']);
                $msg['number'] = count($res);
            }
            return $msg;
        }
        return '非法访问';
    }

// 手机号修改
    public function actionMobileedit() {
        $model = new CarMobile();
        $where = ['id'=>intval($_REQUEST ['id'])];
        $data = $model->table()->select('*')->where($where)->one();
        $companys=  (new CarCompany())->table()->select(['id','name'])->where(['id'=>$data['company_id']])->one();
        if (Yii::$app->request->isPost) {
            $arr = Yii::$app->request->post();
            if($data['uid'] != 0){
                return '已绑定用户不可修改';
            }
            if(!empty($arr['mobile'])){
                $res_m=W::is_mobile($arr['mobile']);
                if(!$res_m) return '手机号码错误';
            }
            $arr['u_time'] = time();
            $model->myUpdate($arr, $where);
            (new FansAccount())->myUpdate(['u_time'=>$arr['u_time']], ['mobile'=>$arr['mobile']]);
            $sqlwhere=Yii::$app->session['sqlwhere'];
            $sqlwhere['mobile'] = $arr['mobile'] ;
            unset($_SESSION['sqlwhere']);
            $url=$this->getSqlWhere(Url::to(['coupon/mobilelist']),$sqlwhere);
            header("Location: $url");

        }
        $data['company_id'] = $companys['name'];
        $data['c_time'] = !empty($data['c_time'])?date("Y-m-d H:i:s",$data['c_time']):'--';
        $data['u_time'] = !empty($data['u_time'])?date("Y-m-d H:i:s",$data['u_time']):'--';
        return $this->render('mobileedit',
            [
                'data' => $data
            ]
        );
    }
// 券包批号修改
    public function actionBatchnoedit() {
        $model = new CarCouponPackage();
        if (Yii::$app->request->isPost) {
            $arr = Yii::$app->request->post();
            $arr['newbatchno'] = trim($arr['newbatchno']);
            $arr['oldbatchno'] = trim($arr['oldbatchno']);
            if(empty($arr['oldbatchno'])  && empty($arr['newbatchno'])){
                return '参数不全';
            }
            if($arr['newbatchno'] == $arr['oldbatchno']){
                return '新旧批号一样无需修改';
            }

            $data = [];
            $batchres = $model->table()->select('id')->where(['batch_nb'=>$arr['newbatchno']])->one();
            if($batchres) return '新批号已存在';
            $ids = $model->table()->select('id')->where(['status' => 1,'batch_nb'=>$arr['oldbatchno']])->page(1, (int)$arr['num'])->orderBy('id desc')->all();
            if(empty($ids) || count($ids) < $arr['num']) return '可操作数据不够';
            $ids = array_column($ids,'id');
            $data['u_time'] = time();
            $data['batch_nb'] = $arr['newbatchno'];
            $res = $model->myUpdate($data , ['id'=>$ids]);
            if(!$res) return '修改失败';
            $this->redirect('mobilelist.html');
        }
        return $this->render('batchnoedit');
    }


 /*人保兑换码管理开始*/

// 人保兑换码搜索条件
    protected function setCodeWhere(){
        $request = Yii::$app->request;
        $package_batch_no = trim($request->get('coupon_batch_no'));
        $batch_no = trim($request->get('batch_no'));
        $company_id = intval($request->get('company_id'));
        $bank_code = trim($request->get('bank_code'));
        $status = $request->get('status');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $check = [];
        $check['orderby'] = 'id DESC';
        $where=' id<>0 ';
        if(!empty($bank_code)){
            $where.=' AND  bank_code LIKE "%'.$bank_code.'%"';
        }
        if(!empty($package_batch_no)){
            $where.=' AND  package_batch_no ="'.$package_batch_no.'"';
        }
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }
        if(!empty($company_id)){
            $where.=' AND  company_id ="'.$company_id.'"';
        }
        if(!empty($status) || $status == '0'){
            if($status == '2') $check['orderby'] = ' u_time DESC ';
            $where.=' AND  status ="'.$status.'"';
        }

        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        $check['where'] = $where;
        return $check;

    }
//人保兑换码管理
    function actionCodelist() {

        $status = Car_bank_code::$status;
        if (Yii::$app->request->isAjax) {
            $Model=new Car_bank_code();
            $check = $this->setCodeWhere();
            $res=$Model->page_list('*',$check['where'],$check['orderby']);
            $listrows=$res['rows'];
            $companys=  (new CarCompany())->table()->select(['id','name'])->where('')->all();
            $companyname=array_column($companys,'name','id');

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['u_time'] = !empty($val['u_time'])?date("Y-m-d H:i:s",$val['u_time']):'--';
                $listrows[$k]['company_id'] = $companyname[$val['company_id']];
                $listrows[$k]['uid'] = $val['uid'] ? $val['uid'] : '--';
                $listrows[$k]['status'] = $status[$val['status']];

            }
            $res['rows']=$listrows;
            $res['where']=$check['where'];
            return json_encode($res);
        }
        $companyModel=new CarCompany();
        $companylist=$companyModel->table()->select(['id','name'])->where([])->all();
        return $this->render('codelist',[
            'companylist'=> $companylist,
            'status'     => $status
        ]);
    }

//批量导入人保兑换码
    public function actionLeadincode() {
        return $this->render('leadincode');
    }

    public function actionExcutexlscode() {
        set_time_limit(0);
        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $coupon_batch_no = Yii::$app->request->post('batch_no');

            $path = $rootPath.$path;
            if(!$path || !file_exists($path)) return $this->json(0,'文件不存在');
            $batch_no=W::createNonceCapitalStr(8);
            $is_you=(new Car_bank_code())->table()->select('id')->where(['batch_no'=>$batch_no])->one();
            if($is_you){
                unlink($path);
                return $this->json(0,'批号重复');
            }

            $couponnum=(new CarCouponPackage())->table()->select('id')->where(['batch_nb'=>$coupon_batch_no,'status'=>1,'is_redeem'=>1])->count();
            if($couponnum==0){
                unlink($path);
                return $this->json(0,'不存在此批券包或此批券包已用完');
            }
            $company_id=(new CarCouponPackage())->table()->select('companyid')->where(['batch_nb'=>$coupon_batch_no,'status'=>1,'is_redeem'=>1])->one();
            $company_id=$company_id['companyid'];
            $file=substr($path, strrpos($path, '.')+1);
            $data = CExcel::getExcel($file)->importExcel($path);
            $c_time = time();
            $data_1=[];
            $data_2=[];
            foreach ($data as $key=>$val){

                array_push($val,$coupon_batch_no,$company_id,$c_time,$batch_no);
                $val[0]=trim($val[0]);
                $length=strlen($val[0]);
                if(!empty($val[0]) && $length < 61 && ! in_array($val[0],$data_1)){
                    array_push($data_1,$val[0]);
                    $data_2[]=$val;
                }
            }
            $arrlength=count($data_2);

            if($arrlength == 0 && empty($data_2)){
                unlink($path);
                return $this->json(0,'数据不存在');

            }
            if($arrlength > $couponnum){
                unlink($path);
                return $this->json(0,'此批券包过少');
            }

            //分割数组并插入缓存
            $result=$this->segmentationArr($data_2);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            unset($data,$data_1,$data_2);
            return $this->json(1,'成功',$result);
        }
        return '非法访问';
    }

//
    public function actionBatchcode(){
        set_time_limit(0);
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
            }else{
                $fileds = ['bank_code','package_batch_no','company_id','c_time','batch_no',];
                $tablename='{{%car_bank_code}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/codelist']);

            return $msg;
        }
        return '非法访问';
    }
/*人保兑换码管理结束*/

/*诚泰兑换码管理开始*/

// 诚泰兑换码搜索条件 tpy_car_chengtai_code
    protected function setChengtaiWhere(){
        $request = Yii::$app->request;
        $package_batch_no = trim($request->get('coupon_batch_no'));
        $batch_no = trim($request->get('batch_no'));
        $company_id = intval($request->get('company_id'));
        $customer_name = trim($request->get('customer_name'));
        $customer_code = trim($request->get('customer_code'));
        $status = $request->get('status');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $check = [];
        $check['orderby'] = 'id DESC';
        $where=' id<>0 ';
        if(!empty($customer_name)){
            $where.=' AND  customer_name LIKE "%'.$customer_name.'%"';
        }
        if(!empty($customer_code)){
            $where.=' AND  customer_code = "'.$customer_code.'"';
        }
        if(!empty($package_batch_no)){
            $where.=' AND  package_batch_no ="'.$package_batch_no.'"';
        }
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }
        if(!empty($company_id)){
            $where.=' AND  company_id ="'.$company_id.'"';
        }
        if(!empty($status) || $status == '0'){
            if($status == '2') $check['orderby'] = ' u_time DESC ';
            $where.=' AND  status ="'.$status.'"';
        }

        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        $check['where'] = $where;
        return $check;

    }

//诚泰洗车兑换码列表
    function actionCtlist() {

        $status = CarChengtaiCode::$status;
        $companys=  (new CarCompany())->table()->select(['id','name'])->where('')->all();
        $companyname=array_column($companys,'name','id');
        if (Yii::$app->request->isAjax) {
            $Model=new CarChengtaiCode();
            $check = $this->setChengtaiWhere();
            $res=$Model->page_list('*',$check['where'],$check['orderby']);
            $listrows=$res['rows'];
            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['u_time'] = !empty($val['u_time'])?date("Y-m-d H:i:s",$val['u_time']):'--';
                $listrows[$k]['company_id'] = $companyname[$val['company_id']];
                $listrows[$k]['uid'] = $val['uid'] ? $val['uid'] : '--';
                $listrows[$k]['status'] = $status[$val['status']];
            }

            $res['rows']=$listrows;
            $res['where']=$check['where'];
            return json_encode($res);
        }

        return $this->render('ctlist',[
            'companylist'=> $companys,
            'status'     => $status
        ]);
    }

//批量导入诚泰兑换码
    public function actionLeadinctcode() {
        return $this->render('leadinctcode');
    }

    public function actionExcutexlsctcode() {
        set_time_limit(0);
        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $coupon_batch_no = Yii::$app->request->post('batch_no');

            $path = $rootPath.$path;
            if(!$path || !file_exists($path)) return $this->json(0,'文件不存在');
            $batch_no=W::createNonceCapitalStr(8);
            $is_you=(new CarChengtaiCode())->table()->select('id')->where(['batch_no'=>$batch_no])->one();
            if($is_you){
                unlink($path);
                return $this->json(0,'批号重复');
            }
            $packageModel = new CarCouponPackage();
            $couponnum=$packageModel->table()->select('id')->where(['batch_nb'=>$coupon_batch_no,'status'=>1,'is_redeem'=>1])->count();
            if($couponnum==0){
                unlink($path);
                return $this->json(0,'不存在此批券包或此批券包已用完');
            }
            $company_id=$packageModel->table()->select('companyid')->where(['batch_nb'=>$coupon_batch_no,'status'=>1,'is_redeem'=>1])->one();
            $company_id=$company_id['companyid'];
            $file=substr($path, strrpos($path, '.')+1);
            $data = CExcel::getExcel($file)->importExcel($path);
            unset($data[0]);
            $c_time = time();
            $data_1=[];
            $data_2=[];
            foreach ($data as $key=>$val){
                array_push($val,$coupon_batch_no,$company_id,$c_time,$batch_no);
                $val[0]=trim($val[0]);
                if(!empty($val[1]) && ! in_array($val[1],$data_1)){
                    $data_2[]=$val;
                    array_push($data_1,$val[1]);
                }
            }

            $arrlength=count($data_2);
            if($arrlength == 0 && empty($data_2)){
                unlink($path);
                return $this->json(0,'数据不存在');

            }
            if($arrlength > $couponnum){
                unlink($path);
                return $this->json(0,'此批券包过少');
            }

            //分割数组并插入缓存
            $result=$this->segmentationArr($data_2);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            unset($data,$data_1,$data_2);
            return $this->json(1,'成功',$result);
        }
        return '非法访问';
    }

    public function actionBatchctcode(){
        set_time_limit(0);
        if(Yii::$app->request->isAjax){
			
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
			
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
            }else{
                $fileds = ['customer_name','customer_code','package_batch_no','company_id','c_time','batch_no'];
                $tablename='{{%car_chengtai_code}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/ctlist']);

            return $msg;
        }
        return '非法访问';
    }

    function actionCtcodeedit() {
        $request = Yii::$app->request;
        $Model=new CarChengtaiCode();
        $id=intval($request->get('id'));
        if($id)$info=$Model->table()->select()->where(['id'=>$id])->one();

        if($request->isPost) {
            $data = $request->post();
            $time = time();
            if(empty($data['customer_name']) || empty($data['customer_code']))return '请填写信息';
            $dbdata=[];
            $dbdata['customer_name']=trim($data['customer_name']);
            $dbdata['customer_code']=trim($data['customer_code']);
            $id=$data['id'];
            $re=$Model->myUpdate($dbdata,['id'=>$id]);
            $this->redirect('ctlist.html');
        }

        return $this->render('ctcodeedit',['info'=>$info]);
    }
    /*诚泰兑换码管理结束*/

    //第三方需求列表
    function actionDemandlist() {
        $request = Yii::$app->request;
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        if (Yii::$app->request->isAjax) {
            $Model=new CarDemand();
            $where=[];

            $order_no  = trim($request->get('order_no',null));
            $companyid = trim($request->get('companyid',null));
            if(!empty($order_no))$where['order_no']=$order_no;
            if(!empty($companyid))$where['companyid']=$companyid;

            $res=$Model->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['timestamp'] = !empty($val['timestamp'])?date("Y-m-d H:i:s",$val['timestamp']):'--';
                $listrows[$k]['info']='' ;
                $info=json_decode($val['package_info'],true);
                foreach ($info as $val){
                    if($val['type']=='1'){
                        $listrows[$k]['info'].='e代驾券（'.$val['amount'].'，'.$val['num'].'）<br>';
                    }elseif ($val['type']=='2'){
                        $listrows[$k]['info'].='道路救援券（'.$val['amount'].'，'.$val['num'].'）<br>';
                    }elseif ($val['type']=='3'){
                        $listrows[$k]['info'].='积分券（'.$val['amount'].'，'.$val['num'].'）<br>';
                    }elseif ($val['type']=='4'){
                        $listrows[$k]['info'].='洗车券（'.$val['amount'].'，'.$val['num'].'）<br>';
                    }elseif ($val['type']=='5'){
                        $listrows[$k]['info'].='油卡充值券（'.$val['amount'].'，'.$val['num'].'）<br>';
                    }elseif ($val['type']=='6'){
                        $listrows[$k]['info'].='在线洗车券（'.$val['amount'].'，'.$val['num'].'）<br>';
                    }elseif ($val['type']=='7'){
                        $listrows[$k]['info'].='年检券（'.$val['amount'].'，'.$val['num'].'）<br>';
                    }


                }
                $listrows[$k]['companyid'] = !empty($listrows[$k]['companyid'])?$companynames[$listrows[$k]['companyid']]:'--';
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('demandlist',
            [
                'companys'=>$companys
            ]
        );
    }

    /*
     * 券包使用率查询条件
     * 数据 @param
     * 返回 @param
     *
     * */

    protected function setRatioWhere(){
        $request = Yii::$app->request;
        $batch_no = trim($request->get('batch_no',null));
        $companyid = intval($request->get('companyid',0));
        $where=' WHERE id <> 0 ';
        if(!empty($batch_no)){
            $where.=' AND  batch_nb ="'.$batch_no.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  companyid="'.$companyid.'"';
        }
        return $where;

    }

    //分批使用率查询

    public function actionRatiocheck(){

        $request = Yii::$app->request;
        $companys=(new CarCompany)->getCompany(['id','name']);
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber", 1);
            $pagesize = $request->get("pageSize", 10);
            $start=($page-1)*$pagesize;
            $where= $this->setRatioWhere();
            $connection  = Yii::$app->db;

            $sql_1="SELECT id  FROM tpy_car_coupon_package ";
            $sql_1.=$where;
            $sql_1.=" GROUP BY batch_nb ORDER BY id DESC ";
            $cototal = $connection->createCommand($sql_1);
            $sum = $cototal->queryAll();

            //分状态查询SQL
            $sql=" SELECT 
                    `companyid`,
                    `batch_nb`,
                    `c_time`,
                    COUNT(`status`) AS total,
                    COUNT( CASE WHEN `status`=1 THEN 1 ELSE NULL END ) AS `not_used`,
                    COUNT( CASE WHEN `status`=2 THEN 1 ELSE NULL END ) AS `already_used`,
                    COUNT( CASE WHEN `status`=3 THEN 1 ELSE NULL END ) AS `expired`,
                    COUNT( CASE WHEN `status`=2 THEN 1 ELSE NULL END )/COUNT(`status`) as `percentage`
                    FROM tpy_car_coupon_package  ";

            $sql.=$where;
            $sql.=" GROUP BY `batch_nb` ORDER BY `percentage` DESC";
            $sql.=" LIMIT {$start},{$pagesize} ";
            $command = $connection->createCommand($sql);
            $duilist = $command->queryAll();
            $companynames=[];
            if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
            foreach($duilist as $k=>$val){
                $duilist[$k]['total']=$val['total'];
                $duilist[$k]['not_used']=$val['not_used'];
                $duilist[$k]['already_used']=$val['already_used'];
                $duilist[$k]['expired']=$val['expired'];
                $duilist[$k]['not_used_percent']=number_format(round(($val['not_used']*100/$val['total']),2),2,".","");
                $duilist[$k]['already_used_percent']=number_format(round(($val['already_used']*100/$val['total']),2),2,".","");
                $duilist[$k]['expired_percent']=number_format(round(($val['expired']*100/$val['total']),2),2,".","");
                $duilist[$k]['batch_nb']=$val['batch_nb'];
                $duilist[$k]['companyid']=$companynames[$val['companyid']];
                $duilist[$k]['c_time'] = date("Y-m-d H:i:s",$val['c_time']);

            }
            $res = ['IsOk' => 1, 'total' => count($sum), 'rows' => $duilist];
            return json_encode($res);
        }
        return      $this->render ('ratiocheck',['companys'=>$companys]);
    }

    /*
     * 券包使用率查询条件
     * 数据 @param
     * 返回 @param
     *
     * */

    protected function setRatioCouponWhere(){
        $request = Yii::$app->request;
        $batch_no = trim($request->get('batch_no',null));
        $companyid = intval($request->get('companyid',0));
        $where=' WHERE id <> 0 ';
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  companyid="'.$companyid.'"';
        }
        return $where;

    }
    //分批券使用率查询
    public function actionRatiocouponcheck(){

        $request = Yii::$app->request;
        $companys=(new CarCompany)->getCompany(['id','name']);
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber", 1);
            $pagesize = $request->get("pageSize", 10);
            $start=($page-1)*$pagesize;
            $where= $this->setRatioCouponWhere();
            $connection  = Yii::$app->db;

            $sql_1="SELECT id  FROM tpy_car_coupon ";
            $sql_1.=$where;
            $sql_1.=" GROUP BY batch_no ORDER BY id DESC ";
            $cototal = $connection->createCommand($sql_1);
            $sum = $cototal->queryAll();

            //分状态查询SQL
            $sql=" SELECT 
                    `companyid`,
                    `batch_no`,
                    `name`,
                    `amount`,
                    COUNT(`status`) AS total,
                    COUNT( CASE WHEN `status`=0 THEN 1 ELSE NULL END ) AS `not_used`,
                    COUNT( CASE WHEN `status`=1 THEN 1 ELSE NULL END ) AS `activation`,
                    COUNT( CASE WHEN `status`=2 THEN 1 ELSE NULL END ) AS `already_used`,
                    COUNT( CASE WHEN `status`=3 THEN 1 ELSE NULL END ) AS `expired`,
                    COUNT( CASE WHEN `status`=2 THEN 1 ELSE NULL END )/COUNT(`status`) as `percentage`
                    FROM tpy_car_coupon  ";

            $sql.=$where;
            $sql.=" GROUP BY `batch_no` ORDER BY `percentage` DESC";
            $sql.=" LIMIT {$start},{$pagesize} ";
            $command = $connection->createCommand($sql);
            $duilist = $command->queryAll();
            $companynames=[];
            if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
            foreach($duilist as $k=>$val){
                $duilist[$k]['total']=$val['total'];
                $duilist[$k]['not_used']=$val['not_used'];
                $duilist[$k]['activation'] = $val['activation'];
                $duilist[$k]['already_used']=$val['already_used'];
                $duilist[$k]['expired']=$val['expired'];
                $duilist[$k]['not_used_percent']=number_format(round(($val['not_used']*100/$val['total']),2),2,".","");
                $duilist[$k]['activation_percent']=number_format(round(($val['activation']*100/$val['total']),2),2,".","");
                $duilist[$k]['already_used_percent']=number_format(round(($val['already_used']*100/$val['total']),2),2,".","");
                $duilist[$k]['expired_percent']=number_format(round(($val['expired']*100/$val['total']),2),2,".","");
                $duilist[$k]['batch_no']=$val['batch_no'];
                $duilist[$k]['companyid']=$companynames[$val['companyid']];

            }
            $res = ['IsOk' => 1, 'total' => count($sum), 'rows' => $duilist];
            return json_encode($res);
        }
        return      $this->render ('ratiocouponcheck',['companys'=>$companys]);
    }

    //洗车券使用率查询

    protected function setRatioWashWhere(){
        $request = Yii::$app->request;
        $batch_no = trim($request->get('batch_no',null));
        $companyid = intval($request->get('companyid',0));
        $where=' WHERE coupon_type=4';
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  companyid="'.$companyid.'"';
        }
        return $where;

    }
    public function actionRatiowashcheck(){

        $request = Yii::$app->request;
        $companys=(new CarCompany)->getCompany(['id','name']);
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber", 1);
            $pagesize = $request->get("pageSize", 10);
            $start=($page-1)*$pagesize;
            $where= $this->setRatioWashWhere();
            $connection  = Yii::$app->db;

            $sql_1="SELECT id  FROM tpy_car_coupon ";
            $sql_1.=$where;
            $sql_1.=" GROUP BY batch_no ORDER BY id DESC ";
            $cototal = $connection->createCommand($sql_1);
            $sum = $cototal->queryAll();

            //分状态查询SQL
            $sql=" SELECT 
                    `companyid`,
                    `batch_no`,
                    `coupon_type`,
                    `used_num`,
                    `name`,
                    `amount`,
                    SUM(`amount`) AS total,
                    SUM( if(`status`=0 ,`amount`,0 )) AS `not_used`,          
                    SUM( if(`status`=1,`amount`-`used_num`,0 )) AS `activation`,
                    SUM(`used_num`) AS `already_used`,
                    SUM( if(`status`=3,`amount`-`used_num`,0)) AS `expired`,
                    SUM(`used_num`)/SUM(`amount`) as `percentage`
                    FROM tpy_car_coupon ";

            $sql.=$where;
            $sql.=" GROUP BY `batch_no` ORDER BY `percentage` DESC";
            $sql.=" LIMIT {$start},{$pagesize} ";
            $command = $connection->createCommand($sql);
            $duilist = $command->queryAll();
            $companynames=[];
            if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
            foreach($duilist as $k=>$val){
                $duilist[$k]['total']=$val['total'];
                $duilist[$k]['not_used']=intval($val['not_used']);
                $duilist[$k]['activation'] = intval($val['activation']);
                $duilist[$k]['already_used']=$val['already_used'];
                $duilist[$k]['expired']=intval($val['expired']);
                $duilist[$k]['not_used_percent']=number_format(round(($val['not_used']*100/$val['total']),2),2,".","");
                $duilist[$k]['activation_percent']=number_format(round(($val['activation']*100/$val['total']),2),2,".","");
                $duilist[$k]['already_used_percent']=number_format(round(($val['already_used']*100/$val['total']),2),2,".","");
                $duilist[$k]['expired_percent']=number_format(round(($val['expired']*100/$val['total']),2),2,".","");
                $duilist[$k]['batch_no']=$val['batch_no'];
                $duilist[$k]['companyid']=$companynames[$val['companyid']];

            }
            $res = ['IsOk' => 1, 'total' => count($sum), 'rows' => $duilist];
            return json_encode($res);
        }
        return      $this->render ('ratiowashcheck',['companys'=>$companys]);
    }



    //查询券包使用情况
    public function actionCheckpackage(){
        $connection  = Yii::$app->db;
        //分状态查询SQL
        $sql=" SELECT 
                    COUNT(`status`) AS total,
                    COUNT( CASE WHEN `status`=1 THEN 1 ELSE NULL END ) AS `not_used`,
                    COUNT( CASE WHEN `status`=2 THEN 1 ELSE NULL END ) AS `already_used`,
                    COUNT( CASE WHEN `status`=3 THEN 1 ELSE NULL END ) AS `expired`
                    
                    FROM tpy_car_coupon_package  ";

        $command = $connection->createCommand($sql);
        $info = $command->queryOne();
        $data['total'] = $info['total'];
        $data['not_used'] = $info['not_used'];
        $data['already_used'] = $info['already_used'];
        $data['expired'] = $info['expired'];
        $data['not_used_percent']=round(($info['not_used']*100/$info['total']),2);
        $data['already_used_percent']=round(($info['already_used']*100/$info['total']),2);
        $data['expired_percent']=round(($info['expired']*100/$info['total']),2);
        return      $this->render ('checkpackage',['info'=>$data]);
    }

    //查询优惠券使用情况
    public function actionCheckcoupon(){
        $connection  = Yii::$app->db;
        //分状态查询SQL   0未激活，1已激活，2已使用，3已过期  activation
        $sql=" SELECT 
                    COUNT(`status`) AS total,
                    COUNT( CASE WHEN `status`=0 THEN 1 ELSE NULL END ) AS `not_used`,
                    COUNT( CASE WHEN `status`=1 THEN 1 ELSE NULL END ) AS `activation`,
                    COUNT( CASE WHEN `status`=2 THEN 1 ELSE NULL END ) AS `already_used`,
                    COUNT( CASE WHEN `status`=3 THEN 1 ELSE NULL END ) AS `expired`
                    FROM tpy_car_coupon  ";

        $command = $connection->createCommand($sql);
        $info = $command->queryOne();
        $data['total'] = $info['total'];
        $data['not_used'] = $info['not_used'];
        $data['activation'] = $info['activation'];
        $data['already_used'] = $info['already_used'];
        $data['expired'] = $info['expired'];
        $data['not_used_percent']=round(($info['not_used']*100/$info['total']),2);
        $data['activation_percent']=round(($info['activation']*100/$info['total']),2);
        $data['already_used_percent']=round(($info['already_used']*100/$info['total']),2);
        $data['expired_percent']=round(($info['expired']*100/$info['total']),2);
        return      $this->render ('checkcoupon',['info'=>$data]);
    }
    function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }



    public function actionUpdeadline(){

        $request = Yii::$app->request;
        if($request->isPost) {
            $data = $request->post();

            if(empty($data['use_limit_time'])) return '过期时间不能为空';
            $time=time();
            $data['use_limit_time']=strtotime($data['use_limit_time']);
            if($data['use_limit_time'] < $time) return '过期时间不能小于当前时间';
            $where=' status in (1,3) ';

            if($data['condition_type'] == '0'){
                if(empty($data['batch_nb'])) return '批号不能为空';
                $data['batch_nb']=trim($data['batch_nb']);
                $where.=' AND batch_nb = "'.$data['batch_nb'].'" ';
            }else{
                if( !is_numeric($data['start_no']) || $data['start_no']>999999999999999 || $data['start_no'] < 100000000000) return '开始号码不合法！';
                if(!is_numeric($data['end_no']) || $data['end_no']>999999999999999 || $data['end_no'] < 100000000000) return '结束号码不合法！';
                if($data['end_no'] < $data['start_no'])return '开始号码不能大于结束号码！';
                $num = $data['end_no'] - $data['start_no'];
                if($num > 100000) return '操作数据过多！';
                $where.=' AND package_sn >= "'.$data['start_no'].'" AND package_sn <= "'.$data['end_no'].'" ';
            }
            $Model= new CarCouponPackage();
            $res=$Model->table()->select("id")->where($where)->one();
            if(!$res) return '无操作数据';
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();

            $up_data['use_limit_time']=$data['use_limit_time'];
            $up_data['status']=1;
            try {
                $Model->myUpdate($up_data,$where);
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $e->getMessage();
            }
            $transaction->commit();
        }
        return      $this->render ('updeadline');

    }
/////////////////////////////////////ETC 设备管理
    protected function setEtcCodeWhere(){
        $request = Yii::$app->request;
        $code = trim($request->get('code',null));
        $batch_no = trim($request->get('batch_no',null));
        $where=' id<>0 ';
        if(!empty($code)){
            $where.=' AND  code ="'.$code.'"';
        }
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }

        return $where;
    }
    public function actionEtccodelist() {
        if (Yii::$app->request->isAjax) {
            $model= new CarEtcCode();
            $where=$this->setEtcCodeWhere();
            $res=$model->page_list('*',$where, ' id desc ');
            $listrows=$res['rows'];
            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = $this->handleTime($val['c_time']);
                $listrows[$k]['u_time'] = $this->handleTime($val['u_time']);
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('etccodelist');
    }
    //批量导入ETC设备码
    public function actionLeadinetccode() {
        return $this->render('leadinetccode');
    }

    public function actionExcutexlsectccode() {

        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)) return $this->json(0,'文件不存在');
            $batch_no=W::createNonceCapitalStr(8);
            $model = new CarEtcCode();
            $is_you = $model->table()->select('id')->where(['batch_no'=>$batch_no])->one();
            if($is_you){
                unlink($path);
                return $this->json(0,'批号重复');
            }
            $file=substr($path, strrpos($path, '.')+1);
            $data = CExcel::getExcel($file)->importExcel($path);
            if(empty($data)){
                unlink($path);
                return $this->json(0,'数据不存在');
            }
            $c_time = time();
            $data_1=[];
            $data_2=[];
            $alloldcode = $model->table()->select('code')->where('id<>0')->all();
            $alloldcode = array_column($alloldcode,'code');
            foreach ($data as $key=>$val){
                array_push($val,$batch_no,$c_time,$c_time);
                $val[0]=trim($val[0]);
                $length=strlen($val[0]);
                if(!empty($val[0]) && $length < 44 && ! in_array($val[0],$data_1) && ! in_array($val[0],$alloldcode)){
                    array_push($data_1,$val[0]);
                    $data_2[]=$val;
                }
            }
            $arrlength=count($data_2);
            if($arrlength == 0 && empty($data_2)){
                unlink($path);
                return $this->json(0,'全为重复数据');
            }

            //分割数组并插入缓存
            $result=$this->segmentationArr($data_2);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            unset($data,$data_1,$data_2);
            return $this->json(1,'成功',$result);
        }
        return '非法访问';
    }
//
    public function actionBatchetccode(){
        set_time_limit(0);
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
            }else{
                $fileds = ['code','batch_no','c_time','u_time',];
                $tablename='{{%car_etc_code}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/etccodelist']);
            return $msg;
        }
        return '非法访问';
    }

    public function actionSendsms(){
        $connection  = Yii::$app->db;
        //分状态查询SQL
        $sql="    SELECT mobile
                  FROM
                  tpy_sms_mobile             
                  GROUP BY mobile; ";

        $command = $connection->createCommand($sql);
        $info = $command->queryall();
        $content = '尊敬的客户：因春节和疫情期间过期的洗车次数和已激活未使用的洗车次数已补充到原卡号，可正常使用。详情咨询：服务热线 4006171981';
        $i = 0;
//        if($info){
//            foreach ($info as $key => $val){
//                W::sendSms($val['mobile'],$content);
//                $i++;
//            }
//        }
        var_dump($i);
        return  true;
    }


    protected function setGsCodeWhere(){
        $request = Yii::$app->request;
        $code = trim($request->get('code',null));
        $batch_no = trim($request->get('coupon_batch_no',null));
        $status = $request->get('status');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $where=' id<>0 ';
        if(!empty($code)){
            $where.=' AND  card_number ="'.$code.'"';
        }
        if(!empty($batch_no)){
            $where.=' AND  coupon_batch_no ="'.$batch_no.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);

        return $where;
    }
        /**
         * 国寿电商券码列表
         * @param 券码 $code  券码批号 $batch_no
         * @return 列表 $json
         * xxz
         * 2020 1010
         **/

        public function actionGscodelist(){

            $status = CarLifeCode::$status;
            if (Yii::$app->request->isAjax) {
                $model = new CarLifeCode();
                $where=$this->setGsCodeWhere();
                $res=$model->page_list('*',$where, ' id desc ');
                $listrows=$res['rows'];
                foreach ($res['rows'] as $k => $val) {
                    $listrows[$k]['c_time'] = $this->handleTime($val['c_time']);
                    $listrows[$k]['u_time'] = $this->handleTime($val['u_time']);
                    $listrows[$k]['apply_time'] = $this->handleTime($val['apply_time']);
                    $listrows[$k]['status'] = $status[$val['status']];
                }
                $res['rows']=$listrows;
                return json_encode($res);
            }
            return $this->render('gscodelist',[
                'status'=>$status
            ]);

        }
    /**
     * 批量导入国寿电商兑换码
     * @param Excel文件
     * @return 状态 1成功  >1 错误
     * **/
    public function actionLeadingscode() {
        return $this->render('leadingscode');
    }

    public function actionExcutexlsgscode() {
        set_time_limit(0);
        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)) return $this->json(0,'文件不存在');
            $file=substr($path, strrpos($path, '.')+1);
            $data = CExcel::getExcel($file)->importExcel($path);
            $c_time = time();
            $data_1=[];//筛选重复
            $data_2=[];
            foreach ($data as $key=>$val){
                array_push($val,'PQ33TG39YH',70,$c_time,'XIANXIA',2);
                $val[0]=trim($val[0]);
                $length=strlen($val[0]);
                if(!empty($val[0]) && $length < 61 && ! in_array($val[0],$data_1)){
                    array_push($data_1,$val[0]);
                    $data_2[]=$val;
                }
            }
            $arrlength=count($data_2);

            if($arrlength == 0 && empty($data_2)){
                unlink($path);
                return $this->json(0,'数据不存在');

            }
            //分割数组并插入缓存
            $result=$this->segmentationArr($data_2);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            unset($data,$data_1,$data_2);
            return $this->json(1,'成功',$result);
        }
        return '非法访问';
    }

//
    public function actionBatchgscode(){
        set_time_limit(0);
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
            }else{
                $fileds = ['card_number','coupon_batch_no','company_id','c_time','batch_no','is_online'];
                $tablename='{{%car_life_code}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['coupon/gscodelist']);

            return $msg;
        }
        return '非法访问';
    }




        //下载优惠券导入模板
        function actionDexceldriving() {
            $rootpath = Yii::$app->params['rootPath'];
            return Yii::$app->response->sendFile($rootpath.'./static/tpl/cardriving.xls');
        }
        //下载优惠券导入模板
        function actionDexceldrivingjt() {
            $rootpath = Yii::$app->params['rootPath'];
            return Yii::$app->response->sendFile($rootpath.'./static/tpl/jiutianquan.xls');
        }

        //下载洗车券导入模板
        function actionDexcelwash() {
            $rootpath = Yii::$app->params['rootPath'];
            return Yii::$app->response->sendFile($rootpath.'./static/tpl/carwash.xls');
        }



    }
