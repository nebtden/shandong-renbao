<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5 0005
 * Time: 上午 9:03
 */


namespace backend\controllers;
use backend\util\BController;
use common\models\VideoCompany;
use yii;
use common\components\W;
use yii\helpers\Url;
use common\components\CExcel;
use common\models\VideoCode;
use common\models\VideoPath;

use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

class CalendaradController extends BController {
    private  $downpagesize=50000;//导出数据最大条数
    private  $cacheTime=7200;
    private  $maxInsertNum=1000;
    /**
     * 视频观看码列表查询条件
     * @return 数据查询条件 $where  string
     */
    protected function setCalendarWhere(){
        $request = Yii::$app->request;
        $code = trim($request->get('code',null));
        $batch_no = trim($request->get('batch_no',null));
        $company_id = $request->get('company_id');
        $where=' id<>0 ';
        if(!empty($code)){
            $where.=' AND  code ="'.$code.'"';
        }
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }
        if(!empty($company_id)){
            $where.=' AND  company_id ="'.$company_id.'"';
        }

        return $where;
    }
    /**
     * 视频观看码列表查询列表
     * @return  $res  json
     */
    public function actionCodelist() {
        $statusarr = VideoCode::$status;
        $company = (new VideoCompany()) ->getCompanyAll();
        if (Yii::$app->request->isAjax) {
            $model = new VideoCode();

            $where=$this->setCalendarWhere();
            $field=[];
            $res=$model->page_list($field,$where);
            $listrows=$res['rows'];
            $companyall = array_column($company,'name','id');
            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time']=$this->handleTime($val['c_time']);
                $listrows[$k]['u_time']=$this->handleTime($val['u_time']);
                $listrows[$k]['status'] = $statusarr[$val['status']];
                $listrows[$k]['company_id'] = $companyall[$val['company_id']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return      $this->render ( 'codelist',[
            'status'=>$statusarr,
            'company'=>$company
        ] );
    }

    /**
     * 视频观看码
     * @return    json数据
     */

    public function actionCodedownload() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $code = trim($request->get('code',null));
        $batch_no = trim($request->get('batch_no',null));
        $model = new VideoCode();
        $where=$this->setCalendarWhere();
        $total = $model->table()->select("count(id) as cot")->where($where)->count();
        if(!$total) return $this->json(0,'没有数据');
        $title = '观看码列表-';
        $pagesize = $this->downpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'calendarad/videocodedown',
                    'page'=>1,
                    'code' => $code,
                    'batch_no' => $batch_no
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'calendarad/videocodedown',
                        'page'=>$i,
                        'code' => $code,
                        'batch_no' => $batch_no
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    /**
     * 观看码列表
     * @return
     * 下载Excel文件
     */

    public function actionVideocodedown(){

        $request = Yii::$app->request;
        $model = new VideoCode();

        $where=$this->setCalendarWhere();
        $pagesize = $this->downpagesize;
        $page = $request->get('page',1);
        $field=['code','views_num','company_id'];
        $list = $model->table()->select($field)->where($where)
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();

        $company = (new VideoCompany()) ->getCompanyAll();
        $companyall = array_column($company,'name','id');
        $str   = [['观看码','string'],'可观看次数','保险公司名称'];
        $tmp   = null;
        $index = 1;
        foreach ($list as $e){
            $tmp['code'] = $e['code'];
            $tmp['views_num'] = $e['views_num'];
            $tmp['company_id'] = $companyall[$e['company_id']];
            $data[] = $tmp;
            $index++;
        }

        $headArr = array_combine($field,$str);
        $fileName = '观看码列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        unset($list,$data,$tmp,$nickname,$nicknamearr,$companys,$companynames);
    }
    /**
     * 批量生成观看码
     * @return
     * 下载Excel文件
     */

    public function actionGenerate() {
        $request = Yii::$app->request;
        $company = (new VideoCompany()) ->getCompanyAll();
        if($request->isPost) {
            $data = $request->post();
            $num = intval($data['generate_num']);
            unset($data['generate_num']);
            $model = new VideoCode();
            //验证数量
            if (!$num || !is_numeric($num)) return $this->json(0,'请确认输入的是一个正整数') ;
            if ($num > 100000) return $this->json(0,'生成数量过多') ;
            //验证批号是否的重复
            $batch=W::createNonceCapitalStr(8);
            $res=$model->table()->select('batch_no')->where(['batch_no'=>$batch])->one();
            if($res) return $this->json(0,'批号重复请重新提交');
            $data['c_time'] = time();
            $fData = null;
            for ($i = 0; $i < $num; $i++) {
                $tmp = [];
                $code = $model->generateCardNoxu('code',8);
                $tmp['code'] = $code;
                $tmp['views_num'] = $data['views_num'] ? $data['views_num'] : 14;
                $tmp['c_time'] = $data['c_time'];
                $tmp['batch_no'] = $batch;
                $tmp['company_id'] = $data['company_id'];
                $fData [] = $tmp;
            }

            //分割数组并插入缓存
            $result=$this->segmentationArr($fData);
            if(!$result)return $this->json(0,'数据写入缓存失败');
            unset($fData,$tmp);
            return $this->json(1,'成功',$result);
        }

        return $this->render('generate',['company'=>$company]);
    }

    /**
     * 优惠券批量插入数据库
     * @return string
     */
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
                $fields = ['code', 'views_num', 'c_time', 'batch_no','company_id'];
                $tablename='{{%video_code}}';
                $msg=$this->insertDb($data,$fields,$tablename);
            }
            $msg['url'] = Url::to(['calendarad/codelist']);
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

    protected function setVideoWhere(){
        $request = Yii::$app->request;
        $title = trim($request->get('title',null));
        $company_id = $request->get('company_id',null);
        $where=' id<>0 ';


        if(! empty($title)){
            $where.=' AND title  LIKE"%'.$title.'%"';
        }
        if(! empty($company_id)){
            $where.=' AND company_id  ="'.$company_id.'"';
        }
        return $where;
    }
    /**
     * 视频观看码列表查询列表
     * @return  $res  json
     */
    public function actionVideolist() {
        $company = (new VideoCompany()) ->getCompanyAll();
        $show_desc = VideoPath::$show_desc;
        if (Yii::$app->request->isAjax) {
            $model = new VideoPath();
            $where=$this->setVideoWhere();
            $field=[];
            $res=$model->page_list($field,$where);
            $listrows=$res['rows'];
            $companyall = array_column($company,'name','id');
            foreach ($listrows as $k => $val) {
                $listrows[$k]['c_time']=$this->handleTime($val['c_time']);
                $listrows[$k]['pic'] = "<img width='100px' height=''  src='" . $val['pic'] . "'/>";
                $listrows[$k]['company_id'] = $companyall[$val['company_id']];
                $listrows[$k]['show_desc'] = $show_desc[$val['show_desc']];
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return   $this->render ( 'videolist' ,['company' =>$company]);
    }



    public function actionVideoedit()
    {

        $id = $_REQUEST['id'] ? $_REQUEST['id'] : "";
        $model = new VideoPath();
        $cates = $model->table()->select('*')->where(['id'=>$id])->one();
        $company = (new VideoCompany()) ->getCompanyAll();
        if (Yii::$app->request->isPost){
            $request = Yii::$app->request;
            $data = $request->post();
            if (empty($data['title']) || empty($data['introduction']))return '缺少参数';
               $arr=[
                   'title' => $data['title'],
                   'sort' => $data['sort']?$data['sort']:0,
                   'introduction' => $data['introduction'],
                   'path' => $data['path'],
                   'pic' => $data['pic'],
                   'show_desc' => $data['show_desc'],
                   'company_id' => $data['company_id'],
                   'status' => $data['status'],
                   'c_time' => time()
                ];

            if (!empty($data['id'])) {
                $model->myUpdate($arr,['id'=>intval($data['id'])]);
            } else {
                $model->myInsert($arr);
            }
            $this->redirect('videolist.html');
        }
        return $this->render('videoedit', ['data' => $cates,'company'=>$company,'show_desc'=>VideoPath::$show_desc,'status'=>VideoPath::$status]);
    }

}