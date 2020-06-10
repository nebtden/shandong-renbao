<?php

namespace frontend\controllers;

use common\components\AlxgBase;
use common\models\CarApkuser;
use Yii;
use common\components\BaseController;
use common\components\W;

class FreeloginController extends BaseController
{
    public function beforeAction($action = NULL)
    {

        Yii::$app->session['token'] = $token = 'dhcarcard';
        //Yii::$app->session['openid'] = 'oH1cBwhdurUPc-MWIoyO7f4OBcXA';
        if (!Yii::$app->session['openid']) {
            W::getOpenid($token);
        }
        return true;
    }

    public function actionDo()
    {
        $request = Yii::$app->request;
        $key = $request->get('key', null);
        if (!$key) {
            return '非法访问';
        }
        //查询是否有key
        $userModel = (new CarApkuser());
        $apkuser = $userModel->table()->where(['g_key'=>$key])->one();
        if (!$apkuser) {
            return '非法访问';
        }
        //如果已经绑定过了，直接跳到会员中心
        if ($apkuser['status']) {
            return $this->renderPartial('do');
        }
        //获得fans信息
        $openid = Yii::$app->session['openid'];
        $fans = (new AlxgBase('fans', 'id'))->table()->select('id,nickname')->where(['openid' => $openid])->one();
        if (!$fans) {
            return '非法访问';
        }
        $trans = Yii::$app->db->beginTransaction();
        $now = time();
        try {
            $data = [
                'uid' => $fans['id'],
                'realname' => $fans['id'] . 'xians',
                'mobile' => $apkuser['mobile'],
                'u_time' => $now,
            ];
            $data['c_time'] = $now;
            $r = (new AlxgBase('fans_account', 'id'))->myInsert($data);
            if (!$r) {
                throw new \Exception('系统繁忙，请重试');
            }
            $r = $userModel->myUpdate(['status' => 1], ['id' => $apkuser['id']]);
            if (!$r) {
                throw new \Exception('系统繁忙，请重试');
            }
            $trans->commit();
            return $this->renderPartial('do');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $e->getMessage();
        }
    }
}