<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 10:29
 */

namespace backend\controllers;


use backend\util\BController;
use common\models\Payment;
use Yii;
class PaymentController  extends BController {

    public function actionIndex(){
        if (Yii::$app->request->isAjax) {
            $payModel = new Payment();
            $pageSize = intval ( $_GET ['limit'] ) ? intval ( $_GET ['limit'] ) : 10;
            $offset = intval ( $_GET ['pageNumber'] )  > 1? intval ( $_GET ['pageNumber'] ) - 1 : 0;
            $where = "`token`='" . Yii::$app->session['token'] . "' and `status`=1";
            $order = "id desc";
            $limit = $offset * $pageSize.','. $pageSize;
            $payments = $payModel->getData('*', 'all', $where, $order, $limit);
            foreach($payments as $k=> $v){
                $payments[$k]['status']=($v['status']==1)?"正常":"不可用";
                $payments[$k]['pay_type']=($v['pay_type']==1)?"微支付":"支付宝";
            }
            $cot = $payModel->getData('count(*) cot', 'one', $where);
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode($payments) . ' }';
            exit ();
        }
        return $this->render('index');
    }

    //支付信息编辑
    public function actionEdit(){
        $id = intval($_REQUEST['id']);
        $payModel = new Payment();
        $payment=null;
        if($id){
            $where="id=".$id;
            $payment = $payModel->getData('*','one',$where);
        }
        if (Yii::$app->request->post()) {
            if($payment){
                unset($_POST['id']);
                $flag = $payModel->upData($_POST,$where);
            }else{
                $_POST['token'] = Yii::$app->session['token'];
                $flag = $payModel->addData($_POST);
            }
            $this->redirect('index.html');
        }
       return $this->render ( 'edit' , array('data' =>$payment));
    }
    //支付方式逻辑删除
    public function actionDel(){
        $id = intval($_REQUEST['id']);
        $ids = implode ( ',', Yii::$app->request->get ( 'params' ) );
        $where='';
        $payModel = new Payment();
        if($ids){
            $where="id in ($ids)";
            $category = $payModel->getData('*','all',$where);
            if($category){
                $payModel ->upData(array('status'=>0),$where);
            }
        }
        exit;
    }

}