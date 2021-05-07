<?php

namespace backend\controllers;

use common\models\News;
use common\models\News_category;
use Yii;
use backend\util\BController;
use common\components\W;
use yii\helpers\Url;

class NewsController extends BController {

    public function actionIndex() {
        return $this->render('index', array('cur' => 1));
    }

    public function actionList() {

        $this->layout = 'fcontent';
        if (Yii::$app->request->isAjax) {
            $pageSize = intval($_GET ['limit']) ? intval($_GET ['limit']) : 10;
            $offset = intval($_GET ['pageNumber']) > 1 ? intval($_GET ['pageNumber']) - 1 : 0;
            $newsModel = new News();
            $cateArr = News_category::getCategroys(Yii::$app->session ['token']);
            $cate = $_GET ['id'];
            $keyword = $_GET ['uname'];
            $where = " status=1 and `token`='" . Yii::$app->session ['token'] . "'";
            if ($cate)
                $where .= " and `cate_id`=$cate";
            if ($keyword)
                $where .= " and `title` like '%" . $keyword . "%'";
            $order = "id desc";
            $limit = $offset * $pageSize . ',' . $pageSize;
            $news = $newsModel->getData('*', 'all', $where, $order, $limit);
            foreach ($news as $k => $v) {
                $news[$k] ['categroy'] = $cateArr [$v ['cate_id']];
                $news [$k] ['img'] = '<img src="' . W::dealPic($v['pic']) . '" width="150"  >';
            }

            $cot = $newsModel->getData('count(*) cot', 'one', $where);
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode($news) . ' }';
            exit();
        }

        $cateModel = new News_category();
        $cates = $cateModel->readCates(Yii::$app->session ['token']);
        $catArr = array();
        foreach ($cates as $v) {
            $catArr [$v ['pid']] [] = $v;
        }

        return $this->render('list', array('cur' => 1, 'cates' => $catArr));
    }

    public function actionCategory() {
        $this->layout = 'fcontent';

        if (Yii::$app->request->isAjax) {
            $pageSize = intval($_GET ['limit']) ? intval($_GET ['limit']) : 10;
            $offset = intval($_GET ['pageNumber']) > 1 ? intval($_GET ['pageNumber']) - 1 : 0;
            $limit = $offset * $pageSize . ',' . $pageSize;
            $order = "id desc";
            $where = " status=1 and `token`='" . Yii::$app->session ['token'] . "'";
            $cateModel = new News_category();
            $cates = $cateModel->getData('*', 'all', $where, $order, $limit);
            $cot = $cateModel->getData('count(*) cot', 'one', $where);
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode($cates) . ' }';
            exit();
        }

        return $this->render('category', array('cur' => 1));
    }

    //
    public function actionCategory_edit() {
        $this->layout = 'fcontent';
        $id = intval($_REQUEST ['id']);
        $where = '';
        $cateModel = new News_category();
        if ($id) {
            $where = "id=" . $id;
            $category = $cateModel->getData('*', 'one', $where);
        }
        if (Yii::$app->request->post()) {
            if ($_POST['act'] == 'edit') {
                $id = $_POST ['id'];
                unset($_POST ['id']);
                unset($_POST['act']);
                unset($_POST['file']);
                $where = 'id=' . $id;

                $flag = $cateModel->upData($_POST, $where);
            } else {
                unset($_POST ['id']);
                unset($_POST ['file']);
                unset($_POST['act']);
                $_POST ['status'] = 1;
                $_POST ['token'] = Yii::$app->session ['token'];
                $flag = $cateModel->addData($_POST);
            }

            $this->redirect('category.html');
        }
        $cates = $cateModel->readCates(Yii::$app->session ['token']);
        $catArr = array();
        foreach ($cates as $v) {
            $catArr [$v ['pid']] [] = $v;
        }
        return $this->render('category_edit', array(
                    'cates' => $catArr,
                    'category' => $category,
                ));
    }

    //资讯删除
    public function actionNews_del() {
        $id = implode(',', Yii::$app->request->get('params'));
        $where = '';
        $newsModel = new News();
        if ($id) {
            $where = "id in ($id)";
            $news = $newsModel->getData('*', 'all', $where);
            if ($news) {
                $newsModel->upData(array(
                    'status' => 0
                        ), $where);
            }
        }
    }

    public function actionCategory_del() {
        $ids = implode(',', Yii::$app->request->get('params'));
        $categoryModel = new News_category();
        $arr = array('status' => 0);
        $where = 'id in(' . $ids . ')';
        $categoryModel->upData($arr, $where);
        exit;
    }

    public function actionNews_edit() {
        $this->layout = 'fcontent';
        // 资讯
        $newsModel = new News();
        $id = intval($_REQUEST['id']);
        $id && $news = $newsModel->getData('*', 'one', "`id`=$id");
//        var_dump($_POST);
//        die;
        if (Yii::$app->request->post()) {
            $newsdata = array(
                'token' => Yii::$app->session ['token'],
                'title' => $_POST ['title'],
                'cate_id' => $_POST ['category'],
                'pic' => W::dealPic($_POST ['pic']),
                'short_desc' => $_POST ['short_desc'],
                'content' => $_POST ['content'],
                'sort' => $_POST ['sort'],
                'shop_code' => 'platform',
                'uptime' => time(),
                'source' => $_POST['source'],
                'status' => 1
            );
            if ($news) {
                $newsId = $news ['id'];
                $newsModel->upData($newsdata, "`id`=$newsId");
            } else {
                $newsId = $newsModel->addData($newsdata);
            }
            $this->redirect(Url::to(['news/list']));
        }
        // 资讯分类
        $cateModel = new News_category();
        $cates = $cateModel->readCates(Yii::$app->session ['token']);
        $catArr = array();
        foreach ($cates as $v) {
            $catArr [$v ['pid']] [] = $v;
        }

        return $this->render('news_edit', array(
                    'cates' => $catArr,
                    'news' => $news,
                ));
    }

}
