<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8 0008
 * Time: 上午 10:22
 */

namespace backend\controllers;

use backend\util\BController;
use common\models\Car_banner;
use common\models\Car_menu;
use common\models\Car_indexad;
use yii;
use yii\helpers\Url;


class CarmenuController extends BController
{


    public function actionMenulist()
    {
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber", 1);
            $pagesize = $request->get("pageSize", 10);
            $menuModel = new Car_menu();
            $where = " status != 0 ";
            $cot = $menuModel->select("count(id) as cot", $where)->one();
            $menulist = $menuModel->select('*', $where)
                ->page($page, $pagesize)
                ->all();
            $satus = [0 => '删除', 1 => '正常'];
            foreach ($menulist as $key => $val) {
                $menulist[$key]['c_time'] = !empty($val['c_time']) ? date("Y-m-d H:i:s", $val['c_time']) : '--';
                $menulist[$key]['shop_status'] = $satus[$menulist[$key]['status']];
                $menulist[$key]['menu_img'] = "<img width='100px' height=''  src='" . $val['menu_img'] . "'/>";
            }
            $res = ['IsOk' => 1, 'total' => $cot['cot'], 'rows' => $menulist];
            return json_encode($res);
        }
        return $this->render('menulist');
    }

    public function actionMenuedit()
    {

        $id = $_REQUEST['id'] ? $_REQUEST['id'] : "";
        $menuModel = new Car_menu();
        $cates = $menuModel->getData('*', 'one', 'id=' . intval($id));
        if (Yii::$app->request->post()) {
            if (empty($_POST['menu_name']) || empty($_POST['menu_url']) || empty($_POST['menu_img'])) return $this->render('menuedit');
            $arr = array(
                'menu_name' => $_POST['menu_name'],
                'menu_url' => $_POST['menu_url'],
                'menu_img' => $_POST['menu_img'],
                'sort' => $_POST['sort'],
                'c_time' => time()
            );

            if (!empty($_POST['id'])) {
                $menuModel->upData($arr, 'id=' . $_POST['id']);
            } else {
                $menuModel->addData($arr);
            }
            $this->redirect('menulist.html');
        }
        return $this->render('menuedit', ['data' => $cates]);
    }

    public function actionDellist()
    {
        $ids = implode(',', Yii::$app->request->get('params'));
        $list = new Car_menu();
        $arr = array('status' => 0);
        $where = 'id in(' . $ids . ')';
        $list->upData($arr, $where);
        exit;
    }

    //首页推荐管理  推荐列表

    public function actionRecommendlist()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Car_indexad();
            $list = $model->page_list('*', ['status' => '1'], 'sort ASC');
            $listrows = $list['rows'];
            foreach ($listrows as $key => $val) {
                $listrows[$key]['c_time'] = !empty($val['c_time']) ? date("Y-m-d H:i:s", $val['c_time']) : '--';
                $listrows[$key]['ad_pic'] = "<img width='100px' height=''  src='" . $val['ad_pic'] . "'/>";
            }
            $list['rows'] = $listrows;
            return json_encode($list);

        }
        return $this->render('recommendlist');
    }

    public function actionRecommendedit()
    {
        $id = $_REQUEST['id'] ? $_REQUEST['id'] : "";
        $Model = new Car_indexad();
        $cates = $Model->table()->select('*')->where(['id' => intval($id)])->one();
        if (Yii::$app->request->post()) {
            if (empty($_POST['ad_title']) || empty($_POST['ad_pic']) || empty($_POST['ad_url']) ||
                empty($_POST['market_price']) || empty($_POST['discount_price'])
            ) return $this->render('recommendedit');
            $arr = array(
                'ad_title' => $_POST['ad_title'],
                'ad_pic' => $_POST['ad_pic'],
                'ad_url' => $_POST['ad_url'],
                'workoff_num' => $_POST['workoff_num'],
                'praise_rate' => $_POST['praise_rate'],
                'discount' => $_POST['discount'],
                'market_price' => $_POST['market_price'],
                'discount_price' => $_POST['discount_price'],
                'sort' => $_POST['sort']
            );

            if (!empty($id)) $arr['id'] = $id;
            $Model->edit_ad($arr);
            $this->redirect('recommendlist.html');
        }
        return $this->render('recommendedit', ['data' => $cates]);
    }

    public function actionDelrelist()
    {
        $ids = implode(',', Yii::$app->request->get('params'));
        $list = new Car_indexad();
        $arr = array('status' => 0);
        $where = 'id in(' . $ids . ')';
        $list->myUpdate($arr, $where);
        exit;
    }

    public function actionBannerlist()
    {
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            $page = $request->get("pageNumber", 1);
            $pagesize = $request->get("pageSize", 10);
            $bannerModel = new Car_banner();
            $where = " status != 0 ";
            $cot = $bannerModel->select("count(id) as cot", $where)->one();
            $bannerlist = $bannerModel->select('*', $where)
                ->page($page, $pagesize)
                ->all();
            foreach ($bannerlist as $key => $val) {
                $bannerlist[$key]['c_time'] = !empty($val['c_time']) ? date("Y-m-d H:i:s", $val['c_time']) : '--';
                $bannerlist[$key]['b_pic'] = "<img width='100px' height=''  src='" . $val['b_pic'] . "'/>";
            }
            $res = ['IsOk' => 1, 'total' => $cot['cot'], 'rows' => $bannerlist];
            return json_encode($res);
        }
        return $this->render('bannerlist');
    }

    public function actionBanneredit()
    {
        $time = time();
        $id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $data = Car_banner::find()->where(' id =:id', [':id' => $id])->one();
        if (Yii::$app->request->post()) {
            if (empty($_POST['b_pic']) || $_POST['sort'] <= 0)
                return $this->render('banneredit');
            $arr = array(
                'b_pic' => $_POST['b_pic'],
                'sort' => $_POST['sort'],
                'url' => $_POST['url']
            );
            if (!empty($id)) {
                $arr['u_time'] = $time;
                (new Car_banner())->upData($arr, 'id=' . $id);
            } else {
                $arr['status'] = 1;
                $arr['c_time'] = $time;
                $arr['u_time'] = $time;
                (new Car_banner())->addData($arr);
            }
            $this->redirect('bannerlist.html');
        }
        return $this->render('banneredit', ['data' => $data]);
    }

    public function actionBannerdel()
    {
        $ids = implode(',', Yii::$app->request->get('params'));
        $list = new Car_banner();
        $arr = array('status' => 0);
        $where = 'id in(' . $ids . ')';
        $list->upData($arr, $where);
        exit;
    }
}