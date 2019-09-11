<?php
namespace frontend\controllers;

use common\models\Settings;
use common\models\ShandongRenbaoRepeat;
use common\models\ShandongRenbaoWish;
use Yii;
use frontend\util\PController;
use common\components\W;


class ShandongRenbaoWishController extends PController {

    public $sitetitle = '山东临沂';

    public $layout = 'shandong-renbao-wish';

    public $rewards = [
        0=>'您未中奖',
        1=>'200元电商优惠券',
        2=>'单次免费洗车',
        3=>'单次浪漫鲜花',
        4=>'九阳开水煲',
        5=>'阳澄湖蟹将军提货券',
//        电商60%；洗车30%；鲜花10%
    ];



    public function actionIndex() {
        $request = Yii::$app->request;
        $id = $request->get('id',0);
        $group_id = $request->get('group_id',0);
        Yii::$app->session['shandong_renbao_parent_id'] = $id;
        Yii::$app->session['shandong_renbao_group_id']  = $group_id;

        $total = ShandongRenbaoWish::find()->count();
        $total = 1234+2*$total;
        return $this->render('index',[
            'total'=>$total
        ]);
    }


    public function actionBlessing() {
        return $this->render('blessing',[

        ]);
    }

    public function actionLetter() {
        $request = Yii::$app->request;
        $id = $request->get('id');
        return $this->render('letter',[
            'id'=>$id
        ]);
    }

    public function actionPrize() {
        return $this->render('prize',[

        ]);
    }
    public function actionIndexs() {
        $this->layout=false;
        return $this->render('indexs',[

        ]);
    }

    public function actionMusic() {
        return $this->render('music',[

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
        $car = ShandongRenbaoWish::find()->where([
            'mobile'=>$mobile
        ])->one();
        if($car){

            return $this->json(-1, '您已经抽过奖，点击确定跳转到中奖页面！',['id'=>$car->id]);
//            return \GuzzleHttp\json_encode($return);
        }


        $f = $this->sendSms($mobile,1);
        if (!$f) return $this->json(0, '验证码发送失败，请重试！');
        return $this->json(1, 'ok');
    }

    /**
     * 结果
     */
    public function actionResult(){

        $request = Yii::$app->request;
        $id = $request->get('id');
        $object = ShandongRenbaoWish::find()->where([
            'id'=>$id
        ])->one();


        $result = $object->toArray();
        $result['rewards'] = $this->rewards[$result['rewards_id']];

        return $this->render('result',[
            'result'=>$result,
            'id'=>$id,
        ]);


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
            $parent = ShandongRenbaoWish::find()->where([
                'id'=>$parent_id
            ])->one();
            $group_id = $parent->group_id;
        }

        Yii::$app->session->set('mobile', 1);

        try{
            //检测手机是否用过
            $object = ShandongRenbaoWish::find()->where([
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
            $object = new ShandongRenbaoWish();
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


        }catch (\Exception $exception){
            $return = [
                'status'=>0,
                'message'=>$exception->getMessage()
            ];
        }

        //发送短信


        echo \GuzzleHttp\json_encode($return);
    }

    public function actionSend(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $car = ShandongRenbaoWish::find()->where([
            'id'=>$id
        ])->one();
        $is_reward = $car->is_reward;

        if($car->rewards>0 && $is_reward==0){
            $car->is_reward = 1;
            $car->save();
            $this->sendSms($car->mobile,0,'【云车驾到】恭喜您在人保财险临沂公司举行“我的车牌值多少钱”活动中获得超值大礼包，相关奖券将在五个工作日内到山东人保财险微信公众号享服务中我的礼包领取权益');
        }
    }

    public function actionSetrand(){

        $setting = Settings::find()->where([
            'key'=>'winning_probability'
        ])->one();
        $probalility_old = $setting->setting_value;
        echo '当前中奖概率为：'.$probalility_old;
        echo "<br>";



        $request = Yii::$app->request;
        $probability = $request->get('probability');
        echo "<br>";
        echo '您将更新中奖概率为'.$probability;
        echo "<br>";
        if($probability>=0 and $probability<=100){

            $setting->setting_value = intval($probability);
            $setting->save();
            echo "更新成功！";
        }

    }


    public function getRewards($mobile){

        //如果名单在结果中，则中奖
        $repeat = ShandongRenbaoRepeat::find()->where([
            'mobile'=>$mobile
        ])->one();
        if($repeat){
            return 0;
        }



        $total1 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>1
        ])->count();
        $total2 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>2
        ])->count();
        $total3 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>3
        ])->count();
        $total4 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>4
        ])->count();
        $total5 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>5
        ])->count();

        if($total1>=2000 and $total2>=1000 and $total3>=400 and $total4>=5 and $total5>=2){
            return 0;
        }

        //考虑中奖概率
        $setting = Settings::find()->where([
            'key'=>'winning_probability'
        ])->one();
        $probalility = $setting->setting_value;
        $rand = rand(0,99);

        if($rand>=0 and $rand<=$probalility){
            $rand = rand(0,999);
            if($total1<2000 && $rand<500){
                return 1;
            }elseif($total2<1000 && $rand>=500 and $rand<850){
                return 2;
            }elseif($total3<400 && $rand>=850 and $rand<990){
                return 3;
            }elseif($total4<5 && $rand>=990 and $rand<998){
                return 4;
            }elseif($total5<2 && $rand>=998 ){
                return 5;
            }else{
                if($total1<2000){
                    return 1;
                }elseif($total2<1000){
                    return 2;

                }elseif($total3<400){
                    return 3;
                }else{
                    return 0;
                }
            }
        }else{
            return 0;
        }
    }



    public function actionDelete()
    {
        $request = Yii::$app->request;
        $mobile = $request->get('mobile');
        $car = ShandongRenbaoWish::deleteAll([
            'mobile'=>$mobile
        ]);
        echo '删除成功';
    }



    public function actionApi(){
        $this->layout =false;
        $cars = ShandongRenbaoWish::find()->all();

        return $this->render('api',[
            'cars'=>$cars
        ]);
    }

    public function actionTotal(){
        $total0 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>0
        ])->count();
        $total1 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>1
        ])->count();
        $total2 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>2
        ])->count();
        $total3 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>3
        ])->count();
        $total4 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>4
        ])->count();
        $total5 = ShandongRenbaoWish::find()->where([
            'rewards_id'=>5
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
        echo '九阳开水煲'.$total4;
        echo "<br>";
        echo '阳澄湖蟹将军提货券'.$total5;
        echo "<br>";

        return false;

    }


    public function actionGroupTotal(){
        $totals = ShandongRenbaoWish::find()->select(['group_id', 'counted' => 'count(*)']) ->groupBy('group_id')->asArray()->all();
        foreach($totals as $total){
            echo $total['group_id']."->".$total['counted'];
            echo "<br>";
        }


    }

    public function actionTest(){
        $array = [];
        $total = 0;
        for($i=0;$i<=10000;$i++){
            $rand = rand(0,99);
            $array[$rand]++;
            if($rand>=90){
                $total = $total+1;
            }
        }

        echo $total;

        var_dump($array);
        die();



        $rand = rand(0,9);
        echo $rand;
        die();
        $is = Yii::$app->session->get('mobile',0);
        var_dump($is) ;
        die();
        $url = $_SERVER['HTTP_HOST'];
        var_dump($_SERVER);
        $mobile = '13365802535';
        $code = Yii::$app->cache->get("shandong_renbao_".$mobile);
        echo $code;
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
            $content = '【云车驾到】您的绑定验证码是：' . $code . ',有效时间3分钟，请验证后立即删除，不要泄露。';
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