<?php
/**
 * Created by PhpStorm.
 * User: zhangzhen
 * Date: 2019/11/12
 * Time: 16:35
 */

namespace console\controllers;

use common\models\TravelCompany;
use common\models\TravelUsers;
use yii\console\Controller;
use Yii;

class TestController  extends Controller {

public function actionImport(){


    $inputFileName = dirname(Yii::$app->BasePath) . '/console/web/users.csv';

    $file = fopen($inputFileName,"r");

    while(! feof($file))
    {

        $line = fgets($file);
        $row = explode(',',$line);
        $name = $row[0];
        $company = TravelCompany::find()->where([
            'name'=>$name
        ])->one();
        if(!$company){
            $company = new TravelCompany();
            $company->name = $name ;
            $company->pid = 0;
            $company->save();
        }

        $son_name = $row[1];
        $son_company = TravelCompany::find()->where([
            'name'=>$son_name
        ])->one();

        //二级目录
        if(!$son_company){
            $son_company = new TravelCompany();
            $son_company->name = $son_name;
            $son_company->pid = $company->id;
            $son_company->save();
        }

        if($row[2]){
            //用户信息
            $user = new TravelUsers();
            $user->code = $row[2];
            $user->name = $row[3];
            $user->organ_id = $son_company->id;
            $user->ctime = time();
            $user->save();

            echo $row[3].' success!';
            echo "\n\r";
        }




    }

    fclose($file);


}
}