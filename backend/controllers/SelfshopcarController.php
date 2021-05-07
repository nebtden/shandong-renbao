<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19 0019
 * Time: 上午 10:36
 */
namespace backend\controllers;



use Yii;
use yii\helpers\Url;
use common\models\Car_shop;
use common\models\Wash_shop;
use common\models\Wash_payee;
use common\models\Wash_withdrawals;
use common\models\Fans;
use backend\util\BController;
use common\components\CExcel;
use common\components\W;
use yii\web\Response;
use common\models\Area;
use yii\helpers\ArrayHelper;
use common\components\AliPay;
use common\models\CarFinanceTransferlog;
use common\models\CarFinanceUserAccount;
use common\models\Car_washarea;


class SelfshopcarController extends BController {

    //支付宝转账时的token令牌名称
    private $token_name = 'zfb_transfer_form_token';
    private $maxpagesize=2000;

    public function actionIndex() {
        return $this->render('index', array('cur' => 1));
    }

    public function actionShop_list() {
        $request = Yii::$app->request;
        $status  = Wash_shop::$status;

        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber",1);
            $pagesize = $request->get("pageSize",10);
            $shopModel = new Wash_shop();
            $where = "  {{%wash_shop}}.id != 0 ";


            if($_REQUEST["shopname"]){
                $where.=" and {{%wash_shop}}.shop_name like '%".trim($_REQUEST["shopname"])."%'";
            }
            if(!empty($_REQUEST['area']))
            {
                $where.=' and {{%wash_shop}}.area='.$_REQUEST['area'];
            }else if(!empty($_REQUEST['city']))
            {
                $where.=' and {{%wash_shop}}.city='.$_REQUEST['city'];
            }else if(!empty($_REQUEST['province']))
            {
                $where.=' and {{%wash_shop}}.province='.$_REQUEST['province'];
            }


            $cot = $shopModel->select("count({{%wash_shop}}.id) as cot",$where)
                ->join('LEFT JOIN','{{%fans}}','{{%fans}}.id = {{%wash_shop}}.uid')
                ->one();


            $shoplist = $shopModel->select('{{%wash_shop}}.*,{{%fans}}.nickname',$where)
                ->join('LEFT JOIN','{{%fans}}','{{%fans}}.id = {{%wash_shop}}.uid')
                ->page($page,$pagesize)
                ->orderBy('{{%wash_shop}}.id desc')
                ->all();
            $satus=$status;
            foreach ($shoplist as $key=>$val){
                $shoplist[$key]['shop_register_time'] =!empty($val['shop_register_time'])?date("Y-m-d H:i:s",$val['shop_register_time']):'--'; ;
                $shoplist[$key]['shop_apply_time'] = !empty($val['shop_apply_time'])?date("Y-m-d H:i:s",$val['shop_apply_time']):'--';
                $shoplist[$key]['adopt_time'] = !empty($val['adopt_time'])?date("Y-m-d H:i:s",$val['adopt_time']):'--';
                $shoplist[$key]['shop_status']=$satus[$shoplist[$key]['shop_status']];
            }
            $res = ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$shoplist,'where'=>$where];
            return json_encode($res);
        }
        $area=new Car_washarea();
        $province=$area->getProvince();
        return      $this->render ('shop_list',[
            'cur'=>1,
            'province'=>$province,
            'status'=>$status
        ]);
    }


    public function actionGetcity() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $code = $request->post("code",null);
            $area = new Car_washarea();
            $city = $area->getCity($code);

            return $this->json(1,'操作成功',$city);
        }
        return $this->json(0,'操作失败');
    }
    public function actionGetarea() {

        $request = Yii::$app->request;
        if($request->isPost) {
            $code = $request->post("code",null);
            $areaModel = new Car_washarea();
            $area = $areaModel->getArea($code);
            return $this->json(1,'操作成功',$area);
        }
        return $this->json(0,'操作失败');
    }

    public function actionShop_edit() {

        $request = Yii::$app->request;
        $id = $request->get("id",null);
        $status  = Wash_shop::$status;

        if (Yii::$app->request->post()) {
            $data=Yii::$app->request->post();
            header('Content-type: text/html; charset=utf-8');


            if(! preg_match("/^1[345789]{1}\d{9}$/",$data['mobile'])){
                exit('请输入正确的手机号码');
            }

            if($data['shop_status']==2) $data['adopt_time']=time();
            $where['id'] = intval($_POST['id']);
            $data['shop_register_time']=strtotime($data['shop_register_time']);
            unset($data['id']);
            unset($data['file']);
            (new Wash_shop())->myUpdate($data,$where);
            $this->redirect('shop_list.html');
        }

        $where = " {{%wash_shop}}.id={$id} ";
        $shopModel = new Wash_shop();
        $shopinfo = $shopModel->select("{{%wash_shop}}.*,{{%fans}}.nickname",$where)
            ->join('LEFT JOIN','{{%fans}}','{{%fans}}.id = {{%wash_shop}}.uid')
            ->one();
        $area=new Car_washarea();
        $province=$area->getProvince();
        $city=$area->getCity($shopinfo['province']);
        $areaxxz=$area->getArea($shopinfo['city']);

        $address=(new Car_washarea())->getData('`name`,`code`,`pid`', 'all', 'pid in ("'.$shopinfo['province'].'","'.$shopinfo['city'].'","'.$shopinfo['area'].'")');
        $shopinfo['shop_register_time'] =!empty($shopinfo['shop_register_time'])?date("Y-m-d H:i:s",$shopinfo['shop_register_time']):'--'; ;
        $shopinfo['shop_apply_time'] = !empty($shopinfo['shop_apply_time'])?date("Y-m-d H:i:s",$shopinfo['shop_apply_time']):'--';
        $shopinfo['adopt_time'] = !empty($shopinfo['adopt_time'])?date("Y-m-d H:i:s",$shopinfo['adopt_time']):'--';

        return $this->render('shop_edit',[
            'data'=>$shopinfo ,
            'address'=>$address,
            'province'=>$province,
            'status'=>$status,
            'city'=>$city,
            'area'=>$areaxxz
        ]);

    }

    //审核
    public function actionShop_check() {
        $request = Yii::$app->request;
        if($request->isPost){
            $data = $request->post();
            $id = intval($data['id']);
            $where = " `id` ='".$id."' and `shop_status`=1  ";
            $res = (new Car_shop())->upData(array('shop_status'=>2,'adopt_time' => time()),$where);
            if(!$res) $this->json(0,'操作失败');
            return $this->json(1,'操作成功');
        }else{
            return $this->json(0,'非法请求');
        }
    }

    /**
     * 兑换记录查询条件
     * @return string
     */

    protected function setCardWhere(){

        $request    = Yii::$app->request;
        $cardcode   = trim($request->get('cardcode',null));
        $area       = trim($request->get('area',null));
        $city       = trim($request->get('city',null));
        $province   = trim($request->get('province',null));
        $company    = trim($request->get('company',null));
        $personname = trim($request->get('personname',null));
        $batch_no   = trim($request->get('batch_no',null));
        $status     =      $request->get('status');
        $where      ='id<>0 ';
        if(!empty($cardcode))
        {
            $where.=' AND card_num='.$_REQUEST['cardcode'];
        }

        if(!empty($area))
        {
            $where.=' AND area="'.$area.'"';
        }else if(!empty($city))
        {
            $where.=' AND city="'.$city.'"';
        }else if(!empty($province))
        {
            $where.=' AND province="'.$province.'"';
        }
        if(!empty($status)){
            $where.=' AND card_status ="'.$status.'"';
        }
        if(!empty($company)){
            $where.=' AND  company ="'.$company.'"';
        }

        if(!empty($personname)){
            $where.=' AND  personname ="'.$personname.'"';
        }
        if(!empty($batch_no)){
            $where.=' AND  batch_no ="'.$batch_no.'"';
        }
        return $where;
    }



    public function actionCard_list() {
        $satus=Car_card::$status;
        if (Yii::$app->request->isAjax) {
            $pageSize = intval ( $_GET ['limit'] ) ? intval ( $_GET ['limit'] ) : 10;
            $offset = intval ( $_GET ['pageNumber'] )  > 1? intval ( $_GET ['pageNumber'] ) - 1 : 0;
            $cardModel = new Car_card();
            $where = self::setCardWhere();
            $order = "id desc";
            $limit = $offset * $pageSize.','. $pageSize;
            $cardlist=$cardModel->getData ( '*', 'all', $where, $order, $limit );
            $cot = $cardModel->getData ( 'count(*) cot', 'one', $where,'id desc' );
            $is_bisable=Car_card::$is_bisable;
            foreach ($cardlist as $key=>$val){
                $cardlist[$key]['card_status'] =$satus[$val['card_status']] ;
                $cardlist[$key]['is_bisable'] =$is_bisable[$val['is_bisable']] ;
                $cardlist[$key]['expire_time'] = !empty($val['expire_time'])?date("Y-m-d H:i:s",$val['expire_time']):'--';
            }
            $res = ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$cardlist];
            return json_encode($res);
        }
        $area=new Car_washarea();
        $province=$area->getProvince();
        return      $this->render ( 'card_list' ,array('cur'=>1,'province'=>$province,'status'=>$satus));
    }


    public function actionCard_edit(){

        $cardModel = new Car_card();
        if (Yii::$app->request->post()) {

            $t_sum=$_POST['t_sum'];
            if(!is_numeric($t_sum) || $t_sum>1 ||  $t_sum < 0.01 ){
                header('Content-type: text/html; charset=utf-8');
                exit('提成系数填写错误');
            }
            $cardwhere = "  id=".intval($_POST['id']);
            $info = $cardModel->getData ( 'card_status,t_sum', 'one', $cardwhere );
            if($info['card_status'] != 1 && floatval($t_sum)!=floatval($info['t_sum'])) return '此卡已回收或已申请回收不可修改提成系数';
            $arr = array(
                't_sum' => $t_sum,
            );

            if (!empty($_POST['id'])) {
                $cardModel->upData($arr, 'id=' . intval($_POST['id']));
            }
//            else {
//                $cardModel->addData($arr);
//            }
            $this->redirect('card_list.html');
        }
        $where = " card_status != 0 and id=".intval($_GET['id']);
        $info = $cardModel->getData ( '*', 'one', $where );
        $satus=[0=>'禁用',1=>'可兑换',2=>'已兑换',3=>'已过期',4=>'已申请兑换'];
        $info['card_status']=$satus[$info['card_status']];
        $info['card_time'] =! empty($info['card_time']) ? date("Y-m-d H:i:s",$info['card_time']) : '--';
        return      $this->render ( 'card_edit' ,['data'=>$info]);

    }

    //批量生成洗车卡
    public function actionGenerate(){
        $request = Yii::$app->request;
        if($request->isPost){
            $data = $request->post();
            $num = intval($data['generate_num']);
            unset($data['generate_num']);
            $model = new Car_card();
            //验证
            if(!$num || !is_numeric($num)) return '卡套餐唯一编号输入不合法，请确认输入的是一个正整数';
            //验证卡的有效性
            $data['c_time']  = time();
            if(!is_numeric($data['t_sum'])) return '提成系数填写错误';
            if(!is_numeric($data['card_amount'])) return '卡的面值不是一个数字';
            if($data['t_sum']>1 || $data['t_sum']<0.0001)return '请填写合理的提成系数';
            $data['expire_time']=strtotime($data['expire_time']);
            if($data['expire_time']<$data['c_time'])return '有效时间不能小于当前时间';
            $batch_no=W::createNonceCapitalStr();
            $check_no=$model->select('id',['batch_no'=>$batch_no])->one();
            if($check_no) return '批号重复，校对数据后重新提交';
            $fData = null;

            for($i = 0; $i < $num; $i++){
                $tmp = $data;
                $tmp['card_num'] = $model->generateCardNo();
                $tmp['card_password'] = mt_rand(100000,999999);
                $tmp['type']=2;
                $tmp['batch_no']=$batch_no;
                $fData [] = $tmp;
            }

            $fields = ['card_amount','province','city','area','card_region','t_sum','expire_time','company','personname','personcode','c_time','card_num','card_password','type','batch_no'];
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                $db->createCommand()->batchInsert('{{%car_card}}',$fields,$fData)->execute();
                $transaction->commit();
            }catch (\Exception $e){
                $transaction->rollBack();
                return $e->getMessage();
            }
            return $this->redirect(['car/card_list']);
        }else{
            $area=new Car_washarea();
            $province=$area->getProvince();
            return $this->render('generate',['province'=>$province]);
        }
    }

    //批量操作兑换卡状态
    public function actionDisable_card(){

        $cardModel = new Car_card();
        $is_bisable=Car_card::$is_bisable;
        if (Yii::$app->request->post()) {

            $poststatus=Yii::$app->request->post('status');
            $batch_no=trim(Yii::$app->request->post('batch_no'));
            if(empty($batch_no)) return '请填批号';
            if(!$is_bisable[$poststatus]) return '状态错误';
            $cardlist=$cardModel->select('id',['batch_no'=>$batch_no])->all();
            if(!$cardlist) return '没有此批号';
            $cardModel->myUpdate(['is_bisable'=>$poststatus], ['batch_no'=>$batch_no]);

            $this->redirect('card_list.html');
        }

        return      $this->render ( 'disable_card' ,['status'=>$is_bisable]);

    }



    /**
     * 提现记录查询条件
     * @return string
     */

    protected function setWithdrawalsWhere(){
        $request = Yii::$app->request;
        $payee = trim($request->get('payee',null));
        $account = trim($request->get('account',null));
        $shop_name = trim($request->get('shop_name',null));
        $withdrawals_no = trim($request->get('withdrawals_no',null));
        $status = $request->get('status');

        $where=' {{%wash_withdrawals}}.id<>0 ';
        if(!empty($payee)){
            $where.=' AND  {{%wash_withdrawals}}.payee ="'.$payee.'"';
        }

        if(!empty($account)){
            $where.=' AND  {{%wash_withdrawals}}.account ="'.$account.'"';
        }

        if(!empty($shop_name)){
            $where.=' AND  {{%wash_shop}}.shop_name ="'.$shop_name.'"';
        }

        if(!empty($status)){
            $where.=' AND  {{%wash_withdrawals}}.status ="'.$status.'"';
        }

        if(!empty($withdrawals_no)){
            $where.=' AND  {{%wash_withdrawals}}.withdrawals_no ="'.$withdrawals_no.'"';
        }
        return $where;
    }
    public function actionWithdrawals_log() {
        $request = Yii::$app->request;
        $wstatus=Wash_withdrawals::$status;
        $ac_type=Wash_withdrawals::$ac_type;
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber",1);
            $pagesize = $request->get("pageSize",10);
            $model = new Wash_withdrawals();
            $where = self::setWithdrawalsWhere();
            $cot = $model->select("count({{%wash_withdrawals}}.id) as cot",$where)
                ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_withdrawals}}.shop_id = {{%wash_shop}}.id')
                ->one();

            $list = $model->select('{{%wash_withdrawals}}.*,{{%wash_shop}}.shop_name',$where)
                ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_withdrawals}}.shop_id = {{%wash_shop}}.id')
                ->page($page,$pagesize)
                ->orderBy('{{%wash_withdrawals}}.id desc')
                ->all();

            foreach ($list as $key=>$val){
                $list[$key]['status'] =$wstatus[$val['status']] ;
                $list[$key]['apply_time'] =!empty($val['apply_time'])?date("Y-m-d H:i:s",$val['apply_time']):'--';
                $list[$key]['playmoney_time'] =!empty($val['playmoney_time'])?date("Y-m-d H:i:s",$val['playmoney_time']):'--';
                $list[$key]['ac_type'] =$ac_type[$val['ac_type']] ;

            }
            $res = ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$list];
            return json_encode($res);
        }

        return      $this->render ( 'withdrawals_log',['status'=>$wstatus] );
    }

    public function actionDownload_w() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $payee = trim($request->get('payee',null));
        $account = trim($request->get('account',null));
        $shop_name = trim($request->get('shop_name',null));
        $withdrawals_no = trim($request->get('withdrawals_no',null));
        $status = $request->get('status');
        $where = self::setWithdrawalsWhere();
        $model = new Wash_withdrawals();
        $cot = $model->select("count({{%wash_withdrawals}}.id) as cot",$where)
            ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_withdrawals}}.shop_id = {{%wash_shop}}.id')
            ->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '提现申请列表-';
        $pagesize = $this->maxpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'selfshopcar/w_down',
                    'page'=>1,
                    'payee'=> $payee,
                    'account' => $account,
                    'shop_name' => $shop_name,
                    'status' => $status,
                    'withdrawals_no'=>$withdrawals_no
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'selfshopcar/w_down',
                        'page'=>$i,
                        'payee'=> $payee,
                        'account' => $account,
                        'shop_name' => $shop_name,
                        'status' => $status,
                        'withdrawals_no'=>$withdrawals_no
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    public function actionW_down(){
        $request = Yii::$app->request;
        $where = self::setWithdrawalsWhere();
        $pagesize = $this->maxpagesize;
        $page = $request->get('page',1);

        $list = (new Wash_withdrawals())->select('{{%wash_withdrawals}}.*,{{%wash_shop}}.shop_name',$where)
            ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_withdrawals}}.shop_id = {{%wash_shop}}.id')
            ->page($page,$pagesize)
            ->orderBy('{{%wash_withdrawals}}.id desc')
            ->all();

        $uid=ArrayHelper::getColumn($list,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=array_column($nickname,'nickname','id');
        $field = ['id','payee','withdrawals_no','amount','account','account_bank','apply_time','playmoney_time','status','shop_name','nickname'];
        $str   = ['序号','提款人姓名','提现单号','提现金额','提现账号','提现开户行','申请时间','确认打款时间','状态','提现商户','提现人微信昵称'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        $satus=Wash_withdrawals::$status;
        foreach ($list as $e){
            $tmp['id'] = $index;
            $tmp['payee'] = $e['payee'];
            $tmp['withdrawals_no'] = $e['withdrawals_no'];
            $tmp['amount'] = $e['amount'];
            $tmp['account'] = $e['account'] ;
            $tmp['account_bank'] = $e['account_bank'];
            $tmp['apply_time']=! empty($e['apply_time']) ? date("Y-m-d H:i:s",$e['apply_time']) : '--';
            $tmp['playmoney_time'] = ! empty($e['playmoney_time']) ? date("Y-m-d H:i:s",$e['playmoney_time']) : '--';
            $tmp['status']=$satus[$e['status']];
            $tmp['shop_name']=$e['shop_name'];
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $data[] = $tmp;
            $index++;
        }

        unset($orderlist);

        $headArr = array_combine($field,$str);
        $fileName = '提现申请列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
    }




    public function actionWithdrawals_edit() {
        $request = Yii::$app->request;
        $model = new wash_withdrawals();
        $id = intval($request->get("id",0));

        $status=Wash_withdrawals::$status;
        $where = " {{%wash_withdrawals}}.status != 0  and {{%wash_withdrawals}}.id = $id";
        $info = $model->select("{{%wash_withdrawals}}.* , {{%wash_shop}}.shop_name, {{%fans}}.nickname",$where)
            ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_withdrawals}}.shop_id = {{%wash_shop}}.id')
            ->join('LEFT JOIN','{{%fans}}','{{%wash_withdrawals}}.uid = {{%fans}}.id')
            ->one();

        $info['apply_time'] = !empty($info['apply_time'])?date("Y-m-d H:i:s",$info['apply_time']):'--';
        $info['playmoney_time'] = !empty($info['playmoney_time'])?date("Y-m-d H:i:s",$info['playmoney_time']):'--';
        $info['status']=$status[$info['status']];

        return      $this->render ( 'withdrawals_edit' ,['info'=>$info]);
    }
    //确认打款操作
    public function actionPlay_money(){
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $id = intval($request->post("id"));
            $model = new wash_withdrawals();
            $shopModel=new wash_shop();
            $data['status']=2;
            $data['playmoney_time']=time();
            $info=$model->getData('*', 'one','id='.$id);
            $shopinfo=$shopModel->getData('amount', 'one','id='.$info['shop_id']);
            if($info['amount'] > $shopinfo['amount']){
                return $this->json(0,'提现金额超出可提现金额数');
            }
            $log['satus']=0;
            $log['operator']= Yii::$app->session ['UNAME'];
            $log['content']='确认打款，打款金额为'.$info['amount'].'，操作单号为'.$info['withdrawals_no'];

            //支付宝转账
            $account = (new Wash_payee())->select('*',['id'=>$info['payee_id'],'type'=>'1'])->one();
            if($account){
                $data_1['id']=$info['payee_id'];
                $data_1['amount']=$info['amount'];
                $data_1['payer_show_name']='云车驾到';
                $res=$this->carTransfer($data_1,$account);
                if($res['status'] != 1 ){
                    return $this->json(0,$res['msg']);
                }
            }

            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try{
                $model->upData($data,"id=$id");
                $res=$shopModel->upData(array('amount'=>'amount-'.$info['amount'],'already_amount'=>'already_amount+'.$info['amount']),'`id` ='.$info['shop_id']);
                $trans->commit();
                if($res)$this->write_log($log);
            }catch (\Exception $e){
                $trans->rollBack();
                $log['content']=$e->getMessage();
                $this->write_log($log);
                return $this->json(0,'系统错误');
            }
            return $this->json(1,'操作成功');
        }
        return $this->json(0,'非法请求');
    }


    private function carTransfer($data,$account)
    {
        set_time_limit(0);
        //判断id字段是否存在
        if (!isset($data['id'])) return ['status'=>0, 'msg'=>'不存在转账用户'];
        //判断是数字是否合法
        if (!is_numeric($data['amount'])) return ['status'=>0, 'msg'=>'转账金额输入不正确'];
        if ($data['amount'] < 0.1) return ['status'=>0, 'msg'=>'转账金额至少0.1元'];

        $id = $data['id'];

        if (!$account) return ['status'=>0, 'msg'=>'该用户没有设置转账账号'];
        if($account['status'] != 1) return ['status'=>0, 'msg'=>'账户已禁用，不能转账'];

        //构建接口调用所需要的参数
        $out_biz_no = CarFinanceTransferlog::create_out_biz_no();
        $payee_account = $account['payee_account'];
        $amount = $data['amount'];
        $payer_show_name = $data['payer_show_name'] ? $data['payer_show_name'] : '云车驾到';
        $remark = '转账商户ID为'.$account['shop_id'];
        //先将转账信息存入数据库
        $log = new CarFinanceTransferlog();
        $log->out_biz_no = $out_biz_no;
        $log->account_type = '1';
        $log->account_id = $id;
        $log->payee_account = $payee_account;
        $log->amount = $amount;
        $log->payer_show_name = $payer_show_name;
        $log->payee_real_name = $account['payee_name'];
        $log->remark = $remark;
        $log->c_time = time();
        $r = $log->save();
        if (!$r) return ['status'=>0, 'msg'=>'记录插入失败'];

        //调用接口
        $res = AliPay::getInstance()->transfer_account($out_biz_no, $payee_account, (string)$amount, $payer_show_name, $remark);
        $flag = 1;
        $msg = '操作成功';
        if ($res) {
            $log->status = 1;
            $log->err_code = $res['code'];
            $log->err_msg = $res['msg'];
            $log->order_id = $res['order_id'];
            $log->pay_date = $res['pay_date'];
        } else {
            $log->status = 2;
            $err = AliPay::getInstance()->getError();
            list($c, $m) = explode(':', $err);
            $log->err_code = $c;
            $log->err_msg = $m;
            $flag = 0;
            $msg = $m;
        }
        $log->pay_day = date("Ymd");
        $log->pay_month = date('Ym');
        $r = $log->save();
        if (!$r) return ['status'=>0, 'msg'=>'记录保存失败,请记录相关信息并与技术联系！'];
        return ['status'=>$flag, 'msg'=>$msg];
    }



    /**
     * 兑换记录查询条件
     * @return string
     */

    protected function setExchangeWhere(){
        $request = Yii::$app->request;
        $company = trim($request->get('company',null));
        $nickname = trim($request->get('nickname',null));
        $personname = trim($request->get('personname',null));
        $batch_no = trim($request->get('batch_no',null));
        $shop_name = trim($request->get('shop_name',null));
        $status = $request->get('status');
        $where='id<>0 ';

        if(!empty($shop_name)){
            $where.=' AND  exchange_name ="'.$shop_name.'"';
        }

        if(!empty($status)){
            $where.=' AND status ="'.$status.'"';
        }

        if(!empty($company) ||!empty($personname) ||!empty($batch_no)){
            $c_where='id<>0 ';
            if(!empty($company)){
                $c_where.=' AND company ="'.$company.'"';
            }
            if(!empty($personname)){
                $c_where.=' AND personname ="'.$personname.'"';
            }
            if(!empty($batch_no)){
                $c_where.=' AND batch_no ="'.$batch_no.'"';
            }
            $cardlist=(new Car_card)->select('id',$c_where)->all();
            $cardacf='(-1)';
            if($cardlist){
                $cardac=ArrayHelper::getColumn($cardlist,'id');
                $cardacf="('".implode("','",$cardac)."')";
            }
            if($where){
                $where .=" AND card_id in ".$cardacf;
            }
        }
        if(! empty($nickname)){
            $fans=(new Fans())->select('id',['nickname'=>$nickname])->all();
            $fansacf='(-1)';
            if($fans){
                $fansac=ArrayHelper::getColumn($fans,'id');
                $fansacf="('".implode("','",$fansac)."')";
            }
            if($where){
                $where .=" AND uid in ".$fansacf;
            }
        }


        return $where;
    }
//兑换记录
    public function actionExchange_log() {
        $request = Yii::$app->request;

        $satus=Exchange_record::$status;
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber",1);
            $pagesize = $request->get("pageSize",10);
            $model = new Exchange_record();
            $where = self::setExchangeWhere();
            //1待审核，2通过审核

            $cot = $model->select("count(id) as cot",$where)->one();
            $list = $model->select(
                'id,exchange_card_num,exchange_name,card_id,exchange_card_amount,carno,exchange_tel,exchange_time,status,uid',$where
            )
                ->page($page,$pagesize)
                ->orderBy('id desc')
                ->all();
            $uid=ArrayHelper::getColumn($list,'uid');
            $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
            $nicknamearr=[];
            if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

            $card_id=ArrayHelper::getColumn($list,'card_id');
            $cardlist=(new Car_card())->select('id,company,personname,batch_no',['id'=>$card_id])->all();
            $cardinfo=[];

            if($cardlist)foreach ($cardlist as $key=>$val){
                $cardinfo[$val['id']]['company']    = $val['company'];
                $cardinfo[$val['id']]['personname'] = $val['personname'];
                $cardinfo[$val['id']]['batch_no']   = $val['batch_no'];
            }

            foreach ($list as $k=>$item) {
                $list[$k]['exchange_time']=!empty($item['exchange_time'])?date("Y-m-d H:i:s",$item['exchange_time']):'--';
                $list[$k]['auditing_time']=!empty($item['auditing_time'])?date("Y-m-d H:i:s",$item['auditing_time']):'--';
                $list[$k]['status']=$satus[$item['status']];
                $list[$k]['nickname']=$nicknamearr[$item['uid']];
                $list[$k]['company']=$cardinfo[$item['card_id']]['company'];
                $list[$k]['personname']=$cardinfo[$item['card_id']]['personname'];
                $list[$k]['batch_no']=$cardinfo[$item['card_id']]['batch_no'];
            }

            $res = ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$list,'where'=>$where];
            return json_encode($res);
        }

        return      $this->render ( 'exchange_log' ,['status'=>$satus]);
    }

    public function actionDownload_e() {
        $request = Yii::$app->request;
        if(!$request->isAjax) return '非法访问';
        $company = trim($request->get('company',null));
        $nickname = trim($request->get('nickname',null));
        $personname = trim($request->get('personname',null));
        $batch_no = trim($request->get('batch_no',null));
        $shop_name = trim($request->get('shop_name',null));
        $status = $request->get('status');
        $where = self::setExchangeWhere();
        $model = new Exchange_record();
        $cot = $model->select("count(id) as cot",$where)->one();
        $total = $cot['cot'];
        if(!$total) return $this->json(0,'没有数据');
        $title = '兑换记录列表-';
        $pagesize = $this->maxpagesize;
        $data = [];
        if($total < $pagesize){
            $title .= '1';
            $data[] = [
                'url' => Url::to([
                    'car/e_down',
                    'page'=>1,
                    'company'=>$company,
                    'nickname'=> $nickname,
                    'personname' => $personname,
                    'batch_no' => $batch_no,
                    'shop_name' => $shop_name,
                    'status' => $status
                ]),

                'name' => $title,
            ];

        }else{
            $total_page = intval(ceil($total / $pagesize));
            for($i = 1; $i <= $total_page; $i++){
                $data[] = [
                    'url' => Url::to([
                        'car/e_down',
                        'page'=>$i,
                        'company'=>$company,
                        'nickname'=> $nickname,
                        'personname' => $personname,
                        'batch_no' => $batch_no,
                        'shop_name' => $shop_name,
                        'status' => $status
                    ]),
                    'name' => $title . $i,
                ];
            }
        }

        return $this->json(1,'ok',$data);
    }

    public function actionE_down(){
        $request = Yii::$app->request;
        $where = self::setExchangeWhere();
        $pagesize = $this->maxpagesize;
        $page = $request->get('page',1);

        $list = (new Exchange_record())->select(
            'id,exchange_card_num,exchange_name,card_id,exchange_card_amount,carno,exchange_tel,exchange_time,status,uid',$where
        )
            ->page($page,$pagesize)
            ->orderBy('id desc')
            ->all();
        $uid=ArrayHelper::getColumn($list,'uid');
        $nickname=(new Fans())->select('id,nickname',['id'=>$uid])->all();
        $nicknamearr=[];
        if($nickname)foreach ($nickname as $key=>$val){$nicknamearr[$val['id']]=$val['nickname'];}

        $card_id=ArrayHelper::getColumn($list,'card_id');
        $cardlist=(new Car_card())->select('id,company,personname,batch_no',['id'=>$card_id])->all();
        $cardinfo=[];

        if($cardlist)foreach ($cardlist as $key=>$val){
            $cardinfo[$val['id']]['company']    = $val['company'];
            $cardinfo[$val['id']]['personname'] = $val['personname'];
            $cardinfo[$val['id']]['batch_no']   = $val['batch_no'];
        }
        $field = ['id','exchange_card_num','exchange_name','exchange_card_amount','carno','exchange_tel','exchange_time','status','nickname','company','personname','batch_no'];
        $str   = ['序号','卡号','回收商户名称','卡面值','客户车牌号','被回收者手机号','回收时间','状态','提现人微信昵称','发卡公司','业务员名称','卡批号'];
        $data  = null;
        $tmp   = null;
        $index = 1;
        $satus=Exchange_record::$status;
        foreach ($list as $e){
            $tmp['id'] = $index;
            $tmp['exchange_card_num'] = $e['exchange_card_num'];
            $tmp['exchange_name'] = $e['exchange_name'];
            $tmp['exchange_card_amount'] = $e['exchange_card_amount'];
            $tmp['carno'] = $e['carno'] ;
            $tmp['exchange_tel'] = $e['exchange_tel'];
            $tmp['exchange_time']=! empty($e['exchange_time']) ? date("Y-m-d H:i:s",$e['exchange_time']) : '--';
            $tmp['status']=$satus[$e['status']];
            $tmp['nickname']=$nicknamearr[$e['uid']];
            $tmp['company']=$cardinfo[$e['card_id']]['company'];
            $tmp['personname']=$cardinfo[$e['card_id']]['personname'];
            $tmp['batch_no']=$cardinfo[$e['card_id']]['batch_no'];
            $data[] = $tmp;
            $index++;
        }

        unset($orderlist);

        $headArr = array_combine($field,$str);
        $fileName = '兑换记录列表-'.$page;
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
    }





    public function  actionExchange_edit(){
        $recordModel = new Exchange_record();

        if (Yii::$app->request->isPost) {
            $postid = intval(Yii::$app->request->post("id"));
            $poststatus=Yii::$app->request->post("status");
            $shopModel=new Car_shop();
            $recordinfo=$recordModel->select('*',['id'=>$postid])->one();
            if($recordinfo['status'] == '2' ) exit('已通过审核不可再做此操作');
            if($recordinfo['status'] == '3' ) exit('已拒绝不可再做此操作');
            if($poststatus == '1' ) return $this->render ( 'exchange_log' );
            $time=time();
            $db = Yii::$app->db;
            $exinfo=[];
            $cardinfo=[];
            $trans = $db->beginTransaction();
            $log['satus']=0;
            $log['operator']= Yii::$app->session ['UNAME'];
            $log['content']='';
            if($poststatus == '2'){
                $exinfo['status']='2';
                $exinfo['exchange_time']=$time;
                $cardinfo['card_status']='2';
                $cardinfo['card_time']=$time;
                $content='审核通过,卡号为'.$recordinfo['exchange_card_num'];
            }elseif ($poststatus == '3'){
                $exinfo['status']='3';
                $cardinfo['card_status']='1';
                $content='拒绝通过,卡号为'.$recordinfo['exchange_card_num'];
            }
            $log['content']=$content;
            try{
                $recordModel->myUpdate($exinfo,['id'=>$postid]);
                (new Car_card())->myUpdate($cardinfo,['id'=>$recordinfo['card_id']]);
                if($poststatus == '2'){
                    $shopModel->upData(array('amount'=>'amount+'.$recordinfo['exchange_card_amount']*(1+$recordinfo['t_amount']),'gross_income'=>'gross_income+'.$recordinfo['exchange_card_amount']*$recordinfo['t_amount']),'`id` ='.intval($recordinfo['shop_id']));
                }
                $this->write_log($log);
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                $log['content']=$e->getMessage();
                $this->write_log($log);
                return '系统错误';
            }

            return      $this->render ( 'exchange_log' );
        }
        $id = intval(Yii::$app->request->get('id'));
        $info= $recordModel->select('{{%exchange_record}}.*,{{%fans}}.nickname',['{{%exchange_record}}.id'=>$id])
            ->join('LEFT JOIN','{{%fans}}','{{%exchange_record}}.uid = {{%fans}}.id')
            ->one();
        $info['exchange_time']=!empty($info['exchange_time'])?date("Y-m-d H:i:s",$info['exchange_time']):'--';
        $info['auditing_time']=!empty($info['auditing_time'])?date("Y-m-d H:i:s",$info['auditing_time']):'--';
        $logstatus=Exchange_record::$status;
        return $this->render ('exchange_edit',['data'=>$info,'logstatus'=>$logstatus]);
    }
    //excel导入页面
    public function  actionLeadin(){
        return $this->render ('leadin');
    }

    //读取excel文件并写入数据库
    public function actionExcutexls(){
        $msg['status'] = 1;
        $msg['error'] = 'ok';


        if(Yii::$app->request->isPost){
            $rootPath = Yii::$app->params['rootPath'];
            $path = Yii::$app->request->post('path');
            $path = $rootPath.$path;
            if(!$path || !file_exists($path)){
                $msg['status'] = 0;
                $msg['error'] = '文件不存在';
            }
            $data = CExcel::getExcel()->importExcel($path);
            $fileds = $data[0];
            array_push($fileds,'card_time');
            $fileds=array_filter($fileds);
            unset($data[0]);;
            $card_time = time();

            foreach ($data as $key=>$val){
                array_push($val,$card_time);
                $val=array_filter($val);
                $data[$key] = $val;
            }
            //执行插入到数据库的操作


            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                $db->createCommand()->batchInsert('{{%car_card}}',$fileds,$data)->execute();
                $transaction->commit();
            }catch (\Exception $e){
                $transaction->rollBack();
                $msg['status'] = 0;
                $msg['error'] = $e->getMessage();
                //throw $e;
            }

            echo json_encode($msg);
            exit;
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode($msg);
            exit;
        }
        echo '非法访问';
        exit;
    }
    //账号列表
    public  function actionAccount_list(){
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber",1);
            $pagesize = $request->get("pageSize",10);
            $status=$_REQUEST["status"];
            $keywords=$_REQUEST["keywords"];
            $model = new Wash_payee();
            $where = " {{%wash_payee}}.id != 0";
            $payee_status=Wash_payee::$status;
            if(!empty($status) && !empty($keywords)){
                if($status == 1){
                    $where.=" and {{%wash_shop}}.shop_name  like '%".$keywords."%'";
                }elseif ($status == 2){
                    $where.=" and {{%wash_payee}}.payee_name  like '%".$keywords."%'";
                }
                elseif($status == 3){
                    $where.=" and {{%fans}}.nickname  like '%".$keywords."%'";
                }elseif ($status == 4){
                    $where.=" and {{%wash_payee}}.payee_account = '".$keywords."'";
                }
            }
            $cot = $model->select("count({{%wash_payee}}.id) as cot",$where)
                ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_payee}}.shop_id = {{%wash_shop}}.id')
                ->join('LEFT JOIN','{{%fans}}','{{%wash_payee}}.uid = {{%fans}}.id')
                ->one();


            $list = $model->select('{{%wash_payee}}.*,{{%wash_shop}}.shop_name,{{%fans}}.nickname',$where)
                ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_payee}}.shop_id = {{%wash_shop}}.id')
                ->join('LEFT JOIN','{{%fans}}','{{%wash_payee}}.uid = {{%fans}}.id')
                ->page($page,$pagesize)
                ->orderBy('{{%wash_payee}}.id desc')
                ->all();

            foreach ($list as $k=>$item) {
                $list[$k]['c_time']   = date("Y-m-d H:i:s",$item['c_time']);
                $list[$k]['u_time']   = !empty($item['u_time']) ? date("Y-m-d H:i:s",$item['u_time']) : '--';
                $list[$k]['nickname'] = $item['up_type']==2 ? '管理员' : $item['nickname'];
                $list[$k]['status']   = $payee_status[$item['status']];

            }

            $res = ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$list];
            return json_encode($res);
        }
        return      $this->render ( 'account_list' );
    }

    public function actionAccount_edit(){

        $model = new Wash_payee();
        $payee_status=Wash_payee::$status;
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $data = $request->post();
            $id=$data['payee_id'];
            unset($data['payee_id']);
            $data['u_time']=time();
            $data['up_type']=2;
            $where['id']=intval($id);
            if(! $model->myUpdate($data ,$where) )return $this->json(0);
            return $this->json(1);
        }

        $where = "{{%wash_payee}}.id=".intval($request->get('id'));
        $info = $model->select("{{%wash_payee}}.*,{{%wash_shop}}.shop_name,{{%fans}}.nickname",$where)
            ->join('LEFT JOIN','{{%wash_shop}}','{{%wash_payee}}.shop_id = {{%wash_shop}}.id')
            ->join('LEFT JOIN','{{%fans}}','{{%wash_payee}}.uid = {{%fans}}.id')
            ->one();
        $info['c_time']=date("Y-m-d H:i:s",$info['c_time']);
        return      $this->render ( 'account_edit',[
            'data'=>$info,
            'status'=>$payee_status,
        ] );
    }

}