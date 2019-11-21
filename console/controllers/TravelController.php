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
use common\models\CarCouponPackage;
use common\models\CarSubstituteDriving;
use common\models\FansAccount;
use common\models\Redis;
use common\models\TravelListDate;
use common\models\TravelUsersLocked;
use yii\console\Controller;
use common\components\CarCouponAction;
use Yii;

/**
 * @package console\controllers
 */
class TravelController extends Controller
{

    public function actionLose(){
        $e = new EDaiJia();
        $date_model = new TravelListDate();
        $time = time();

        TravelUsersLocked::updateAll(['number'=>0],
            "ctime <$time-60*30 and number >0"
        );

        $lockeds = TravelUsersLocked::find()->select(['sum(number) as num','travel_date_id'])->where([
            '>=','number',0
        ])
            ->groupBy('travel_date_id')->asArray()->all();
        print_r($lockeds);

        foreach($lockeds as $lose){
            print_r('$lose');
            print_r($lose);
            print_r('travel_date_id');
            print_r($lose['travel_date_id']);
            $date = TravelListDate::findOne($lose['travel_date_id']);
            print_r('$date');
//            print_r($date);
            $date->locked = $lose['num'];
            print_r($lose['num']);
            $date->save();

        }
    }


}