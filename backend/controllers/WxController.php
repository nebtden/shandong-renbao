<?php

namespace backend\controllers;

use common\models\Wxuser;
use Yii;
use backend\util\BController;
use common\components\W;
use yii\helpers\Url;

class WxController extends BController {
	public function actionIndex() {
		if (Yii::$app->request->isAjax) {
			$model = new Wxuser ();
			$uid = Yii::$app->session ['UID'];
			$where = "`uid`='$uid' and status=1";
			$wxusr = $model->getData ( '*', 'all', $where );
			foreach ( $wxusr as $k => $v ) {
				$wxusr [$k] ['headpic'] = '<img width="130" src="' . $v ['headpic'] . '"/>';
				if ($v ['status'] == 1)
					$wxusr [$k] ['status'] = '开启';
				if ($v ['status'] == 0)
					$wxusr [$k] ['status'] = '异常';
				$wxusr [$k] ['url'] = 'http://dev.y100n.com/weixin/index/token/' . $v ['token'];
			}
			
			$cot = $model->getData ( 'count(*) cot', 'one', $where );
			echo '{"IsOk":1,"total": ' . $cot ['cot'] . ',"rows":' . json_encode ( $wxusr ) . ' }';
			exit ();
		}
		$model = new Wxuser ();
		$uid = Yii::$app->session ['UID'];
		$where = "`uid`='$uid' and status=1";
		$wxusr = $model->getData ( '*', 'all', $where );
		
		return $this->render ( 'index', array (
				'wxusr' => $wxusr 
		) );
	}
	public function actionWx_edit() {
		
		$model = new Wxuser ();
		$id = intval ( $_REQUEST ['id'] );
		$id && $wx = $model->getData ( '*', 'one', "`id`=$id" );
		if (Yii::$app->request->post ()) {
			$wxdata = array (
					'token' => Yii::$app->session ['token'],
					'uid' => Yii::$app->session ['UID'],
					'wxname' => $_POST ['wxname'],
					'headpic' => W::dealPic ( $_POST ['pic'] ),
					'weixin' => $_POST ['weixin'],
					'ghid' => $_POST ['ghid'],
					'appId' => $_POST ['appId'],
					'appSecret' => $_POST ['appSecret'] 
			)
			;
			if ($wx) {
				$wxId = $wx ['id'];
				$model->upData ( $wxdata, "`id`=$wxId" );
			} else {
				$wxdata ['createtime'] = time ();
				$wxId = $model->addData ( $wxdata );
			}
			$this->redirect ( Url::to ( [ 
					'wx/index' 
			] ) );
		}
		return $this->render ( 'wx_edit', array (
				'wx' => $wx 
		) );
	}
	public function actionWx_del() {
		$id = implode ( ',', Yii::$app->request->get ( 'params' ) );
		$where = '';
		$model = new Wxuser ();
		if ($id) {
			$where = "id in ($id)";
			$wx = $model->getData ( '*', 'all', $where );
			if ($wx) {
				$model->upData ( array (
						'status' => 0 
				), $where );
			}
		}
	}
}