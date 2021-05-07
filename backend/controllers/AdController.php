<?php

namespace backend\controllers;

use backend\util\BController;
use common\models\Base_model;
use common\models\Ad_list;
use common\models\Ad_location;
use yii;
use yii\helpers\Url;
use yii\web\UrlRule;
use yii\web\UrlRuleInterface;

class AdController extends BController {

    public function actionLocation() {
        $cates = array('', '轮播广告', '普通图片', '文字连接');
        if (Yii::$app->request->isAjax) {
            $location = new Ad_location();
            $pageSize = intval($_GET ['limit']) ? intval($_GET ['limit']) : 10;
            $offset = intval($_GET ['pageNumber']) > 1 ? intval($_GET ['pageNumber']) - 1 : 0;
            $order = "id asc";
            $limit = $offset * $pageSize . ',' . $pageSize;
            $data = $location->getData('*', 'all', 'status=1', $order, $limit);
            foreach ($data as $k => $v) {
                $data[$k]['show'] = ($v['show'] == 1) ? '<font color="red">开启</font>' : '<font color="green">关闭</font>';
                $data[$k]['type'] = $cates[$v['type']];
                $data[$k]['typeid'] = $v['type'];
                $data[$k]['script'] = "<input type='text'  style='width:240px' value='<script src=\"http://" . $_SERVER[HTTP_HOST] . '/frontend/web/ad/index.html?id=' . $v['id'] . "\"></script>'>";
                // $data[$k]['script']="<input type='text'  style='width:240px' value='<script src=\"".yii\web\UrlRule::createUrl(Yii::$app->urlManager, 'ad/index', ['id'=>$v['id']])."\"></script>'>";
            }
            $cot = $location->getData('count(*) cot', 'one', 'status=1');
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode($data) . ' }';
            exit;
        }
        return $this->render('location');
    }

    public function actionDellocation() {
        $ids = implode(',', Yii::$app->request->get('params'));
        $location = new Ad_location();
        $arr = array('status' => 0);
        $where = 'id in(' . $ids . ')';
        $location->upData($arr, $where);
        exit;
    }

    public function actionEditlocation() {
        $id = $_REQUEST['id'] ? $_REQUEST['id'] : "";
        $location = new Ad_location();
        $data = array();
        if ($id) {
            $data = $location->getData('*', 'one', 'id=' . $id);
        }
        $cates = array('', '轮播广告', '普通图片', '文字连接');
        if (Yii::$app->request->post()) {
            $arr = array(
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'height' => $_POST['height'],
                'width' => $_POST['width'],
                'show' => $_POST['show']
            );
            if (!empty($_POST['id'])) {
                $location->upData($arr, 'id=' . $_POST['id']);
            } else {
                $location->addData($arr);
            }
            $this->redirect('location.html');
        }
        return $this->render('editlocation', array('cates' => $cates, 'data' => $data));
    }

    public function actionList() {
        if (Yii::$app->request->isAjax) {
            $location = new Ad_location();
            $categorys = $location::Categorys();
            $list = new Ad_list();
            $pageSize = intval($_GET ['limit']) ? intval($_GET ['limit']) : 10;
            $offset = intval($_GET ['pageNumber']) > 1 ? intval($_GET ['pageNumber']) - 1 : 0;
            $order = "id asc";
            $limit = $offset * $pageSize . ',' . $pageSize;
            $location = new Ad_location();
            $where = 'status=1';
            if (!empty($_REQUEST['nid'])) {
                $where .= ' and nid=' . $_REQUEST['nid'];
            }
            $data = $list->getData('*', 'all', $where, $order, $limit);
            foreach ($data as $k => $v) {
                $data[$k]['picurl'] = "<img width='100px' height=''  src='" . $v['picurl'] . "'/>";
                if ($v['nid'] != 0) {
                    $data[$k]['catname'] = $categorys[$v['nid']];
                }
            }
            $cot = $list->getData('count(*) cot', 'one', 'status=1');
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode($data) . ' }';
            exit;
        }
        return $this->render('list');
    }

    public function actionDellist() {
        $ids = implode(',', Yii::$app->request->get('params'));
        $list = new Ad_list();
        $arr = array('status' => 0);
        $where = 'id in(' . $ids . ')';
        $list->upData($arr, $where);
        exit;
    }

    public function actionEditlist() {
        $id = $_REQUEST['id'] ? $_REQUEST['id'] : "";
        $list = new Ad_list();
        $location = new Ad_location();
        $data = array();
        $cates = $location->getData('*', 'all', 'status=1 ');
        if ($id) {
            $data = $list->getData('*', 'one', 'id=' . $id);
        }
        if (Yii::$app->request->post()) {
            $arr = array(
                'title' => $_POST['title'],
                'nid' => $_POST['nid'],
                'picurl' => $_POST['picurl'],
                'url' => $_POST['url']
            );

            if (!empty($_POST['id'])) {
                $list->upData($arr, 'id=' . $_POST['id']);
            } else {
                $this->redirect('list.html?nid=' . $_POST['nid'] . '&type=' . $_REQUEST['type']);
                $list->addData($arr);
            }
            $this->redirect('list.html?nid=' . $_REQUEST['nid'] . '&type=' . $_REQUEST['type']);
        }
        return $this->render('editlist', array('data' => $data, 'cates' => $cates));
    }

}
