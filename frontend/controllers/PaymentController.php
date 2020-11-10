<?php
namespace frontend\controllers;

use common\components\W;
use common\models\PayOrder;
use Yii;
use frontend\util\FController;
use frontend\util\PController;
use common\components\Payment;
use common\models\Lottery_order;
use common\models\Lottery_orders;

class PaymentController extends PController {

    public $is_web = null;

	public function actionIndex() {
		$this->layout='test';
		return $this->render('index', []);
	}

    public function checkWeb()
    {
        $session = \Yii::$app->session;
        return $this->is_web = $session['xxz_mobile'];
    }
	
	public function actionJsapi(){
        Yii::$app->session['token'] = $token = 'dhcarcard';
//		if($_GET['alxg'] == 'zhouzhouxs'){
//            Yii::$app->session['openid'] = 'oVkGm0RmhqUK5qX3EujOwClpdFzg';
//        }
//        if($_GET['alxg'] == 'zyj'){
//            Yii::$app->session['openid'] = 'oVkGm0U0HN-mJIQAFU1hNpl4BpCc';
//        }
        //如果是web端传过来的链接，
        $is_web = $this->checkWeb();
        if(!$is_web){
//            Yii::$app->session['openid'] = 'oVkGm0bs3UIT9r9Esm0oAY-Rh27w';
            // Yii::$app->session['openid'] = 'oVkGm0djGbBhMhw8K3SiK3UWaeCA';
            if(!Yii::$app->session['openid']){
                W::getOpenid($token,'snsapi_userinfo');
            }

        }

		$this->layout=false;
		$code = trim($_REQUEST['product_id']);//支付订单号
		$third = trim($_REQUEST['third']);


        if($third=='pay'){
            $id = substr($code,2);
            $pay = PayOrder::findOne($id);
            $amount = $pay->money;
            $amount = intval(floatval($amount)*100);
        }elseif($third=='yes'){
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
//			print_r($pay);


			return $this->render('jsapi', ['pay'=>$pay,'amount'=>$amount,'order'=>$order,'third'=>$third]);
		}
	  
	}
	
	
	public function actionNotice(){

        $data  = file_get_contents("php://input");

        $list = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $code = substr($list['out_trade_no'], 0, 2);

        if($code == 'sg'){
            //公司显示检测
            $total_fee = $list['total_fee'];
            $orderModel = PayOrder::find()->where([
                'order_sn'=>$list['out_trade_no']
            ])->one();
            $orderModel->status = 1;
            $orderModel->paid_money = $total_fee/100;
            $orderModel->save();

        }



//        Payment::notice();
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
