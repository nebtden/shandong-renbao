<?php

namespace frontend\controllers;


use common\models\Car_card;
use common\models\Car_payee;
use common\models\Car_withdrawals;
use common\models\Exchange_record;
use common\models\Fans;
use Yii;
use common\models\Car_shop;
use common\models\Area;
use common\components\W;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class CarController extends FcarcardController {
	

    //申请认证
    public function actionAuthentication() {

        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        $shopModel=new Car_shop();
        $uid=$user['uid'];
        if(!empty($user['pid'])) $uid=$user['pid'];
        $shopinfo = $shopModel->getData('*','one'," `uid`=".$uid);
        if (Yii::$app->request->isAjax) {
            $data = $request->post();
            if(! empty($user['pid']))  return $this->json(0,'您暂无权限申请');

            $data['shop_register_time']=strtotime($data['shop_register_time']);
            if($data['shop_register_time'] > time()){
                return $this->json(0,'请选择正确的注册时间');
            }
            $data['shop_status']=1;
            if(!$shopinfo){
                $data['uid']=$user['uid'];
                $data['shop_apply_time']=time();
                $status=$shopModel->myInsert($data);
            }else{
                $data['u_time']=time();
                $status=$shopModel->myUpdate($data,['uid'=>$user['uid']]);
            }
            if(! $status){
                return $this->json(0,'操作失败');
            }
            return $this->json(1,'操作成功');

        }
        $area=new Area();
        $province=$area->getProvince();


        if($shopinfo){
            $city = $area->getCity($shopinfo['province']);
            $area = $area->getArea($shopinfo['city']);
        }else{
            $city = $area->getCity('001');
            $area = $area->getArea('001001');
        }

        return $this->render('authentication',array('shopinfo'=>$shopinfo,'userinfo'=>$user,'province'=>$province,'city'=>$city,'area'=>$area,));
    }

    //获取省级单位
    public function actionGetprovince() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $area = new Area();
            $province=$area->getProvince();
            return $this->json(1,'操作成功',$province);
        }
        return $this->json(0,'操作失败');
    }


    //获取市级单位
    public function actionGetcity() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $code = $request->post("code",null);
            $area = new Area();
            $city = $area->getCity($code);
            return $this->json(1,'操作成功',$city);
        }
        return $this->json(0,'操作失败');
    }
    //获取区级单位
    public function actionGetarea() {

        $request = Yii::$app->request;
        if($request->isPost) {
            $code = $request->post("code",null);
            $areaModel = new Area();
            $area = $areaModel->getArea($code);
            return $this->json(1,'操作成功',$area);
        }
        return $this->json(0,'操作失败');
    }

//商户基础信息
    public function actionDetails() {

        $user=Yii::$app->session['wx_user_auth'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        $shopModel=new Car_shop();
        $shopinfo = $shopModel->getData('*','one'," `uid`=".$user['uid']);
        $shopinfo['shop_register_time']=date('Y-m-d',$shopinfo['shop_register_time']);
        if(!$shopinfo){
            header('Content-type: text/html; charset=utf-8');
            echo '<script language="JavaScript">;alert("您还没申请成为商户或申请未通过");location.href="/frontend/web/car/authentication.html";</script>;';
            exit;
        }
        return $this->render('details',['shopinfo'=>$shopinfo]);
    }

    public function actionPayeeinfo() {
        $user=Yii::$app->session['wx_user_auth'];
        if (Yii::$app->request->isAjax) {
            $payeeModel=new Car_payee();
            $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
            $payeeinfo = $payeeModel->getData('*','one'," `status`=1 and `uid`=".$user['uid']);
            if(!$payeeinfo)$payeeinfo =[];
            return $this->json(1,'成功',$payeeinfo);
        }
    }

    public function actionApply_withdrawal() {

        $user=Yii::$app->session['wx_user_auth'];
        $payeeModel=new Car_payee();
        $old_uid=$user['uid'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        $payeeinfo = $payeeModel->getData('*','one'," `status`=1 and `uid`=".$user['uid']);
        $shopModel=new Car_shop();
        $shopinfo = $shopModel->getData('*','one'," `shop_status`!=0 and `uid`=".$user['uid']);
        if(empty($shopinfo)) exit('非法访问');
        if(empty($shopinfo['w_password'])) exit('<script language="JavaScript">;alert("您还没设置提现密码您可以去商户中心-商户认证去设置");location.href="/frontend/web/car/details.html";</script>;');;
        $withdrawalsModel=new Car_withdrawals();
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {

            $amount= $request->post('amount',0);
            $password= $request->post('password');

            $data=[];
            if(! $payeeinfo){
                return $this->json(0,'请先设置收款人信息');
            }

            if(! $shopinfo){
                return $this->json(0,'商户不存在');
            }

            if($shopinfo['shop_status'] != 2){
                return $this->json(0,'商户还未通过申请');
            }
            if(empty($shopinfo['w_password'])){
                return $this->json(0,'您还没设置提现密码您可以去商户中心-商户认证去设置');
            }
            if(empty($amount)){
                return $this->json(0,'提现金额不能为空或零');
            }

            if(!is_numeric($amount)){
                return $this->json(0,'提现金额错误');
            }

            if($amount > $shopinfo['amount']){
                return $this->json(0,'您输的提现金额大于可提现金额');
            }

            if(empty($password)){
                return $this->json(0,'密码不能为空');
            }

            $newpassword=md5(md5($password).'dhxxz');

            if($newpassword != $shopinfo['w_password']){
                return $this->json(0,'提现密码错误');
            }
            $data['uid']=$old_uid;
            $data['payee']=$payeeinfo['payee_name'];
            $data['withdrawals_no']=time().mt_rand(100000,999999).$old_uid;
            $data['amount']=$amount;
            $data['account']=$payeeinfo['payee_account'];
            $data['account_bank']=$payeeinfo['payee_bank'];
            $data['apply_time']=time();
            $data['payee_id']=$payeeinfo['id'];
            $data['shop_id']=$shopinfo['id'];

            $status=$withdrawalsModel->myInsert($data);

            if(! $status){
                return $this->json(0,'系统错误');
            }

            return $this->json(1,'申请提现成功');
        }

        $withdrawalslog = $withdrawalsModel->getData('*','all'," `status`!=0 and `uid`=".$old_uid,'id desc'," 0,15 ");

        return $this->render('apply_withdrawal',['shopinfo'=>$shopinfo,'payeeinfo'=>$payeeinfo,'log'=>$withdrawalslog]);
    }

 //下拉分页
    public function actionAjaxpage(){
        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        if (Yii::$app->request->isAjax) {
            $pagenum= intval($request->post('pagenum',0));//
            $type=intval($request->post('type',0));
            $starts=($pagenum-1)*15;
            if($type==1){
                $where=" `status`!=0 and `uid`=".$user['uid'];

            }else if($type==2){
                $where=" `status`=2 and `uid`=".$user['uid'];
            }else{
                return $this->json(0,'加载失败');
            }

            $withdrawalsModel=new Car_withdrawals();
            $withdrawalslog = $withdrawalsModel->getData('*','all',$where,'id desc'," $starts,15 ");
            foreach($withdrawalslog as $k=>$v){
                $withdrawalslog[$k]['apply_time']=date("Y-m-d H:i:s",$v['apply_time']);
            }
            return $this->json(1,'成功',$withdrawalslog);
        }

    }



//卡券核销第一步
    public function actionRecovery() {
        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        $shopModel=new Car_shop();
        $shop_info = $shopModel->getData('*','one'," `shop_status`=2 and `uid`=".$user['uid']);
        if(!$shop_info){
            header('Content-type: text/html; charset=utf-8');
            echo '<script language="JavaScript">;alert("您还没申请成为商户或申请未通过");location.href="/frontend/web/car/authentication.html";</script>;';
            exit;
        }
        if (Yii::$app->request->isAjax) {
            $shopinfo = $shopModel->getData('*','one'," `shop_status`<>0 and `uid`=".$user['uid']);
            if(false===$shopinfo) return $this->json(101,'您还没申请成为商户现在可去申请成为商户');
            if($shopinfo['shop_status']!=2) return $this->json(0,'您的商户还没通过申请');

            $carcode = $request->post('code');
            $password = $request->post('password');
            $codeModel=new Car_card();
            $codeinfo = $codeModel->getData('*','one',"  `card_num`='".$carcode."' and card_password='".$password."'");

            if(!$codeinfo) return $this->json(0,'不存在此卡！');
            if($codeinfo['is_bisable']==1) return $this->json(0,'此卡已禁用！');
            if($codeinfo['card_status']==2) return $this->json(0,'此卡已回收！');
            if($codeinfo['card_status']==4) return $this->json(0,'此卡已被申请回收！');
            if($codeinfo['card_status']!=1) return $this->json(0,'此卡不可被回收！');
            if($codeinfo['province']!=$shopinfo['province'] || $codeinfo['city']!=$shopinfo['city'] ||$codeinfo['area']!=$shopinfo['area']){
                return $this->json(0,'您店铺的区域与卡的区域不相同');
            }
            unset(Yii::$app->session['recovery_info_1']);
            Yii::$app->session['recovery_info_1']=['card_num'=>$carcode,'card_password'=>$password];

            return $this->json(1,'第一步完成');

        }
        return $this->render('recovery');
    }
    //获取验证码
    public function actionSendcode(){
        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        if($request->isPost){
            $allmobile = (new Car_shop())->getData('mobile','all'," `shop_status`=2 ");
            $allmobile=ArrayHelper::getColumn($allmobile,'mobile');
            $mobile = $request->post('mobile',null);
            if(in_array($mobile,$allmobile))return $this->json(0,'所有商户手机号不能用来验证！');
            if(!$mobile){
                return $this->json(0,'请输入您的手机号码！');
            }
            $f = W::sendSms($mobile);
            if (!$f) return $this->json(0, '验证码发送失败，请重试！');
            return $this->json(1);
        }
        return '非法访问';
    }
//卡券核销第二步
    public function actionRecovery_2() {
        $this->layout = "recovery";
        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        $shopModel=new Car_shop();
        $shop_info = $shopModel->getData('*','one'," `shop_status`=2 and `uid`=".$user['uid']);
        if(!$shop_info){
            header('Content-type: text/html; charset=utf-8');
            echo '<script language="JavaScript">;alert("您还没申请成为商户或申请未通过");location.href="/frontend/web/car/authentication.html";</script>;';
            exit;
        }
        if (Yii::$app->request->isAjax) {
            $shopinfo = $shopModel->getData('*','one'," `shop_status`<>0 and `uid`=".$user['uid']);
            if(false===$shopinfo) return $this->json(101,'您还没申请成为商户现在可去申请成为商户');
            if($shopinfo['shop_status']!=2) return $this->json(0,'您的商户还没通过申请');
            $data = $request->post();
            //检验验证码
            $carcode_info=Yii::$app->session['recovery_info_1'];
            if(!$carcode_info) return $this->json(0,'信息已过期，请重新填写');
            if(!$data['carcode'])return $this->json(0,'车牌号输入有误');
            $cacheCode = Yii::$app->cache->get($data['mobile'].Yii::$app->session['token']);
            if(!$cacheCode) return $this->json(0,'请先获取验证码');
            if($data['code'] == $cacheCode){
                Yii::$app->cache->delete($data['mobile'].Yii::$app->session['token']);
            }else{
                return $this->json(0,'验证码输入错误');
            }


            $codeModel=new Car_card();
            $codeinfo = $codeModel->getData('*','one',"  `card_num`='".$carcode_info['card_num']."' and card_password='".$carcode_info['card_password']."'");

            if(!$codeinfo) return $this->json(0,'不存在此卡！');
            if($codeinfo['is_bisable']==1) return $this->json(0,'此卡已禁用！');
            if($codeinfo['card_status']==2) return $this->json(0,'此卡已回收！');
            if($codeinfo['card_status']==4) return $this->json(0,'此卡已被申请回收！');
            if($codeinfo['card_status']!=1) return $this->json(0,'此卡不可被回收！');
            if($codeinfo['province']!=$shopinfo['province'] || $codeinfo['city']!=$shopinfo['city'] ||$codeinfo['area']!=$shopinfo['area']){
                return $this->json(0,'您店铺的区域与卡的区域不相同');
            }
            unset(Yii::$app->session['recovery_info_2']);
            Yii::$app->session['recovery_info_2']=[
                'card_num'=>$carcode_info['card_num'],
                'card_password'=>$carcode_info['card_password'],
                'carcode'=>$data['carcode'],
                'mobile'=>$data['mobile']
            ];
            unset(Yii::$app->session['recovery_info_1']);
            return $this->json(1,'第二步完成',[],Url::to(['recovery_3']));

        }
        return $this->render('recovery_2');
    }

//卡券核销第三步
    public function actionRecovery_3() {
        $this->layout = "recovery";
        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        $shopModel=new Car_shop();
        $shop_info = $shopModel->getData('*','one'," `shop_status`=2 and `uid`=".$user['uid']);
        if(!$shop_info){
            header('Content-type: text/html; charset=utf-8');
            echo '<script language="JavaScript">;alert("您还没申请成为商户或申请未通过");location.href="/frontend/web/car/authentication.html";</script>;';
            exit;
        }
        if (Yii::$app->request->isAjax) {

            if(false===$shop_info) return $this->json(0,'您还没申请成为商户现在可去申请成为商户');
            $data = $request->post();
            if(empty($data['picurl'])) return $this->json(0,'请上传车牌正面照！');
            if($data['picurl'])$data['picurl'] = W::getWechatImg(Yii::$app->session['token'],$data['picurl']);
            $carcode_info=Yii::$app->session['recovery_info_2'];
            if(!$carcode_info) return $this->json(0,'信息已过期，请重新填写');
            $codeModel=new Car_card();
            $codeinfo = $codeModel->getData('*','one',"  `card_num`='".$carcode_info['card_num']."' and card_password='".$carcode_info['card_password']."'");
            if(!$codeinfo) return $this->json(0,'不存在此卡！');
            if($codeinfo['is_bisable']==1) return $this->json(0,'此卡已禁用！');
            if($codeinfo['card_status']==2) return $this->json(0,'此卡已回收！');
            if($codeinfo['card_status']==4) return $this->json(0,'此卡已被申请回收！');
            if($codeinfo['card_status']!=1) return $this->json(0,'此卡不可被回收！');
            if($codeinfo['province']!=$shop_info['province'] || $codeinfo['city']!=$shop_info['city'] ||$codeinfo['area']!=$shop_info['area']){
                return $this->json(0,'您店铺的区域与卡的区域不相同');
            }
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            $exchangeModel = new Exchange_record();
            $time=time();
            $timestrat=intval(time()-31536000);
            $checkwhere=' exchange_tel="'.$carcode_info['mobile'].'" AND exchange_time >'.$timestrat.' AND exchange_time <'.$time.' AND status = 2';
            $exlog=$exchangeModel->select('id',$checkwhere)->all();
            $applynum=count($exlog);
            $data_w=[];
            $data_w['exchange_card_num']=$carcode_info['card_num'];
            $data_w['exchange_card_amount']=$codeinfo['card_amount'];
            $data_w['card_id']=$codeinfo['id'];
            $data_w['t_amount']=$codeinfo['t_sum'];
            $data_w['exchange_name']=$shop_info['shop_name'];
            $data_w['exchange_tel']=$carcode_info['mobile'];
            $data_w['shop_id']=$shop_info['id'];
            $data_w['uid']=$user['uid'];
            $data_w['carno']=$carcode_info['carcode'];
            $data_w['carpic']=$data['picurl'];
            $data_w['status']='2';
            if($exlog){
                $msg='提交信息完成，待审核';
                $data_w['status']='1';
                $cardstatus=['card_status'=>'4','auditing_time'=>$time];
                $data_w['auditing_time']=$time;
            }else{
                $msg='洗车卡核销完成';
                $data_w['status']='2';
                $cardstatus=['card_status'=>'2','card_time'=>$time];
                $data_w['exchange_time']=$time;
            }
            unset(Yii::$app->session['recovery_info_1']);
            unset(Yii::$app->session['recovery_info_2']);

            try{
                $codeModel->upData($cardstatus,'`id` ='.$codeinfo['id']);
                if($applynum < 6) $shopModel->upData(array('amount'=>'amount+'.$codeinfo['card_amount']*(1+$codeinfo['t_sum']),'gross_income'=>'gross_income+'.$codeinfo['card_amount']*(1+$codeinfo['t_sum'])),'`id` ='.$shop_info['id']);
                $exchangeModel->myInsert($data_w);
                $trans->commit();
            }catch (\Exception $e){

                $trans->rollBack();
                return $this->json(0,'系统错误回收失败');
            }
            if($exlog){
                $successurl=Url::to(['shop_core']);
            }else{
                $successurl=Url::to(['recovery_success','str'=>'dhcarcard']);
            }
            return $this->json(1,$msg,[],$successurl);

        }
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        return $this->render('recovery_3',['alxg_sign'=>$alxg_sign]);
    }


//回购成功
    public function actionRecovery_success() {
        if(Yii::$app->request->get()) {
            if($_GET['str']!='dhcarcard'){
                echo 'error';
                exit;
            }
            return $this->render('recovery_success');
        }else{
            echo 'error'; exit;
        }
    }

    public function actionShop_core() {

        $user=Yii::$app->session['wx_user_auth'];

        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        $shopModel=new Car_shop();
        $shopinfo = $shopModel->getData('*','one'," `shop_status`=2 and `uid`=".intval($user['uid']));
        if(!$shopinfo)  return $this->redirect('authentication.html');
        return $this->render('shop_core',['shopinfo'=>$shopinfo]);
    }
//收支明细
    public function actionIncome_details() {

        $user=Yii::$app->session['wx_user_auth'];
        $olduid=$user['uid'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        $shopModel=new Car_shop();
        $recordModel=new Exchange_record();
        $shopinfo = $shopModel->getData('`gross_income`,`already_amount`,`amount`','one'," `shop_status`=2 and `uid`=".$user['uid']);
        if(!$shopinfo)  return $this->redirect('authentication.html');
        $shopinfo['gross_income']=$shopinfo['gross_income'] ? $shopinfo['gross_income'] : 0;
        $shopinfo['amount']=$shopinfo['amount'] ? $shopinfo['amount'] : 0;
        $log = $recordModel->getData('*','all'," `status`=2 and `uid`=".$olduid,'id desc'," 0,15 ");

        return $this->render('income_details',['shopinfo'=>$shopinfo,'log'=>$log]);
    }

    //回收记录下拉分页
    public function actionAjaxincomepage(){
        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        if (Yii::$app->request->isAjax) {
            $pagenum= intval($request->post('pagenum',0));
            $starts=($pagenum-1)*15;
            $where=" `status`=2 and `uid`=".$user['uid'];
            $recordModel=new Exchange_record();
            $list = $recordModel->getData('*','all',$where,'id desc'," $starts,15 ");
            foreach($list as $k=>$v){
                $list[$k]['exchange_time']=date("Y-m-d H:i:s",$v['exchange_time']);
                $list[$k]['exchange_card_amount']=$list[$k]['exchange_card_amount']*$list[$k]['t_amount'];
            }
            return $this->json(1,'成功',$list);
        }
        exit('erorr');

    }


//回收点列表
    public function actionShop_list() {

        $request = Yii::$app->request;
        $shopModel=new Car_shop();
        $where= 'shop_status=2';
        if (Yii::$app->request->isAjax) {
            $code = $request->post("code",null);
            $where= 'shop_status=2 and area like "'.(string)$code.'%"';
            $shoplist = $shopModel->getData('`id`,`shop_name`,`shop_pic`,`shop_address`,`mobile`','all',$where,'`id` desc ');
            return $this->json(1,'操作成功',$shoplist);
        }

        $shoplist = $shopModel->getData('`id`,`shop_name`,`shop_pic`,`shop_address`,`mobile`','all',$where,'`id` desc ');
        $area=new Area();
        $province=$area->getProvince();
        return $this->render('shop_list',['shoplist'=>$shoplist,'province'=>$province]);
    }

    public function actionOne_password() {
        $user=Yii::$app->session['wx_user_auth'];
        $request = Yii::$app->request;

        if (Yii::$app->request->isAjax) {
            if(!empty($user['pid'])) return $this->json(0,'您没有权限');
            $data = $request->post();
            //检验验证码
            $cacheCode = Yii::$app->cache->get($data['mobile'].Yii::$app->session['token']);
            if(!$cacheCode) return $this->json(0,'请先获取验证码');
            if($data['yzcode'] == $cacheCode){
                Yii::$app->cache->delete($data['mobile'].Yii::$app->session['token']);
            }else{
                return $this->json(0,'验证码输入错误');
            }


            return $this->json(1,'验证通过');

        }
        $shopModel=new Car_shop();
        $payee = $shopModel->getData('`mobile`,`shop_status`','one'," `shop_status`!=0 and `uid`=".$user['uid']);
        if($payee['shop_status'] != 2){
            header('Content-type: text/html; charset=utf-8');
            echo '<script language="JavaScript">;alert("您的商户还未通过申请不可修改密码");location.href="/frontend/web/car/shop_core.html";</script>;';
            exit;
        }
        return $this->render('one_password',['payee'=>$payee]);

    }
//修改提现密码第二步
    public function actionTwo_password() {
        $user=Yii::$app->session['wx_user_auth'];
        $request = Yii::$app->request;

        if (Yii::$app->request->isAjax) {
            $data = $request->post();
            if(!empty($user['pid'])) return $this->json(0,'您没有权限');
            if(!$data['one_password']) return $this->json(0,'请设置提现密码');
            if(!$data['two_password']) return $this->json(0,'请重新输入密码');
            if (! preg_match('/^[_0-9a-z]{6,16}$/i',$data['one_password']) || ! preg_match('/^[_0-9a-z]{6,16}$/i',$data['two_password'])) return $this->json(0,'密码只能是6-16位数字或字母');

            if($data['one_password'] != $data['two_password']){
                return $this->json(0,'两次输入的密码不一致');
            }
            $data_s['w_password']=md5(md5($data['one_password']).'dhxxz');
            $shopModel=new Car_shop();
            $shopinfo = $shopModel->getData('`id`,`w_password`','one'," `shop_status`=2 and `uid`=".$user['uid']);
            if(!$shopinfo) return $this->json(0,'该商户不存在或没通过审核');
            if($data_s['w_password']==$shopinfo['w_password']) return $this->json(1,'修改提现密码成功');
           if(! $shopModel->upData($data_s,"uid=".$user['uid'])) return $this->json(0,'修改提现密码失败');


            return $this->json(1,'修改提现密码成功');

        }
        return $this->render('two_password');
    }

    //修改提现密码第三步
    public function actionThree_password() {

        return $this->render('three_password');
    }


    //获取验证码
    public function actionSmscode(){
        $request = Yii::$app->request;
        if($request->isPost){
            $mobile = $request->post('mobile',null);
            if(!$mobile){
                return $this->json(0,'请输入您的手机号码！');
            }
            $f = W::sendSms($mobile);
            if (!$f) return $this->json(0, '验证码发送失败，请重试！');
            return $this->json(1);
        }
        return '非法访问';
    }

    //验证数据
    public function actionCardup(){
        $request = Yii::$app->request;
        $user=Yii::$app->session['wx_user_auth'];
        if($request->isPost){
            $data = $request->post();

            //先进行验证
            if(! empty($user['pid'])) return $this->json(0,'您暂无权限修改');
            if(!$data['mobile']) return $this->json(0,'请输入您的手机号码');
            if(!$data['payee_name']) return $this->json(0,'请输入账户所有人姓名');
            if(!$data['payee_account']) return $this->json(0,'请输入账号');
            if(!$data['payee_bank']) return $this->json(0,'请输入开户行');
            if($data['payee_account_type']!='1' && $data['payee_account_type']!='2') return $this->json(0,'数据错误');
            //检验验证码
                $cacheCode = Yii::$app->cache->get($data['mobile'].Yii::$app->session['token']);
                if(!$cacheCode) return $this->json(0,'请先获取验证码');
                if($data['yzcode'] == $cacheCode){
                    Yii::$app->cache->delete($data['mobile'].Yii::$app->session['token']);
                }else{
                    return $this->json(0,'验证码输入错误');
                }
            $data_p=[];
            $payeeModel = new Car_payee();
            $where=" uid=".$user['uid'];
            $data_p['uid']=$user['uid'];
            $data_p['admin_mobile']=$data['mobile'];
            $data_p['payee_name']=$data['payee_name'];
            $data_p['payee_account']=$data['payee_account'];
            $data_p['payee_bank']=$data['payee_bank'];
            $data_p['type']=$data['payee_account_type'];
            $data_p['shop_id']=intval($data['shop_id']);
            $data_p['c_time']=time();

            $pay = $payeeModel->getData('`id`','one'," `status`=1 and `uid`=".$user['uid']);
            if($pay){
                $status=$payeeModel->upData($data_p,$where);
            }else{
                $status=$payeeModel->myInsert($data_p);
            }

            if(!$status){
                return $this->json(0,'添加或修改账户失败');
            }

            return $this->json(1,'添加或修改账户成功',$data_p);
        }else{
            return $this->json(0,'请求错误');
        }
    }

///把商户张片存入数据库

    public function actionInsertimg()
    {
        $request = Yii::$app->request;

        $user=Yii::$app->session['wx_user_auth'];
        $user['uid']=! empty($user['pid']) ? $user['pid'] : $user['uid'];
        if($request->isPost) {
            $img_url = $request->post('img_url', null);
            $shopModel=new Car_shop();
            $where = " uid=" . $user['uid'];
            $status = $shopModel->upData(['shop_pic' =>$img_url], $where);
            if(!$status) return $this->json(0,'修改商户照片失败');
            $this->json(1,'操作成功');
        }
    }

    //账户管理
    public function actionAdmin_account()
    {
        $token=Yii::$app->session['token'];
        $user=Yii::$app->session['wx_user_auth'];
        if(!empty($user['pid'])) exit('error');
        $code=W::createStrQrcode($user['uid'],$token);
        $shopModel=new Car_shop();
        $shopinfo = $shopModel->getData('*','one'," `shop_status`!=0 and `uid`=".$user['uid']);
        $fansModel=new Fans();
        $ch_fans = $fansModel->getData('`id`,`nickname`,`openid`,`realname`,`telphone`','all'," `token`= '".$token."' and `pid`=".$user['uid']);
        return $this->render('admin_account',['code'=>$code,'user'=>$user,'shopinfo'=>$shopinfo,'ch_fans'=>$ch_fans]);
    }
    //给子账号添加备注信息
    public function actionUpdate_chaccount()
    {
        $token=Yii::$app->session['token'];
        $user=Yii::$app->session['wx_user_auth'];
        $request = Yii::$app->request;
        if($request->isPost) {
            if(!empty($user['pid'])) return $this->json(0,'你没有权限');
            $ch_uid = intval($request->post('uid', 0));
            $realname = $request->post('realname', null);
            $telphone = $request->post('telphone', null);
            $shopModel=new Car_shop();
            $shopinfo = $shopModel->getData('*','one'," `shop_status`=2 and `uid`=".$user['uid']);
            if(! $shopinfo) return $this->json(0,'您的商户已不存在或未通过审核');
            $fansModel=new Fans();//'`nickname`,`openid`,`realname`,`telphone`','all'," `token`= '".$token."' and `pid`=".$user['uid'])
            $where=" `token`= '".$token."' and id=$ch_uid and `pid`=".$user['uid'];
            $ch_fans = $fansModel->upData(['realname'=>$realname,'telphone'=>$telphone],$where);
            if(!$ch_fans) return $this->json(0,'操作失败');
            return $this->json(1,'操作成功');
        }
        return $this->json(0,'非法请求');
    }
//删除子账号
    public function actionDel_chaccount()
    {
        $token=Yii::$app->session['token'];
        $user=Yii::$app->session['wx_user_auth'];
        $request = Yii::$app->request;
        if($request->isPost) {
            if(!empty($user['pid'])) return $this->json(0,'你没有权限');
            $ch_uid = intval($request->post('uid', 0));
            $shopModel=new Car_shop();
            $shopinfo = $shopModel->getData('*','one'," `shop_status`=2 and `uid`=".$user['uid']);
            if(! $shopinfo) return $this->json(0,'您的商户已不存在或未通过审核');
            $fansModel=new Fans();//'`nickname`,`openid`,`realname`,`telphone`','all'," `token`= '".$token."' and `pid`=".$user['uid'])
            $where=" `token`= '".$token."' and id=".$ch_uid ;
            $ch_fans = $fansModel->upData(['pid'=>0],$where);
            if(!$ch_fans) return $this->json(0,'操作失败');
            return $this->json(1,'删除子账号成功');
        }
        return $this->json(0,'非法请求');
    }


    //通过文本解析地图位置

    public function actionAddress()
    {
        $request = Yii::$app->request;
        if($request->isGet) {//shop_name
            $address['address']= $request->get('address', '湖南省长沙市岳麓区麓谷企业广场B4栋');
            $address['shop_name']= $request->get('shop_name', null);
            return $this->renderPartial('address',['address'=>$address,]);
        }
        exit('请求错误');
    }

}
