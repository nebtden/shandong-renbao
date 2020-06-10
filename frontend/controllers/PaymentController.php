<?php
namespace frontend\controllers;

use Yii;
use frontend\util\FController;
use common\components\Payment;
use common\models\Order;
use common\models\Lottery_order;
use common\models\Lottery_orders;

class PaymentController extends FController {
	
	public function actionIndex() {
		$this->layout='test';
		return $this->render('index', []);
	}
	
	public function actionJsapi(){
		$this->layout=false;
		$code = trim($_REQUEST['product_id']);//支付订单号
		$third = trim($_REQUEST['third']);
		if($third=='yes'){
			$lotorderModel= new Lottery_order();
			$token=Yii::$app->session ['token'];
			$openid=Yii::$app->session['openid'];
			$where="code='".$code."'";
			$data=array('openId'=>$openid);
			$flag=$lotorderModel->upData($data,$where);
			$order=$lotorderModel->getData('*','one',$where);
			$amount = intval(floatval($order['priceTotal'])*100);
		}elseif($third=='yet'){
			$lotorderModel= new Lottery_orders();
			$token=Yii::$app->session ['token'];
			$openid=Yii::$app->session['openid'];
			$where="code='".$code."'";
			$data=array('openId'=>$openid);
			$flag=$lotorderModel->upData($data,$where);
			$order=$lotorderModel->getData('*','one',$where);
			$amount = intval(floatval($order['priceTotal'])*100);
		}else{
			$orderModel= new Order();
			$where="order_code='".$code."'";
			$order=$orderModel->getData('*','one',$where);
			$amount = intval($order['amount_total']*100);
		}
		//$amount=1;
		if($code && $amount){
			$info = array(
				'body' => '购买产品',
				'attcch' => '',
				'trade_no' => $code,
				'fee' => $amount,
				'tag' => '',
				'notify_url' => Yii::$app->params['url'].'/frontend/web/payment/notice.html'
			);
			$pay = Payment::jsApi($info);
			return $this->render('jsapi', ['pay'=>$pay,'amount'=>$amount,'order'=>$order,'third'=>$third]);
		}
	  
	}
	
	
	public function actionNotice(){
		Payment::notice();
	}
	
	
	public function actionRefund(){
		$refundInfo = array(
			'transaction_id' => '',
			'out_trade_no' => 'or56e9252e16892',
			'total_fee' => '1',
			'refund_fee' => '1'
		);
		Payment::refund($refundInfo);
	}
}
