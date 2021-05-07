<?php
/**
 * 车后加系统正式环境
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;


use common\components\CarCouponAction;
use common\components\DianDianOilCard;
use common\components\Oilcard;
use common\models\CarOilor;
use common\models\Redis;
use yii\console\Controller;


/**
 * @package console\controllers
 */
class ChehoujiaController extends Controller
{

    public function actionBuy(){

        $model = new CarOilor();
        $list = $model->table()->where("company_id=1 and status<=0")->all();
        $oilObj = new Oilcard();
        foreach($list as $order_info){
            try{
                sleep(5);
                //充值
                $itemid   = $order_info['itemid'];
                $card_no  = $order_info['card_no'];
                $orderid  = $order_info['orderid'];


                $res = $oilObj->buy($itemid, $card_no, $orderid);

                $update = [];
                if ($res === false) {
                    throw new \Exception('服务商未响应');
                } else {
                    if($res['code']=='00') {
                        $update['status'] = 1;
                    } elseif ($res['code'] == '31'  or $res['code']=='23') {
                        $update['status'] = 9;
                    } else {
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


    public function actionReset(){


    }



}