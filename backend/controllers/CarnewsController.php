<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6 0006
 * Time: 上午 9:05
 */
namespace backend\controllers;

use backend\util\BController;
use common\models\CarNews;
use yii;
use yii\helpers\Url;


class CarnewsController extends BController {




    public function actionNewslist() {

        if (Yii::$app->request->isAjax) {
            $newsModel = new CarNews();
            $where = ['status'=>1];
            $res=$newsModel->page_list('*',$where, 'sort asc');
            $status=CarNews::$status;
            $newslist=$res['rows'];
            foreach ($newslist as $key=>$val){
                $newslist[$key]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $newslist[$key]['status']=$status[$val['status']];

            }
            $res['rows']=$newslist;
            return json_encode($res);
        }
        return      $this->render ('newslist');
    }

    public function actionNewsedit() {

        $id = $_REQUEST['id'] ? intval($_REQUEST['id']) : "";
        $newsModel = new CarNews();
        $newsinfo = $newsModel->table()->select("*")->where(['id'=>intval($id)])->one();
        if (Yii::$app->request->isPost) {
            $data=Yii::$app->request->post();
            if(empty($data['title']) || empty($data['short_desc']) || empty($data['content'])) return '数据不全';
            $time=time();

            $arr = [
                'title' => $data['title'],
                'short_desc' => $data['short_desc'],
                'content' => $data['content'],
                'sort' => intval($data['sort']),
                'img' => $data['news_img'],
                'c_time'=>$time
            ];

            if (!empty($_POST['id'])) {
                $arr['u_time']=$time;
                $newsModel->myUpdate($arr, ['id'=> $_POST['id']]);
            } else {
                $newsModel->myInsert($arr);
            }
            $this->redirect('newslist.html');
        }
        return      $this->render ('newsedit',['news'=>$newsinfo]);
    }

    public function actionDellist() {
        $ids =  Yii::$app->request->get('params');
        $list = new CarNews();
        $arr = ['status' => 0];
        $list->myUpdate($arr, ['id'=>$ids]);
        exit;
    }


}