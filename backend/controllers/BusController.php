<?php
namespace backend\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\util\BController;
use backend\models\Admin;
use common\components\ValidataCode;
use common\models\Wxuser;

class BusController extends BController{

    public function actionIndex(){

        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber",1);
            $pagesize = $request->get("pageSize",10);
            $status=$_REQUEST["status"];
            $keywords=$_REQUEST["keywords"];
            $model = new Car_payee();
            $where = " {{%car_payee}}.id != 0";
            if(!empty($status) && !empty($keywords)){
                if($status == 1){
                    $where.=" and {{%car_shop}}.shop_name  like '%".$keywords."%'";
                }elseif ($status == 2){
                    $where.=" and {{%car_payee}}.payee_name  like '%".$keywords."%'";
                }
                if($status == 3){
                    $where.=" and {{%fans}}.nickname  like '%".$keywords."%'";
                }elseif ($status == 4){
                    $where.=" and {{%car_payee}}.payee_account like '%".$keywords."%'";
                }
            }
            $cot = $model->select("count({{%car_payee}}.id) as cot",$where)
                ->join('LEFT JOIN','{{%car_shop}}','{{%car_payee}}.shop_id = {{%car_shop}}.id')
                ->join('LEFT JOIN','{{%fans}}','{{%car_payee}}.uid = {{%fans}}.id')
                ->one();


            $list = $model->select('{{%car_payee}}.*,{{%car_shop}}.shop_name,{{%fans}}.nickname',$where)
                ->join('LEFT JOIN','{{%car_shop}}','{{%car_payee}}.shop_id = {{%car_shop}}.id')
                ->join('LEFT JOIN','{{%fans}}','{{%car_payee}}.uid = {{%fans}}.id')
                ->page($page,$pagesize)
                ->orderBy('{{%car_payee}}.id desc')
                ->all();
            $status=[0=>'删除',1=>'正常',2=>'禁用'];
            foreach ($list as $k=>$item) {
                $list[$k]['c_time']=date("Y-m-d H:i:s",$item['c_time']);
                $list[$k]['status']=$status[$list[$k]['status']];

            }

            $res = ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$list];
            return json_encode($res);
        }
        return      $this->render ( 'account_list' );
    }


}