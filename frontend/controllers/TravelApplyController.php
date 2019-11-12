<?php

namespace frontend\controllers;



use Yii;
use frontend\util\PController;
use common\models\TravelUsers;
use common\models\TravelCompany;
use common\models\TravelList;
use common\models\TravelListDate;
use common\models\TravelData;
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
        $id = Yii::$app->request->get('id');
        $model = new TravelCompany();
        $organ  = $model->select('*',['pid'=>0])->all();
        $mechanism = $model->select('*',['pid'=>1])->all();
        return $this->render('index',['organ'=>$organ,'mechanism'=>$mechanism,'id'=>$id]);
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
    public function actionChangedate(){
        $request = Yii::$app->request;
        $list_id = intval($request->get('id'));
        Yii::$app->session['travel_list_id'] = $list_id;
        $dateModel = new TravelListDate();
        $info = $dateModel ->select('*',['travel_list_id'=>$list_id])->all();

        if($request->isPost) {
            $list_id = intval($request->post('list_id'));
            $date_id = intval($request->post('date_id'));
            $res = $dateModel ->select('*',['travel_list_id'=>$list_id,'id'=>$date_id])->one();
            if(empty($res)) return $this->json(0,'信息填写错误');
            $url= Url::to(['travel-apply/add','date_id'=>$date_id,'list_id'=>$list_id]);
            return $this->json(1,'操作成功',[],$url);
        }
        return $this->render('changedate',['info'=>$info,'list_id'=>$list_id]);
    }
    /**
     * @return string
     * 请传递id
     */
    public function actionAdd(){
        $request = Yii::$app->request;
        $list_id = intval($request->get('list_id'));
        $date_id = intval($request->get('date_id'));
        Yii::$app->session['travel_date_id'] = $date_id;
        $dateModel = new TravelListDate();
        $numModel = new TravelData();
        $info = $dateModel ->select('*',['id'=>$list_id])->one();
        $num = $numModel ->select('*',['travel_date_id'=>$date_id])->count();
        $sum = $info['number'] - $info['locked']-$num;
        if($request->isPost) {
            $date_id = intval($request->post('date_id'));
            $num = intval($request->post('num'));
            $sum = intval($request->post('sum'));
            
            if($num  > $sum) return $this->json(0,'人数过多');
            $num = $info['locked'] + $num;
            $res = (new TravelListDate())->myUpdate(['locked'=>$num],['id'=>$date_id]);
            if(empty($res)) return $this->json(0,'信息填写错误');
            $url= Url::to(['travel-information/index','date_id'=>$date_id,'list_id'=>Yii::$app->session['travel_list_id']]);
            return $this->json(1,'操作成功',[],$url);
        }

        return $this->render('add',[ 'sum'=>$sum,'date_id'=>$date_id, 'locked'=>$info['locked']?$info['locked']:0]);
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
            $res = $model ->select('*',$data)->one();
            if(empty($res)) return $this->json(0,'信息填写错误',$data);
            $model->myUpdate(['utime'=>time()],$data);
            Yii::$app->session['travel_user_id'] = $res['id'];
            $url= Url::to(['travel-apply/changedate','id'=>intval($request->post('opt5'))]);
            return $this->json(1,'操作成功',$data,$url);
        }
    }

}
