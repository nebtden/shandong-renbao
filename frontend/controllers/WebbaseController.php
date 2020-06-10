<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 下午 2:19
 */
namespace frontend\controllers;

use Yii;
use frontend\util\WController;
use yii\web\Response;
use common\components\AlxgBase;
use common\components\BaseController;

class WebbaseController extends BaseController
{
    public $layout = "webcloudcar";
    public $site_title = '云车驾到';

    public function beforeAction($action = null)
    {
        Yii::$app->session['token'] = $token = 'dhcarcard';
        parent::beforeAction($action);
        return true;
    }

}