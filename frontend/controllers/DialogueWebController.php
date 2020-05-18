<?php
namespace frontend\controllers;

use common\models\DialogueWeb;
use Yii;
use frontend\util\PController;
use common\components\W;


class DialogueWebController extends PController {



    public function actionIndex(){
        return 222;
    }

    /**
     * 验证码发送
     */
    public function actionSubmit(){


        $this->enableCsrfValidation =false;
        $request = Yii::$app->request;
        $name = $request->post('name');
        $mobile = $request->post('mobile');
        $address = $request->post('address');

//        //检测手机号码是否发送过
//        $model = DialogueWeb::find()->where([
//            'mobile'=>$mobile
//        ])->one();
//        if($model){
//            return $this->json(-1, '手机号码已经提交过！',['id'=>$car->id]);
////            return \GuzzleHttp\json_encode($return);
////        }
//
//
//        $f = $this->sendSms($mobile,1);
//        if (!$f) return $this->json(0, '验证码发送失败，请重试！');

        $object = new DialogueWeb();
        $object->mobile = $mobile;
        $object->name = $name;
        $object->address = $address;
        $object->ctime = time();
        $res = $object->save();
        if(!$res){
            return 00;
        }


        return $this->json(1, 'ok');
    }




}