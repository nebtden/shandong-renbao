<?php
namespace common\components;

use Yii;
use yii\web\Controller;

class BaseController extends Controller {
	
	protected $path,$url;
	
	public function init(){
		parent::init();
		$this->path = dirname(Yii::$app->basePath);
	}
	
	public function beforeAction($action = NULL){
		//var_dump($action);
		//echo $this->id;
		//echo $this->action->id;
		return true;
	}
	
	public function afterAction($action, $result){
		//var_dump($action);
		//var_dump($result);
		//echo 'after';
		return $result;
	}
    public function json($status = 1,$msg = '操作成功',$data = [],$url = '',$waiting = 0){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => $status,'msg' => $msg, 'data' => $data, 'url' => $url, 'waiting' => $waiting];
    }
}
