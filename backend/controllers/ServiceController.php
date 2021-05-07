<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30 0030
 * Time: 上午 9:18
 */

namespace backend\controllers;
use backend\util\BController;
use common\models\Car_tuhunotice;
use common\models\CarCompany;
use common\models\CarCouponMeal;
use common\models\CarDemand;
use yii;
use common\components\W;
use yii\helpers\Url;
use common\components\CExcel;
use common\models\Fans;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\Car_wash_coupon;
use common\models\Car_coupon_explain;
use common\models\CarMeal;
use common\models\Area;
use common\models\CarMobile;

use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

class ServiceController extends BController {


    /**
     * 券包查询条件
     * @return string
     */

    protected function setPackageWhere(){
        $request = Yii::$app->request;
        $package_sn = trim($request->get('package_sn',null));
        $package_pwd = trim($request->get('package_pwd',null));
        $mobile=$request->get('mobile',0);
        if(empty($package_sn) && empty($mobile) && empty($package_pwd)) return ' id=-1 ';
        $where=' id<>0 ';
        if(!empty($package_sn)){
            $where.=' AND  package_sn ="'.$package_sn.'"';
        }
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        if(!empty($package_pwd)){
            $where.=' AND  package_pwd ="'.$package_pwd.'"';
        }
        return $where;

    }
    public function actionPackagelist() {
        $status=CarCouponPackage::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $allcompany=CarCoupon::$coupon_company_info;
        $companys=(new CarCompany)->getCompany(['id','name']);

        if (Yii::$app->request->isAjax) {
            $model= new CarCouponPackage();
            $where=$this->setPackageWhere();
            $res=$model->page_list('*',$where, 'id DESC');
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
                $listrows[$k]['source'] = '--';
                if($val['uid']) $listrows[$k]['source'] = $sourceall[$val['uid']]=='web端用户'?$sourceall[$val['uid']]:'微信';
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
            return json_encode($res);
        }
        return $this->render('packagelist',['status'=>$status,'companys'=>$companys]);
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
        if($where == ' id<>0 ') $where=' id=-1 ';

        return $where;

    }
    function actionCouponmeallist() {
        $status=CarCouponPackage::$status;
        $companys=(new CarCompany())->getCompany(['id','name']);
        if (Yii::$app->request->isAjax) {
            $model= new CarCouponMeal();
            $where=$this->setCouponMealWhere();
            $res=$model->page_list('*',$where, 'id DESC',true);
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

    //客服优惠券查询
    protected function setCouponWhere(){
        $request = Yii::$app->request;
        $coupon_sn = trim($request->get('coupon_sn',null));
        $mobile = trim($request->get('mobile',null));
        if(empty($coupon_sn) && empty($mobile)){
            $where=' id=-1 ';
        }else{
            $where=' status<>0 ';
        }
        if(!empty($coupon_sn)){
            $where.=' AND  coupon_sn ="'.$coupon_sn.'"';
        }
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        return $where;

    }


    public function actionCouponlist() {
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
            $nicknamearr= array_column($nickname,'nickname','id');
            $sourceall= array_column($nickname,'source','id');
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
                $listrows[$k]['source'] = '--';
                if($val['uid']) $listrows[$k]['source'] = $sourceall[$val['uid']]=='web端用户'?$sourceall[$val['uid']]:'微信';
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('couponlist');
    }
}