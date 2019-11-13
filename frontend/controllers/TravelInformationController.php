<?php

namespace frontend\controllers;

use common\models\TravelCompany;
use common\models\TravelListDate;
use common\models\TravelData;
use common\models\TravelUsers;
use common\models\TravelUsersLocked;
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


        $user_id = Yii::$app->session['travel_user_id'];
        $id = Yii::$app->session['travel_list_id'];

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

    //人数总结
//    public function actionLocked(){
//
//
//        $request = Yii::$app->request;
//
//        $travel_user_id = Yii::$app->session['travel_user_id'];
//        $travel_list_id = Yii::$app->session['travel_list_id'];
//        $travel_date_id = Yii::$app->session['travel_date_id'];
//        $locked = new TravelUsersLocked();
//        $locked->travel_user_id = $travel_user_id;
//        $locked->travel_list_id = $travel_list_id;
//        $locked->travel_date_id = $travel_date_id;
//        $locked->number = $number =  $request->post('number');
//        $locked->ctime = time();
//        $res = $locked->save();
//
//        Yii::$app->session['travel_locked'] = $number;
//
//        //将总数写入执行
//        $date = TravelListDate::find()->where([
//            'id'=>$travel_date_id
//        ])->one();
//        $date->locked =  $date->locked+$number;
//        $date->save();
//
//
//        if($res){
//            //锁定人数-1
//            return $this->json(1, '添加成功！请继续添加',['id'=>$locked->id]);
//
//        }else{
//            //添加失败，可以不考虑
//            return $this->json(-1, '添加失败，请联系管理员！');
//
//        }
//    }


    //写入数据库
    public function actionAdd(){

        $travel_user_id = Yii::$app->session['travel_user_id'];
        if(!$travel_user_id){
            return $this->json(-1, '添加失败，请联系管理员！');
        }

        //查询机构
        $travel_user = TravelUsers::findOne($travel_user_id);
        $travel_user_company_id  = $travel_user->organ_id;

        //查询父机构
        $company = TravelCompany::findOne($travel_user_company_id);
        $travel_user_pcompany_id = $company->pid;



        $request = Yii::$app->request;

        $data = new TravelData();
        $data->sex = $request->post('sex');
        $data->code = $request->post('code');
        $data->name = $request->post('name');
        $data->mobile = $request->post('mobile');
        $data->ctime = time();

        $data->travel_date_id = Yii::$app->session['travel_date_id'];
        $data->travel_user_id = $travel_user_id;
        $data->company_id = $travel_user_company_id;
        $data->company_pid  = $travel_user_pcompany_id;
        $data->travel_user_id = $travel_user_id;
        $data->travel_list_id = Yii::$app->session['travel_list_id'];
        $data->remark = $request->post('remark');

        //根据id，查找date
        $date = TravelListDate::find()->where([
            'id'=>Yii::$app->session['travel_date_id']
        ])->one();

        $data->travel_date = $date->date;


        $res = $data->save();
        if($res){
            //锁定人数-1
            $date = TravelListDate::find()->where([
                'id'=>Yii::$app->session['travel_date_id']
            ])->one();
            $locked=$date->locked-1;
            if($locked>0){
                $date->locked =  $date->locked-1;
                $date->save();
            }

            //


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
        $request = Yii::$app->request;
        $id = $request->get('id');


        return $this->render('submit',[
            'id'=>$id
        ]);
    }


    /**
     * @return string 成功信息
     */
    public function actionSuccess(){
        $is_empty = false;
        $user_id = Yii::$app->session['travel_user_id'];
        $request = Yii::$app->request;

        if(!$user_id){
            $is_empty = true;
        }

        $id = $request->get('id');
        if(!$id){

            $travel_date_id = Yii::$app->session['travel_date_id'];
            $list = TravelData::find()->where([
                'travel_user_id'=>$user_id,
                'travel_date_id'=>$travel_date_id
            ])->asArray()->all();
        }else{
            $list = TravelData::find()->where([
                'travel_user_id'=>$user_id,
                'travel_list_id'=>$id
            ])->asArray()->all();
        }


        return $this->render('success',[
            'list'=>$list,
            'is_empty'=>$is_empty,
        ]);
    }




}
