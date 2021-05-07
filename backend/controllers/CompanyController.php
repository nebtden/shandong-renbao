<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/12 0012
 * Time: 上午 10:38
 */

namespace backend\controllers;
use backend\util\BController;
use yii;
use common\components\W;
use yii\helpers\Url;
use common\components\CExcel;
use common\models\VideoCode;
use common\models\VideoCompany;

use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

class CompanyController extends BController {


    //公司管理
    function actionCompanylist() {
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $companyModel=new VideoCompany();
            $where='';
            $name = $request->get('name',null);
            if(!empty($name))$where=' `name` LIKE "%'.trim($name).'%" ';
            $res=$companyModel->page_list('*',$where, 'id DESC');
            $listrows=$res['rows'];

            foreach ($res['rows'] as $k => $val) {
                $listrows[$k]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$k]['background_pic'] = "<img width='100px' height=''  src='" . $val['background_pic'] . "'/>";
            }
            $res['rows']=$listrows;
            return json_encode($res);
        }
        return $this->render('companylist');
    }
    function actionCompanyedit() {
        $request = Yii::$app->request;
        $companyModel=new VideoCompany();
        $companyid=intval($request->get('id'));
        if($companyid)$info=$companyModel->table()->select()->where(['id'=>$companyid])->one();
        if($request->isPost) {
            $data = $request->post();
            $time = time();
            if(empty($data['name']))return '请填写公司名称';
            $dbdata=[];
            $dbdata['name']=trim($data['name']);
            $dbdata['background_pic']=$data['background_pic'];
            $dbdata['webpage_title']=trim($data['webpage_title']);
            $dbdata['c_time'] = $time;

            if(empty($data['id'])){
                if($companyModel->table()->select()->where(['name'=>$data['name']])->one())return '公司名称重复';
                $re=$companyModel->myInsert($dbdata);
            }else{
                $id=$data['id'];
                unset($data['id']);
                $re=$companyModel->myUpdate($dbdata,['id'=>$id]);
            }
            $this->redirect('companylist.html');
        }

        return $this->render('companyedit',['info'=>$info]);
    }
}