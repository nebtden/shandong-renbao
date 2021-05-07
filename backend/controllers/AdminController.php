<?php

namespace backend\controllers;

use common\models\Menu;
use Yii;
use common\models\User;
use backend\models\Admin;
use backend\util\BController;
use common\components\W;
use common\components\CExcel;


class AdminController extends BController {
	public function actionIndex() {
		//$res = CExcel::getExcel()->importExcel($this->path . "/static/upload/excel/test.xlsx");
		//var_dump($res);
		return $this->render ( 'index' );
	}
	public function actionTest() {
		if (Yii::$app->request->isAjax) {
			$pageSize = intval ( $_GET ['limit'] ) ? intval ( $_GET ['limit'] ) : 10;
			$offset = intval ( $_GET ['pageNumber'] )  > 1? intval ( $_GET ['pageNumber'] ) - 1 : 0;
			$sort = addslashes ( $_GET ['sort'] ) ? addslashes ( $_GET ['sort'] ) : 'id';
			$order = addslashes ( $_GET ['order'] ) ? addslashes ( $_GET ['order'] ) : 'desc';
			$uname = ! empty ( $_GET ['uname'] ) ? addslashes ( trim ( $_GET ['uname'] ) ) : '';
			$id = ! empty ( $_GET ['id'] ) ? addslashes ( $_GET ['id'] ) : '';
			$where = '1';
			if ($uname)	$where .= ' and locate("' . $uname . '",username)>0';
			if ($id) $where .= ' and locate(' . $id . ',id)>0';
			$limit = $offset * $pageSize.','. $pageSize;
			$userModel = new User ();
			$users = $userModel->getData ( '*', 'all', $where, $sort . ' ' . $order,$limit );
			$count = $userModel->getData ( 'count(*) cot', 'one', $where );
			echo '{"IsOk":1,"total": ' . $count ['cot'] . ',"rows":' . json_encode ( $users ) . ' }';
			exit ();
		} else {
			return $this->render ( 'test' ,array('cur'=>1));
		}
	}
	public function actionDel() {
		$ids = implode ( ',', Yii::$app->request->get ( 'params' ) );
		$user = new User ();
		$con = \Yii::$app->db;
		$cmd = $con->createCommand ( 'DELETE FROM' . $user->tableName () . ' WHERE id in(' . $ids . ')' );
		$cmd->query ();
	}
	public function actionUptdata() {

		$user = new User ();
		if (Yii::$app->request->post ()) {
			
			$where = 'id=' . Yii::$app->request->post ( 'uid' );
			$data = array (
					'username' => Yii::$app->request->post ( 'op1' ),
					'email' => Yii::$app->request->post ( 'op2' ) 
			);
			$user->upData ( $data, $where );
			exit ();
		}
	}
    public function actionDelmain() {

        $menu = new Menu() ;
        if (Yii::$app->request->post ()) {
            $where = 'id=' . intval(Yii::$app->request->post ( 'id' ));
            $data = array (
                'status' => 0,
            );
            $type=$menu->upData ( $data, $where );
            if(!$type) return $this->json(0,'删除失败');
            return $this->json(1);
        }
        exit ("error");
    }

    public function actionMenulist(){
        $menuModel=new Menu();
        $token = Yii::$app->session['token'];

        if (Yii::$app->request->post()) {
            $menuModel->upData(array('status'=>0),"`token`='{$token}'");
            $data = '{"button":[';
            foreach ($_POST['mainMenu'] as $key => $val){
                $menu =array(
                    'token'   => $token,
                    'pid'     => 0,
                    'menu'    => $val,
                    'type'    => $_POST['mainType'][$key],
                    'content' => $_POST['mainCon'][$key],
                    'sort'    => $key
                );
                $menuId = $menuModel-> addData($menu);
                if($_POST['sunMenu'][$key]){
                    $data.='{"name":"'.$val.'",	"sub_button":[';
                    foreach ($_POST['sunMenu'][$key] as $k =>$v){
                        $subMenu=array(
                            'token'   => $token,
                            'pid'     => $menuId,
                            'menu'    => $v,
                            'type'    => $_POST['sunType'][$key][$k],
                            'content' => $_POST['sunCon'][$key][$k],
                            'sort'    => $_POST['sunOrder'][$key][$k]
                        );
                        $menuModel-> addData($subMenu);
                        $data.='{';
                        $data.='"type":"'.($_POST['sunType'][$key][$k]==2?"view":"click").'",';
                        $data.='"name":"'.$v.'",';
                        $data.='"';
                        $data.=$_POST['sunType'][$key][$k]==2?"url":"key";
                        $data.='":"'.$_POST['sunCon'][$key][$k].'"';
                        $data.='},';
                    }
                    $data=substr($data, 0,-1);
                    $data.=']},';
                }else{
                    $data.='{';
                    $data.='"type":"'.($_POST['mainType'][$key]==2?"view":"click").'",';
                    $data.='"name":"'.$val.'",';
                    $data.='"';
                    $data.=$_POST['mainType'][$key]==2?"url":"key";
                    $data.='":"'.$_POST['mainCon'][$key].'"';
                    $data.='},';
                }
            }
            $data=substr($data, 0,-1);
            $data.=']}';
            $access_token = W::getAccessToken ( $token );
            $apiUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
            file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token);
            $result = W::http_post($apiUrl,$data);
            var_dump($result);die;
        }
        $menus=Menu::readMenus(Yii::$app->session['token']);
        return $this->render('menulist' ,array('menus'=>$menus,'result'=>json_decode($result,true)));
    }

    public function actionResetpwd()
    {
        header('Content-type:text/html;charset=utf-8');
        $admin=new Admin();
        if(Yii::$app->request->post())
        {
            $uid = Yii::$app->session ['UID'] ;
            $pwd = $_POST ['oldpwd'];
            $rpwd= $_POST ['resurepwd'];
            $where="id=".$uid;
            $user=$admin->getData('*','one',$where);
            if(md5($pwd.$user['salt'])!=$user['password']) {
                echo '旧密码输入错误';
                exit;
            }
            $password=md5($rpwd.$user['salt'] );
            $arr=array(
                'password'=>$password
            );
            $admin->myUpdate($arr,$where);
            echo  '密码更新成功！' ;
            exit;
        }
        else
            return  $this->render('resetpwd');
    }


}
