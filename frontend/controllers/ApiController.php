<?php
namespace frontend\controllers;
use Yii;
use frontend\util\Msg;
use frontend\util\PController;
use common\components\W;
use common\models\Lottery_order;
use yii\helpers\Url;
/**
 * APP接口类
 * @author yinkp
 */
class ApiController extends PController {
	private $at; //access_token
	private $type;
	private $data;
	private $style;
	public function init() {
		header ( 'Content-Type:text/html;Charset=utf-8' );
		$this->at = trim ( $_GET ['at'] ); 
		$this->_auth ( $this->at ) || Msg::error( '1001' );
		$this->type = trim ( $_GET ['type'] )?trim ( $_GET ['type'] ):'oprate';
		$this->_request ();
	}
	
	public function actionIndex() {
		
	}
	
	public function actionCrtorder() {//创建订单
	//	var_dump($this->data);
		$third_order=$this->data;
		if(!$third_order['openId'] || !$third_order['orderId'] || !$third_order['commodityNo'] || !$third_order['priceTotal']){
			Msg::error('2001');
		}
		$where="orderId='".$third_order['orderId']."'";
		$lotorderModel=new Lottery_order();
		$res=$lotorderModel->getData('*','one',$where);
		
		if(!$res){
			$order = array (
					'token' => Yii::$app->session ['token'],
					'code' => W::createNumber ( 'orderThird' ),
					'third_openId' => $third_order['openId'],
					'orderId' => $third_order['orderId'],
					'commodityNo' => $third_order['commodityNo'],
					'giftNumbers' => $third_order['giftNumbers']>0?$third_order['giftNumbers']:1,
					'priceTotal' => $third_order['priceTotal'],
					'create_time' => time()
			);
			
			$orderId = $lotorderModel->addData ( $order );
			$payurl= 'http://buy.y100n.com'.Url::toRoute(['payment/jsapi','product_id'=> $order['code'],'third'=>'yes']);
			$return = array('url'=>$payurl);
			Msg::success($return);
		}else{
			Msg::error('5001');
		}
		
	}
	
	public function actionCirmaddr() {
		$res=$this->data;
		if(!$res['openId'] || !$res['orderId'] || !$res['consignee'] || !$res['phoneNo'] || !$res['address']){
			Msg::error('2001');
		}
		$where="orderId='".$res['orderId']."' and `status` in (0,1)";
		$lotorderModel=new Lottery_order();
		$order=$lotorderModel->getData('*','one',$where);
		if($order){
			$data=array('consignee'=>$res['consignee'],'phoneNo'=>$res['phoneNo'],'address'=>$res['address'],'status'=>-1);
			if($order['status'])$data['status'] = 2;
			$flag=$lotorderModel->upData($data,$where);
			$return=array('openId'=>$res['openId'],'orderId'=>$order['orderId'],'orderNum'=>$order['code']);
			Msg::success($return);
		}else{
			Msg::error('5001');
		}
	}
	
	public function actionTrack(){
		$from = $this->data;
		if(!$from['openId'] || !$from['orderId'] || !$from['orderNum'] || !$from['trackingNum']){
			Msg::error('2001');
		}
		$where="`code`='".$from['orderNum']."' and orderId='".$from['orderId']."' and trackingNum='".$from['trackingNum']."' and `status` in (4,5)";
		$lotorderModel=new Lottery_order();
		$order=$lotorderModel->getData('id,trackingNum','one',$where);
		if($order['id']){
			$url = 'http://58.32.246.70:8002';
			$trackNum = $order['trackingNum'];
			$timestamp = date("Y-m-d H:i:s");
			$sign = strtoupper(md5('rcEKOwapp_key6gYhA7formatJSONmethodyto.Marketing.WaybillTracetimestamp'.$timestamp.'user_id165895v1.01'));
			$key = 'sign='.$sign.'&app_key=6gYhA7&format=JSON&method=yto.Marketing.WaybillTrace&timestamp='.$timestamp.'&user_id=165895&v=1.01&param=[{"Number":"'.$trackNum.'"}]';
			$res = W::http_post($url,$key);
			$trackInfo = json_decode($res,true);
			$return=array('trackInfo'=>$trackInfo);
			Msg::success($return);
		}else{
			Msg::error('5001');
		}
	}
	
	private function _auth($at) {
		return $at === '66ae40986452977e432fd9976c2915ac';
	}
	
	private function _request() {
		if ($this->type == 'oprate') {
			$obj = file_get_contents ( 'php://input' );
			$this->data = json_decode ( $obj, true );
		}
		if ($this->type == 'upfile') {
			$obj = $_POST;
			if(!$obj['data']){ 
				$obj['data'] = $_FILES['data'];
				$obj['tp'] = 'fileInput';
			}
			//print_r($obj);die;
			$this->data = $obj;
		}
	}
}