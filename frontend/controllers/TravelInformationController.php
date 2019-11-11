<?php

namespace frontend\controllers;

use common\models\TravelListDate;
use common\models\TravelData;
use frontend\util\PController;
use Yii;


class TravelInformationController extends PController
{

    public $site_title = '云车驾到';

    public $layout = 'travelpublic';
    /*
     * 首页
     * */
    public function actionIndex(){

        $request = Yii::$app->request;
        $id = $request->get('id',1);
//        Yii::$app->session['travel_user_id'] = 1;

        $user_id = Yii::$app->session['travel_user_id'];
        Yii::$app->session['travel_list_id'] = $id;
        if(!$user_id){
            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/travel-list/index.html?id=$id";
            header("Location:$url ");
            exit;
        }

        //日期选择

        $dates = TravelListDate::find()->where([
            'travel_list_id'=>$id
        ])->asArray()->all();

        return $this->render('index',[
            'dates'=>$dates
        ]);
    }


    //写入数据库
    public function actionAdd(){

        $travel_user_id = Yii::$app->session['travel_user_id'];
        if(!$travel_user_id){
            return $this->json(-1, '添加失败，请联系管理员！');
        }

        $request = Yii::$app->request;

        $data = new TravelData();
        $data->sex = $request->post('sex');
        $data->code = $request->post('code');
        $data->name = $request->post('name');
        $data->mobile = $request->post('mobile');
        $data->travel_date = $request->post('date');
        $data->travel_user_id = $travel_user_id;
        $data->travel_list_id = Yii::$app->session['travel_list_id'];
        $data->remark = $request->post('remark');


        $res = $data->save();
        if($res){
            $locked = Yii::$app->session['travel_locked'] = Yii::$app->session['travel_locked']-1;
            if($locked==0){
                //检测是否有写入
                return $this->json(2, '恭喜添加成功！点击查看',['id'=>$data->id]);
            }else{
                //检测是否有写入
                return $this->json(1, '添加成功！请继续添加',['id'=>$data->id]);
            }

        }else{
            //添加失败，可以不考虑
            return $this->json(-1, '添加失败，请联系管理员！');

        }

    }

    public function actionSubmit(){
        return $this->render('submit',[

        ]);
    }

    /**
     * @return string 成功信息
     */
    public function actionSuccess(){

        $user_id = Yii::$app->session['travel_user_id'];
        $list = TravelData::find()->where([
            'travel_user_id'=>$user_id
        ])->asArray()->all();

        return $this->render('success',[
            'list'=>$list
        ]);
    }




}
