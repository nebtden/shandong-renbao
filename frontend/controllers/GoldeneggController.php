<?php
/**
 * @title:砸金蛋抽奖
 * @time:2018-11-26
 */

namespace frontend\controllers;

use common\components\BaseController;
use common\components\W;
use common\models\GoldenEgg;
use common\models\Goldeneggactivity;
use frontend\util\FController;
use yii\db\Exception;
use yii\helpers\Url;
use Yii;


class GoldeneggController extends BaseController
{
    public $layout = 'goldenegg.php';
    public $title = '活动抽奖';
    public $background = 'h1-bg';
    public $user;

    public function init(){
        if(!Yii::$app->session['token']){
            Yii::$app->session['token']  = 'dhcarcard';
        }
        parent::init();

    }

    public function checkLogin()
    {
        $session = Yii::$app->session;
        $this->user = $session->get('golden_egg_user');
        if(!$this->user){
            return $this->redirect(['goldenegg/login']);
        }
        return true;
    }
    /**
     * 登录页面
     * @return array|string
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        if($request->isPost){
            $jobNum = $request->post('jobNum',null);
            $name = $request->post('name',null);
            $jobNum = trim($jobNum);
            $name = trim($name);
            $res = (new GoldenEgg())->selectName($jobNum,$name);
            $activeDate = Goldeneggactivity::findOne('1');
            if(time()< $activeDate->start_time){
                return $this->json(3,'活动未开始');
            }
            if(!$res){
              return $this->json(0,'error');
            }
            $user = [
                'id' => $res->id,
                'jobNum' => $res->job_num,
                'name' => $res->name,
            ];
            $session->remove('golden_egg_user');
            $session->set('golden_egg_user',$user);


            if(!$res->image){
                $url = Url::to(['goldenegg/answer','id'=>$res->id]);
            } elseif(!$res->prize){
                $url = Url::to(['goldenegg/instant','id'=>$res->id]);
            }
            else {
                $url = Url::to(['goldenegg/already','id'=>$res->id]);
            }
            return $this->json(1,'ok', $res, $url);
        }
        return $this->render('login');
    }

    /**
     * 答题页面
     * @return string
     */
    public function actionAnswer()
    {
        $this->title = '答题';
        $this->background = 'h2-bg';
        $this->checkLogin();
        return $this->render('answer',['id'=>$this->user['id']]);
    }

    public function actionInstant()
    {
        $this->title = '立即参与';
        $this->background = 'h2-bg';
        $id = Yii::$app->request->get('id');
        $isLogin = Yii::$app->session->get('golden_egg_user', null);
        if(!$isLogin){
            $url = Url::to(['goldenegg/login']);
        }else {
            $url = Url::to(['goldenegg/game','id'=>$isLogin['id']]);
        }
        //查询用户
        $res = (new GoldenEgg())->selectId($id);
        if(!$res->image){
            //GD库生成用户姓名PNG图片
            $pngPath = $this->createPng($res);
            $res->image = '/frontend/web/goldenegg/imguser/'.$res->id.'.png';
            $res->save();
        }
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        $shareInfo = $this->shareInfo($res);
        $activeDate = Goldeneggactivity::findOne('1');

        return $this->render('instant',[
            'res' => $res,
            'isLogin' => $isLogin,
            'alxg_sign' => $alxg_sign,
            'shareInfo' => $shareInfo,
            'url' => $url,
            'activeDate' => $activeDate
        ]);
    }

    /**
     * GD库生成用户姓名PNG图片
     * @param $res
     */
    public function createPng($res)
    {
        $width = 280;
        $height = 100;
        $size = 50;//字体类型，
        $font = Yii::$app->getBasePath()."/web/goldenegg/font/simhei.ttf";
        //显示的文字
        $text = $res->name;
        $a = imagettfbbox($size, 0, $font, $text);   //得到字符串虚拟方框四个点的坐标
        $len = $a[2] - $a[0];
        $x = ($width-$len)/2;
        $y = ($height-$a[3]-$a[5])/2;
        //创建一个长为187高为70的空白图片
        $img = imagecreate($width, $height);
        //$img = imagecreatefrompng(Yii::$app->getBasePath()."/web/goldenegg/front/shareImg.png");// 加载已有图像
        //给图片分配颜色
        imagecolorallocatealpha($img, 0xff, 0xff, 0xff,127);
        //设置字体颜色
        $black = imagecolorallocate($img, 235, 85, 4);
        //将ttf文字写到图片中
        imagettftext($img, $size, 0, 10, $y, $black, $font, $text);
        //输出图片
        // ImagePNG($img);
        $pngPath = Yii::$app->getBasePath()."/web/goldenegg/imguser/".$res->id.".png";
        //保存图片至指定路径
        ImagePNG($img, $pngPath);
        imagedestroy($img);
    }

    /**
     * 砸金蛋
     * @return array|string
     */
    public function actionGame()
    {
        $this->checkLogin();
        $request = Yii::$app->request;
        $url = '';
        if($request->isPost){
            try {
                $id = $request->post('id');
                if($id != $this->user['id']){
                    $url = Url::to(['goldenegg/login']);
                    throw new \Exception('用户ID不匹配,请重新登录');
                }
                $activeDate = Goldeneggactivity::findOne('1');
                $now = time();
                if($now < $activeDate->start_time){
                    $url = Url::to(['goldenegg/instant','id'=>$id]);
                    throw new \Exception('活动尚未开始');
                }
                if($now > $activeDate->end_time){
                    $url = Url::to(['goldenegg/instant','id'=>$id]);
                    throw new \Exception('活动已结束');
                }
                $goldenObj = new GoldenEgg();
                $prizeInfo = $goldenObj->getPrizeInfo($this->user['id']);
                if(!$prizeInfo){
                    $url = Url::to(['goldenegg/share']);
                    throw new \Exception('您已经抽过奖了');
                }
                $proArr = $goldenObj->prizeNum();
                if(!array_sum($proArr)){
                    $url = Url::to(['goldenegg/already']);
                    throw new \Exception('活动结束');
                }
                $prize = $this->getRand($proArr);
                $userObj = GoldenEgg::findOne($this->user['id']);
                $prizeName = Goldeneggactivity::getPrizeName($prize);
                $userObj->prize = $prize;
                $userObj->prize_name = $prizeName;
                $userObj->prize_time = date('Y-m-d H:i:s',time());
                $userObj->save();

                return $this->json(1,'ok',$prizeName);
            } catch (\Exception $e){
                $msg = $e->getMessage();
                return $this->json(0,$msg,'',$url);
            }
        }

        return $this->render('game',['id' => $this->user['id']]);
    }

    /**
     * 抽奖算法
     * @param $proArr
     * @return int|string
     */
    public function getRand($proArr) {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);

        return $result;
    }

    public function actionShare()
    {

        $this->checkLogin();
        $this->title = '分享';
        $this->background = 'h2-bg';
        $res = GoldenEgg::findOne($this->user['id']);
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        $shareInfo = $this->shareInfo($res);
        return $this->render('share',[
            'user'=>$res,
            'alxg_sign' => $alxg_sign,
            'shareInfo' => $shareInfo
        ]);
    }

    public function actionAlready()
    {
        $this->checkLogin();
        $this->title = '分享';
        $this->background = 'h2-bg';
        $res = GoldenEgg::findOne($this->user['id']);
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        $shareInfo = $this->shareInfo($res);
        return $this->render('already',[
            'user'=>$res,
            'alxg_sign' => $alxg_sign,
            'shareInfo' => $shareInfo
            ]);
    }

    public function shareInfo($user)
    {
        return $shareInfo = [
            'title' => '我是'.$user->name.',我为e生保代言',
            'desc' =>'平安E生保（保证续保版）12月1日火爆预售,点击参与砸金蛋',
            'pengyouquan' => '我是'.$user->name.',我为e生保代言,平安e生保（保证续保版) 12月1日火爆预售',
            'link' => Url::to(['goldenegg/instant','id'=>$user->id],true),
            'imgUrl' => Yii::$app->params['url'].'/frontend/web/goldenegg/images/sharepic.png'
        ];
    }

}
