<?php
namespace common\components;
use Yii;
use common\models\Order;
use common\models\Lottery_order;
use common\models\Lottery_orders;

ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "./../../vendor/pay/weixins/lib/WxPay.Api.php";
require_once "./../../vendor/pay/weixins/example/WxPay.JsApiPay.php";
require_once "./../../vendor/pay/weixins/example/log.php";
require_once './../../vendor/pay/weixins/lib/WxPay.Notify.php';

class Payments{
	
	public static function jsApi($info){
		//初始化日志
		$logHandler= new \CLogFileHandler("../logs/".date('Y-m-d').'.log');
		$log = \Log::Init($logHandler, 15);
		
		
		//①、获取用户openid
		$tools = new \JsApiPay();
		$openId = \Yii::$app->session['openid'];
		//$openId = $tools->GetOpenid();
		
		//②、统一下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($info['body']?$info['body']:"test");
		$input->SetAttach($info['attcch']?$info['attcch']:"test");
		$input->SetOut_trade_no($info['trade_no']?$info['trade_no']:\WxPayConfig::MCHID.date("YmdHis"));
		$input->SetTotal_fee($info['fee']?$info['fee']:"1");
		$input->SetTime_start(date("YmdHis"));
		//$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag($info['tag']?$info['tag']:"test");
		$input->SetNotify_url($info['notify_url']?$info['notify_url']:"http://paysdk.weixin.qq.com/example/notify.php");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = \WxPayApi::unifiedOrder($input);
	//	echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		//self::printf_info($order);die;

		$jsApiParameters = $tools->GetJsApiParameters($order);
		
		//获取共享收货地址js函数参数
		//$editAddress = $tools->GetEditAddressParameters();
		
		return array('jsApiParameters'=>$jsApiParameters);
	}
	
	public static function notice(){
		//初始化日志
		$logHandler= new \CLogFileHandler("../logs/".date('Y-m-d').'.log');
		$log = \Log::Init($logHandler, 15);
		
		\Log::DEBUG("begin notify");
		$notify = new PayNotifyCallBack();
		$notify->Handle(false);
	}
	
	
	public static function refund($refundInfo){
		if((isset($refundInfo["transaction_id"]) && $refundInfo["transaction_id"] != "") || (isset($refundInfo["out_trade_no"]) && $refundInfo["out_trade_no"] != "")){
			$input = new \WxPayRefund();
			if(isset($refundInfo["transaction_id"]) && $refundInfo["transaction_id"] != ""){
				$transaction_id = $refundInfo["transaction_id"];
				$input->SetTransaction_id($transaction_id);
			}elseif(isset($refundInfo["out_trade_no"]) && $refundInfo["out_trade_no"] != ""){
				$out_trade_no = $refundInfo["out_trade_no"];
				$input->SetOut_trade_no($out_trade_no);
			}
			$total_fee = $refundInfo["total_fee"];
			$refund_fee = $refundInfo["refund_fee"];
			$input->SetTotal_fee($total_fee);
			$input->SetRefund_fee($refund_fee);
			$input->SetOut_refund_no(\WxPayConfig::MCHID.date("YmdHis"));
			$input->SetOp_user_id(\WxPayConfig::MCHID);
			$res = \WxPayApi::refund($input);
			//self::printf_info($res);
			return $res;
			//exit();
		}
	}
	
	//打印输出数组信息
	public static function printf_info($data)
	{
		foreach($data as $key=>$value){
			echo "<font color='#00ff55;'>$key</font> : $value <br/>";
		}
	}
}

class PayNotifyCallBack extends \WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new \WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = \WxPayApi::orderQuery($input);
		\Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
		&& array_key_exists("result_code", $result)
		&& $result["return_code"] == "SUCCESS"
				&& $result["result_code"] == "SUCCESS")
		{   
			return true;
		}
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		\Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();

		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		//这里写回调处理
		error_log(json_encode($data),3,APP_PATH.'/prr.txt');
		
		$code = substr($data['out_trade_no'], 0, 2);
		if($code == 'or'){
			$where = "`order_code` ='".$data['out_trade_no']."' and `pay_status`=0";
			$orderModel = new Order();
			$flag = $orderModel->upData(array('pay_status'=>1,'pay_time'=>time(),'order_status'=>1),$where);
		}
		if($code == 'ot'){
			$where = "`code` ='".$data['out_trade_no']."' and `pay_status`=0";
			$lotorderModel= new Lottery_order();
			$lotorder=$lotorderModel->getData('*','one',$where);
			$res = array('openId'=>$data['openid'],'pay_status'=>1,'pay_time'=>time(),'status'=>1);
			if($lotorder['status'] == -1)$res['status'] = 2;
			$flag = $lotorderModel->upData($res,$where);
			W::runThread('http://buy.y100n.com/frontend/web/apitest/syncpay.html');
		}
		if($code == 'st'){
			$where = "`code` ='".$data['out_trade_no']."' and `pay_status`=0";
			$lotorderModel= new Lottery_orders();
			$lotorder=$lotorderModel->getData('*','one',$where);
			$res = array('openId'=>$data['openid'],'pay_status'=>1,'pay_time'=>time(),'status'=>1);
			if($lotorder['status'] == -1)$res['status'] = 2;
			$flag = $lotorderModel->upData($res,$where);
			W::runThread('http://buy.y100n.com/frontend/web/apitests/syncpay.html');
		}
		return true;
	}
}