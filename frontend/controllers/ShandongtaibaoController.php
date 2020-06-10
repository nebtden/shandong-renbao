<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\4\15 0015
 * Time: 11:08
 */

namespace frontend\controllers;


use common\components\Openssl;
use common\components\ShengDaCarWash;
use common\components\W;
use common\models\Car_paternalor;
use common\models\WashOrder;
use frontend\util\PController;
use Yii;

class ShandongtaibaoController extends PController
{
    protected $url = '';
    protected $key = '';
    protected $company = '30';
    protected $status = '0';
    protected $msg = '请求成功';

    public function beforeAction($action = NULL)
    {
        parent::beforeAction($action); // TODO: Change the autogenerated stub
        $this->url = Yii::$app->params['shandongtaibao']['url'];
        $this->key = Yii::$app->params['shandongtaibao']['key'];

        return true;
    }


    /**
     * 获取服务码
     * @return array
     */
    public function actionGetconsumercode()
    {
        $data = '';
        $postBody = yii::$app->getRequest()->getRawBody();
        $openSll = new Openssl($this->key,'12345678','DES-EDE3-CBC');
        $params = $openSll->decrypt($postBody);
        $params = json_decode($params,true);
        try {
            if(!is_array($params)){
                throw new \Exception('解密请求数据出错');
            }
            if(!$params['mobile'] || !preg_match('/^1[0-9]\d{9}$/', $params['mobile'])){
                throw new \Exception('手机号码不存在或者格式不正确');
            }
            //查询洗车订单中是否有该用户订单，如果有未完成订单，直接返回该订单
            $washObj = new WashOrder();
            $washInfo = $washObj->getOrder(['mobile'=>$params['mobile'],'order_id'=>$params['OrderId']]);
            if($washInfo){
               $data = $washInfo;
            } else {
                $data = $this->playOrder($params['mobile'],$params['OrderId']);
            }

            if($data){
                $reponseData = [
                    'extendOrderId' => $data['mainOrderSn'],
                    'extendOrderNo' => $data['mainOrderSn'],
                    'orderStatus' => WashOrder::$order_status[$data['status']],
                    'exchangeCode' => $data['consumerCode']
                ];
                $reponseData = json_encode($reponseData);
                $data = $openSll->encrypt($reponseData);
            }

        } catch (\Exception $e){
            $this->status = 1;
            $this->msg = $e->getMessage();
        }

        return $this->json($this->status,$this->msg,$data);
    }

    /**
     * 盛大平台下单
     * @param $mobile
     * @param $orderId
     * @return array|bool
     * @throws \yii\db\Exception
     */
    protected function playOrder($mobile,$orderId)
    {
        $time = time();
        $trans = Yii::$app->db->beginTransaction();
        try {
            //通过盛大接口下单
            $sourceApp = Yii::$app->params['shengda_sourceApp'];
            $order_no = $this->getOrderNo($mobile);
            $param = [
                'source' => $sourceApp,
                'orgSource' => $sourceApp,
                'order' => $sourceApp.$order_no,
                'randStr' => $order_no,
                'carType' => '01',
                'userInfo' => $mobile,
                //过期时间为当月最后一天
                'endTime' => date('Y-m-t'),
                'userOrderType' => 'order',
                'generationRule' => '02',
            ];
            $washObj = new ShengDaCarWash();
            $result = $washObj->receiveOrder($param);
            $result = json_decode($result,true);
            if($result['resultCode'] != 'SUCCESS'){
                throw new \Exception('服务器响应失败');
            }
            $encryptJsonStr = $washObj->decrypt($result['encryptJsonStr']);
            $resultJson = strstr($encryptJsonStr,'|',true);

            $resultCode = json_decode( $resultJson,true);
            $insertData = [
                'consumerCode' => $resultCode['order'],
                'expiredTime' => strtotime(date('Y-m-t',$time)),
                'uid' => '0',
                'mobile' => $mobile,
                'mainId' => '0',
                'mainOrderSn' => $order_no,
                'outOrderNo' => $resultCode['encryptCode'], //盛大订单编号
                'order_id' => $orderId, //山东太保订单编号
                'couponId' => '000',
                'shopId' => 9006287,
                'shopName' => '车后汽车美容中心',
                'used_num' => '0',
                'c_time' => $time,
                'date_day' => date('d', $time),
                'date_month' => date('Ym', $time),
                'serverType' => 1036,
                'amount' => '30.00',
                'serviceName' => '普洗（轿车）',
                'status' => ORDER_HANDLING,
                'company_id' => 2, //洗车平台
                'companyid' => $this->company,
            ];
            //写入洗车订单
            $orderObj = new WashOrder();
            $query = $orderObj->myInsert($insertData);
            if ($query == false) {
                throw new \Exception('订单写入失败');
            }
            $trans->commit();
        } catch (\Exception $e){
            $trans->rollBack();
            $this->status= 1;
            $this->msg = $e->getMessage();
            return false;
        }

        return $insertData;
    }

    /**
     * 生成订单编号
     * @param $mobile
     * @return string
     */
    protected function getOrderNo($mobile)
    {
        $time = date('YmdHis');
        $rd = rand(1000, 9999);

        return 'W'.$time.$rd.$mobile;
    }

}