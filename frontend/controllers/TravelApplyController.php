<?php

namespace frontend\controllers;



use Yii;
use frontend\util\PController;
use common\models\TravelUsers;
use common\models\TravelCompany;
use yii\helpers\Url;

class TravelApplyController extends PController
{

    public $site_title = '太平洋保险';

    public $layout = 'travelpublic';


    /**
     * 报名首页
     * */
    public function actionIndex(){
        //显示一级机构
        $model = new TravelCompany();
        $organ  = $model->select('*',['pid'=>0])->all();
        $mechanism = $model->select('*',['pid'=>1])->all();
        return $this->render('index',['organ'=>$organ,'mechanism'=>$mechanism]);
    }
    public function actionMechanism(){
        $request = Yii::$app->request;
        if($request->isPost) {
            $model = new TravelCompany();
            $company_id = intval($request->post('opt1'));
            $data['pid'] = $company_id;
            $res  = $model->select('*',$data)->all();
            if(empty($res)) return $this->json(0,'系统错误');
            return $this->json(1,'操作成功',$res);
        }
    }

    /**
     * @return string
     * 请传递id
     */
    public function actionAdd(){
        return $this->render('add',[

        ]);
    }
    /**
     * @return string
     * 请传递id
     */
    public function actionLogin(){
        $request = Yii::$app->request;
        if($request->isPost) {
            $model = new TravelUsers();
            $company_id = intval($request->post('opt1'));
            $organ_id = intval($request->post('opt2'));
            $code = trim($request->post('opt3'));
            $name = trim($request->post('opt4'));
            $data['organ_id'] = $organ_id;
            $data['code'] = $code;
            $data['name'] = $name;
            $res  = $model->select('*',$data)->one();
            if(empty($res)) return $this->json(0,'信息填写错误',$data);
            $url= Url::to(['travel-apply/add']);
            return $this->json(1,'操作成功',$data,$url);
        }
    }
    
}
