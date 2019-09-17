<?php
namespace frontend\controllers;

use common\models\Settings;
use common\models\ShandongRenbaoCar;
use common\models\ShandongRenbaoHeroAnswers;
use common\models\ShandongRenbaoHeroQuestion;
use common\models\ShandongRenbaoRepeat;
use common\models\ShandongRenbaoHero;
use common\models\ShandongRenbaoRewards;
use common\models\ShandongRenbaoWish;
use Yii;
use frontend\util\PController;
use common\components\W;


class ShandongRenbaoHeroController extends PController {

    public $sitetitle = '山东临沂';

    public $layout = 'shandong-renbao-hero';



    public function actionIndex() {
        $request = Yii::$app->request;
        $id = $request->get('id',0);
        $group_id = $request->get('group_id',0);
        Yii::$app->session['shandong_renbao_parent_id'] = $id;
        Yii::$app->session['shandong_renbao_group_id']  = $group_id;

        //分数清零，开始答题
        Yii::$app->session['shandong_renbao_question_ids'] = [];
        Yii::$app->session['shandong_renbao_scores'] = 0;


        $total = ShandongRenbaoHero::find()->count();
        $total = 1234+2*$total;


        return $this->render('index_new',[
            'total'=>$total
        ]);
    }

    public function actionQuestion(){
        $request = Yii::$app->request;
        $id = $request->get('id',0);


        $question = ShandongRenbaoHeroQuestion::find()->where([
            'id'=>$id
        ])->asArray()->one();
        $answers = ShandongRenbaoHeroAnswers::find()->where([
            'question_id'=>$id
        ])->asArray()->all();

        
        return $this->render('question',[
            'question'=>$question,
            'answers'=>$answers,
        ]);
    }


    public function actionAnswer() {
        $request = Yii::$app->request;
        $answer = $request->post('answer', 0);

        $answers = Yii::$app->session['shandong_renbao_question_ids'];
        //answer 存入session
        Yii::$app->session['shandong_renbao_question_ids']= array_push($answers,$answer);

        $question_id = $request->post('question_id', 0);

        //查看正确答案

        $question = ShandongRenbaoHeroQuestion::find()->where([
            'id'=>$question_id
        ])->asArray()->one();
        $scores = Yii::$app->session['shandong_renbao_scores'];
        if($question['correct_answer_id']==$answer){
            $scores = $scores+25;
            Yii::$app->session['shandong_renbao_scores']= $scores;
        }

        //查看是否有下一题
        $question = ShandongRenbaoHeroQuestion::find()->where([
            'id'=>$question_id+1
        ])->asArray()->one();
        if($question){
            return $this->json(1, '',['id'=>$question['id']]);
        }else{
            //把结果拿出来，计算分数

            return $this->json(0, '',['scores'=>$scores]);

        }




    }

    public function actionLast() {
        $request = Yii::$app->request;
        $scores = $request->get('scores', 0);

//        $scores = Yii::$app->session['shandong_renbao_scores'];
        $total = ShandongRenbaoHero::find()->count();
        $total = 1234+2*$total;

        return $this->render('last',[
            'scores'=>$scores,
            'total'=>$total,
        ]);
    }

    public function actionMobile() {


        return $this->render('mobile',[

        ]);
    }


    public function actionWay() {
        return $this->render('way',[

        ]);
    }



    /**
     * 验证码发送
     */
    public function actionCode(){


        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $mobile = $request->post('mobile', null);
        if (!$mobile) return $this->json(0, '请输入手机号码');

        //检测手机号码是否发送过
        $car = ShandongRenbaoHero::find()->where([
            'mobile'=>$mobile
        ])->one();
        if($car){
            return $this->json(-1, '您已经抽过奖，点击确定跳转到中奖页面！',['id'=>$car->id]);
        }


        $f = $this->sendSms($mobile,1);
        if (!$f) return $this->json(0, '验证码发送失败，请重试！');
        return $this->json(1, 'ok');
    }




    public function actionSubmit(){
        $this->enableCsrfValidation =false;
        $request = Yii::$app->request;
        $code = $request->post('code');
        $mobile = $request->post('mobile');
        $parent_id = Yii::$app->session['shandong_renbao_parent_id'];
        $group_id = Yii::$app->session['shandong_renbao_group_id'];
        $group_id = intval($group_id);
        if($group_id==0){
            $parent = ShandongRenbaoHero::find()->where([
                'id'=>$parent_id
            ])->one();
            $group_id = $parent->group_id;
        }

        Yii::$app->session->set('mobile', 1);

        try{
            //检测手机是否用过
            $object = ShandongRenbaoHero::find()->where([
                'mobile'=>$mobile
            ])->one();
            if($object){
                $return = [
                    'status'=>-1,
                    'message'=>'您已经抽过奖，点击确定跳转到中奖页面！',
                    'data'=>['id'=>$object->id]
                ];
                return \GuzzleHttp\json_encode($return);

            }


            //检测验证码是否正确
            $mobile_code = Yii::$app->cache->get("shandong_renbao_".$mobile);
            if($code!=$mobile_code){
                 throw new \Exception('验证码错误,请检查！');
            }


            $rewards_id = $this->getRewards($mobile);
            $object = new ShandongRenbaoHero();
            $object->mobile = $mobile;
            $object->rewards_id = $rewards_id;
            $object->group_id   = $group_id;
            $object->created_at = date('Y-m-d H:i:s');
            $object->updated_at = date('Y-m-d H:i:s');
            $object->save();

            $id = $object->id;
            $return = [
                'status'=>1,
                'data'=>['id'=>$id]
            ];

            //发送短信
            $this->sendSms($mobile,0,'恭喜您在临沂人保财险举行“猜英雄赢大奖”活动中获得超值大礼，奖券将在五个工作日内充入您山东人保财险公众号账号，还可邀请好友一起来抢！');


        }catch (\Exception $exception){
            $return = [
                'status'=>0,
                'message'=>$exception->getMessage()
            ];
        }



        echo \GuzzleHttp\json_encode($return);
    }






    public function getRewards($mobile){

        //先检测日期,先看抽奖是否在日期里面。。
        $time = time();
        if($time>=1568736000 and $time<1568995200){
            //如果名单在结果中，则不再中奖
            $repeat = ShandongRenbaoWish::find()->where(
                ['mobile'=>$mobile,]
            )->andWhere(['>','rewards_id',0])->one();
            if($repeat){
                return 0;
            }
            $repeat = ShandongRenbaoCar::find()->where(
                ['mobile'=>$mobile,]
            )->andWhere(['>','rewards',0])->one();
            if($repeat){
                return 0;
            }
        }

        //奖品数量
        $rewards = ShandongRenbaoRewards::find()->asArray()->all();
        $rewards_total  = ShandongRenbaoRewards::find()->sum('number');

        $numbers= [] ;
        $totals = ShandongRenbaoHero::find()->select(['rewards_id','count(1) as count'])->groupBy('rewards_id')->asArray()->all();
        foreach ($totals as $total){
            $numbers[$total['rewards_id']] = $total['count'];
        }


       //考虑中奖概率
        $setting = Settings::find()->where([
            'key'=>'winning_probability'
        ])->one();
       $probalility = $setting->value;
       $rand = rand(0,99);

	   if($rand>=0 and $rand<=$probalility){
		   $rand = rand(0,$rewards_total);
		   $begin = 0;
		   foreach($rewards as $reward){

               $last = $begin+$reward['number'];
                //分区间中奖
               if($numbers[$reward['id']]<$reward['number'] && $rand>=$begin and $rand<$last){
                    return $reward['id'];
               }
               $begin = $begin+$reward['number'];
           }
		   return 0;

	   }else{
	       return 0;
       }
    }



    public function actionDelete()
    {
        $request = Yii::$app->request;
        $mobile = $request->get('mobile');
        $car = ShandongRenbaoHero::deleteAll([
            'mobile'=>$mobile
        ]);
        echo '删除成功';
    }

    public function actionForm()
    {
        $this->layout =false;
        return $this->render('form',[

        ]);
    }

    public function actionRemind()
    {

        return $this->render('remind',[

        ]);
    }

    public function actionAddress(){

        $request = Yii::$app->request;
        $rewards_id = $request->get('rewards_id');

        return $this->render('address',[
            'rewards_id'=>$rewards_id
        ]);
    }

    public function actionAdressSave(){

        $request = Yii::$app->request;
        $rewards_id = $request->post('rewards_id');
        $mobile = $request->post('mobile');
        $name = $request->post('name');
        $address = $request->post('address');


        //检测地址是否正确，不正确，报错
        $hero = ShandongRenbaoHero::find()->where([
            'mobile'=>$mobile,
            'rewards_id'=>$rewards_id,
        ])->asArray()->one();
        if(!$hero){
            $return = [
                'status'=>0,
                'message'=>'中奖号码不对，请检查'
            ];
            echo \GuzzleHttp\json_encode($return);
        }else{
            $hero->address = $address;
            $hero->save();
            $return = [
                'status'=>1,
                'message'=>'您的地址保存成功'
            ];
            echo \GuzzleHttp\json_encode($return);
        }
    }



    public function actionApi(){
        $this->layout =false;
        $cars = ShandongRenbaoHero::find()->all();

        return $this->render('api',[
            'cars'=>$cars
        ]);
    }
	
	public function actionTotal(){
		$total0 = ShandongRenbaoHero::find()->where([
            'rewards_id'=>0
        ])->count();
		$total1 = ShandongRenbaoHero::find()->where([
            'rewards_id'=>1
        ])->count();
        $total2 = ShandongRenbaoHero::find()->where([
            'rewards_id'=>2
        ])->count();
        $total3 = ShandongRenbaoHero::find()->where([
            'rewards_id'=>3
        ])->count();
        echo '总参加人数：';
        echo ($total0+$total1+$total2+$total3);
        echo "<br>";
		echo '未中奖'.$total0;
        echo "<br>";
        echo '1奖'.$total1;
        echo "<br>";
        echo '2奖'.$total2;
        echo "<br>";
        echo '3奖'.$total3;
        echo "<br>";
		return false;

    }
	
	
	public function actionGroupTotal(){
		$totals = ShandongRenbaoHero::find()->select(['group_id', 'counted' => 'count(*)']) ->groupBy('group_id')->asArray()->all();
        foreach($totals as $total){
			echo $total['group_id']."->".$total['counted'];
			echo "<br>";
		}
		

    }

    public function actionTest(){

        $rewards_id = $this->getRewards(111);
        echo $rewards_id;
        die();
        $totals = ShandongRenbaoHero::find()->select(['rewards_id','count(1) as count'])->groupBy('rewards_id')->asArray()->all();
        var_dump($totals);
    }


    public  function sendSms($tel = NULL,$is_code=0, $content = '')
    {
        $tel || exit('电话不能为空');
        $config = \Yii::$app->params ['sms'];
        $header = array(
            'Content-Type: application/json; charset=utf-8'
        );
        if($is_code){
            $code = rand(100000, 999999);
            $content = '【云车驾到】您参与山东临沂活动的验证码是：' . $code . ',请验证后立即删除，不要泄露。';
        }

        $postArr = array(
            'account' => $config['api_account'],
            'password' => $config['api_password'],
            'msg' => urlencode($content),
            'phone' => $tel,
            'report' => true
        );
        $postArr = json_encode($postArr);
        $resArr = W::http_post($config['api_send_url'], $postArr, $header);
        $resArr = json_decode($resArr, true);

        if($is_code){
            if ((int)$resArr['code'] == 0 ) {
                Yii::$app->cache->set("shandong_renbao_" . $tel, $code, 18000);
                return true;
            } else {
                Yii::$app->cache->delete("shandong_renbao_" . $tel);
                return false;
            }
        }else{
            if ((int)$resArr['code'] == 0 ){
                return true;
            }else{
                return false;
            }
        }


        return true;
    }




    public function actionTests(){
        $license_plate = '111bc';
        $re = $this->get($license_plate);
        var_dump($re) ;

    }





}