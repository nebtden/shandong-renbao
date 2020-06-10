<?php
namespace frontend\controllers;
use common\components\Eddriving;
use common\components\Oilcard;
use common\components\W;
use frontend\util\PController;
use common\models\Lottery_order;
use common\components\Payment;
/**
 * APP接口类测试
 * @author yinkp
 */
class ApitestController extends PController {
	private $oprateurl = 'http://buy.y100n.com/frontend/web/api/track.html?at=66ae40986452977e432fd9976c2915ac';
	//private $thirdurl='http://mswgj.minshenglife.com/mslife_wx/flyingStart/applyState.json';
	private $thirdurl='http://mswxgj.minshenglife.com/mslife_wx/flyingStart/applyState.json';
	public function actionIndex() {
		$data = array (
				'openId' => 'oAq08t7KAJkAJ-JyMEU44e4wkRH',
				'orderId' => '47785202739000840',
				'orderNum' => 'ot5903e79b7cef0432',
				'trackingNum' => '809863224519'
		);
		echo W::http_post ( $this->oprateurl, json_encode ( $data ) );
	}
	
	//付款同步
	public function actionSyncpay(){
		$lotorderModel= new Lottery_order();
		$where = "pay_status = 1 and flag = 0";
		$rs = $lotorderModel->getData('*','all',$where,'id asc','0,50');
		foreach ($rs as $v){
			$data = array(
				'openId'=>$v['third_openId'],
				'orderId'=>$v['orderId'],
				'orderNum'=>$v['code'],
				'flag'=>0,
				'trackingNum'=>'',
				'trackcompany'=>''
			);
			$res = W::http_post ( $this->thirdurl, json_encode ( $data ) );
			$re = json_decode($res,true);
			if($re['status'] && !$re['status']['code']){
				$lotorderModel->upData(array('flag'=>1),"`id`=".$v['id']);
			}
		}
	}
	
	//发货同步
	public function actionSynctrack(){
		$lotorderModel= new Lottery_order();
		$where = "pay_status = 1 and flag = 1 and ship_status = 1";
		$rs = $lotorderModel->getData('*','all',$where,'id asc','0,50');
		foreach ($rs as $v){
			$data = array(
					'openId'=>$v['third_openId'],
					'orderId'=>$v['orderId'],
					'orderNum'=>$v['code'],
					'flag'=>1,
					'trackingNum'=>$v['trackingNum'],
					'trackcompany'=>$v['trackcompany']
			);
			$res = W::http_post ( $this->thirdurl, json_encode ( $data ) );
			$re = json_decode($res,true);
			if($re['status'] && !$re['status']['code']){
				$lotorderModel->upData(array('flag'=>2),"`id`=".$v['id']);
			}
		}
	}
	
	//退款
	public function actionSyncreturn(){
		$lotorderModel= new Lottery_order();
		$time=time();
		$where = "pay_status = 1 and status = 1 and ship_status = 0 and ($time-pay_time)>3600*24*3";
		$rs = $lotorderModel->getData('*','all',$where,'id asc','0,50');
		foreach ($rs as $v){
			$refundInfo = array(
					'transaction_id' => '',
					'out_trade_no' => $v['code'],
					'total_fee' => intval($v['priceTotal']*100),
					'refund_fee' => intval($v['priceTotal']*100)
			);
			$result = Payment::refund($refundInfo);
			var_dump($result);
			if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
				$arr['return_time'] = time();
				$arr['pay_status'] = 2;
				$arr['status'] = 3;
				echo $lotorderModel->upData($arr,"`id`=".$v['id']);
			}
		}
	}
	
	//退款同步
	public function actionSyncreturnprocess(){
		$id = intval($_GET['id']);
		$lotorderModel= new Lottery_order();
		$where = "pay_status = 2 and flag = 1 and ship_status = 0";
		$rs = $lotorderModel->getData('*','all',$where,'id asc','0,50');
		foreach ($rs as $v){
			$data = array(
					'openId'=>$v['third_openId'],
					'orderId'=>$v['orderId'],
					'orderNum'=>$v['code'],
					'flag'=>3,
					'trackingNum'=>'',
					'trackcompany'=>''
			);
			$res = W::http_post ( $this->thirdurl, json_encode ( $data ) );
			$re = json_decode($res,true);
			print_r($re);
			if($re['status'] && !$re['status']['code']){
				$arr['flag'] = 4;
				$lotorderModel->upData($arr,"`id`=".$v['id']);
			}
		}
	}
	
	//收货同步
	public function actionSyncsign($s = 0){
		$url = 'http://58.32.246.70:8002';
		$lotorderModel= new Lottery_order();
		$time=time();
		$where = "flag = 2 and ship_status = 1 and ($time-ship_time)>3600*24*3";
		$rs = $lotorderModel->getData('*','all',$where,'id asc',$s.',50');
		foreach ($rs as $v){
			if($v['trackingNum']){
				$timestamp = date("Y-m-d H:i:s");
				$trackNum = $v['trackingNum'];
				$sign = strtoupper(md5('rcEKOwapp_key6gYhA7formatJSONmethodyto.Marketing.WaybillTracetimestamp'.$timestamp.'user_id165895v1.01'));
				$key = 'sign='.$sign.'&app_key=6gYhA7&format=JSON&method=yto.Marketing.WaybillTrace&timestamp='.$timestamp.'&user_id=165895&v=1.01&param=[{"Number":"'.$trackNum.'"}]';
				$res = W::http_post($url,$key);
				if(strpos($res, '已签收') !== false || ($time-$v['ship_time'])>3600*24*10){
					$data = array(
							'openId'=>$v['third_openId'],
							'orderId'=>$v['orderId'],
							'orderNum'=>$v['code'],
							'flag'=>2,
							'trackingNum'=>$v['trackingNum'],
							'trackcompany'=>$v['trackcompany']
					);
					$res = W::http_post ( $this->thirdurl, json_encode ( $data ) );
					$re = json_decode($res,true);
					$arr['status'] = 5;
					if($re['status'] && !$re['status']['code']){
						$arr['flag'] = 3;
					}
					$lotorderModel->upData($arr,"`id`=".$v['id']);
				}
			}
		}
	}

    /**
     * 测试卡号
     * 9030000002707313
     * 9030000005107514
     * 9030000002254399
     * 9030000005637967
     */
	public function actionOil(){
	    $oil = new Oilcard();
	    $r = $oil->query_balance();
	    var_dump((int)$r);
        //充值
//        $res = $oil->buy(20000,'9030000002707313','2018071715170001');
//        var_dump($res);

        //查询订单
//        $res = $oil->query_order('2018071715170001');
//        var_dump($oil->errMsg);
//        var_dump($oil->errCode);
//        print_r($res);
    }

    public function actionCar(){
	    $request = \Yii::$app->request;
	    $sn = $request->get('sn');
	    $mobile = $request->get('phone');
	    $obj = new Eddriving();
	    $res = $obj->coupon_allinfo($sn,$mobile);
	    print_r($res);
    }
}