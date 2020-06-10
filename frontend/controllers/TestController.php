<?php

namespace frontend\controllers;

use common\components\FaceKernel;
use common\components\ShengDaCarNewApi;
use common\models\Car_wash_order_taibao;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use Yii;
use common\models\User;
use frontend\util\FController;
use common\components\W;
use frontend\util\PController;
use common\models\Car_rescueor;
use common\models\Car_type;
use common\models\Car_oraddlog;
use common\models\Car_orstatuslog;
use common\models\Car_paternalor;
use common\components\NoLogin;
use common\models\CarMobile;
use common\models\CarFace;
use GuzzleHttp\Client;
use common\components\AESUtil;
use common\components\CyxAES;
use common\components\PiccInterface;
use common\components\Encrypt3Des;
use common\components\Openssl;
use common\models\Car_wash_pinganhcz_shop;
use common\components\Pinganhcznew;


class TestController extends PController
{


    public function actionIndex()
    {
        $this->layout = 'test';
        $user = new User ();
        //var_dump($user->findOne(['id' => 1]));
        $connection = \Yii::$app->db;
        $command = $connection->createCommand('SELECT * FROM ' . $user->tableName());
        //$posts = $command->queryAll();
        $post = $command->queryOne();
        var_dump($post);


        $users = $user->getData('*', 'all');
        //var_dump ( $users );

// 		$flag = $user->upData ( array (
// 				'username' => '安徽发' 
// 		), "id = 1" );
// 		echo $flag;

// 		$user->addData ( array (
// 				'username' => 'fsafasf',
// 				'auth_key' => md5 ( '1234' ) 
// 		) );
        return $this->render('index', ['users' => $users]);
    }

    public function actionTest()
    {
        $url = 'http://58.32.246.70:8002';
        $trackNum = '810542342208';
        $timestamp = date("Y-m-d H:i:s");
        $sign = strtoupper(md5('rcEKOwapp_key6gYhA7formatJSONmethodyto.Marketing.WaybillTracetimestamp' . $timestamp . 'user_id165895v1.01'));
        $key = 'sign=' . $sign . '&app_key=6gYhA7&format=JSON&method=yto.Marketing.WaybillTrace&timestamp=' . $timestamp . '&user_id=165895&v=1.01&param=[{"Number":"' . $trackNum . '"}]';
        $res = W::http_post($url, $key);
        $trackInfo = json_decode($res, true);
        var_dump($trackInfo);
    }
	
	public function actionLog(){
        $savePath = Yii::$app->getBasePath() . '/web/log/simon/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
        }
        $f = fopen($savePath  . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'post_body:' . yii::$app->getRequest()->getRawBody() . "\n");
        fwrite($f, 'post:' . json_encode(Yii::$app->request->post()) . "\n");
        fwrite($f, 'url:' . Yii::$app->request->queryString . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
        return json_encode(["status"=>1]);
    }

//道路救援  创建救援订单（九天）
    public function actionJiutiantest()
    {
        $time = time();
        $data = [];
        $data_1 = [];
        $data['orderId'] = $data_1['orderid'] = W::createNumber('orderThirds');
        $data['callTime'] = $data_1['calltime'] = date("YmdHis", $time);
        $data['customerName'] = $data_1['customername'] = '许雄泽';
        $data['mobile'] = $data_1['phone'] = '15802657270';
        $data['carNo'] = $data_1['carno'] = '京NDD678';
        $data['carBrand'] = $data_1['carbrand'] = '宝马';
        $data['carModel'] = $data_1['carmodel'] = 'A6';
        $data['faultAddress'] = $data_1['faultaddress'] = '北京市东城区潘家园南路42号';//116.43	39.92
        $data['longitude'] = $data_1['longitude'] = '116.4212';
        $data['latitude'] = $data_1['latitude'] = '39.9211';
        $data['rescueWay'] = $data_1['rescueway'] = '3';
        $data['cardNo'] = '13265998';
        $data['cardPassword'] = '789963';

        $rearr = W::rescuejiutianapi($data);
        if (intval($rearr['error_code']) == 0) {
            $data_1['c_time'] = $time;
            $data_1['is_sub'] = '1';
            $data_1['date_day'] = date("Y-m-d", $time);
            $data_1['date_month'] = date("Y-m", $time);


            $data_m['order_no'] = $data['orderId'];
            $data_m['type'] = '1';
            $data_m['coupon_id'] = '18899';
            $data_m['coupon_amount'] = '188';
            $data_m['integral'] = '50';
            $data_m['c_time'] = $time;

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {

                $m_id = (new Car_paternalor())->myInsert($data_m);
                $data_1['m_id'] = $m_id;
                (new Car_rescueor())->myInsert($data_1);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $e->getMessage();
            }
        }

        var_dump($rearr);
    }


//道路救援  创建救援订单
    public function actionOsstest()
    {
        $time = time();
        $data = [];
        $data_1 = [];
        $data['orderId'] = $data_1['orderid'] = W::createNumber('orderThirds');
        $data['callTime'] = $data_1['calltime'] = date("YmdHis", $time);
        $data['customerName'] = $data_1['customername'] = '许雄泽';
        $data['phone'] = $data_1['phone'] = '15802657270';
        $data['carNo'] = $data_1['carno'] = '京NDD678';
        $data['carBrand'] = $data_1['carbrand'] = '奔驰';
        $data['carModel'] = $data_1['carmodel'] = 'A6';
        $data['faultAddress'] = $data_1['faultaddress'] = '北京市朝阳区潘家园南路42号';//116.43	39.92
        $data['longitude'] = $data_1['longitude'] = '116.4212';
        $data['latitude'] = $data_1['latitude'] = '39.9211';//rescueWay
        $data['rescueWay'] = $data_1['rescueway'] = '3';
        $rearr = W::rescueapi($data);
        if (intval($rearr['error_code']) == 0) {
            $data_1['c_time'] = $time;
            $data_1['is_sub'] = '1';
            $data_1['date_day'] = date("Y-m-d", $time);
            $data_1['date_month'] = date("Y-m", $time);


            $data_m['order_no'] = $data['orderId'];
            $data_m['type'] = '1';
            $data_m['coupon_id'] = '18899';
            $data_m['coupon_amount'] = '188';
            $data_m['integral'] = '50';
            $data_m['c_time'] = $time;

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {

                $m_id = (new Car_paternalor())->myInsert($data_m);
                $data_1['m_id'] = $m_id;
                (new Car_rescueor())->myInsert($data_1);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $e->getMessage();
            }
        }

        var_dump($rearr);
    }

//取消订单cancel

    public function actionOsscancelor()
    {
        $info = (new Car_rescueor())->table()->select('orderid')->orderBy('id DESC')->one();
        $data['orderId'] = 'st5b03e0db0b6f5330';
        $data['reason'] = '测试----问题已解决';
        $rearr = W::cancelorder($data);
        if (intval($rearr['error_code']) == 0) {
            (new Car_rescueor())->myUpdate(['status' => '7', 'cancel_time' => time()], ['orderid' => $info['orderid']]);
        }
        var_dump($rearr);
    }

    public function actionAutonotice()
    {//orderStatus remark  responseTime
        $info = Yii::$app->request->post();
        $msg = '回调收到，但数据错误';
        $sign = $info['sign'];
        unset($info['sign']);
        ksort($info);
        $ossstr = implode("", $info);
        $mysign = md5($ossstr . Yii::$app->params['serverKey']);
        if ($mysign != $sign) {
            $msg = '回调收到，但签名错误';
        } else {
            if (!empty($info['orderId'])) {
                $model = new Car_rescueor();
                $res = $model->table()->select('id')->where(['orderid' => $info['orderId']])->one();
                if (!$res) {
                    $msg = '回调收到，但订单不存在';
                } else {
                    $status = intval($info['orderStatus']);
                    if ($status > -1 && $status < 7) {
                        $arr = [0 => '', 1 => 'acceptance_time', 2 => 'sendcar_time', 3 => 'setout_time', 4 => 'arrive_time', 5 => 'complete_time', 6 => 'cancel_time'];
                        $nv_time = time();
                        $data['status'] = $status;
                        $data[$arr[$status]] = $nv_time;
                        $data_m['order_id'] = $res['id'];
                        $data_m['order_no'] = $info['orderId'];
                        $data_m['orderstatus'] = $status;
                        $data_m['responsetime'] = $info['responseTime'];
                        $data_m['remark'] = $info['remark'];
                        $data_m['c_time'] = $nv_time;
                        $db = Yii::$app->db;
                        $transaction = $db->beginTransaction();
                        try {
                            $model->myUpdate($data, ['orderid' => $info['orderId']]);
                            (new Car_orstatuslog())->myInsert($data_m);
                            $transaction->commit();
                            $msg = '回调成功';
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                            $msg = '回调失败，系统错误';
                        }
                    } else {
                        $msg = '回调收到，但订单类型不存在';
                    }
                }
            }
        }

        $jsonarr = ['errorCode' => 0, 'msg' => $msg];
        //var_dump($jsonarr);
        return json_encode($jsonarr);
    }

    //实时获取地理位置
    public function actionAddressnotice()
    {//orderStatus remark  responseTime
        $info = Yii::$app->request->post();
        $msg = '回调收到，但数据错误';
        $sign = $info['sign'];
        unset($info['sign']);
        ksort($info);
        $ossstr = implode("", $info);
        $mysign = md5($ossstr . Yii::$app->params['serverKey']);
        if ($mysign != $sign) {
            $msg = '回调收到，但签名错误';
        } else {
            if (!empty($info['orderId'])) {
                $model = new Car_rescueor();
                $addModel = new Car_oraddlog();
                $res = $model->table()->select('id')->where(['orderid' => $info['orderId']])->one();
                $resadd = $addModel->table()->select('id')->where(['orderid' => $info['orderId']])->orderBy('id desc')->one();
                if (!$res) {
                    $msg = '回调收到，但订单不存在';
                } else {
                    $typeid = intval($info['typeID']);
                    if ($typeid > -1 && $typeid < 3) {
                        $data['orderid'] = $info['orderId'];
                        $data['typeid'] = $typeid;
                        $address = json_decode($info['lblData'], true);
                        $data['longitude'] = $address['key1'];
                        $data['latitude'] = $address['key2'];
                        $data['or_id'] = $res['id'];
                        if (!$resadd) {
                            $data['c_time'] = time();
                            $addModel->myInsert($data);
                        } else {
                            $data['u_time'] = time();

                            $addModel->myUpdate($data, ['orderid' => $info['orderId']]);
                        }
                        $msg = '回调成功';
                    } else {
                        $msg = '回调收到，但订单类型不存在';
                    }
                }
            }
        }

        $jsonarr = ['errorCode' => 0, 'msg' => $msg];
        return json_encode($jsonarr);
    }


    public function actionGs()
    {
        $mcity = (new CarMobile())->table()->select('city')->where(['package_id'=>20])->one();
        var_dump($mcity);die;
        $data['type'] = '3';
        $data['mobile'] = '15802657270';
        $data['time'] = time();
        $data['activationcode'] = '5TFPN255';
        $obj = new NoLogin('ca2e01bb386287fb','1d63578a86a2acc11a1a01912da51d29');
        $data['sign'] = $obj->make_sign($data);
        $datastr = json_encode($data);
        header('Content-type: text/html; charset=utf-8');
        $header = array(
            "content-type:application/json; charset=UTF-8",
            "Content-Length: " . strlen($datastr)
        );
        //$url='http://www.bxxz.com/frontend/web/test/addressnotice.html';
        $url = Yii::$app->params['url'] . '/frontend/web/apploginsxgs/freelogin.html?appkey=ca2e01bb386287fb';
        $result = W::http_post($url, $datastr, $header);
        $rearr = json_decode($result, true);
        if($rearr['errno'] != 0 ){
            var_dump($rearr);die;
        }
        $url = $rearr['data']['url'];
        //var_dump($rearr);die;
        header("Location: $url");
    }

    public function actionTestnotice()
    {
          $map = [
            'province' =>'山东省',
            'city' =>'济南市',
            'district' =>'历城区',
        ];
        $datastr = json_encode($map);
        header('Content-type: text/html; charset=utf-8');
        $header = array(
            "content-type:application/json; charset=UTF-8",
            "Content-Length: " . strlen($datastr)

        );
        $url = 'http://www.yunche168.com/frontend/web/notice/washshop2.html';
        $result = W::http_post($url, $datastr, $header);
        //$rearr = json_decode($result, true);
        var_dump($result);
    }

    public function actionTjson()
    {
        $str = '';
        $arr = json_decode($str, true);
        $data = $arr['result'];
        var_dump(count($data));
    }

    public function actionTjson0()
    {
        $str = '"a:3:{s:7:\"orderId\";s:20:\"R2018042720475312821\";s:7:\"lblData\";s:51:\"[[\"28.20768\",\"112.88821\"],[\"28.20525\",\"112.88821\"]]\";s:4:\"sign\";s:32:\"91b2adc98ecfbb1c291580106f1eaf10\";}';
        $arr = unserialize(stripslashes($str));
        var_dump(date('Y-m-d H:m:s', '1524847942'));
    }

    public  function actionTongbu(){
        set_time_limit(0);
        $arr=(new CarCouponPackage())->table()->select('id,package_sn')->where('id > 3285 and companyid=0')->all();
        $data=[];
        foreach ($arr as $k=>$v){
            $data[intval(substr($v['package_sn'],2,2))][]=$v['id'];
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            foreach($data as $key => $val){
                (new CarCouponPackage())->myUpdate(['companyid'=>$key],['id'=>$val]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
        $transaction->commit();
        exit('success');
    }

    public function actionTaiping(){
        $tbObj = new Car_wash_order_taibao();
        $tbOrder = $tbObj->table()->where([
            'consumer_code'=>'qy/C5dDf7LLukWDkOVD2HQ==',
            'apply_phone'=>'15123442221',
            'status' => 3])->one();
        if(!$tbOrder){
            throw new \Exception('订单不存在');
        }
        $result = $this->tbOrderStatus($tbOrder,'DW01002','1006','0');
        print_r($result);
    }

    private function tbOrderStatus($washOrder,$code,$status,$type)
    {
        $params = [
            'TicketId' => $washOrder['ticket_id'],
            'ServiceType' => $washOrder['service_type'],
            'SurveyUnitCode' => $status,
            'TicketType' => $type,
            'StatusTime' => date('Y-m-d H:i:s',time()),
            'TheTimeStamp' => (string)sprintf('%.0f', microtime(true)*1000),
        ];

        $repuestData = [
            'RequestCode' => $code,
            'AppId' => "cpic_e_rescue",
            'RequestBodyJson' => $params
        ];

        $url = Yii::$app->params['shandongtaibao']['url'];
        $params = json_encode($repuestData);
        $res = W::http_post($url,$params);
        (new DianDian())->requestlog($url,$params,json_encode($res,JSON_UNESCAPED_UNICODE),'taibaowash',$status,'taibaowash');

        return json_decode($res,true);
    }

    public function actionFacetest(){
        $url = 'https://idasc.webank.com/api/oauth2/access_token?app_id=wx790a5eeaad8aa436&secret=182b614e4f3c5fec88006ec958b15751&grant_type=client_credential&version=1.0.0';
        $res = W::http_get($url);
        var_dump($res);
    }

    public function actionSimontest(){
        $client = new Client();
        $url = 'https://faceid.tencentcloudapi.com/';
        $data = [];
        $data['RuleId']=0;
        $data['Action']='DetectAuth';
        $data['Version']='2018-03-01';
        $data['Region']='ap-guangzhou';
        $data['Nonce']=rand(100000,999999);
        $data['SecretId']='AKIDwjGc6PIbLu10u5eNlsmWzFk06wpXFS5a';
        $data['IdCard']='430321199001118889';
        $data['Name']='许多多';
        $data['RedirectUrl'] = 'https://www.yunche168.com/backend/web/login.html';
        $data['Timestamp']=time();
        ksort($data);
        $string = '';
        $i = 0;
        foreach($data as $key =>$val){
            if($i == 0){
                $string.=$key.'='.$val;
            }else{
                $string.='&'.$key.'='.$val;
            }
            $i++;
        }
        //$string = http_build_query($data);
        $value = 'GETfaceid.tencentcloudapi.com/?'.$string;
        $secretKey = 'XosjPpz84tToxgDbr1zlHafU6Tq5YSGt';
        echo $value;
        $signStr = base64_encode(hash_hmac('sha1', $value, $secretKey, true));
        $data['Signature']=$signStr;
        $res = $client->request('GET', $url,  ['query' =>$data]);
        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        var_dump($return);
    }

    public function actionSitest(){
        $Face  = new FaceKernel();
        $data['IdCard']='430321199001118889';
        $data['Name']='许多多';
        $data['RedirectUrl'] = 'http://testing.yunche168.com/frontend/web/face-kernel-notice/chac-notice.html';
        $data['Extra']='15802657270';
        $data['companyid']='76';
        $res= $Face->DetectAuth($data);

        if(!empty($res['BizToken'])){
            $time = time();
            $dbdata['id_card'] = $data['IdCard'];
            $dbdata['name'] = $data['Name'];
            $dbdata['redirect_url'] = $data['RedirectUrl'];
            $dbdata['extra'] = $data['Extra'];
            $dbdata['companyid'] = $data['companyid'];
            $dbdata['url'] = $res['Url'];
            $dbdata['biz_token'] = $res['BizToken'];
            $dbdata['request_id'] = $res['RequestId'];
            $dbdata['c_time'] = $time;
            $dbdata['u_time'] = $time;
            (new CarFace())->myInsert($dbdata);
        }
        var_dump($res);
    }

    public function actionGetinfo(){
        $Face  = new FaceKernel();
        $data['BizToken']='1D35F538-D502-489D-ABE8-9A8C8A748316';
        $data['companyid']='76';
        $res= $Face->GetDetectInfo($data);
        var_dump($res);
    }
    public function actionDis(){
        //通过盛大接口下单
//        {"sourceCode":"WHCXWY","orgSource":"WHCXWY","order":"WHCXWYDIS20200305092330545512",
//            "phoneNum":"15802657270","randStr":"DIS20200305092330545512","carType":"03",
//            "endTime":"2020-03-31","activityType":"5221"}
        $sourceApp = Yii::$app->params['shengda_sourceApp_new'];
        $time = time();
        $now = $time+86400*7;
        $str = 'DIS'.$time.rand(10000,99999);
        $param = [
            'sourceCode' => $sourceApp,
            'orgSource' => $sourceApp,
            'order' =>$str ,
            'phoneNum' => '1829983524',
            'randStr' => $str,
            'carType' => '03',
            'endTime' => '2020-03-31',
            'activityType' => '5221'
        ];
        $washObj = new ShengDaCarNewApi();
        $result = $washObj->receiveOrder($param);
        $res=$result['data'];
        $resultarr = json_decode( $res,true);
        $resultJson = $washObj->decrypt($resultarr['encryptJsonStr']);// ($resultarr['encryptJsonStr'],'|',true);
        $resultdata = explode('|',$resultJson);
        $resultCode = json_decode( $resultdata[0],true);
        var_dump($resultCode);
    }

    public function actionDis1(){
        //通过盛大接口查询
        $sourceApp = Yii::$app->params['shengda_sourceApp_new'];
        //sourceCode: "WHCXWY", pageNo: "1", pageSize: "50", longitude: "112.97935279", latitude: "28.21347823"
        $param = [
            'sourceCode' => $sourceApp,
//            'longitude' => '114.310000',
//            'latitude' => '30.520000',
            'serviceId' => '5044',
            'cityNumber' =>'620400',
            'areaNumber' => '620403',
            'pageNo' => '1',
            'pageSize' => '20'

        ];
        $washObj = new ShengDaCarNewApi();
        $result = $washObj->merchantDistanceList($param);
        $res=$result['data'];
        $resultarr = json_decode( $res,true);
        $resultJson = $washObj->decrypt($resultarr['encryptJsonStr']);// ($resultarr['encryptJsonStr'],'|',true);
        $resultdata = explode('|',$resultJson);
        $shoparr = json_decode( $resultdata[0],true);
        var_dump($resultdata[0]);
    }
    public function actionDisce(){

        $sourceApp = Yii::$app->params['shengda_sourceApp_new'];
        $param = [
            'sourceCode' => $sourceApp,
            'orderId' => 'WHCXABDE2451'
        ];
        $washObj = new ShengDaCarNewApi();
        $result = $washObj->cancelOrder($param);
        $res=$result['data'];
        $resultarr = json_decode( $res,true);
        var_dump($resultarr);
    }

    public function actionTdisn(){

        $map = [
            'order' =>'tDPF1mTf1md5N5Zwy082AA==',
            'userInfo' =>'15802657270'
        ];
        $datastr = json_encode($map);
        header('Content-type: text/html; charset=utf-8');
        $header = array(
            "content-type:application/json; charset=UTF-8",
            "Content-Length: " . strlen($datastr)

        );
        $url = 'http://testing.yunche168.com/frontend/web/notice/shengda.html';
        $result = W::http_post($url, $datastr, $header);
        var_dump($result);
    }

    public function actionHgs()
    {
        $obj = new AESUtil();
        $data['mobile'] = $obj->encrypt('15802657278',Yii::$app->params['PICC']['aeskey']);
        $data['outerOrderId'] = $obj->encrypt('202004'.rand(100000,90000),Yii::$app->params['PICC']['aeskey']);;
        $datastr = json_encode($data,true);
        $header = array(
            "content-type:application/json; charset=UTF-8",
            "Content-Length: " . strlen($datastr)
        );
        var_dump($datastr);
        $url = 'http://testing.yunche168.com/frontend/web/piccclub/notice.html';
        $result = W::http_post($url, $datastr, $header);
        var_dump($result);die;
        $rearr = json_decode($result, true);
    }
    public function actionPicctest(){


        $str = 'PICC831606B2FA599271DA9373BC96447AC3';
        $sign = strtoupper(md5($str));
        $obj = new AESUtil();
        $desstr =  $obj->encrypt($sign,'6F47197921E9F999CAC2CBC7C650260C');
        $jiestr =  $obj->decrypt($desstr,'6F47197921E9F999CAC2CBC7C650260C');
        $data['timeStamp'] = 1585757994297;
        $data['passWord'] = $desstr;
        $data['userName'] = 'PICCdhycjdsm7Ul';

        $md5str = $obj->sign($data,'PICCYSrtyzzSLvGT621a88Uym6ZHCFh64SqGstMgFGO5');
        $md5sign = $obj->md5str($md5str);
        $data['sign'] = $md5sign;
        echo  $desstr;
        echo '<br>';
        echo $jiestr;
    }



    public  function actionPic()//045DD28691D44B22AB32B13BC4C44B71Pe4L
    {
//        $obj = new PiccInterface();
//        $res= $obj ->refreshToken('045DD28691D44B22AB32B13BC4C44B71Pe4L');
//        var_dump($res);
 //orderNo=1010597681&cuponNo=145001027766732674&mobile=13207585201&productCode=DHYCCJ001&orderCode=14000000&expirationTime=1599408000000&userName=xxxxxc
        $obj = new AESUtil();
        $jiestr['orderNo'] =  $obj->encrypt('1010597681',Yii::$app->params['PICC']['weixinAeskey']);
        $jiestr['cuponNo'] =  $obj->encrypt('145001027766732674',Yii::$app->params['PICC']['weixinAeskey']);
        $jiestr['mobile'] =  $obj->encrypt('13207585201',Yii::$app->params['PICC']['weixinAeskey']);
        $jiestr['productCode'] =  $obj->encrypt('DHYCCJ001',Yii::$app->params['PICC']['weixinAeskey']);
        $jiestr['orderCode'] =  $obj->encrypt('14000000',Yii::$app->params['PICC']['weixinAeskey']);
        $jiestr['expirationTime'] =  $obj->encrypt('1599408000000',Yii::$app->params['PICC']['weixinAeskey']);//
        $jiestr['userName'] =  $obj->encrypt('xxxxxc',Yii::$app->params['PICC']['weixinAeskey']);
        $str = http_build_query($jiestr);
//        $chestr = Yii::$app->cache->get('picc_token'.Yii::$app->params['PICC']['userName']);
//        $str = $obj->decrypt('D5DED209E56341548DBA26039CB51A2FFEN6',Yii::$app->params['PICC']['aeskey']);
       
        echo $str;
        echo '<br>';
        echo 'orderNo=C3C1BB88A23138AF53B20A13D35F7EDD&cuponNo=09D219DA9A7E77D6866E1CFF0AC5D120942ADAE72E4DB3EB6A97139F0EE35F08&mobile=2DCF6C408EDF3ACD2F56265FC5BACE1A&productCode=C91CE6281624D5AA5A3B8F56019CA57C&orderCode=D35E1D89263BF7F49FD62837FD0D5CD5&expirationTime=EB54B801D149431EBA7EED6A7DD9F235&userName=3A1C47A1D2D18D56E0AAE4B0865125B7';

    }

    public  function actionPiccl()//045DD28691D44B22AB32B13BC4C44B71Pe4L
    {


        $data['orderNo'] = '1010597575';
        $data['cuponNo'] = '144486058576589965';
        $data['outerOrderId'] = '2020042131122556';
        $data['connectPhone'] = '15888896667';
        $data['userName'] = 'xxxxxc';
        $data['status'] = 1;

        $obj = new PiccInterface();
        //$res= $obj ->cuponnoLock($data);

        $res= $obj ->syncOrderInfo($data);
        var_dump($res);
    }
    public  function actionPingan()//045DD28691D44B22AB32B13BC4C44B71Pe4L
    {
        //sdPSpxxCcgaT2iL1HtTDAtAGh+osCE/clE5BZeJxpIlx5s9GP85DnmSmeVBP5cOnhP/aDlAgrBGaLhcC7cNXcV6tJjPVxrLFWLGM5jTNpsx241HixvKANkx6L+xdF1BEe0Vn+4TScpFT6wR71wKlI6JeDTCi7rbSyVHyQQEqeEct3XPjKSgIpOe35lJeVk79VqwzJ0OCrsuid67v3xhE+g==
        $openSsl = new Openssl(Yii::$app->params['pinganhcz']['key'],'','AES-128-ECB');
        $data = $openSsl->decrypt('sdPSpxxCcgaT2iL1HtTDAtAGh+osCE/clE5BZeJxpIlx5s9GP85DnmSmeVBP5cOnhP/aDlAgrBGaLhcC7cNXcV6tJjPVxrLFWLGM5jTNpsx241HixvKANkx6L+xdF1BEe0Vn+4TScpFT6wR71wKlI6JeDTCi7rbSyVHyQQEqeEct3XPjKSgIpOe35lJeVk79VqwzJ0OCrsuid67v3xhE+g==');
        $list = (new Car_wash_pinganhcz_shop())->select('store_name,company_id,outlet_id,third_outlet_no',['store_name'=>'三马路爱义行（天水店）'])->orderBy(' id DESC ')->all();
        var_dump($list);
        var_dump($data);
    }

    /**
     * 测试
     * @return array|string
     */
    public function actionPatest()
    {
        $coupon = Yii::$app->request->get('code');
        $storeName = Yii::$app->request->get('shopid');
        $pingAn = new Pinganhcznew();
        $list = (new Car_wash_pinganhcz_shop())->select('company_id,outlet_id,third_outlet_no',['store_name'=>$storeName])->orderBy(' id DESC ')->all();
        $index = '';
        $shopNo = '';
        foreach ($list as $key=>$val){
            //从平安查询卡券详情

            $res = $pingAn->couponDetail($coupon,$val['outlet_id']);
            $str = $res;
            $res = json_decode($res,true);
            if($res['data']['code'] == 200){
                $index = $key;
                $shopNo = $val['outlet_id'];
                if($res['data']['data']['profitOfferItemVersion'] == '1' && strpos($val['outlet_id'],'O') !== false ) break;
                if($res['data']['data']['profitOfferItemVersion'] == '0' && strpos($val['outlet_id'],'O') === false ) break;
                if($res['data']['data']['profitOfferItemVersion'] == '0' && strpos($val['outlet_id'],'O') !== false ) break;
            }
        }
        var_dump($str);
        var_dump($index.'--'.$shopNo);
    }
}
