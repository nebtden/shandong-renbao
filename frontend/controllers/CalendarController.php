<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5 0005
 * Time: 下午 3:44
 */
namespace frontend\controllers;

use common\models\VideoCompany;
use Yii;
use frontend\util\WController;
use yii\web\Response;
use common\components\AlxgBase;
use common\components\BaseController;
use common\models\VideoCode;
use common\models\VideoPath;
use yii\helpers\Url;
class CalendarController extends BaseController
{
    public $layout = "calendar";
    public $site_title = "声临其境说保险";

    public function beforeAction($action = null)
    {
        Yii::$app->session['token'] = $token = 'dhcarcard';
        parent::beforeAction($action);
        return true;
    }
    /**
     * 太平洋保险
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndex()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(1);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(1);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-tpy';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo ]);
    }
    /**
     * 中国人保寿险
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndexrbsx()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(3);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(3);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-rbsx';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }
    /**
     * 新华保险
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndexxh()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(2);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(2);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-xhbx';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }

    /**
     * 中国平安保险
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndexpa()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(4);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(4);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-zgpa';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }
    /**
     * 中国人寿
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndexrs()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(5);
        $companyinfo = (new VideoCompany()) -> getCompanyInfo(5);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-zgrs';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }
    /**
     * 中国太平
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndextp()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(6);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(6);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-zgtp';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }
    /**
     * 通用
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndexall()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(7);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(7);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-com';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }
    /**
     * 中国太平
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionIndexms()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(8);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(8);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-com2';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }
    public function actionIndexrb()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(11);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(11);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-com4';
        return $this->render('index',['list'=>$list,'info'=>$companyinfo]);
    }
    public function actionDetail()
    {

        $request = Yii::$app->request;
        $backurlall = [
            1=>Url::to(['calendar/index']),
            2=>Url::to(['calendar/indexxh']),
            3=>Url::to(['calendar/indexrbsx']),
            4=>Url::to(['calendar/indexpa']),
            5=>Url::to(['calendar/indexrs']),
            6=>Url::to(['calendar/indextp']),
            7=>Url::to(['calendar/indexall']),
            8=>Url::to(['calendar/indexms']),
            11=>Url::to(['calendar/indexrb']),
        ];
        $id = (int)$request->get('id');
        $model  = new VideoPath();
        $info = $model->table()->select('*')->where(['status' => 1,'id'=>$id])->one();
        $companyinfo = (new VideoCompany())-> getCompanyInfo($info['company_id']);
        $this->site_title = $companyinfo['webpage_title'];

        return $this->render('detail',['info'=>$info,'backurl'=>$backurlall[$info['company_id']]]);
    }

    /**
     * 验证观看码
     * @param $code
     * @return json
     */
    public function actionCheckviewsn() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $model = new VideoCode();
            $code = trim($request->post('viewsn'));
            $company_id = intval($request->post('company_id'));

            $codeinfo  = $model->table()->select('*')->where(['code' => $code])->one();
            if(empty($codeinfo)) return $this->json(0,'观看码错误，请重新输入');
            if($company_id != $codeinfo['company_id'])return $this->json(0,'此观看码无效');
            $num = $codeinfo['views_num'] - $codeinfo['use_num'];
            if($num <= 0) return $this->json(0,'此码观看次数已用完');
            $data['num'] = $codeinfo['views_num'] - $codeinfo['use_num'];
            $data['code'] = $codeinfo['code'];
            return $this->json(1,'操作成功',$data);
        }
        return $this->json(0,'操作失败');
    }
    /**
     * 验证观看码
     * @param $code
     * @return json
     */
    public function actionViewvideo() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $model = new VideoCode();
            $code = trim($request->post('viewsn'));
            $codeinfo  = $model->table()->select('*')->where(['code' => $code])->one();
            if(empty($codeinfo)) return $this->json(0,'观看码错误，请重新输入');
            $num = $codeinfo['views_num'] - $codeinfo['use_num'];
            if($num <= 0) return $this->json(0,'此码观看次数已用完');
            $data = [];
            $data['use_num'] = $codeinfo['use_num']+1;
            $data['u_time'] = time();
            if($codeinfo['views_num'] == $data['use_num'] && $codeinfo['views_num'] > 0) $data['status'] = 2;
            $res = $model->myUpdate($data ,['code'=>$code]);
            if(!$res) return $this->json(0,'系统错误');
            return $this->json(1,'操作成功',$codeinfo);
        }
        return $this->json(0,'操作失败');
    }
    /**
     * 长城人寿
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionVideolist()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(9);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(9);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-com2';
        return $this->render('videolist',['list'=>$list,'info'=>$companyinfo]);
    }
    public function actionBroadcast()
    {

        $request = Yii::$app->request;
        $id = (int)$request->get('id');
        $model  = new VideoPath();
        $info = $model->table()->select('*')->where(['status' => 1,'id'=>$id])->one();
        $companyinfo = (new VideoCompany())-> getCompanyInfo($info['company_id']);
        $this->site_title = $companyinfo['webpage_title'];
        return $this->render('broadcast',['info'=>$info]);
    }
    /**
     *
     * @param $companyid 公司ID
     * @return $arr    别表
     */
    public function actionVideorbjk()
    {
        $model  = new VideoPath();
        $list = $model->getvideolist(10);
        $companyinfo = (new VideoCompany())-> getCompanyInfo(10);
        $this->site_title  = $companyinfo['webpage_title'];
        $companyinfo['class'] = 'content-bg-com4';
        return $this->render('videolist',['list'=>$list,'info'=>$companyinfo]);
    }



}