<?php
namespace frontend\controllers;

use common\models\ShandongRenbaoCar;
use Yii;
use frontend\util\PController;
use common\components\W;

/**
 * 临时开发的一个版本
 * @author yinkp
 */
class ShandongRenbaoCarController extends PController {

    public $sitetitle = '山东临沂';

    public $layout = 'shandong-renbao-car';

    public $rewards = [
        0=>'您未中奖',
        1=>'200元电商优惠券',
        2=>'单次免费洗车',
        3=>'单次浪漫鲜花',
//        电商60%；洗车30%；鲜花10%
    ];

 

    public function actionIndex() {
 
       
        return $this->render('index2',[
             
        ]);
    }

    /*
     * 输入手机号码
     */
    public function actionMobile() {
        $license_plate = $_GET['license_plate'];
        $total = ShandongRenbaoCar::find()->count();
        $total = 1234+2*$total;

        return $this->render('mobile',[
            'license_plate'=>$license_plate,
            'total'=>$total,
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
        $car = ShandongRenbaoCar::find()->where([
            'mobile'=>$mobile
        ])->one();
        if($car){
            return $this->json(0, '恭喜您已获超值大礼，您可以将此游戏分享给更多的朋友。');
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
        $car = ShandongRenbaoCar::find()->where([
            'id'=>$id
        ])->one();
        if(!$car){
            return '数据错误，请联系管理员！';
        }

        //检测是否为本人

        $is_exists = Yii::$app->session->get('mobile');
        if($is_exists){

            $license_plate = $car->license_plate;
            $result = $this->get($license_plate);
            $result['reward'] = $this->rewards[$car->rewards];
            return $this->render('result',[
                'result'=>$result,
                'car'=>$car,
                'id'=>$id,
            ]);
        }else{

            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao/index.html?id=$id";
            header("Location:$url ");
            exit;
        }



    }


    public function actionSubmit(){
        $this->enableCsrfValidation =false;
        $request = Yii::$app->request;
        $license_plate = $request->post('license_plate');
        $code = $request->post('code');
        $parent_id = Yii::$app->session['shandong_renbao_parent_id'];
        $group_id = Yii::$app->session['shandong_renbao_group_id'];
        $group_id = intval($group_id);
        if($group_id==0){
            $parent = ShandongRenbaoCar::find()->where([
                'id'=>$parent_id
            ])->one();
            $group_id = $parent->group_id;
        }
        $mobile = $request->post('mobile');
        Yii::$app->session->set('mobile', 1);

        try{
            //检测验证码是否正确
            $mobile_code = Yii::$app->cache->get("shandong_renbao_".$mobile);
            if($code!=$mobile_code){
                throw new \Exception('验证码错误,请检查！');
            }

            $car = ShandongRenbaoCar::find()->where([
                'mobile'=>$mobile
            ])->one();
            $rewards_id = $this->getRewards();
            if(!$car){
                $car = new ShandongRenbaoCar();
                $car->mobile = $mobile;
                $car->parent_id = $parent_id;
                $car->group_id = $group_id;
                $car->rewards = $rewards_id;
            }

            $car->license_plate = $license_plate;
            $car->created_at = date('Y-m-d H:i:s');
            $car->updated_at = date('Y-m-d H:i:s');
            $car->save();

            $id = $car->id;
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
        $car = ShandongRenbaoCar::find()->where([
            'id'=>$id
        ])->one();
        $is_reward = $car->is_reward;

        if($car->rewards>0 && $is_reward==0){
            $car->is_reward = 1;
            $car->save();
            $this->sendSms($car->mobile,0,'【云车驾到】恭喜您在人保财险临沂公司举行“我的车牌值多少钱”活动中获得超值大礼包，相关奖券将在五个工作日内到山东人保财险微信公众号享服务中我的礼包领取权益');
        }
    }


    public function getRewards(){
        return 0;

        $total1 = ShandongRenbaoCar::find()->where([
            'rewards'=>1
        ])->count();
        if($total1<2000){
            return 1;
        }else{
            return 0;
        }
        $total2 = ShandongRenbaoCar::find()->where([
            'rewards'=>2
        ])->count();
        $total3 = ShandongRenbaoCar::find()->where([
            'rewards'=>3
        ])->count();
       if($total1>=2000 and $total2>=1500 and $total3>=400){
           return 0;
       }

       $rand = rand(0,9);

	   if($rand>=0 and $rand<=4){
		   if($total2<1500){
			   return 2;
		   }else{
			   if($total3<400){
				   return 3;
			   }else{
				   return 1;
			   }
		   }

	   }elseif ($rand>=5 and $rand<=9){

		   if($total3<400){
			   return 3;
		   }else{
			   if($total2<1500){
				   return 2;
			   }else{
				   return 1;
			   }
		   }

	   }




    }

    public function getRewardss(){
        $total1 = ShandongRenbaoCar::find()->where([
            'rewards'=>1
        ])->count();
        $total2 = ShandongRenbaoCar::find()->where([
            'rewards'=>2
        ])->count();
        $total3 = ShandongRenbaoCar::find()->where([
            'rewards'=>3
        ])->count();
        echo $total1;
        echo "<br>";
        echo $total2;
        echo "<br>";
        echo $total3;
        echo "<br>";
        if($total1>=2000 and $total2>=1500 and $total3>=200){
            return 0;
        }

        $rand = rand(0,9);
        if($rand<=5){
            if($total1<2000){
                return 1;
            }else{
                if($total2<1500){
                    return 2;
                }else{
                    return 3;
                }
            }

        }elseif ($rand>5 and $rand<=8){

            if($total2<1500){
                return 2;
            }else{
                if($total1<2000){
                    return 1;
                }else{
                    return 3;
                }
            }

        }else{
            if($total3<200){
                return 3;
            }else{
                return 0;
            }

        }
    }



    public function actionDelete()
    {
        $request = Yii::$app->request;
        $mobile = $request->get('mobile');
        $car = ShandongRenbaoCar::deleteAll([
            'mobile'=>$mobile
        ]);
        echo '删除成功';
    }


    public function actionGetway(){
        $request = Yii::$app->request;
        $id = $request->get('id');
        $car = ShandongRenbaoCar::find()->where([
            'id'=>$id
        ])->one();
        if(!$car){
            return '数据错误，请联系管理员！';
        }

        $is_exists = Yii::$app->session->get('mobile');
        if($is_exists){
            return $this->render('getway',[
                'id'=>$id,
            ]);
        }else{
            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao/index.html?id=$id";
            header("Location:$url ");
            exit;
        }
    }


    /**
     * 奖励
     */
    public function actionRewards(){
        $request = Yii::$app->request;
        $id = $request->get('id');
        $car = ShandongRenbaoCar::find()->where([
            'id'=>$id
        ])->one();
        if(!$car){
            return '数据错误，请联系管理员！';
        }


        $is_exists = Yii::$app->session->get('mobile');
        if($is_exists){
            return $this->render('rewards',[
                'id'=>$id,
            ]);
        }else{
            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao/index.html?id=$id";
            header("Location:$url ");
            exit;
        }

    }


    public function actionApi(){
        $this->layout =false;
        $cars = ShandongRenbaoCar::find()->all();

        return $this->render('api',[
            'cars'=>$cars
        ]);
    }
	
	public function actionTotal(){
		$total0 = ShandongRenbaoCar::find()->where([
            'rewards'=>0
        ])->count();
		$total1 = ShandongRenbaoCar::find()->where([
            'rewards'=>1
        ])->count();
        $total2 = ShandongRenbaoCar::find()->where([
            'rewards'=>2
        ])->count();
        $total3 = ShandongRenbaoCar::find()->where([
            'rewards'=>3
        ])->count();
		echo '0等奖'.$total0;
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
		$totals = ShandongRenbaoCar::find()->select(['group_id', 'counted' => 'count(*)']) ->groupBy('group_id')->asArray()->all();
        foreach($totals as $total){
			echo $total['group_id']."->".$total['counted'];
			echo "<br>";
		}
		

    }

    public function actionTest(){
        $id = $this->getRewardss();
        echo $id;
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

    //车牌逻辑
    public function get($license_plate ){
        switch ($license_plate){
            case $this->isSame($license_plate,5):
                return $this->license_plates[1];
            case $this->isPair($license_plate,5):
                return $this->license_plates[1];
            case $this->isSeries($license_plate,5):
                return $this->license_plates[1];
            case $this->isSame($license_plate,3):
                return $this->license_plates[2];
            case $this->isPair($license_plate,3):
                return $this->license_plates[2];
            case $this->isSeries($license_plate,3):
                return $this->license_plates[2];
            //第三矩阵
            case $this->isSame($license_plate,2):
                return $this->license_plates[3];
            //第四矩阵
            case $this->actionFour($license_plate):
                return $this->license_plates[4];
            //第五矩阵
            case $this->actionFive($license_plate):
                return $this->license_plates[5];

            case $this->actionSix($license_plate):
                return $this->license_plates[6];
            case $this->actionSeven($license_plate):
                return $this->license_plates[7];
            case $this->actionEight($license_plate):
                return $this->license_plates[8];
            case $this->actionNine($license_plate):
                return $this->license_plates[9];
        }


    }

    //号码是否相同
    public function isSame($license_plate,$num=5){

        if($num==5){
            return $license_plate[0]==$license_plate[1]
                && $license_plate[1]==$license_plate[2]
                && $license_plate[2]==$license_plate[3]
                && $license_plate[3]==$license_plate[4];
        }
        if($num==3){
            $result = false;
            for ($i=0;$i<=2;$i++){
                $slice = substr($license_plate,$i,3);
                $result =  $slice[0]==$slice[1]
                    && $slice[1]==$slice[2];
                if($result){
                    break;
                }
            }
            return $result;
        }
    }

    //是否对数
    public function isPair($license_plate,$num){
        if($num==5){
            return $license_plate[0]==$license_plate[4] && $license_plate[1]==$license_plate[3];
        }
        if($num==3){
            $result = false;
            for ($i=0;$i<=2;$i++){
                $slice = substr($license_plate,$i,3);
                $result =  $slice[0]==$slice[2];
                if($result){
                    break;
                }
            }
            return $result;
        }

    }




    public function  isSeries($license_plate,$num) {

        if($num==5){
            $result1 = preg_match('/(0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){4}/',$license_plate);
            $result2 = preg_match('/(9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){4}/',$license_plate);
            return $result1 || $result2 ;
        }

        if($num==3){
            $result1 = preg_match('/(0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){3}/',$license_plate);
            $result2 = preg_match('/(9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){3}/',$license_plate);
            return $result1 || $result2 ;
        }

    }

    //第四矩阵
    public function actionFour($license_plate){
        $last = $license_plate[4];
        if(in_array($last,[6,8])){
            return true;
        }else{
            return false;
        }
    }

    public function actionFive($license_plate){
        $last = $license_plate[4];
        if(in_array($last,[1,5])){
            return true;
        }else{
            return false;
        }
    }

    public function actionSix($license_plate){
        $last = $license_plate[4];
        if(in_array($last,[0,2])){
            return true;
        }else{
            return false;
        }
    }

    public function actionSeven($license_plate){
        $last = $license_plate[4];
        if(in_array($last,[3,9])){
            return true;
        }else{
            return false;
        }
    }

    public function actionEight($license_plate){
        $last = $license_plate[4];
        if(in_array($last,[4,7])){
            return true;
        }else{
            return false;
        }
    }

    public function actionNine($license_plate){
        return true;
    }


    public function actionTests(){
        $license_plate = '111bc';
        $re = $this->get($license_plate);
        var_dump($re) ;

    }





}