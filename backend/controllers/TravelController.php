<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/12 0012
 * Time: 下午 4:35
 */

namespace backend\controllers;

use yii;
use backend\util\BController;
use common\components\W;
use yii\helpers\Url;
use yii\web\Response;
use common\components\CExcel;
use common\models\TravelListDate;
use common\models\TravelData;
use common\models\TravelCompany;
use common\models\TravelUsers;
use common\models\TravelAgenc;
use common\models\TravelList;



class TravelController extends BController {
    private  $downpagesize=5000;//导出数据最大条数
    private  $cacheTime=7200;
    private  $maxInsertNum=1000;
    /**
     * 报名列表查询条件
     * @return 数据查询条件 $where  string
     */

    protected function setEnrollWhere(){
        $request = Yii::$app->request;
        $name = trim($request->get('name',null));
        $code = trim($request->get('code',null));
        $mobile = $request->get('mobile');
        $luxian = (int)$request->get('luxian');
        $orgain = (int)$request->get('orgain');
        $jigou = (int)$request->get('jigou');
        $yincode = $request->get('yincode');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);

        $where=' id<>0 ';
        if(!empty($name)){
            $where.=' AND  name ="'.$name.'"';
        }
        if(!empty($code)){
            $where.=' AND  code ="'.$code.'"';
        }
        if(!empty($mobile)){
            $where.=' AND  mobile ="'.$mobile.'"';
        }
        if(!empty($luxian)){
            $where.=' AND  travel_list_id ="'.$luxian.'"';
        }
        if(!empty($orgain)){
            $where.=' AND  company_pid ="'.$orgain.'"';
        }
        if(!empty($jigou)){
            $where.=' AND  company_id ="'.$jigou.'"';
        }
        if(!empty($yincode)){
            $user = (new TravelUsers()) -> getUserOne(['code'=>$yincode]);
            $where.=' AND  travel_user_id ="'.$user['id'].'"';
        }

        $where=$this->getDateWhere($where,'travel_date',$start_time,$end_time);
        return $where;
    }
    /**
     * 报名列表查询列表
     * @return  $res  json
     */
    public function actionEnrollList() {
        $luxianModel= new TravelList();
        $companyModel = new TravelCompany();
        $luxianlist =  $luxianModel->getLuxianAll();
        if (Yii::$app->request->isAjax) {
            $model = new TravelData();
            $where=$this->setEnrollWhere();
            $field=[];
            $res=$model->page_list($field,$where);

            $uids=array_column($res['rows'],'travel_user_id');
            $userModel = new TravelUsers();
            $users = $userModel -> getUserAll([],['id'=>$uids]);
            $username = array_column($users,'name','id');
            $usercode = array_column($users,'code','id');
            $companylist =  $companyModel->getCompanyAll();
            $listrows=$res['rows'];
            foreach ($listrows as $k => $val) {
                $listrows[$k]['ctime']=$this->handleTime($val['ctime']);
                $listrows[$k]['username'] = $username[$val['travel_user_id']];
                $listrows[$k]['usercode'] = $usercode[$val['travel_user_id']];
                $listrows[$k]['compayname'] = $companylist[$val['company_id']];
                $listrows[$k]['compaypname'] = $companylist[$val['company_pid']];
                $listrows[$k]['luxian'] = $luxianlist[$val['travel_list_id']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        $orgain =  $companyModel->getCompanyAll(['pid'=>0]);

        return      $this->render ( 'enroll-list',['luxianlist'=>$luxianlist,'orgain'=>$orgain] );
    }
    public function actionEnrolldownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $name = trim($request->get('name',null));
        $code = trim($request->get('code',null));
        $mobile = $request->get('mobile');
        $luxian = (int)$request->get('luxian');
        $orgain = (int)$request->get('orgain');
        $jigou = (int)$request->get('jigou');
        $yincode = $request->get('yincode');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $where = $this->setEnrollWhere();
        $cot = (new TravelData())->select("count(id) as cot",$where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '报名订单列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'travel/enrolldown',
                    'page'=>1,
                    'name'=> $name,
                    'code' => $code,
                    'mobile'=> $mobile,
                    'luxian' => $luxian,
                    'orgain' => $orgain,
                    'jigou' => $jigou,
                    'yincode'=>$yincode,
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
                        'travel/enrolldown',
                        'page'=>$i,
                        'name'=> $name,
                        'code' => $code,
                        'mobile'=> $mobile,
                        'luxian' => $luxian,
                        'orgain' => $orgain,
                        'jigou' => $jigou,
                        'yincode'=>$yincode,
                        'start_time' => $start_time,
                        'end_time' => $end_time
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    /*
     * 报名列表导出
     * */

    public function actionEnrolldown(){
        $request = Yii::$app->request;
        $where = $this->setEnrollWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field = [];
        $list = (new TravelData())->select($field,$where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();

        $luxianModel= new TravelList();
        $companyModel = new TravelCompany();
        $luxianlist =  $luxianModel->getLuxianAll();
        $uids=array_column($list,'travel_user_id');
        $userModel = new TravelUsers();
        $users = $userModel -> getUserAll([],['id'=>$uids]);
        $username = array_column($users,'name','id');
        $usercode = array_column($users,'code','id');
        $companylist =  $companyModel->getCompanyAll();

        $field = ['id','name','code','mobile','sex','travel_list_id','travel_user_id','yincode','travel_date','ctime','company_pid','company_id'];
        $str   = ['序号','游客姓名',['身份证号','string'],['游客手机','string'],'性别','路线','营销员姓名','营销员代码','出发日期','添加时间','中支','机构'];
        $data  = null;
        $tmp   = null;
        foreach ($list as $e){
            $tmp['id'] = $e['id'];
            $tmp['name'] = $e['name'];
            $tmp['code'] = $e['code'];
            $tmp['mobile'] = $e['mobile'];
            $tmp['sex'] = $e['sex'] ;
            $tmp['travel_list_id'] = $luxianlist[$e['travel_list_id']];
            $tmp['travel_user_id']=$username[$e['travel_user_id']];
            $tmp['yincode'] = $usercode[$e['travel_user_id']];
            $tmp['travel_date'] = $e['travel_date'];
            $tmp['ctime']=! empty($e['ctime']) ? date("Y-m-d H:i:s",$e['ctime']) : '--';
            $tmp['company_pid']=$companylist[$e['company_pid']];
            $tmp['company_id']=$companylist[$e['company_id']];
            $data[] = $tmp;
        }

        $headArr = array_combine($field,$str);
        $fileName = '旅游报名列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($list,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);

    }
    public function actionGetjigou() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $companyModel = new TravelCompany();
            $pid = (int)$request->post('orgain');
            $list = $companyModel->getCompanyAll(['pid'=>$pid]);
            return $this->json(1,'suc',$list);
        }
        exit('err');
    }
    public function actionGetuser() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $userModel = new TravelUsers();
            $organ_id = (int)$request->post('company_id');
            $list = $userModel->getUserAll([],['organ_id'=>$organ_id]);
            return $this->json(1,'suc',$list);
        }
        exit('err');
    }
    public function actionGetdate() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $model = new TravelListDate();
            $travel_list_id = (int)$request->post('travel_list_id');
            $list = $model->getDateAll(['travel_list_id'=>$travel_list_id]);
            return $this->json(1,'suc',$list);
        }
        exit('err');
    }


    /**
     * 旅游日期查询条件
     * @return  $res  json
     */

    protected function setDatelWhere(){
        $request = Yii::$app->request;
        $luxian = (int)$request->get('luxian');
        $start_time = $request->get('start_time',null);
        $end_time = $request->get('end_time',null);
        $where=' id<>0 ';
        if(!empty($luxian)){
            $where.=' AND  travel_list_id ="'.$luxian.'"';
        }
        $where=$this->getDateWhere($where,'date',$start_time,$end_time);
        return $where;
    }
    /**
     * 旅游日期查询列表
     * @return  $res  json
     */
    public function actionDateList() {
        $luxianModel= new TravelList();
        $luxianlist =  $luxianModel->getLuxianAll();
        if (Yii::$app->request->isAjax) {
            $model = new TravelListDate();
            $where=$this->setDatelWhere();
            $field=[];
            $res=$model->page_list($field,$where);
            $listrows=$res['rows'];
            foreach ($listrows as $k => $val) {
                $listrows[$k]['ctime']=$this->handleTime($val['ctime']);
                $listrows[$k]['luxian'] = $luxianlist[$val['travel_list_id']];
                $listrows[$k]['prenum'] = (new TravelData())->getUserNum(['travel_date_id'=>$val['id']]);
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return      $this->render ('date-list',['luxianlist'=>$luxianlist]);
    }

    public function actionEditDate() {
        $request = Yii::$app->request;
        $luxianModel= new TravelList();
        $luxianlist =  $luxianModel->getLuxianAll();
        $info = [];
        $luxianid=intval($request->get('id'));
        $luxianModel = new TravelListDate();
        if($luxianid)$info=$luxianModel->select('*',['id'=>$luxianid])->one();
        if ($request->isPost) {
            $data = $request->post();
            $datadb['travel_list_id'] = $data['luxian'];
            $datadb['date'] = $data['date'];
            $datadb['number'] = $data['number'];
            $datadb['ctime'] = time();
            if($data['id']){
                $luxianModel->myUpdate($datadb,['id'=>(int)$data['id']]);
            }else{
                $luxianModel->myInsert($datadb);
            }
            $this->redirect('date-list.html');
        }
        return   $this->render('edit-date',['luxianlist'=>$luxianlist,'info'=>$info]);
    }
    public function actionEnrollEdit() {
        $request = Yii::$app->request;
        $luxianModel= new TravelList();
        $luxianlist =  $luxianModel->getLuxianAll();
        $info = [];
        $id=intval($request->get('id'));
        $dataModel = new TravelData();
        $dateModel = new TravelListDate();
        $companyModel = new TravelCompany();
        $userModel = new TravelUsers();
        $companylist =  $companyModel->getCompanyAll(['pid'=>0]);
        $datelist = $dateModel->getDateAll(['travel_list_id'=>'1']);
        $jigoulist =  $companyModel->getCompanyAll(['pid'=>'429']);
        $userlist = $userModel->getUserAll(['id','name'],['organ_id'=>'430']);

        if($id){
            $info=$dataModel->select('*',['id'=>$id])->one();
            $datelist = $dateModel->getDateAll(['travel_list_id'=>$info['travel_list_id']]);
            $companylist =  $companyModel->getCompanyAll(['pid'=>0]);
            $jigoulist =  $companyModel->getCompanyAll(['pid'=>$info['company_pid']]);
            $userlist = $userModel->getUserAll(['id','name'],['organ_id'=>$info['company_id']]);
        }
        if ($request->isPost) {
            $data = $request->post();
            $datadb['name'] = $data['name'];
            $datadb['code'] = $data['code'];
            $datadb['mobile'] = $data['mobile'];
            $datadb['remark'] = $data['remark'];
            $datadb['sex'] = $data['sex'];
            $datadb['travel_list_id'] = $data['travel_list_id'];
            $datadb['travel_user_id'] = $data['travel_user_id'];
            $datadb['travel_date_id'] = $data['travel_date_id'];
            $dateinfo=$dateModel->getDateOne(['id'=>$data['travel_date_id']]);
            $datadb['travel_date'] = $dateinfo['date'];
            $datadb['company_id'] = $data['company_id'];
            $datadb['company_pid'] = $data['company_pid'];
            $datadb['ctime'] = time();
            if($data['id']){
                $dataModel->myUpdate($datadb,['id'=>(int)$data['id']]);
            }else{
                $dataModel->myInsert($datadb);
            }
            $this->redirect('enroll-list.html');
        }
        return   $this->render('enroll-edit',[
            'info'=>$info,
            'luxianlist'=>$luxianlist,
            'companylist'=>$companylist,
            'datelist'=>$datelist?$datelist:[],
            'jigoulist'=>$jigoulist?$jigoulist:[],
            'userlist'=>$userlist?$userlist:[]
        ]);
    }

    /**
     * 报名列表查询条件
     * @return 数据查询条件 $where  string
     */

    protected function setSalesmanlWhere(){
        $request = Yii::$app->request;
        $name = trim($request->get('name',null));
        $code = trim($request->get('code',null));
        $orgain = (int)$request->get('orgain');
        $jigou = (int)$request->get('jigou');
        $where=' id<>0 ';
        if(!empty($name)){
            $where.=' AND  name ="'.$name.'"';
        }
        if(!empty($code)){
            $where.=' AND  code ="'.$code.'"';
        }

        if(!empty($orgain) && empty($jigou)){
            $companyModel = new TravelCompany();
            $orgainlist =  $companyModel->select('id',['pid'=>$orgain])->all();
            if($orgainlist){
                $oids = array_column($orgainlist,'id');
                $orgainstr = implode(',',$oids);
                $where.=' AND  organ_id IN('.$orgainstr.')';
            }else{
                $where.=' AND  organ_id IN(-1)';
            }
        }else if(!empty($jigou)){
            $where.=' AND  organ_id ="'.$jigou.'"';
        }
        return $where;
    }
    /**
     * 报名列表查询列表
     * @return  $res  json
     */
    public function actionSalesmanList() {
        $luxianModel= new TravelList();
        $companyModel = new TravelCompany();
        $luxianlist =  $luxianModel->getLuxianAll();
        if (Yii::$app->request->isAjax) {
            $model = new TravelUsers();
            $where=$this->setSalesmanlWhere();
            $field=[];
            $res=$model->page_list($field,$where);
            $companylist =  $companyModel->getCompanyAll([],false);
            $company = [];
            foreach ($companylist as $key=>$val){
                $company[$val['id']]['name']=$val['name'];
                $company[$val['id']]['pid']=$val['pid'];
            }
            $listrows=$res['rows'];
            foreach ($listrows as $k => $val) {
                $listrows[$k]['ctime']=$this->handleTime($val['ctime']);
                $listrows[$k]['compayname'] = $company[$val['organ_id']]['name'];
                $listrows[$k]['compaypname'] = $company[$company[$val['organ_id']]['pid']]['name'];
            }
            $res['rows']=$listrows;
            $res['data']=$where;
            return json_encode($res);
        }
        $orgain =  $companyModel->getCompanyAll(['pid'=>0]);
        return      $this->render ( 'salesman-list',['luxianlist'=>$luxianlist,'orgain'=>$orgain] );
    }
}

