<?php
namespace backend\controllers;



use common\models\Car_wash_pinganhcz_shop;
use Yii;
use backend\util\BController;

use yii\helpers\Url;
use common\components\CExcel;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use common\components\W;
use common\models\Fans;
use common\models\Car_rescueor;
use common\models\CarCoupon;
use common\models\Car_tuhunotice;
use common\models\CarOilor;
use common\models\CarWashpay;
use common\models\CarInsorder;
use common\models\CarSubstituteDriving;
use common\models\CarCompany;
use common\models\WashOrder;
use common\models\CarSegorder;
use common\models\CarWashPinganhcz;
use common\models\Car_wash_order_taibao;
use common\models\CarEtcorder;

class OrderController extends BController {

    private  $downpagesize=10000;//导出数据最大条数
    private  $cacheTime=7200;
    private  $maxInsertNum=1000;

    public function actionIndex() {
        return      $this->render ( 'index' ,array('cur'=>1));
    }

    /**
     * 代驾订单查询条件
     * @return string
     */

    protected function setDaicarWhere(){


        $request = Yii::$app->request;
        $mobile = trim($request->get('mobile',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $orderid = trim($request->get('orderid',null));
        $order_id = trim($request->get('order_id',null));
        $status = $request->get('status');
        $uid = intval($request->get('uid'));
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $company_id = $request->get('company_id');
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $c_amount = intval($request->get('c_amount'));
        $batch_no = trim($request->get('batch_no',null));
        $where=' id<>0 ';
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        if(!empty($coupon_sn)){
            $where.=' AND  coupon_sn ="'.$coupon_sn.'"';
        }
        if(!empty($orderid)){
            $where.=' AND  orderid ="'.$orderid.'"';
        }
        if(!empty($order_id)){
            $where.=' AND  order_id ="'.$order_id.'"';
        }
        if(!empty($uid)){
            $where.=' AND  uid ="'.$uid.'"';
        }
        if(!empty($status)){
            if($status==201){
                $where.=' AND  `status` IN (0,101,201)';
            }else{
                $where.=' AND  `status` ="'.$status.'"';
            }

        }
        if(! empty($companyid)){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        if($company_id == '1'){
            $where.=' AND  company_id = 0 ';
        }elseif ($company_id == '2'){
            $where.=' AND  company_id = 1 ';
        }
        if(! empty($batch_no)){
            $couponids = (new CarCoupon())->table()->select('id')->where(['batch_no'=>$batch_no,'coupon_type'=>1])->all();
            if(!empty($couponids)){
                $couponids = array_column($couponids,'id');
                $idsstr = implode(',',$couponids);
                $where.=' AND  coupon_id IN ('.$idsstr.')';
            }else{
                $where.=' AND  coupon_id IN (-1)';
            }
        }
        if(! empty($c_amount)){
            $couponids = (new CarCoupon())->table()->select('id')->where(['amount'=>$c_amount,'coupon_type'=>1])->all();
            if(!empty($couponids)){
                $couponids = array_column($couponids,'id');
                $idsstr = implode(',',$couponids);
                $where.=' AND  coupon_id IN ('.$idsstr.')';
            }
        }
        $where=$this->getTimeWhere($where,'start_time',$start_time,$end_time);
        $where=$this->getTimeWhere($where,'end_time',$s_time,$e_time);

        return $where;

    }

    //e代驾订单列表
    public function actionDaicarorlist() {
        
        $status=CarSubstituteDriving::$status_text;
        $companys=(new CarCompany())->getCompany(['id','name']);
        $driving_company=CarCoupon::$driving_company;
        if (Yii::$app->request->isAjax) {
            $model = new CarSubstituteDriving();

            $where=$this->setDaicarWhere();
            $field=['id','mobile','coupon_id','coupon_sn','date_day','date_month','orderid','departure','destination','driver_id','order_id','drivername','status','amount','cast','start_time','end_time','uid','companyid','company_id'];
            $res=$model->page_list($field,$where, 'id DESC');
            $listrows=$res['rows'];
            $uid=ArrayHelper::getColumn($listrows,'uid');

            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

            $companynames=[];
            if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}


            //券码批号显示
            $couponid=array_column($listrows,'coupon_id');
            $couponids=(new CarCoupon())->table()->select(['id','batch_no'])->where(['id'=>$couponid])->all();
            $batchall = array_column($couponids,'batch_no','id');

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['start_time'] = !empty($val['start_time'])?date("Y-m-d H:i:s",$val['start_time']):'--';
                $listrows[$k]['end_time'] = !empty($val['end_time'])?date("Y-m-d H:i:s",$val['end_time']):'--';
                $listrows[$k]['status'] = $status[$val['status']];
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['companyid'] = !empty($listrows[$k]['companyid'])?$companynames[$listrows[$k]['companyid']]:'--';
                $listrows[$k]['company_id'] = $driving_company[$val['company_id']];
                $listrows[$k]['batch_no'] = $batchall[$val['coupon_id']];

            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        unset($status[0],$status[101]);
        return      $this->render ( 'daicarorlist' ,[
            'status'=>$status,
            'companys'=>$companys,
            'driving_company'=>['1' => 'e代驾','2' => '滴滴代驾']
        ]);
    }

    //e代驾订单导出列表

    public function actionDaicardownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $mobile = trim($request->get('mobile',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $orderid = trim($request->get('orderid',null));
        $order_id = trim($request->get('order_id',null));
        $status = $request->get('status');
        $uid = intval($request->get('uid'));
        $companyid = intval($request->get('companyid',0));
        $company_id = intval($request->get('company_id',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $c_amount = intval($request->get('c_amount'));
        $batch_no = trim($request->get('batch_no',null));
        $where = $this->setDaicarWhere();
        $cot = (new CarSubstituteDriving())->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = 'e代驾订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/daiorderdown',
                    'page'=>1,
                    'mobile'=> $mobile,
                    'coupon_sn' => $coupon_sn,
                    'orderid'=> $orderid,
                    'order_id' => $order_id,
                    'status' => $status,
                    'uid' => $uid,
                    'companyid'=>$companyid,
                    'company_id'=>$company_id,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    's_time' => $s_time,
                    'e_time' => $e_time,
                    'c_amount' => $c_amount,
                    'batch_no' => $batch_no
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/daiorderdown',
                        'page'=>$i,
                        'mobile'=> $mobile,
                        'coupon_sn' => $coupon_sn,
                        'orderid'=> $orderid,
                        'order_id' => $order_id,
                        'status' => $status,
                        'uid' => $uid,
                        'companyid'=>$companyid,
                        'company_id'=>$company_id,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        's_time' => $s_time,
                        'e_time' => $e_time,
                        'c_amount' => $c_amount,
                        'batch_no' => $batch_no
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //e代驾订单导出Excel

    public function actionDaiorderdown(){
        $request = Yii::$app->request;
        $where = $this->setDaicarWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field = ['id','mobile','coupon_id','coupon_sn','date_day','date_month','orderid','departure','destination','driver_id','order_id','drivername','status','amount','start_time','end_time','uid','companyid','company_id'];
        $list = (new CarSubstituteDriving())->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($list,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

        $field = ['id','mobile','coupon_sn','date_day','date_month','orderid','departure','destination','driver_id','drivername','order_id','status','amount','start_time','end_time','nickname','companyid','company_id'];
        $str   = ['序号',['手机号','string'],['优惠券号','string'],['订单产生时间-日','string'],['订单产生时间-月','string'],'订单ID','出发地','目的地','司机编号','司机姓名','代驾方订单编号','订单状态','订单金额','订单开始时间','订单结束时间',['使用人微信昵称','string'],'客户公司名称','供应商'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        $status=CarSubstituteDriving::$status_text;
        $driving_company=CarCoupon::$driving_company;
        foreach ($list as $e){
            $tmp['id'] = $e['id'];
            $tmp['mobile'] = $e['mobile'];
            $tmp['coupon_sn'] = $e['coupon_sn'];
            $tmp['date_day'] = $e['date_day'];
            $tmp['date_month'] = $e['date_month'] ;
            $tmp['orderid'] = $e['orderid'];
            $tmp['departure']=$e['departure'];
            $tmp['destination'] = $e['destination'];
            $tmp['driver_id'] = $e['driver_id'];
            $tmp['drivername'] = $e['drivername'];
            $tmp['order_id'] = $e['order_id'];
            $tmp['status']=$status[$e['status']];
            $tmp['amount'] = $e['amount'];
            $tmp['start_time']=! empty($e['start_time']) ? date("Y-m-d H:i:s",$e['start_time']) : '--';
            $tmp['end_time']=! empty($e['end_time']) ? date("Y-m-d H:i:s",$e['end_time']) : '--';
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['companyid']=$companynames[$e['companyid']];
            $tmp['company_id'] = $driving_company[$e['company_id']];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = 'e代驾订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($list,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);

    }

    /**
     * 道路救援订单查询条件
     * @return string
     */

    protected function setRescueWhere(){
        $request = Yii::$app->request;

        $rescueway  = $request->get('coupon_type');
        $status     = $request->get('status');
        $companyid  = intval($request->get('companyid',0));
        $phone      = trim($request->get('mobile',null));
        $carno      = trim($request->get('carno',null));

        $where=' id<>0 ';

        if(!empty($rescueway) || $rescueway=='0'){
            $where.=' AND  rescueway ="'.$rescueway.'"';
        }

        if(!empty($phone)){
            $where.=' AND  phone ="'.$phone.'"';
        }

        if(!empty($carno)){
            $where.=' AND  carno ="'.$carno.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }


        if(! empty($start_time) && empty($end_time)){
            $s_time=strtotime($start_time);
            $where.=' AND  complete_time >"'.$s_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $e_time=strtotime($end_time);
            $where.=' AND  complete_time > 0 AND  complete_time < "'.$e_time.'"';
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
            $where.=' AND  complete_time >"'.$s_time.'" AND  complete_time < "'.$e_time.'"';
        }


        return $where;

    }

    //道路救援订单列表
    public function actionRescueorlist() {
        $status=Car_rescueor::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        if (Yii::$app->request->isAjax) {
            $couponmodel = new Car_rescueor();
            $where=$this->setRescueWhere();
            $field=['id','orderid','calltime','customername','carno','phone','carbrand','carmodel','faultaddress','rescueway','status','c_time','acceptance_time','complete_time','uid','companyid'];
            $res=$couponmodel->page_list($field,$where, 'id DESC');
            $listrows=$res['rows'];
            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['acceptance_time'] = !empty($val['acceptance_time'])?date("Y-m-d H:i:s",$val['acceptance_time']):'--';
                $listrows[$k]['complete_time'] = !empty($val['complete_time'])?date("Y-m-d H:i:s",$val['complete_time']):'--';
                $listrows[$k]['status'] = $status[$val['status']];
                $listrows[$k]['rescueway'] = $coupon_faulttype[$val['rescueway']];
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['companyid'] = !empty($val['companyid'])?$companynames[$val['companyid']]:'--';
            }

            $res['rows']=$listrows;

            return json_encode($res);
        }

        return      $this->render ( 'rescueorlist' ,['status'=>$status,'coupon_faulttype'=>$coupon_faulttype,'companys'=>$companys]);
    }

    //道路救援订单导出列表

    public function actionDownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $rescueway = trim($request->get('rescueway',null));
        $phone = trim($request->get('phone',null));
        $carno = trim($request->get('carno',null));
        $orderid = trim($request->get('orderid',null));
        $status = intval($request->get('status'));
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $where = $this->setRescueWhere();
        $cot = (new Car_rescueor())->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '救援订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/orderdown',
                    'page'=>1,
                    'rescueway'=> $rescueway,
                    'phone' => $phone,
                    'carno' => $carno,
                    'orderid'=> $orderid,
                    'status' => $status,
                    'companyid'=>$companyid,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/orderdown',
                        'page'=>$i,
                        'rescueway'=> $rescueway,
                        'phone' => $phone,
                        'carno' => $carno,
                        'orderid'=> $orderid,
                        'status' => $status,
                        'companyid'=>$companyid,
                        'start_time' => $start_time,
                        'end_time' => $end_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //道路救援订单导出Excel

    public function actionOrderdown(){
        $request = Yii::$app->request;
        $where = $this->setRescueWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['id','orderid','calltime','customername','carno','phone','carbrand','carmodel','faultaddress','rescueway','status','c_time','acceptance_time','complete_time','uid','companyid'];
        $couponlist = (new Car_rescueor())->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($couponlist,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

        $field = ['id','orderid','calltime','customername','carno','phone','carbrand','carmodel','faultaddress','rescueway','status','c_time','acceptance_time','complete_time','nickname','companyid'];
        $str   = ['序号',['订单号','string'],['求救时间','string'],['联系人姓名','string'],'车牌号','联系电话','车辆品牌','车型','故障地址','施救方式 ','订单状态','订单生成时间','订单受理时间','订单完成时间',['使用人微信昵称','string'],'客户公司名称'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        $status=Car_rescueor::$status;
        $coupon_faulttype=CarCoupon::$coupon_faulttype;
        foreach ($couponlist as $e){
            $tmp['id'] = $index;
            $tmp['orderid'] = $e['orderid'];
            $tmp['calltime'] = $e['calltime'];
            $tmp['customername'] = $e['customername'];
            $tmp['carno'] = $e['carno'] ;
            $tmp['phone'] = $e['phone'];
            $tmp['carbrand']=$e['carbrand'];
            $tmp['carmodel'] = $e['carmodel'];
            $tmp['faultaddress'] = $e['faultaddress'];
            $tmp['rescueway'] = $coupon_faulttype[$e['rescueway']];
            $tmp['status']=$status[$e['status']];
            $tmp['c_time']=! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['acceptance_time']=! empty($e['acceptance_time']) ? date("Y-m-d H:i:s",$e['acceptance_time']) : '--';
            $tmp['complete_time']=! empty($e['complete_time']) ? date("Y-m-d H:i:s",$e['complete_time']) : '--';
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['companyid']=$companynames[$e['companyid']];
            $data[] = $tmp;
            $index++;
        }

        var_dump($where);

        $headArr = array_combine($field,$str);
        $fileName = '救援订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($couponlist,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);

    }

    /**
     * 途虎回调记录列表查询条件
     * @return string
     */

    protected function setWashWhere(){
        $request = Yii::$app->request;
        $servicecode = trim($request->get('servicecode',null));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $companyid = intval($request->get('companyid',0));
        $where=' id<>0 ';
        if(!empty($servicecode)){
            $where.=' AND  servicecode ="'.$servicecode.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        if(! empty($start_time) && empty($end_time)){
            $s_time=date("YmdHis",strtotime($start_time));
            $where.=' AND  verifytime >= "'.$s_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $e_time=date("YmdHis",strtotime($end_time));
            $where.=' AND  verifytime > 0 AND  verifytime < "'.$e_time.'"';
        }elseif (! empty($start_time) && ! empty($end_time)){
            $st_time=date("YmdHis",strtotime($start_time));
            $en_time=date("YmdHis",strtotime($end_time));
            if($st_time > $en_time) {
                $s_time = $en_time;
                $e_time = $st_time;
            }else{
                $s_time = $st_time;
                $e_time = $en_time;
            }
            $where.=' AND  verifytime >= "'.$s_time.'" AND  verifytime < "'.$e_time.'"';
        }

        return $where;

    }

    //途虎回调记录列表
    public function actionCarwashlist() {
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        if (Yii::$app->request->isAjax) {
            $couponmodel = new Car_tuhunotice();
            $where=$this->setWashWhere();
            $res=$couponmodel->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];
            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['is_dao'] = $val['is_dao']=='1'?'已导':'未导';
                $listrows[$k]['price'] = (int)$val['price']/100;
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['companyid'] = !empty($val['companyid'])?$companynames[$val['companyid']]:'--';
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }

        return      $this->render ( 'carwashlist' ,['companys'=>$companys]);
    }

    //途虎回调记录导出列表

    public function actionWashdownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $servicecode = trim($request->get('servicecode',null));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $companyid = intval($request->get('companyid',0));
        $where = $this->setWashWhere();
        $cot = (new Car_tuhunotice())->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '途虎回调列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/washdown',
                    'page'=>1,
                    'servicecode' => $servicecode,
                    'companyid' => $companyid,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/washdown',
                        'page'=>$i,
                        'servicecode' => $servicecode,
                        'companyid' => $companyid,
                        'start_time' => $start_time,
                        'end_time' => $end_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //道路救援订单导出Excel

    public function actionWashdown(){
        $request = Yii::$app->request;
        $where = $this->setWashWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $couponlist = (new Car_tuhunotice())->table()->select('*')
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($couponlist,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

        $field = ['id','shopid','shopname','serviceid','servicename','verifytime','price','region','servicecode','c_time','nickname','companyid'];
        $str   = ['序号','核销门店ID',['核销门店名称','string'],['服务ID','string'],'服务名称','核销时间','价格','核销门店所属地区','服务码','接收回调时间','用户微信昵称','客户公司名称'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($couponlist as $e){
            $tmp['id'] = $index;
            $tmp['shopid'] = $e['shopid'];
            $tmp['shopname'] = $e['shopname'];
            $tmp['serviceid'] = $e['serviceid'] ;
            $tmp['servicename'] = $e['servicename'];
            $tmp['verifytime']=$e['verifytime'];
            $tmp['price'] = intval($e['price'])/100;
            $tmp['region'] = $e['region'];
            $tmp['servicecode'] = $e['servicecode'];
            $tmp['c_time']=! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['companyid']=$companynames[$e['companyid']];
            $data[] = $tmp;
            $index++;
        }



        $headArr = array_combine($field,$str);
        $fileName = '途虎回调列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($couponlist,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);


    }


    //燃油订单列表
    //status:opt1,
    //orderid:opt2,
    //card_no:opt3,
    //start_time:opt4,
    //end_time:opt5
    protected function setOilorWhere(){
        $request = Yii::$app->request;
        $status = $request->get('status');
        $orderid = trim($request->get('orderid',null));
        $card_no = trim($request->get('card_no',null));
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $coupon_sn = trim($request->get('coupon_sn',null));
        $oil_company= intval($request->get('oil_company',0));
        $mobile= trim($request->get('mobile',null));
        $where=' id<>0 ';
        if(!empty($orderid)){
            $where.=' AND  orderid ="'.$orderid.'"';
        }

        if(!empty($card_no)){
            $where.=' AND  card_no ="'.$card_no.'"';
        }
        if(!empty($coupon_sn)){
            $couponid=(new CarCoupon())->table()->select(['id'])->where(['coupon_sn'=>$coupon_sn])->one();
            if($couponid)$where.=' AND  coupon_id ="'.$couponid['id'].'"';
        }

        if(!empty($mobile)){
            $couponid=(new CarCoupon())->table()->select(['id'])->where(['mobile'=>$mobile])->all();
            $couponids=array_column($couponid,'id');
            $couponidstr='(-1)';
            if($couponid)$couponidstr=implode('","',$couponids);
            $where.=' AND  coupon_id in("'.$couponidstr.'")';
        }


        if(!empty($status) ){
            if($status=='1'){
                $where.=' AND  status in(0,1,4,9)';
            }else{
                $where.=' AND  status ="'.$status.'"';
            }

        }
        if(!empty($companyid)){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        if(!empty($oil_company)){
            $where.=' AND  company_id ="'.$oil_company.'"';
        }

        if(! empty($start_time) && empty($end_time)){
            $s_time=strtotime($start_time);
            $where.=' AND  c_time >"'.$s_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $e_time=strtotime($end_time);
            $where.=' AND  c_time > 0 AND  c_time < "'.$e_time.'"';
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
            $where.=' AND  c_time >"'.$s_time.'" AND  c_time < "'.$e_time.'"';
        }
        return $where;
    }

    public function actionOilorlist() {
        $status=CarOilor::$status;
        $oilcardtype=CarOilor::$oilcardtype;
        $status_text=CarOilor::$status_text;
        $oil_company=CarCoupon::$oil_company;
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        if (Yii::$app->request->isAjax) {
            $oilorModel = new CarOilor();

            $where=$this->setOilorWhere();
            $res=$oilorModel->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];
            //微信昵称
            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
            //券码显示
            //券码显示
            $couponid=ArrayHelper::getColumn($listrows,'coupon_id');
            $couponids=(new CarCoupon())->table()->select(['id','coupon_sn','mobile'])->where(['id'=>$couponid])->all();
            $couponidall=[];
            $couponmobileall=[];
            if($couponids)foreach ($couponids as $key=>$val){
                $couponidall[$val['id']]=$val['coupon_sn'];
                $couponmobileall[$val['id']]=$val['mobile'];
            }

            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['s_time'] = !empty($val['s_time'])?date("Y-m-d H:i:s",$val['s_time']):'--';
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['card_type']=$oilcardtype[$val['card_type']];
                $listrows[$k]['status'] =$status_text[$val['status']] ;
                $listrows[$k]['companyid']=$companynames[$val['companyid']];
                $listrows[$k]['coupon_id']=$couponidall[$val['coupon_id']];
                $listrows[$k]['mobile'] = $couponmobileall[$val['coupon_id']];
                $listrows[$k]['company_id']=$oil_company[$val['company_id']];
            }
            $res['rows']=$listrows;
            $res['where']=$where;
            return json_encode($res);
        }

        return  $this->render ( 'oilorlist',
            [
                'oilorstatus'=>$status,
                'companys'=>$companys,
                'oil_company'=>$oil_company
            ]
        );
    }

    //油券订单导出列表

    public function actionOilordownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $status = $request->get('status');
        $orderid = trim($request->get('orderid',null));
        $card_no = trim($request->get('card_no',null));
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $coupon_sn = trim($request->get('coupon_sn',null));
        $oil_company= intval($request->get('oil_company',0));
        $mobile= trim($request->get('mobile',null));
        $model = new CarOilor();
        $where=$this->setOilorWhere();
        $cot = $model->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '油券订单导出列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/oilordown',
                    'page'=>1,
                    'status' => $status,
                    'orderid' => $orderid,
                    'card_no' => $card_no,
                    'companyid' => $companyid,
                    'status' => $status,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'coupon_sn' => $coupon_sn,
                    'oil_company' => $oil_company,
                    'mobile' => $mobile


                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/oilordown',
                        'page'=>$i,
                        'status' => $status,
                        'orderid' => $orderid,
                        'card_no' => $card_no,
                        'companyid' => $companyid,
                        'status' => $status,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'coupon_sn' => $coupon_sn,
                        'oil_company' => $oil_company,
                        'mobile' => $mobile
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //油券订单导出Excel

    public function actionOilordown(){
        $request = Yii::$app->request;

        $model = new CarOilor();
        $status_text=CarOilor::$status_text;
        $oil_company=CarCoupon::$oil_company;
        $where=$this->setOilorWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field = [];

        $couponlist = $model->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($couponlist,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        //券码显示
        $couponid=ArrayHelper::getColumn($couponlist,'coupon_id');
        $couponids=(new CarCoupon())->table()->select(['id','coupon_sn','mobile'])->where(['id'=>$couponid])->all();
        $couponidall=[];
        $couponmobileall=[];
        if($couponids)foreach ($couponids as $key=>$val){
            $couponidall[$val['id']]=$val['coupon_sn'];
            $couponmobileall[$val['id']]=$val['mobile'];
        }
        $field = ['id','orderid','mobile','coupon_id','card_no','card_type','amount','c_time','s_time','status','bizorderid','carriertype','nickname','companyid','company_id'];
        $str   = ['编号','订单ID',['用户手机号','string'],['优惠券号','string'],['充值的油卡卡号','string'],'油卡类型','充值金额','创建时间','完成时间','订单状态','充值方订单号','充值方加油卡类型','用户微信昵称','客户公司名称','供应商'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($couponlist as $e){
            $tmp['id'] = $e['id'];
            $tmp['orderid'] = $e['orderid'];
            $tmp['mobile'] = $couponmobileall[$e['coupon_id']];
            $tmp['coupon_id'] = $couponidall[$e['coupon_id']];
            $tmp['card_no'] = $e['card_no'] ;
            $tmp['card_type'] = $e['card_type'];
            $tmp['amount']=$e['amount'];
            $tmp['c_time']=! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['s_time']=! empty($e['s_time']) ? date("Y-m-d H:i:s",$e['s_time']) : '--';
            $tmp['status'] = $status_text[$e['status']];
            $tmp['bizorderid'] = $e['bizorderid'];
            $tmp['carriertype'] = $e['carriertype'];
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['companyid']=$companynames[$e['companyid']];
            $tmp['company_id']=$oil_company[$e['company_id']];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '油券订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($couponlist,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }


/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 在线洗车券购买订单列表查询条件
     * @return string
     */

    protected function setWashPayWhere(){
        $request = Yii::$app->request;
        $orderid = trim($request->get('orderid',null));
        $mobile = trim($request->get('mobile',null));
        $package_pwd = trim($request->get('package_pwd',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $where=' id<>0 ';
        if(!empty($orderid)){
            $where.=' AND  orderid ="'.$orderid.'"';
        }
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        if(!empty($package_pwd)){
            $where.=' AND  package_pwd ="'.$package_pwd.'"';
        }
        if(!empty($userid)){
            $where.=' AND  uid ="'.$userid.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        if(!empty($companyid) ){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }

        if(! empty($start_time) && empty($end_time)){
            $s_time=strtotime($start_time);
            $where.=' AND  pay_time >"'.$s_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $e_time=strtotime($end_time);
            $where.=' AND  pay_time > 0 AND  pay_time < "'.$e_time.'"';
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
            $where.=' AND  pay_time >"'.$s_time.'" AND  pay_time < "'.$e_time.'"';
        }

        return $where;

    }

    //在线洗车券购买订单列表
    public function actionWashpaylist() {
        //0，未支付，1已支付，2已激活
        $statusarr=CarWashpay::$statusarr;
        $paytype=CarWashpay::$paytype;
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        if (Yii::$app->request->isAjax) {
            $couponmodel = new CarWashpay();
            $where=$this->setWashPayWhere();
            $field=['id','uid','orderid','date_day','date_month','mobile','promotion_code','num','price','amount','package_id','package_pwd','status','pay_time','pay_type','c_time','companyid'];
            $res=$couponmodel->page_list($field,$where, 'id DESC');
            $listrows=$res['rows'];
            //微信昵称显示
            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
            //券码显示
            $couponid=ArrayHelper::getColumn($listrows,'coupon_id');
            $couponids=(new CarCoupon())->table()->select(['id','coupon_sn'])->where(['id'=>$couponid])->all();
            $couponidall=[];
            if($couponids)foreach ($couponids as $key=>$val){$couponidall[$val['id']]=$val['coupon_sn'];}

            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['pay_time'] = !empty($val['pay_time'])?date("Y-m-d H:i:s",$val['pay_time']):'--';
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['status'] = $statusarr[$val['status']];
                $listrows[$k]['pay_type'] = $paytype[$val['pay_type']];
                $listrows[$k]['companyid'] = $companynames[$val['companyid']];
                $listrows[$k]['coupon_id']=$couponidall[$val['coupon_id']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }

        return      $this->render ( 'washpaylist',[
            'status'=>$statusarr,
            'companys'=>$companys
        ] );
    }

    //在线洗车券购买订单导出列表

    public function actionWashpaydownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $orderid = trim($request->get('orderid',null));
        $mobile = trim($request->get('mobile',null));
        $package_pwd = trim($request->get('package_pwd',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $model = new CarWashpay();
        $where=$this->setWashPayWhere();
        $cot = $model->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '在线洗车券购买订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/washpaydown',
                    'page'=>1,
                    'orderid' => $orderid,
                    'mobile' => $mobile,
                    'package_pwd' => $package_pwd,
                    'userid' => $userid,
                    'status' => $status,
                    'companyid' => $companyid,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/washpaydown',
                        'page'=>$i,
                        'orderid' => $orderid,
                        'mobile' => $mobile,
                        'package_pwd' => $package_pwd,
                        'userid' => $userid,
                        'status' => $status,
                        'companyid' => $companyid,
                        'start_time' => $start_time,
                        'end_time' => $end_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //在线洗车券购买订单导出Excel

    public function actionWashpaydown(){
        $request = Yii::$app->request;

        $model = new CarWashpay();
        $statusarr=CarWashpay::$statusarr;
        $where=$this->setWashPayWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['id','uid','orderid','date_day','date_month','mobile','promotion_code','num','price','amount','package_id','package_pwd','status','pay_time','pay_type','c_time','companyid'];

        $couponlist = $model->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($couponlist,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

        $field = ['id','orderid','mobile','promotion_code','num','price','amount','package_pwd','status','pay_type','pay_time','c_time','nickname','companyid'];
        $str   = ['编号','订单ID',['购买人手机号','string'],['平安好车主优惠码','string'],'购买数量','单价','支付金额','兑换码','订单状态','支付方式','支付时间','下单时间','用户微信昵称','客户公司名称'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($couponlist as $e){
            $tmp['id'] = $e['id'];
            $tmp['orderid'] = $e['orderid'];
            $tmp['mobile'] = $e['mobile'];
            $tmp['promotion_code'] = $e['promotion_code'] ;
            $tmp['num'] = $e['num'];
            $tmp['price']=$e['price'];
            $tmp['amount'] = $e['amount'];
            $tmp['package_pwd'] = $e['package_pwd'];
            $tmp['status'] = $statusarr[$e['status']];
            $tmp['paytype'] = $statusarr[$e['paytype']];
            $tmp['pay_time']=! empty($e['pay_time']) ? date("Y-m-d H:i:s",$e['pay_time']) : '--';
            $tmp['c_time']=! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['companyid']=$companynames[$e['companyid']];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '在线洗车券购买订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($couponlist,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 年检订单列表查询条件
     * @return string
     */

    protected function setAsorderWhere(){
        $request = Yii::$app->request;
        $orderid = trim($request->get('orderid',null));
        $carresp = trim($request->get('carresp',null));
        $carnum= trim($request->get('carnum',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $where=' id<>0 ';
        if(!empty($orderid)){
            $where.=' AND  orderid ="'.$orderid.'"';
        }
        if(!empty($carresp)){
            $where.=' AND  carresp ="'.$carresp.'"';
        }
        if(!empty($carnum)){
            $where.=' AND  carnum ="'.$carnum.'"';
        }
        if(!empty($userid)){
            $where.=' AND  uid ="'.$userid.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        if(!empty($companyid) ){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        if(!empty($coupon_sn) ){
            $couponid=(new CarCoupon())->table()->select(['id'])->where(['coupon_sn'=>$coupon_sn])->one();
            $where.=' AND  couponid ="'.$couponid['id'].'"';
        }
        if(! empty($start_time) && empty($end_time)){
            $s_time=strtotime($start_time);
            $where.=' AND  c_time >"'.$s_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $e_time=strtotime($end_time);
            $where.=' AND  c_time > 0 AND  c_time < "'.$e_time.'"';
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
            $where.=' AND  c_time >"'.$s_time.'" AND  c_time < "'.$e_time.'"';
        }

        return $where;

    }

    //年检订单列表
    public function actionAsorderlist() {

        $statusarr=CarInsorder::$order_status;

        //客户公司名称
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        if (Yii::$app->request->isAjax) {
            $model = new CarInsorder();
            $where=$this->setAsorderWhere();
            $field=['id','uid','couponid','date_day','date_month','orderid','carnum','carcity','carresp','uaddrid','amount','c_time','s_time','status','bizorderid','express','expresscom','company_id','companyid'];
            $res=$model->page_list($field,$where, 'id DESC');
            $listrows=$res['rows'];
            //微信昵称
            $uid=ArrayHelper::getColumn($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
            //优惠券号码
            $couponid=ArrayHelper::getColumn($listrows,'couponid');
            $couponsnall=(new CarCoupon())->table()->select('id,coupon_sn')->where(['id'=>$couponid])->all();
            $couponsn=[];
            if($couponsnall)foreach ($couponsnall as $key=>$val){$couponsn[$val['id']]=$val['coupon_sn'];}

            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['s_time'] = !empty($val['s_time'])?date("Y-m-d H:i:s",$val['s_time']):'--';
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['couponid'] =!empty($val['couponid'])?$couponsn[$val['couponid']]:'--';
                $listrows[$k]['status'] = $statusarr[$val['status']];
                $listrows[$k]['companyid'] = $companynames[$val['companyid']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }

        return      $this->render ( 'asorderlist',[
            'status'=>$statusarr,
            'companys'=>$companys
        ] );
    }

    //年检订单导出列表

    public function actionAsorderdownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $orderid = trim($request->get('orderid',null));
        $carresp = trim($request->get('carresp',null));
        $carnum= trim($request->get('carnum',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $model = new CarInsorder();
        $where=$this->setAsorderWhere();
        $cot = $model->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '年检订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/asorderdown',
                    'page'=>1,
                    'orderid' => $orderid,
                    'carresp' => $carresp,
                    'carnum' => $carnum,
                    'userid' => $userid,
                    'status' => $status,
                    'companyid' => $companyid,
                    'coupon_sn' => $coupon_sn,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/asorderdown',
                        'page'=>$i,
                        'orderid' => $orderid,
                        'carresp' => $carresp,
                        'carnum' => $carnum,
                        'userid' => $userid,
                        'status' => $status,
                        'companyid' => $companyid,
                        'coupon_sn' => $coupon_sn,
                        'start_time' => $start_time,
                        'end_time' => $end_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //年检订单导出Excel

    public function actionAsorderdown(){

        $request = Yii::$app->request;
        $model = new CarInsorder();
        $statusarr=CarInsorder::$order_status;
        $where=$this->setAsorderWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['id','uid','couponid','date_day','date_month','orderid','carnum','carcity','carresp','uaddrid','amount','c_time','s_time','status','bizorderid','express','expresscom','company_id','companyid'];

        $list = $model->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($list,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];

        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

        //优惠券号码
        $couponid=ArrayHelper::getColumn($list,'couponid');
        $couponsnall=(new CarCoupon())->table()->select('id,coupon_sn')->where(['id'=>$couponid])->all();
        $couponsn=[];
        if($couponsnall)foreach ($couponsnall as $key=>$val){$couponsn[$val['id']]=$val['coupon_sn'];}

        $field=['id','couponid','orderid','carnum','carcity','carresp','amount','c_time','s_time','status','bizorderid','uid','express','expresscom','companyid'];
        $str   = ['编号',['优惠券号码','string'],['定单ID','string'],['车牌号','string'],'城市',['进度接收者手机号','string'],'金额','下单时间','完成时间','订单状态','充值方订单号','用户微信昵称','快递公司','快递单号','客户公司名称'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($list as $e){
            $tmp['id'] = $e['id'];
            $tmp['couponid'] = $couponsn[$e['couponid']];
            $tmp['orderid'] = $e['orderid'];
            $tmp['carnum'] = $e['carnum'];
            $tmp['carcity'] = $e['carcity'] ;
            $tmp['amount'] = $e['amount'];
            $tmp['carresp']=$e['carresp'];
            $tmp['amount'] = $e['amount'];
            $tmp['c_time']=! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['s_time']=! empty($e['s_time']) ? date("Y-m-d H:i:s",$e['s_time']) : '--';
            $tmp['status'] = $statusarr[$e['status']];
            $tmp['bizorderid'] = $e['bizorderid'];
            $tmp['uid'] = $nicknamearr[$e['uid']];
            $tmp['express']=$e['express'];
            $tmp['expresscom'] = $e['expresscom'];
            $tmp['companyid']=$companynames[$e['companyid']];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '年检订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($list,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }
    //////////////////////////////////////////////////////////
    /**
     * 典典洗车订单列表查询条件
     * @return string
     */

    protected function setDianWashWhere(){
        $request = Yii::$app->request;
        $orderid = trim($request->get('orderid',null));
        $mobile = trim($request->get('mobile',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $shopName=trim($request->get('shopName',null));
        $company_id = intval($request->get('company_id',0));
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $coupon_batch_no = trim($request->get('coupon_batch_no',null));
        $city = trim($request->get('city',null));
        $c_start_time = $request->get('c_start_time',null);
        $c_end_time = $request->get('c_end_time',null);
        $where=' id<>0 ';
        if(!empty($orderid)){
            $where.=' AND  outOrderNo ="'.$orderid.'"';
        }
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        if(!empty($coupon_sn) ){
            $couponid=(new CarCoupon())->table()->select(['id'])->where(['coupon_sn'=>$coupon_sn])->one();
            $where.=' AND  couponid ="'.$couponid['id'].'"';
        }

        $cwhere = 'id<>0';
        if(!empty($coupon_batch_no))$cwhere .= ' AND batch_no="'.$coupon_batch_no.'"';
        $ctwhere=$this->getTimeWhere($cwhere,'c_time',$c_start_time,$c_end_time);
       
        if($ctwhere != 'id<>0'){
            $couponid =(new CarCoupon())->table()->select(['id'])->where($ctwhere)->all();
            if($couponid){
                $couponids = array_column($couponid,'id');
                $couponidsstr = implode(',',$couponids);
                $where.=' AND  couponid IN('.$couponidsstr.')';
            }else{
                $where.=' AND  couponid IN(-1)';
            }
        }
        if(!empty($userid)){
            $where.=' AND  uid ="'.$userid.'"';
        }
        if(!empty($status)){
            $wherestr=' AND  status ="'.$status.'"';
            if($status == 1)$wherestr=' AND  status in(0,1)';
            $where.=$wherestr;
        }
        if(!empty($companyid) ){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        if(!empty($company_id) ){
            $where.=' AND  company_id ="'.$company_id.'"';
        }

        if(!empty($shopName) ){
            $where.=' AND  shopName ="'.$shopName.'"';
        }
        if(!empty($city) ){
            $where.=' AND  city ="'.$city.'"';
        }
        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        $where=$this->getTimeWhere($where,'s_time',$s_time,$e_time);

        return $where;

    }

    /**
     * 典典洗车订单列表
     * @return json
     */
    public function actionDianwashlist() {

        $statustext=WashOrder::$status_text;
        $wash_company=CarCoupon::$wash_company;
        unset($wash_company['0']);
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}

        if (Yii::$app->request->isAjax) {
            $model = new WashOrder();
            $where=$this->setDianWashWhere();
            $field=['id','uid','outOrderNo','couponId','date_day','date_month','mobile','shopId','shopName','serverType','serviceName','consumerCode','expiredTime','amount','c_time','s_time','companyid','status','company_id','city'];
            $res=$model->page_list($field,$where, 'id DESC');
            $listrows=$res['rows'];
            //微信昵称显示
            $uid=array_column($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)$nicknamearr=array_column($nickname,'nickname','id');
            //券码显示
            $couponid=array_column($listrows,'couponId');
            $couponids=(new CarCoupon())->table()->select(['id','coupon_sn','batch_no'])->where(['id'=>$couponid])->all();
            $couponidall=[];
            $batchall = [];
            if($couponids)$couponidall=array_column($couponids,'coupon_sn','id');
            if($couponids)$batchall=array_column($couponids,'batch_no','id');
            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['s_time'] = !empty($val['s_time'])?date("Y-m-d H:i:s",$val['s_time']):'--';
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['status'] = $statustext[$val['status']];
                $listrows[$k]['companyid'] = $companynames[$val['companyid']];
                $listrows[$k]['couponId'] = $couponidall[$val['couponId']];
                $listrows[$k]['company_id'] = $wash_company[$val['company_id']];
                $listrows[$k]['coupon_batch_no'] = $batchall[$val['couponId']];
            }
            $res['rows']=$listrows;
            $res['where']=$where;
            return json_encode($res);
        }
        $statusarr=WashOrder::$status_arr;
        return      $this->render ( 'dianwashlist',[
            'status'=>$statusarr,
            'companys'=>$companys,
            'wash_company'=>$wash_company
        ] );
    }

    //典典洗车订单列表

    public function actionDianwashdownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $orderid = trim($request->get('orderid',null));
        $mobile = trim($request->get('mobile',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $shopName=trim($request->get('shopName',null));
        $company_id = intval($request->get('company_id',0));
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $coupon_batch_no = trim($request->get('coupon_batch_no',null));
        $city = trim($request->get('city',null));
        $c_start_time = $request->get('c_start_time',null);
        $c_end_time = $request->get('c_end_time',null);
        $model = new WashOrder();
        $where=$this->setDianWashWhere();
        $cot = $model->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '洗车订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/dianwashdown',
                    'page'=>1,
                    'orderid' => $orderid,
                    'mobile' => $mobile,
                    'coupon_sn' => $coupon_sn,
                    'userid' => $userid,
                    'status' => $status,
                    'companyid' => $companyid,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'shopName' => $shopName,
                    'company_id' => $company_id,
                    's_time' => $s_time,
                    'e_time' => $e_time,
                    'coupon_batch_no' => $coupon_batch_no,
                    'city'=>$city,
                    'c_start_time' => $c_start_time,
                    'c_end_time'=>$c_end_time
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/dianwashdown',
                        'page'=>$i,
                        'orderid' => $orderid,
                        'mobile' => $mobile,
                        'coupon_sn' => $coupon_sn,
                        'userid' => $userid,
                        'status' => $status,
                        'companyid' => $companyid,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'shopName' => $shopName,
                        'company_id' => $company_id,
                        's_time' => $s_time,
                        'e_time' => $e_time,
                        'coupon_batch_no' => $coupon_batch_no,
                        'city'=>$city,
                        'c_start_time' => $c_start_time,
                        'c_end_time'=>$c_end_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //典典洗车订单列表导出

    public function actionDianwashdown(){
        $request = Yii::$app->request;

        $model = new WashOrder();
        $statusarr=WashOrder::$status_text;
        $wash_company=CarCoupon::$wash_company;
        unset($wash_company['0']);
        $where=$this->setDianWashWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['id','uid','outOrderNo','couponId','date_day','date_month','mobile','shopId','shopName','serverType','serviceName','consumerCode','expiredTime','amount','c_time','s_time','companyid','status','company_id','city'];
        $couponlist = $model->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($couponlist,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=[];
        if($companys)foreach ($companys as $key=>$val){$companynames[$val['id']]=$val['name'];}
        //券码显示
        $couponid=array_column($couponlist,'couponId');
        $couponids=(new CarCoupon())->table()->select(['id','coupon_sn','batch_no'])->where(['id'=>$couponid])->all();
        $couponidall=[];
        $batchall = [];
        if($couponids)$couponidall=array_column($couponids,'coupon_sn','id');
        if($couponids)$batchall=array_column($couponids,'batch_no','id');

        $field = ['id','outOrderNo','couponId','mobile','shopId','shopName','serverType','serviceName','consumerCode','expiredTime','amount','c_time','s_time','status','nickname','companyid','company_id','coupon_batch_no','city'];
        $str   = ['编号','订单编号',['优惠券码','string'],['联系人手机号','string'],'核销门店ID','核销门店名称','洗车服务类型','服务名称','消费凭证','消费凭证过期时间','订单金额','下单时间','完成时间','订单状态','用户微信昵称','客户公司名称','供应商','券批号','区域'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($couponlist as $e){
            $tmp['id'] = $e['id'];
            $tmp['outOrderNo'] = $e['outOrderNo'];
            $tmp['couponId']=$couponidall[$e['couponId']];
            $tmp['mobile'] = $e['mobile'];
            $tmp['shopId'] = $e['shopId'] ;
            $tmp['shopName'] = $e['shopName'];
            $tmp['serverType']=$e['serverType'];
            $tmp['serviceName'] = $e['serviceName'];
            $tmp['consumerCode'] = $e['consumerCode'];
            $tmp['expiredTime'] = ! empty($e['expiredTime']) ? date("Y-m-d H:i:s",$e['expiredTime']) : '--';
            $tmp['amount']=$e['amount'];
            $tmp['c_time']=! empty($e['c_time']) ? date("Y-m-d H:i:s",$e['c_time']) : '--';
            $tmp['s_time']=! empty($e['s_time']) ? date("Y-m-d H:i:s",$e['s_time']) : '--';
            $tmp['status']=$statusarr[$e['status']];
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['companyid']=$companynames[$e['companyid']];
            $tmp['company_id']=$wash_company[$e['company_id']];
            $tmp['coupon_batch_no']=$batchall[$e['couponId']];
            $tmp['city'] = $e['city'];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '洗车订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($couponlist,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 代步车订单列表查询条件
     * @return string
     */

    protected function setSegorderWhere(){
        $request = Yii::$app->request;
        $orderid = trim($request->get('orderid',null));
        $telphone = trim($request->get('telphone',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $where=' id<>0 ';
        if(!empty($orderid)){
            $where.=' AND  orderid ="'.$orderid.'"';
        }
        if(!empty($telphone)){
            $where.=' AND  telphone ="'.$telphone.'"';
        }
        if(!empty($userid)){
            $where.=' AND  uid ="'.$userid.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        if(!empty($companyid) ){
            $where.=' AND  companyid ="'.$companyid.'"';
            (new CarCompany)->browseNum($companyid);
        }
        if(!empty($coupon_sn) ){
            $couponid=(new CarCoupon())->table()->select(['id'])->where(['coupon_sn'=>$coupon_sn])->one();
            $where.=' AND  couponid ="'.$couponid['id'].'"';
        }
        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        $where=$this->getTimeWhere($where,'s_time',$s_time,$e_time);
        return $where;

    }

    //代步车订单列表
    public function actionSegorderlist() {

        $statusarr=CarSegorder::$orderStatusTextxu;
        //客户公司名称
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=array_column($companys,'name','id');
        if (Yii::$app->request->isAjax) {
            $model = new CarSegorder();
            $where=$this->setSegorderWhere();
            $field=['id','uid','couponid','date_day','date_month','orderid','liaison','telphone','pre_u_time','pre_r_time','rel_u_time','rel_r_time','c_time','s_time','r_time','status','company_id','companyid','preprocode','prepro','precitycode','precity','precountycode','precounty','prestorecode','prestore','sendmsg'];
            $res=$model->page_list($field,$where, 'id DESC');
            $listrows=$res['rows'];
            //微信昵称
            $uid=array_column($listrows,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=array_column($nickname,'nickname','id');
            //优惠券号码
            $couponid=array_column($listrows,'couponid');
            $couponsnall=(new CarCoupon())->table()->select('id,coupon_sn')->where(['id'=>$couponid])->all();
            $couponsn=array_column($couponsnall,'coupon_sn','id');


            foreach ($listrows as $k => $val) {
                $listrows[$k]['pre_u_time'] = $this->handleTime($val['pre_u_time']);
                $listrows[$k]['pre_r_time']= $this->handleTime($val['pre_r_time']);
                $listrows[$k]['rel_u_time'] = $this->handleTime($val['rel_u_time']);
                $listrows[$k]['rel_r_time']=$this->handleTime($val['rel_r_time']);
                $listrows[$k]['c_time']=$this->handleTime($val['c_time']);
                $listrows[$k]['s_time']=$this->handleTime($val['s_time']);
                $listrows[$k]['r_time']=$this->handleTime($val['r_time']);
                $listrows[$k]['nickname'] = !empty($val['uid'])?$nicknamearr[$val['uid']]:'--';
                $listrows[$k]['couponid'] =!empty($val['couponid'])?$couponsn[$val['couponid']]:'--';
                $listrows[$k]['status'] = $statusarr[$val['status']];
                $listrows[$k]['companyid'] = $companynames[$val['companyid']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return      $this->render ( 'segorderlist',[
            'status'=>$statusarr,
            'companys'=>$companys
        ] );
    }

    //代步车订单导出列表

    public function actionSegorderdownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $orderid = trim($request->get('orderid',null));
        $telphone = trim($request->get('telphone',null));
        $coupon_sn = trim($request->get('coupon_sn',null));
        $userid=intval($request->get('userid'));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $model = new CarSegorder();
        $where=$this->setSegorderWhere();
        $cot = $model->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '代步车订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/segorderdown',
                    'page'=>1,
                    'orderid' => $orderid,
                    'telphone' => $telphone,
                    'coupon_sn' => $coupon_sn,
                    'userid' => $userid,
                    'status' => $status,
                    'companyid' => $companyid,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    's_time' => $s_time,
                    'e_time' => $e_time
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/segorderdown',
                        'page'=>$i,
                        'orderid' => $orderid,
                        'telphone' => $telphone,
                        'coupon_sn' => $coupon_sn,
                        'userid' => $userid,
                        'status' => $status,
                        'companyid' => $companyid,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        's_time' => $s_time,
                        'e_time' => $e_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //代步车订单导出Excel

    public function actionSegorderdown(){

        $request = Yii::$app->request;
        $model = new CarSegorder();
        $statusarr=CarSegorder::$orderStatusTextxu;
        $where=$this->setAsorderWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['id','uid','couponid','date_day','date_month','orderid','liaison','telphone','pre_u_time','pre_r_time','rel_u_time','rel_r_time','c_time','s_time','r_time','status','prepro','precity','precounty','prestorecode','prestore','sendmsg','company_id','companyid'];

        $list = $model->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        //微信昵称
        $uid=array_column($list,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=array_column($nickname,'nickname','id');
        //优惠券号码
        $couponid=array_column($list,'couponid');
        $couponsnall=(new CarCoupon())->table()->select('id,coupon_sn')->where(['id'=>$couponid])->all();
        $couponsn=array_column($couponsnall,'coupon_sn','id');
        //公司名称
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=array_column($companys,'name','id');


        $field=['id','uid','couponid','orderid','liaison','telphone','pre_u_time','pre_r_time','rel_u_time','rel_r_time','c_time','s_time','r_time','status','prepro','precity','precounty','prestore','sendmsg','company_id','companyid'];
        $str   = ['编号','微信昵称',['优惠券号码','string'],['定单ID','string'],'联系人',['联系人手机号','string'],'预约用车时间','预约还车时间','实际用车时间','实际还车时间','创建时间','完成时间','接单时间','订单状态','省','市','区或县','预约网点','是否短信','供应商','客户公司'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($list as $e){
            $tmp['id'] = $e['id'];
            $tmp['uid'] = preg_quote($nicknamearr[$e['uid']]);
            $tmp['couponid'] = $couponsn[$e['couponid']];
            $tmp['orderid'] = $e['orderid'];
            $tmp['liaison'] = $e['liaison'];
            $tmp['telphone'] = $e['telphone'] ;
            $tmp['pre_u_time'] = $this->handleTime($e['pre_u_time']);
            $tmp['pre_r_time']= $this->handleTime($e['pre_r_time']);
            $tmp['rel_u_time'] = $this->handleTime($e['rel_u_time']);
            $tmp['rel_r_time']=$this->handleTime($e['rel_r_time']);
            $tmp['c_time']=$this->handleTime($e['c_time']);
            $tmp['s_time']=$this->handleTime($e['s_time']);
            $tmp['r_time']=$this->handleTime($e['r_time']);
            $tmp['status'] = $statusarr[$e['status']];
            $tmp['prepro'] = $e['prepro'];
            $tmp['precity']=$e['precity'];
            $tmp['precounty'] = $e['precounty'];
            $tmp['prestore'] = $e['prestore'];
            $tmp['sendmsg']=$e['sendmsg']==1 ? '是' : '否' ;
            $tmp['company_id'] = '盛大';
            $tmp['companyid']=$companynames[$e['companyid']];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '代步车订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($list,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }
    //////////////////////////////////////////////////////////





//批量导入优惠券
    public function actionLeadin() {
        return $this->render('leadin');
    }

    //批量导入优惠券，插入数据库

//    public function actionExcutexls(){
//        set_time_limit(0);
//        $msg['status'] = 1;
//        $msg['error'] = 'ok';
//        $msg['data'] = [];
//        if(Yii::$app->request->isPost){
//            $rootPath = Yii::$app->params['rootPath'];
//            $path0 = Yii::$app->request->post('path');
//            $path = $rootPath.$path0;
//            if(!$path || !file_exists($path)){
//                unlink($path);
//                return $this->json(0,'文件不存在');
//            }
//            $file=substr($path, strrpos($path, '.')+1);
//            $data = CExcel::getExcel($file)->importExcel($path);
//            //unlink($path);var_dump(\GuzzleHttp\json_decode($data[0][0]));
//            $datadb = [];
//            foreach($data as $key => $val ){
//                $val[0] = \GuzzleHttp\json_decode($val[0],true);
//                $datadb[$key]['servicecode']=$val[0]['serviceCode'];
//                $datadb[$key]['shopid']=$val[0]['shopId'];
//                $datadb[$key]['shopname']=$val[0]['shopName'];
//                $datadb[$key]['verifytime']=$val[0]['verifyTime'];
//                $datadb[$key]['region']=$val[0]['region'];
//                $datadb[$key]['serviceid']=$val[0]['serviceId'];
//                $datadb[$key]['servicename']=$val[0]['serviceName'];
//                $datadb[$key]['price']=$val[0]['price'];
//            }
//
//            //unlink($path);var_dump($datadb);
//
//            $fileds = ['servicecode','shopid','shopname','verifytime','region','serviceid','servicename','price'];
//            $db = Yii::$app->db;
//            $transaction = $db->beginTransaction();
//            try{
//                $db->createCommand()->batchInsert('{{%car_tuhunotice}}',$fileds,$datadb)->execute();
//            }catch (\Exception $e){
//                $transaction->rollBack();
//            }
//            $transaction->commit();
//            return $this->json(1,'suc');
//        }
//        return '非法访问';
//    }


//    public function actionExcutexls(){
//        set_time_limit(0);
//        $msg['status'] = 1;
//        $msg['error'] = 'ok';
//        $msg['data'] = [];
//        if(Yii::$app->request->isPost){
//            $rootPath = Yii::$app->params['rootPath'];
//            $path0 = Yii::$app->request->post('path');
//            $key=Yii::$app->request->post('key');
//            $path = $rootPath.$path0;
//            if(!$path || !file_exists($path)){
//                return $this->json(0,'文件不存在');
//            }
//            $data = CExcel::getExcel()->importExcel($path);
//
//            if(!empty($data[$key]) ){
//                $datastr['serviceCode']=$data[$key][0];
//                $datastr['shopId']=999999;
//                $datastr['shopName']=$data[$key][2];
//                $datastr['verifyTime']=date("YmdHis",strtotime($data[$key][7]));
//                $datastr['region']=$data[$key][3].$data[$key][4].$data[$key][5];
//                $datastr['serviceId']="FU-MD-DKHBZXC|1";
//                $datastr['serviceName']="标准洗车券（前海云车在线）";
//                $datastr['price']=100*$data[$key][6];
//                ksort($datastr);
//                $str = '';
//                foreach ($datastr as $k => $v) {
//                    $str .= $k . '=' . $v . '&';
//                }
//                $str .= hash("sha256", Yii::$app->params['signKey']);
//                $datastr['signature'] = hash("sha256", $str);
//                $datastr=json_encode($datastr);
//                $header = array(
//                    "content-type:application/json; charset=UTF-8",
//                    "Content-Length: " . strlen($datastr)
//
//                );
//                $url = Yii::$app->params['url'].'/frontend/web/notice/tuhunotice.html';
//                $result = W::http_post($url, $datastr, $header);
//
//                $postdata['path']=$path0;
//                $postdata['key']=$key+1;
//                $maxkey=count($data);
//                if($key<$maxkey){
//                    return $this->json(0,'hao',$postdata);
//                }
//            }
//            return $this->json(1,'suc');
//        }
//        return '非法访问';
//    }
    /**
     * 平安洗车订单列表查询条件
     * @return string
     */
    protected function setPinganorderWhere(){
        $request = Yii::$app->request;
        $coupon_code = trim($request->get('coupon_code',null));
        $mobile = trim($request->get('mobile',null));
        $partner_order = trim($request->get('partner_order',null));
        $order_id = trim($request->get('order_id',null));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $service_text = $request->get('service_text',null);
        $store_name = $request->get('store_name',null);
        $where=' id<>0 ';
        if(!empty($coupon_code)){
            $where.=' AND  coupon_code ="'.$coupon_code.'"';
        }
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        if(!empty($partner_order)){
            $where.=' AND  partner_order ="'.$partner_order.'"';
        }
        if(!empty($order_id) ){
            $where.=' AND  order_id ="'.$order_id.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        if(!empty($companyid)){
            $where.=' AND  company_id ="'.$companyid.'"';
        }
        if(!empty($service_text)){
            $where.=' AND  product_name ="'.$service_text.'"';
        }
        if(!empty($store_name)){
            $where.=' AND  store_name ="'.$store_name.'"';
        }

        $where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        $where=$this->getTimeWhere($where,'s_time',$s_time,$e_time);
        return $where;

    }

    //平安洗车订单列表
    public function actionPinganorlist() {

        $statusarr=CarWashPinganhcz::$order_status;
        $pcoupon_status=CarWashPinganhcz::$pcoupon_status;
        $service_text =CarWashPinganhcz::$service_text;
        //公司名称
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=array_column($companys,'name','id');
        if (Yii::$app->request->isAjax) {
            $model = new CarWashPinganhcz();
            $where=$this->setPinganorderWhere();
            $field=['id','coupon_id','coupon_code','coupon_name','uuid','mobile','product_id','product_name','merchant_shop','start_time','end_time','coupon_status','partner_order','store_id','store_name','province','city','district','address','verifytime','status','order_id','c_time','s_time','company_id'];
            $res=$model->page_list($field,$where,'id DESC');
            $listrows=$res['rows'];
            foreach ($listrows as $k => $val) {

                $listrows[$k]['verifytime'] = $this->handleTime($val['verifytime']);
                $listrows[$k]['c_time']=$this->handleTime($val['c_time']);
                $listrows[$k]['s_time']=$this->handleTime($val['s_time']);
                $listrows[$k]['status'] = $statusarr[$val['status']];
                $listrows[$k]['coupon_status'] = $pcoupon_status[$val['coupon_status']];
                $listrows[$k]['company_id'] = $companynames[$val['company_id']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return      $this->render ( 'pinganorlist',[
            'status'       =>$statusarr,
            'companys'     =>$companys,
            'service_text' =>$service_text
        ] );
    }

    //平安洗车订单导出列表

    public function actionPorderdownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $coupon_code = trim($request->get('coupon_code',null));
        $mobile = trim($request->get('mobile',null));
        $partner_order = trim($request->get('partner_order',null));
        $order_id = trim($request->get('order_id',null));
        $status = $request->get('status');
        $companyid = intval($request->get('companyid',0));
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $service_text = $request->get('service_text',null);
        $store_name = $request->get('store_name',null);

        $model = new CarWashPinganhcz();
        $where=$this->setPinganorderWhere();
        $cot = $model->table()->select("count(id) as cot")->where($where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '平安洗车订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/porderdown',
                    'page'=>1,
                    'coupon_code' => $coupon_code,
                    'mobile' => $mobile,
                    'partner_order' => $partner_order,
                    'order_id' => $order_id,
                    'status' => $status,
                    'companyid'=>$companyid,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    's_time' => $s_time,
                    'e_time' => $e_time,
                    'service_text' => $service_text,
                    'store_name' => $store_name
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/porderdown',
                        'page'=>$i,
                        'coupon_code' => $coupon_code,
                        'mobile' => $mobile,
                        'partner_order' => $partner_order,
                        'order_id' => $order_id,
                        'status' => $status,
                        'companyid'=>$companyid,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        's_time' => $s_time,
                        'e_time' => $e_time,
                        'store_name' => $store_name
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    //平安洗车订单导出Excel

    public function actionPorderdown(){

        $request = Yii::$app->request;
        $model = new CarWashPinganhcz();
        $statusarr=CarWashPinganhcz::$order_status;
        $pcoupon_status=CarWashPinganhcz::$pcoupon_status;
        $companys=(new CarCompany())->getCompany(['id','name']);
        $companynames=array_column($companys,'name','id');
        $where=$this->setPinganorderWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['id','coupon_id','coupon_code','coupon_name','uuid','mobile','product_id','product_name','merchant_shop','start_time','end_time','coupon_status','partner_order','store_id','store_name','province','city','district','address','verifytime','status','order_id','c_time','s_time','company_id'];
        $list = $model->table()->select($field)
            ->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $str   = ['编号','平安卡券编号',['平安兑换码','string'],['平安卡券名称','string'],'统一身份ID',['客户手机号码','string'],'平安商品ID','平安商品名称',['平安网点ID','string'],'卡券生效时间','卡券失效时间','卡券核销状态','盛大订单编号','洗车门店编号','洗车门店名称','省','市','区或县','洗车门店地址','核销时间','平安接口核销状态','平安订单编号','创建时间','完成时间','客户公司'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        foreach ($list as $e){
            $tmp['id'] = $e['id'];
            $tmp['coupon_id'] = $e['coupon_id'];
            $tmp['coupon_code'] = $e['coupon_code'];
            $tmp['coupon_name'] = $e['coupon_name'];
            $tmp['uuid'] = $e['uuid'];
            $tmp['mobile'] = $e['mobile'] ;
            $tmp['product_id'] = $e['pre_u_time'];
            $tmp['product_name'] = $e['product_name'];
            $tmp['merchant_shop'] = $e['merchant_shop'];
            $tmp['start_time'] = $e['start_time'];
            $tmp['end_time'] = $e['end_time'];
            $tmp['coupon_status'] = $pcoupon_status[$e['coupon_status']];
            $tmp['partner_order'] = $e['partner_order'];
            $tmp['store_id'] = $e['store_id'];
            $tmp['store_name'] = $e['store_name'];
            $tmp['province'] = $e['province'];
            $tmp['city']=$e['city'];
            $tmp['district'] = $e['district'];
            $tmp['address'] = $e['address'];
            $tmp['verifytime'] = $this->handleTime($e['verifytime']);
            $tmp['status'] = $statusarr[$e['status']];
            $tmp['order_id'] = $e['order_id'];
            $tmp['c_time'] = $this->handleTime($e['c_time']);
            $tmp['s_time'] = $this->handleTime($e['s_time']);
            $tmp['company_id'] = $companynames[$e['company_id']];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '平安洗车订单列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($list,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }


    /**
     * ETC订单列表查询条件
     * @return 数据查询条件 $where  string
     */
    protected function setEtcorderWhere(){
        $request = Yii::$app->request;
        $outlet_id = trim($request->get('outlet_id',null));
        $store_name=trim($request->get('store_name',null));
        $where=' id<>0 ';
        if(!empty($outlet_id)){
            $where.=' AND  outlet_id ="'.$outlet_id.'"';
        }
        if(!empty($store_name)){
            $where.=' AND  store_name  LIKE "%'.$store_name.'%"';
        }

        //$where=$this->getTimeWhere($where,'c_time',$start_time,$end_time);
        return $where;

    }

    /**
     * ETC订单列表查询列表
     * @return  $res  json
     */
    public function actionWangdianlist() {

        if (Yii::$app->request->isAjax) {
            $model = new Car_wash_pinganhcz_shop();
            $where=$this->setEtcorderWhere();
            $field=[];
            $res=$model->page_list($field,$where);
            $listrows=$res['rows'];
            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time']=$this->handleTime($val['c_time']);
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return      $this->render ( 'wangdianlist');
    }


    //网点名称编辑
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
                'data' => $data
            ]
        );
    }
    public function actionLeadinwangdian() {

        return $this->render('leadinwangdian');
    }
    public function actionExcutexlswangdian() {
        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)) return $this->json(0,'文件不存在');
            $file=substr($path, strrpos($path, '.')+1);
            $data = CExcel::getExcel($file)->importExcel($path);
            if($data){
                unset($data[0]);
            }else{
                unlink($path);
                return $this->json(0,'数据不存在');
            }
            $c_time = time();
            $data_2=[];
            $companyid = 50;
            $tmp = null;
            $Model = new Car_wash_pinganhcz_shop();
            $shopallold = $Model->select('outlet_id,store_name',[])->all();
            $shopall = array_column($shopallold,'outlet_id');

            //构造数组
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                foreach ($data as $key=>$val){
                    array_push($val,$c_time,$companyid);
                    $val[0]=trim($val[0]);
                    $val[1]=trim($val[1]);
                    if(!empty($val[0]) && !empty($val[1])){
                        if(!in_array($val[0],$shopall)){
                            $data_2[]=$val;
                        }
                    }
                }
                $transaction->commit();
            }catch (\Exception $e){
                $transaction->rollBack();
            }

            $arrlength=count($data_2);
            if($arrlength == 0 && empty($data_2)){
                unlink($path);
                return $this->json(0,'数据不存在或全为重复数据');
            }
            //分割数组并插入缓存
            $this->maxInsertNum = 1000;
            $result=$this->segmentationArr($data_2);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            unset($data,$data_2);
            return $this->json(1,'成功',$result);
        }
        return '非法访问';
    }

    public function actionBatchwangdian(){
        set_time_limit(0);
        if(Yii::$app->request->isAjax){
            ini_set('memory_limit','1024M');
            $data = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($data['num'] >= $data['xxzmaxnum']){
                $msg['status'] = 0;
                $msg['error'] = '非法调用';
            }else{
                $fileds = ['outlet_id','store_name','city','address','c_time','company_id'];
                $tablename='{{%car_wash_pinganhcz_shop}}';
                $msg=$this->insertDb($data,$fileds,$tablename);
            }
            $msg['url'] = Url::to(['order/wangdianlist']);
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

    /**
     * 山东太保洗车订单列表查询条件
     * @return 数据查询条件 $where  string
     */
    protected function setTaibaoOrderWhere(){
        $request = Yii::$app->request;
        $ticket_id = trim($request->get('ticket_id',null));
        $apply_name = trim($request->get('apply_name',null));
        $apply_phone = trim($request->get('apply_phone',null));
        $car_no = trim($request->get('car_no',null));
        $encrypt_code = trim($request->get('encrypt_code'),null);
        $status = $request->get('status');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $shop_name = $request->get('shop_name',null);
        $where=' id<>0 ';
        if(!empty($ticket_id)){
            $where.=' AND  ticket_id ="'.$ticket_id.'"';
        }
        if(!empty($apply_name)){
            $where.=' AND  apply_name ="'.$apply_name.'"';
        }
        if(!empty($apply_phone)){
            $where.=' AND  apply_phone ="'.$apply_phone.'"';
        }
        if(!empty($car_no)){
            $where.=' AND  car_rental_vehicle_no ="'.$car_no.'"';
        }
        if(!empty($encrypt_code)){
            $where.=' AND  encrypt_code ="'.$encrypt_code.'"';
        }
        if(!empty($status) || $status == '0'){
            $where.=' AND  status ="'.$status.'"';
        }
        if(!empty($shop_name)){
            $where.=' AND  shop_name ="'.$shop_name.'"';
        }

        $where=$this->getTimeWhere($where,'c_time',$s_time,$e_time);
        $where=$this->getTimeWhere($where,'u_time',$start_time,$end_time);
        return $where;

    }

    /**
     * 太保洗车推送记录列表查询列表
     * @return  $res  json
     */
    public function actionTaibaoorlist() {

        $statusarr=Car_wash_order_taibao::$status_text;
        $service_text=Car_wash_order_taibao::$service_type;
        $equity_status=Car_wash_order_taibao::$equity_status;
        if (Yii::$app->request->isAjax) {
            $model = new Car_wash_order_taibao();
            $where=$this->setTaibaoOrderWhere();
            $field=['id','ticket_id','apply_name','unit_code','apply_phone','car_rental_vehicle_no','point_time','service_type','reason','address','consumer_code','encrypt_code','status','c_time','u_time','equity_status','shop_name'];
            $res=$model->page_list($field,$where);
            $listrows=$res['rows'];
            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time']=$this->handleTime($val['c_time']);
                $listrows[$k]['u_time']= $val['status']==2 ? $this->handleTime($val['u_time']) : "--";
                $listrows[$k]['status'] = $statusarr[$val['status']];
                $listrows[$k]['service_type'] = $service_text[$val['service_type']];
                $listrows[$k]['equity_status'] = $equity_status[$val['equity_status']];
                $address = explode(' ',$val['address']);
                $listrows[$k]['addressdesc']  = $address[3];
                unset($address[3]);
                $address = implode(' ',$address);
                $listrows[$k]['address']  = $address;

            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return      $this->render ( 'taibaoorlist',[
            'status'=>$statusarr
        ] );
    }

    /**
     * 太保洗车推送记录列表查询列表
     * @return    json数据
     */

    public function actionTaibaodownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $ticket_id = trim($request->get('ticket_id',null));
        $apply_name = trim($request->get('apply_name',null));
        $apply_phone = trim($request->get('apply_phone',null));
        $car_no = trim($request->get('car_no',null));
        $encrypt_code = trim($request->get('encrypt_code'),null);
        $status = $request->get('status');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $s_time = $request->get('s_time',null);
        $e_time = $request->get('e_time',null);
        $shop_name = $request->get('shop_name',null);
        $model = new Car_wash_order_taibao();
        $where=$this->setTaibaoOrderWhere();
        $total = $model->table()->select("count(id) as cot")->where($where)->count();
        if(!$total) return $this->json(0,'没有数据');
        $title = '太保洗车推送记录-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'order/taibaodown',
                    'page'=>1,
                    'ticket_id' => $ticket_id,
                    'apply_name' => $apply_name,
                    'apply_phone' => $apply_phone,
                    'car_no' => $car_no,
                    'encrypt_code' => $encrypt_code,
                    'status' => $status,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    's_time' => $s_time,
                    'e_time' => $e_time,
                    'shop_name' => $shop_name
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'order/taibaodown',
                        'page'=>$i,
                        'ticket_id' => $ticket_id,
                        'apply_name' => $apply_name,
                        'apply_phone' => $apply_phone,
                        'car_no' => $car_no,
                        'encrypt_code' => $encrypt_code,
                        'status' => $status,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        's_time' => $s_time,
                        'e_time' => $e_time,
                        'shop_name' => $shop_name
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    /**
     * 太保洗车推送记录
     * @return
     * 下载Excel文件
     */

    public function actionTaibaodown(){

        $request = Yii::$app->request;
        $model = new Car_wash_order_taibao();
        $statusarr=Car_wash_order_taibao::$status_text;
        $service_text=Car_wash_order_taibao::$service_type;
        $where=$this->setTaibaoOrderWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['id','ticket_id','branch_code','unit_code','apply_name','apply_phone','car_rental_vehicle_no','point_time','u_time','service_type','reason','address','consumer_code','encrypt_code','status','c_time'];
        $list = $model->table()->select($field)->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $str   = ['编号','太保订单号',['分公司代码','string'],['中支公司代码','string'],'客户姓名',['客户电话','string'],'订单车牌号','预约时间','订单完成时间','服务类型','取消原因','地址','盛大核销二维码','洗车凭证','状态','创建时间'];
        $tmp   = null;
        $index = 1;
        foreach ($list as $e){
            $tmp['id'] = $e['id'];
            $tmp['ticket_id'] = $e['ticket_id'];
            $tmp['branch_code'] = $e['branch_code'];
            $tmp['unit_code'] = $e['unit_code'];
            $tmp['apply_name'] = $e['apply_name'];
            $tmp['apply_phone'] = $e['apply_phone'];
            $tmp['car_rental_vehicle_no'] = $e['car_rental_vehicle_no'] ;
            $tmp['point_time'] = $e['point_time'];
            $tmp['u_time'] = $e['status']==2 ? $this->handleTime($e['u_time']):'--' ;
            $tmp['service_type'] = $service_text[$e['service_type']];
            $tmp['reason'] = $e['reason'];
            $tmp['address'] = $e['address'];
            $tmp['consumer_code'] = $e['consumer_code'];
            $tmp['encrypt_code'] = $e['encrypt_code'];
            $tmp['status'] = $statusarr[$e['status']];
            $tmp['c_time'] = $this->handleTime($e['c_time']);

            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '太保洗车推送记录列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($list,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }

    //下载平安网点导入模板
    function actionDexcel() {
        $rootpath = Yii::$app->params['rootPath'];
        return Yii::$app->response->sendFile($rootpath.'./static/tpl/wangdian.xls');
    }
}

