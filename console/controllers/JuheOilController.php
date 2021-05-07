<?php
/**
 * 聚合油卡充值
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;


use common\components\CarCouponAction;
use common\components\Juhe;
use common\components\Oilcard;
use common\models\CarOilor;
use common\models\FansAccount;
use common\models\Redis;
use yii\console\Controller;


/**
 * @package console\controllers
 */
class JuheOilController extends Controller
{

    public function actionBuy(){

        $model = new CarOilor();
        $list = $model->table()->where("company_id=3 and status<=0")->all();
        $juhe = new Juhe();

        foreach($list as $order_info){
            echo $order_info['id'];

            try{
//                sleep(10);


                $uid = $order_info['uid'];
                $fans_account = FansAccount::find()->where(['uid' => $uid])->one();
                $mobile = $fans_account['mobile'];

                //充值
                $data  = [];
                $data['card_type'] = $order_info['card_type'];
                $data['orderid'] = $order_info['orderid'];
                $data['game_userid'] = $order_info['card_no'];
                $data['gasCardTel']  = $mobile;


                $res = $juhe->OilerPay($data);

                $update = [];
                if ($res === false) {
                    throw new \Exception('服务商未响应');
                } else {
                    if($res['error_code']=='0') {
                        $update['status'] = 1;
                    }  else {
                        if(isset($res['code']) and $res['code']>10){
                            $update['status'] = 3;

                            //恢复优惠券
                            $r = (new CarCouponAction())->unuseCoupon($order_info['coupon_id']);
                            if ($r === false) {
                                throw new \Exception('优惠券恢复失败');
                            }
                        }


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