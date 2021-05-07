<?php

namespace backend\controllers;

use backend\models\Admin;
use common\models\Group;
use Yii;
use backend\util\BController;
use common\components\W;
use yii\helpers\Url;

class AuthorityController extends BController {

    public function actionIndex() {
        return $this->render('index', array('cur' => 1));
    }

    public function actionList() {

        $this->layout = 'fcontent';
        if (Yii::$app->request->isAjax) {
            $pageSize = intval($_GET ['limit']) ? intval($_GET ['limit']) : 10;
            $offset = intval($_GET ['pageNumber']) > 1 ? intval($_GET ['pageNumber']) - 1 : 0;
            $adminModel = new Admin();
            $where = "status=1";
            $cate = $_GET ['id'];
            $keyword = $_GET ['uname'];
            if ($keyword)
                $where .= " and `username` like '%" . $keyword . "%'";
            $order = "id";
            $limit = $offset * $pageSize . ',' . $pageSize;
            $users = $adminModel->getData('*', 'all', $where, $order, $limit);
            $groupModel = new Group();
            $where = "status=1";
            $groups = $groupModel->getData('*', 'all', $where);
            $grpArr = array();
            foreach ($groups as $v) {
                $grpArr[$v['id']] = $v['group_name'];
            }
            foreach ($users as $k => $v) {
                $users[$k] ['last_login_time'] = date('Y-m-d H:i:s', $v['last_login_time']);
                $arr = explode('|', $v['group_id']);
                foreach ($arr as $val) {
                    $users[$k]['grparr'] .= $grpArr[$val] . ' , ';
                }
                $users[$k]['grparr'] = substr($users[$k]['grparr'], 0, -3);
            }

            $cot = $adminModel->getData('count(*) cot', 'one', $where);
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode($users) . ' }';
            exit();
        }


        return $this->render('list', array('cur' => 1, 'cates' => $catArr));
    }

    public function actionGroup() {
        $this->layout = 'fcontent';

        if (Yii::$app->request->isAjax) {
            $pageSize = intval($_GET ['limit']) ? intval($_GET ['limit']) : 10;
            $offset = intval($_GET ['pageNumber']) > 1 ? intval($_GET ['pageNumber']) - 1 : 0;
            $limit = $offset * $pageSize . ',' . $pageSize;
            $order = "id";
            $where = "status=1";
            $groupModel = new Group();
            $groups = $groupModel->getData('*', 'all', $where, $order, $limit);
            $cot = $groupModel->getData('count(*) cot', 'one', $where);
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode($groups) . ' }';
            exit();
        }

        return $this->render('group', array('cur' => 1));
    }

    public function actionUser_edit() {
        $this->layout = 'fcontent';
        $userModel= new Admin();
        $id = intval ( $_REQUEST['id'] );
        $id && $user = $userModel->getData ( '*', 'one', "`id`=$id" );

        if (Yii::$app->request->post()) {
            $group_id=$_REQUEST['group_id'];
            $group_id = implode('|',$group_id);
            $userdata = array (
                'group_id'=>$group_id
            );
            if ($user) {
                $userId = $user ['id'];
                $userModel->upData ( $userdata, "`id`=$userId" );
            }else{
                if(empty($id) && !empty($_REQUEST['proname'])){
                    $useradd['group_id']=$group_id;
                    $useradd['username']=$_REQUEST['proname'] ;
                    $useradd['salt']='1234';
                    $useradd['password']=md5('admin'.$useradd['salt']);
                    $useradd['telphone']='0731-98657421';
                    $useradd['mobile']='13875826522';
                    $useradd['contact']='管理员';
                    $useradd['create_time']=time();
                    $userModel->addData ($useradd);
                }

            }
            $this->redirect (Url::to(['authority/list']));
        }

        $groupModel = new Group();
        $where="status=1";
        $groups=$groupModel->getData('*','all',$where);
        $grpArr=explode('|', $user['group_id']);

        return   $this->render ( 'user_edit', array (
            'groups' => $groups,
            'user' => $user,
            'grpArr' => $grpArr,
        ) );
    }

    public function actionGroup_edit() {

        $this->layout = 'fcontent';
        $id = intval($_REQUEST ['id']);
        $groupModel = new Group();
        if ($id) {
            $where = "id=" . $id;
            $group = $groupModel->getData('*', 'one', $where);
            $modulesall = json_decode($group['modules'],true);
            $modules = array_column($modulesall,'modules');
            $subdirectory = array_column($modulesall,'subdirectory');
            $subdirectory = array_reduce($subdirectory, 'array_merge', array());
        }
        $menu = $this->menu;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            if (! empty($data['id'])) {
                $data['token'] = Yii::$app->session ['token'];
                $where = 'id=' . $data['id'];
                unset($data['id']);
                $flag = $groupModel->upData($data, $where);
                if($flag)return $this->json(1,'修改成功');
            } else {
                $data['status'] = 1;
                $data['token'] = Yii::$app->session ['token'];
                $flag = $groupModel->addData($data);
                $url = Url::to(["authority/group"]);
                if($flag)return $this->json(2,'添加成功',[],$url);
            }
            return $this->json(0,'操作失败');
        }

        return $this->render('group_edit', array('group' => $group, 'modules' => $modules,'subdirectory'=>$subdirectory,'menu' => $menu));
    }

}
