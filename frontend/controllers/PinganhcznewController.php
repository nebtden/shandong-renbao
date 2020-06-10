<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/19 0019
 * Time: 上午 10:45
 */

namespace frontend\controllers;


use common\components\Pinganhcz;
use common\components\W;
use common\models\Car_wash_pinganhcz;
use common\models\Car_wash_pinganhcz_shop;
use frontend\util\PController;
use common\components\ShengDaCarWash;
use Yii;

class PinganhcznewController extends PController
{
    /**
     * 返回盛大错误代码
     * 200 请求成功
     * 1001 卡券不存在
     * 1002 兑换码已使用
     * 1003 接口请求错误
     * 1004 异常码，具体看消息
     * @var int
     */
    protected $status=200;
    protected $msg='处理成功';


    /**
     * 根据手机号码查询卡券权益
     * @return bool|mixed|string
     */
    public function actionProfit()
    {
        $mobile = Yii::$app->request->get('mobile');
        $pingAn = new Pinganhcz();
        $res = $pingAn->profit($mobile);
        return $res;
    }

    private function log($type,$return)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/pinganhcz_new/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'post_body:' . yii::$app->getRequest()->getRawBody() . "\n");
        fwrite($f, 'url:' . Yii::$app->request->queryString . "\n");
        fwrite($f, 'return:' . $return . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }

    /**
     * 查询平安卡券
     * @param $code
     * @return array|bool
     */
    protected function couponDetail($code,$storeName)
    {
        $pingAn = new Pinganhcz();
        try {
            if($code == false){
                throw new \Exception('没有兑换码');
            }
            if(!$pingAn->access_token){
                throw new \Exception('access_token获取失败');
            }
            $order = (new Car_wash_pinganhcz())->select('*',['coupon_code'=>$code,'status'=>2])->one();
            if($order){
                $this->status = 1002;
                throw new \Exception('兑换码已经被使用');
            }
            $list = (new Car_wash_pinganhcz_shop())->select('company_id,outlet_id',['store_name'=>$storeName])->all();
            $index = '';
            $res = '';
            foreach ($list as $key=>$val){
                //从平安查询卡券详情
                $res = $pingAn->couponDetail($code,$val['outlet_id']);
                $res = json_decode($res,true);
                if($res['data']['code']== 200){
                    $index = $key;
                    break;
                }
            }



            if(!$index){


//                $this->status = 1003;
//                throw new \Exception('请求接口失败');

                //继续去掉店铺查询
                $res = $pingAn->couponDetail($code,null);

                $res = json_decode($res,true);


                if($res['data']['code']== 200){

                }else{
                    $this->status = 1003;
                    throw new \Exception('请求接口失败');
                }
            }
            echo 44;

            if($res['data']['code']!= 200){
                $this->status = 1001;
                throw new \Exception($res['data']['message']);
            }
            $resData = $res['data']['data'];

            $insertData = [
                'company_id' => $list[$index]['company_id'],
                'coupon_id' => $resData['couponId'],
                'coupon_code' => $code,
                'uuid' => $resData['uuid'],
                'mobile' => $resData['mobileNo'],
                'start_time' => $resData['startTime'],
                'end_time' => $resData['endTime'],
                'coupon_status' => $resData['status'],
                'product_id' => $resData['productItemList'][0]['productId'],
                'product_name' => $resData['productItemList'][0]['productName'],
                //'third_party_goods_id' => $resData['productItemList']['thirdPartyGoodsId'],
                'merchant_shop' => $resData['productItemList'][0]['merchantShop'],
                //'total_amount' => $resData['productItemList'][0]['totalAmount'],
            ];
        } catch (\Exception $e){
            $this->msg = $e->getMessage();
            return false;
        }
        echo 55;
        return $insertData;
    }

    public function actionCoupondetail()
    {
        $coupon = Yii::$app->request->get('code');
        $shop = Yii::$app->request->get('shop');
        $pangAn = new Pinganhcz();
        $res = $pangAn->couponDetail($coupon,$shop);

        return $res;
    }


    /**
     * 盛大接口回调平安核销信息
     * @return array
     */
    public function actionNotice()
    {
        $pingAn = new Pinganhcz();
        $postParam = $pingAn->postRequest();
        $responseData = [];
        try {
            if(!$postParam){
                $this->status = 1004;
                throw new \Exception('sign验证错误');
            }
            if(!$pingAn->access_token){
                $this->status = 1004;
                throw new \Exception('access_token获取失败');
            }
            $couponDetail = $this->couponDetail($postParam['couponNo'],$postParam['storeName']);
            if(!$couponDetail){
                throw new \Exception($this->msg);
            }
            if($couponDetail['status'] !=0 ){
                $this->status = 1002;
                throw new \Exception('兑换码已经核销过');
            }
            //平安核销
            echo json_encode($couponDetail);
            die();
            $result = $pingAn->redemption($postParam,$couponDetail);

            $result = json_decode($result,true);
            if($result['data']['code'] != 200){
                $this->status = 1003;
                throw new \Exception($result['data']['message']);
            }
            if($result['data']['data']['status'] != 1){
                $this->status = 1004;
                throw new \Exception($result['data']['data']['messages']);
            }
            //将核销时间格式转化成时间戳
            $verifytime=strtotime($postParam['verifytime']);
            $insertData = [
                'store_name'=> $postParam['storeName'],
                'store_id'=> $postParam['storeId'],
                'address'=> $postParam['address'],
                'province'=> $postParam['province'],
                'city'=> $postParam['city'],
                'district'=> $postParam['district'],
                'verifytime'=> $verifytime,
                'date_month'=> date('Y-m',$verifytime),
                'date_day'=> date('d',$verifytime),
                'partner_order'=> $postParam['partnerOrder'],
                'coupon_status' => $result['data']['data']['status'],
                'order_id' => $result['data']['data']['orderId'],
                'company_id' => $couponDetail['company_id'],
                'status' => 2,
                'c_time' => time(),
                's_time' => time()
            ];
            $insertData = array_merge($couponDetail,$insertData);
            $washObj = new Car_wash_pinganhcz();
            $updateInfo = $washObj->myInsert($insertData);
            if(!$updateInfo){
                $this->status = 1004;
                throw new \Exception('核销成功，数据更新失败');
            }
            $responseData = [
                'couponId' => $insertData['coupon_id'],
                'couponName' => $insertData['coupon_name'],
                'productId' => $insertData['product_id'],
                'productName' => $insertData['product_name'],
                'mobileNo' => $insertData['mobile'],
                'status' => $insertData['coupon_status'],
            ];
        } catch (\Exception $e){
            $this->msg = $e->getMessage();
        }
        $data = json_encode($postParam,JSON_UNESCAPED_UNICODE);
        $resData = json_encode($result,JSON_UNESCAPED_UNICODE);
        $pingAn->log($this->status,$this->msg,$data,$resData);
        $this->log(1,\GuzzleHttp\json_encode($responseData));

        return $this->json($this->status, $this->msg,$responseData);
    }

    /**
     * 核銷时冻结平安卡券
     * @return array
     */
    public function actionFreezecoupon()
    {
        $pingAn = new Pinganhcz();
        $data = $pingAn->getRequest();
        if($data == false){
            return 'sign验证错误';
        }
        $res = $pingAn->freezeCoupon($data['redemptionCode'],$data['productId']);
        $pingAn->log($res);

        return $pingAn->json_pingan($res);
    }

    /**
     * 核销成功后解除冻结平安卡券
     * @return array|string
     */
    public function actionUnfreezecoupon()
    {
        $pingAn = new Pinganhcz();
        $data = $pingAn->getRequest();
        if($data == false){
            return 'sign验证错误';
        }

        $res = $pingAn->unFreezeCoupon($data['redemptionCode']);
        $this->log($res);

        return $this->json_pingan($res);
    }

    /**
     * 测试
     * @return array|string
     */
    public function actionTest()
    {
        $coupon = Yii::$app->request->get('code');
        $shopid = Yii::$app->request->get('shopid');
        $pingAn = new Pinganhcz();
        $res = $pingAn->couponDetail($coupon,$shopid);
        var_dump($res);
    }


}