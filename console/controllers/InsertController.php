<?php
/**
 * Created by PhpStorm.
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;

use common\components\EDaiJia;
use common\components\Eddriving;
use common\models\Car_paternalor;
use common\models\CarCouponPackage;
use common\models\CarOilor;
use common\models\CarSubstituteDriving;
use common\models\FansAccount;
use common\models\Redis;
use yii\console\Controller;
use common\components\CarCouponAction;
use Yii;

/**

 * @package console\controllers
 */
class InsertController extends Controller
{

    /**
     * 订单更新
     */
    public function actionIndex(){
        $file = Yii::$app->getBasePath().'/web/log/orders.txt';

        $file_readlines = file($file);
        $obj = new Car_paternalor();

        foreach ($file_readlines as $key=>$value){

            $temp_data = explode(',',$value);
            $uid = $temp_data[2];
            $coupon_id = $temp_data[0];
            $coupon_amount = 100;
            $card_no = $temp_data[5];

            $order_no = $temp_data[3];

            $data = [
                'order_no' => $order_no,
                'uid' => $uid,
                'type' => 5,
                'coupon_id' => $coupon_id,
                'coupon_amount' => $coupon_amount,
                'c_time' => 1542297600
            ];
            $id = $obj->myInsert($data);


            $data = [
                'uid' => $uid,
                'coupon_id' => $coupon_id,
                'm_id' => $id,
                'orderid' => $order_no,
                'card_no' => $card_no,
                'card_type' => 2,//中石化
                'amount' => 100,
                'status' => 2,
                'itemid' => 19408,
                'date_day' => '20181116',
                'date_month' => date('Ym'),
                'c_time' => 1542297600,
                's_time' => 1542297600,
                'errmsg' => '补充信息',
                'company_id' => 1,
                'companyid' => 0,
            ];
            $car_oilor = new  CarOilor();
            $result = $car_oilor->myInsert($data);
            print($id);
            print("\n");

        }
    }



}