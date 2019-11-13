<?php

namespace frontend\controllers;



use common\models\TravelUsersLocked;
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
        $filde = ['id','name','pid'];
        $organ  = $model->select($filde)->all();
        $mechanism = $model->select('*',['pid'=>1])->all();
        $info = [];
        foreach ($organ as $key=>$val){
            if($val['pid'] == 0){
                $info[$val['id']]['id'] =$val['id'];
                $info[$val['id']]['value'] =$val['name'];
            }else{
                $info[$val['pid']]['childs'][$val['id']]['id'] =$val['id'];
                $info[$val['pid']]['childs'][$val['id']]['value'] =$val['name'];
            }
        }
        $i = 0;
        $arr = [];
        foreach ($info as $key=>$val){
            $arr[$i]['id'] = $val['id'];
            $arr[$i]['value'] =$val['value'];
            $temp = $val['childs'];
            $temp_1 = [];
            $j = 0;
            foreach($temp as $k=>$v ){
                $temp_1[$j]['id'] = $v['id'];
                $temp_1[$j]['value'] = $v['value'];
                $j++;
            }
            $arr[$i]['childs'] = $temp_1;
            $i++;
        }


        $arr = json_encode($arr);
        return $this->render('index',['info'=>$arr,'mechanism'=>$mechanism,'id'=>$id]);
    }

    public function actionMechanism(){
        $request = Yii::$app->request;
        if($request->isPost) {
            $model = new TravelCompany();
            $name = $request->post('name');
            $data['name'] = $name;
            $result  = $model->select('id',$data)->one();
            $where['pid'] = $result['id'];
            $res  = $model->select('*',$where)->all();
            $res  =  array_column($res,'name');
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
//        Yii::$app->session['travel_list_id'] = $list_id;
        $dateModel = new TravelListDate();
        $info = $dateModel ->select('*',['travel_list_id'=>$list_id])->all();

        if($request->isPost) {
            $list_id = intval($request->post('list_id'));
            $date_id = intval($request->post('date_id'));

            $res = $dateModel ->select('*',['travel_list_id'=>$list_id,'id'=>$date_id])->one();
            if(empty($res)) return $this->json(0,'信息填写错误');

            Yii::$app->session['travel_date_id']  =  $request->post('date_id');

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
        $dateModel = new TravelListDate();
        $numModel = new TravelData();

        if($request->isPost) {

            $travel_date_id = intval($request->post('date_id'));
            $travel_list_id = intval($request->post('list_id'));

            Yii::$app->session['travel_user_id'] = $travel_list_id;
            $number   = intval($request->post('num'));
            $travel_user_id = Yii::$app->session['travel_user_id'];

            $pdate_id = intval($request->post('date_id'));
            $plist_id = intval($request->post('list_id'));
            $pinfo = $dateModel ->select('*',['id'=>$pdate_id])->one();
            $pnum = $numModel ->select('*',['travel_date_id'=>$pdate_id])->count();
            $sum = $pinfo['number'] - $pinfo['locked']- $pnum;
            if($number < 2) return $this->json(0,'报名人数至少2人');
            if($number  > $sum) return $this->json(0,'人数过多');
            $total_number = $pinfo['locked'] + $number;

            $locked = new TravelUsersLocked();
            $locked->travel_user_id = $travel_user_id;
            $locked->travel_list_id = $travel_list_id;
            $locked->travel_date_id = $travel_date_id;
            $locked->number = $number;
            $locked->ctime = time();
            $res = $locked->save();

            Yii::$app->session['travel_locked'] = $number;

            $res = (new TravelListDate())->myUpdate(['locked'=>$total_number],['id'=>$pdate_id]);
            if(empty($res)) return $this->json(0,'报名失败');
            $url= Url::to(['travel-information/index','date_id'=>$pdate_id,'list_id'=>$plist_id]);
            return $this->json(1,'操作成功',[],$url);
        }
        $request = Yii::$app->request;
        $list_id = intval($request->get('list_id'));
        $date_id = intval($request->get('date_id'));
        Yii::$app->session['travel_date_id'] = $date_id;

        $info = $dateModel ->select('*',['id'=>$date_id])->one();
        $num = $numModel ->select('*',['travel_date_id'=>$date_id])->count();
        $sum = $info['number'] - $info['locked']-$num;

        return $this->render('add',[ 'sum'=>$sum,'date_id'=>$date_id,'list_id'=>$list_id, 'locked'=>$info['locked']?$info['locked']:0]);
    }
    /**
     * @return string
     * 请传递id
     */
    public function actionLogin(){
        $request = Yii::$app->request;
        if($request->isPost) {
            $model = new TravelUsers();
            $company_name = $request->post('opt1');
            $organ_name = $request->post('opt2');
            $code = trim($request->post('opt3'));
            $name = trim($request->post('opt4'));
            $res = (new TravelCompany()) ->select('id',['name'=>$organ_name])->one();
            $data['organ_id'] = $res['id'];
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
