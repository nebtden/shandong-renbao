<?php
/**
 * 聚合油卡充值
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;


use common\components\BangChong;
use common\components\CarCouponAction;
use common\components\Juhe;
use common\components\Oilcard;
use common\models\CarOilor;
use common\models\FansAccount;
use common\models\Redis;
use yii\console\Controller;
use Yii;


/**
 * @package console\controllers
 */
class BangChongController extends Controller
{

    public function actionBuy(){

        $model = new CarOilor();
        $list = $model->table()->where("company_id=4 and status<=0")->all();
        $bj = new BangChong();

        $parms = Yii::$app->params['bangchong'];

        foreach($list as $order_info){
            echo $order_info['id'];

            try{
                $uid = $order_info['uid'];
                $fans_account = FansAccount::find()->where(['uid' => $uid])->one();
//                $mobile = $fans_account['mobile'];

                //充值
                $data['re_url'] = $parms['notice_url'];
                $data['recharge_no'] = $order_info['card_no'];
                $data['user_order_no'] = $order_info['orderid'];
                $data['amount'] = intval($order_info['amount']);
                $data['recharge_type']  = $order_info['card_type'];

                $res = $bj->OilerPay($data);

                $update = [];
                if ($res === false) {
                    throw new \Exception('服务商未响应');
                } else {
                    if($res['code']==10000){
                        $update['status'] = 1;
                        $update['bizorderid'] = $res['result']['order_no'];
                    }else{
                        $update['status'] = -1;
                    }
                    $model->myUpdate($update, ['id' => $order_info['id']]);
                }
            }catch (\Exception $exception){
                $update = [];
                $update['status'] = -1;
                $update['errmsg'] = $exception->getMessage();
                $model->myUpdate($update, ['id' => $order_info['id']]);
            }

        }
    }





}