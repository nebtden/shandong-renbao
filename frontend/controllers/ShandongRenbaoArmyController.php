<?php
namespace frontend\controllers;

use common\models\ShandongRenbaoArmyShare;
use frontend\util\PController;
use common\models\Settings;
use common\models\ShandongRenbaoArmy;
use common\models\ShandongRenbaoArmyQuestion;
use common\models\ShandongRenbaoArmyAnswers;
use common\models\ShandongRenbaoRewards;
use Yii;
use common\components\W;

class ShandongRenbaoArmyController extends PController {

    public $sitetitle = '山东临沂';

    public $layout = 'shandong-renbao-army';

    public static $share_rewards = [
        2=>'洗车券',
        3=>'鲜花（乐享）',
        4=>'E惠通（200）',
    ];



    public function getTotal(){
        $army_base_count = Settings::find()->where([
            'key'=>'army_base_count'
        ])->one();
        $army_multiple_count = Settings::find()->where([
            'key'=>'army_multiple_count'
        ])->one();
        $total = ShandongRenbaoArmy::find()->count();
        $total = $army_base_count->setting_value+$army_multiple_count->setting_value*$total;
        return $total;
    }

    public function actionIndex(){
        $request = Yii::$app->request;
        $id = $request->get('id',0);
        $group_id = $request->get('group_id',0);
        Yii::$app->session['shandong_renbao_parent_id'] = $id;
        Yii::$app->session['shandong_renbao_group_id']  = $group_id;

        //分数清零，开始答题
        Yii::$app->session['shandong_renbao_question_ids'] = [];
        Yii::$app->session['shandong_renbao_scores'] = [];
        Yii::$app->session->set('renbao_army_mobile',1);
        Yii::$app->session->set('mobile', 1);


        $total = $this->getTotal();
        if(time()<1568736000 || true){
//            return 2;
            return $this->render('index_new',[
                'total'=>$total
            ]);
        }else{
            return $this->render('index',[
                'total'=>$total
            ]);
        }
    }


    public function actionHome() {
        $request = Yii::$app->request;
        $id = $request->get('id',0);
        $group_id = $request->get('group_id',0);
        Yii::$app->session['shandong_renbao_parent_id'] = $id;
        Yii::$app->session['shandong_renbao_group_id']  = $group_id;

        //分数清零，开始答题
        Yii::$app->session['shandong_renbao_question_ids'] = [];
        Yii::$app->session['shandong_renbao_scores'] = [];
        Yii::$app->session->set('renbao_army_mobile',1);
        Yii::$app->session->set('mobile', 1);


        $total = $this->getTotal();
        if(time()<1568736000 || true){
//            return 2;
            return $this->render('index_new',[
                'total'=>$total
            ]);
        }else{
            return $this->render('index',[
                'total'=>$total
            ]);
        }
    }

    public function actionQuestion(){
        $is_exists = Yii::$app->session->get('renbao_army_mobile');

        $request = Yii::$app->request;
        $id = $request->get('id',0);

        if($is_exists){
            $question = ShandongRenbaoArmyQuestion::find()->where([
                'id'=>$id
            ])->asArray()->one();
            $answers = ShandongRenbaoArmyAnswers::find()->where([
                'question_id'=>$id
            ])->asArray()->all();

            $total = $this->getTotal();
            return $this->render('question',[
                'question'=>$question,
                'answers'=>$answers,
                'total'=>$total
            ]);
        }else{

            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao-army/index.html";
            header("Location:$url ");
            exit;
        }


    }


    public function actionAnswer() {
        $request = Yii::$app->request;
        $answer = $request->post('answer', 0);

        $answers = Yii::$app->session['shandong_renbao_question_ids'];
        //answer 存入session
        Yii::$app->session['shandong_renbao_question_ids']= array_push($answers,$answer);

        $question_id = $request->post('question_id', 0);

        //查看正确答案

        $questionAnswer = ShandongRenbaoArmyAnswers::find()->where(['id' => $answer])->one();
        $scores = Yii::$app->session['shandong_renbao_scores'];
        if ($question_id == 6) {
            $scores[$questionAnswer->army] += 1.5;
        } elseif ($question_id >= 3 && $question_id <= 5) {
            $scores[$questionAnswer->army]++;
        }
        Yii::$app->session['shandong_renbao_scores']= $scores;

        //查看是否有下一题
        $question = ShandongRenbaoArmyQuestion::find()->where([
            'id'=>$question_id+1
        ])->asArray()->one();
        if($question){
            return $this->json(1, '',['id'=>$question['id']]);
        }else{
            //把结果拿出来，计算分数

            return $this->json(0, '');

        }
    }

    public function actionLast() {
        $is_exists = Yii::$app->session->get('renbao_army_mobile');
        if($is_exists){
            $scores = Yii::$app->session['shandong_renbao_scores'];
            $maxIndex = 0;
            for ($i = 1; $i <= 3; $i++) {
                if ($scores[$i] > $scores[$maxIndex] || !$scores[$maxIndex]) {
                    $maxIndex = $i;
                }
            }
            $results = ['陆军', '海军', '空军', '火箭军'];
            $descs = [
                '恭喜您获得陆军认证，是陆上作战中坚力量，血雨腥风中锻造铁的队伍，为保卫国家主权总是冲锋向前，剑锋所指，战无不胜。',
                '恭喜您获得海军认证，您的主要任务是负责海上作战任务，守卫波澜壮阔300万平方公里的蓝色国土，亚丁湾护航展现大国风采，御敌人于千里之外。',
                '恭喜您获得空军认证，担负国土防空,支援陆、海军作战。翱翔蓝天，只为守护一方净土，战争中，制空权往往起着决定性作用。',
                '恭喜您获得火箭军认证，俗称“东风快递”，是国家安全坚实筑石，是一支不可忽视的力量，是震慑敌人最有力的杀手锏。'];

            $total = $this->getTotal();

            return $this->render('last',[
                'result'=>$results[$maxIndex],
                'desc' => $descs[$maxIndex],
                'total'=>$total,
            ]);


        }else{

            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao-army/index.html";
            header("Location:$url ");
            exit;
        }


    }

    public function actionMobile() {
        $is_exists = Yii::$app->session->get('renbao_army_mobile');
        if($is_exists){

            return $this->render('mobile',[

            ]);
        }else{

            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao-army/index.html";
            header("Location:$url ");
            exit;
        }

    }



    public function actionPrize() {

        $is_exists = Yii::$app->session->get('renbao_army_mobile');
        $request = Yii::$app->request;
        $id = $request->get('id');

        if($is_exists){
            $object = ShandongRenbaoArmy::find()->where([
                'id'=>$id
            ])->one();

            $result = $object->toArray();

            $rewards  = ShandongRenbaoRewards::findOne($result['rewards_id']);
            $result['rewards'] = $rewards->name;

            return $this->render('prize',[
                'result'=>$result,
                'id'=>$id,
            ]);
        }else{
            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao-army/index.html?id=$id";
            header("Location:$url ");
            exit;
        }



    }

    public function actionPrizeStep(){
        $is_exists = Yii::$app->session->get('renbao_army_mobile');
        $request = Yii::$app->request;
        $id = $request->get('id');
        if($is_exists){

            return $this->render('prize-step',[
                'id'=>$id
            ]);
        }else{
            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao-army/index.html?id=$id";
            header("Location:$url ");
            exit;
        }

    }

    public function actionRegister(){
        $request = Yii::$app->request;
        $id = $request->get('id');
        $is_exists = Yii::$app->session->get('renbao_army_mobile');
        if($is_exists){
            return $this->render('register',[
                'id'=>$id
            ]);
        }else{
            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao-army/index.html?id=$id";
            header("Location:$url ");
            exit;
        }

    }


    public function actionWay() {
        $is_exists = Yii::$app->session->get('mobile');
        if($is_exists){
            return $this->render('way',[]);
        }else{
            $url = $_SERVER['HTTP_HOST'];
            $url = "http://$url/frontend/web/shandong-renbao-army/index.html";
            header("Location:$url ");
            exit;
        }
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
        $car = ShandongRenbaoArmy::find()->where([
            'mobile'=>$mobile
        ])->one();
        if($car){
            $rewards_id = $car->rewards_id;
            $rewards = ShandongRenbaoRewards::find()->where([
                'id'=>$rewards_id
            ])->one();
            $rewards_name = $rewards->name;
            $return = [
                'status'=>-1,
                'msg'=>'您已经抽过奖，点击确定查看中奖结果！',
                'data'=>['id'=>$car->id,'rewards_id'=>$rewards_id,'rewards'=>$rewards_name]
            ];
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \GuzzleHttp\json_encode($return);
//            return $this->json(-1, '您已经抽过奖,点击确定查看中奖结果',['id'=>$car->id]);
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
        Yii::$app->session->set('mobile', 1);
        if($group_id==0){
            $parent = ShandongRenbaoArmy::find()->where([
                'id'=>$parent_id
            ])->one();
            $group_id = $parent->group_id;
        }

        Yii::$app->session->set('mobile', 1);

        try{
            //检测手机是否用过
            $object = ShandongRenbaoArmy::find()->where([
                'mobile'=>$mobile
            ])->one();
            if($object){
                $rewards_id = $object->rewards_id;
                $rewards = ShandongRenbaoRewards::find()->where([
                    'id'=>$rewards_id
                ])->one();
                $rewards_name = $rewards->name;
                $return = [
                    'status'=>-1,
                    'message'=>'您已经抽过奖，点击确定查看中奖结果！',
                    'data'=>['id'=>$object->id,'rewards_id'=>$rewards_id,'rewards'=>$rewards_name]
                ];
                return \GuzzleHttp\json_encode($return);

            }


            //检测验证码是否正确
            $mobile_code = Yii::$app->cache->get("shandong_renbao_".$mobile);
            if($code!=$mobile_code){
                throw new \Exception('验证码错误,请检查！');
            }


            $rewards_id = $this->getRewards($mobile);
//            $rewards_id = 0;
            $object = new ShandongRenbaoArmy();
            $object->mobile = $mobile;
            $object->rewards_id = $rewards_id;
            $object->group_id   = $group_id;
            $object->parent_id   = $parent_id;
            $object->created_at = date('Y-m-d H:i:s');
            $object->updated_at = date('Y-m-d H:i:s');
            $object->save();

            $id = $object->id;


            //查看分享的人是否有三次分享
            $parent_count = ShandongRenbaoArmy::find()->where([
                'parent_id'=>$parent_id
            ])->count();
            if($parent_count>=1){
                //检测是否在分享里面
                $share = ShandongRenbaoArmyShare::find()->where([
                    'army_id'=>$parent_id
                ])->one();
                $parent = ShandongRenbaoArmy::find()->where([
                    'parent_id'=>$parent_id
                ])->one();
                if(!$share){
                    $share = new ShandongRenbaoArmyShare();
                    $share->army_id = $parent_id;  //数据库需要同步更新
                    $share->mobile = $parent->mobile;  //数据库需要同步更新
                    $share->rewards_id = $this->getShareRewards();  //数据库需要同步更新
                    $share->save();
                }
            }

            if($rewards_id){
                $rewards = ShandongRenbaoRewards::find()->where([
                    'id'=>$rewards_id
                ])->one();
                $rewards_name = $rewards->name;
            }else{
                $rewards_name = '';
            }

            $return = [
                'status'=>1,
                'data'=>['id'=>$id,'rewards_id'=>$rewards_id,'rewards'=>$rewards_name],

            ];

            //发送短信
            if($rewards_id>0){
                $this->sendSms($mobile,0,'恭喜您在临沂人保财险举行“测测你是什么军种”活动中获得超值大礼，奖券将在五个工作日内充入您山东人保财险公众号账号，还可邀请好友一起来抢！');
            }


        }catch (\Exception $exception){
            $return = [
                'status'=>0,
                'message'=>$exception->getMessage()
            ];
        }

        echo \GuzzleHttp\json_encode($return);
    }


    //确定分享的奖品id
    public function getShareRewards(){
        $numbers = $this->getNumbers();
        $rand = rand(0,100);

        if($rand<50){
            if($numbers[2]<1980){
                return 2;
            }
        }
        if($rand<75){
            if($numbers[3]<256){
                return 3;
            }
        }

        if($numbers[4]<530){
            return 4;
        }

        return 0;
    }


    public function  getNumbers(){
        $numbers= [] ;
        $totals = ShandongRenbaoArmy::find()->select(['rewards_id','count(1) as count'])->groupBy('rewards_id')->asArray()->all();
        foreach ($totals as $total){
            $numbers[$total['rewards_id']] = $total['count'];
        }

        //分享的，也合并
        $totals = ShandongRenbaoArmyShare::find()->select(['rewards_id','count(1) as count'])->groupBy('rewards_id')->asArray()->all();
        foreach ($totals as $total){
            $numbers[$total['rewards_id']] = $numbers[$total['rewards_id']]+$total['count'];
        }
        return $numbers;
    }




    public function getRewards($mobile){

        //先检测日期,先看抽奖是否在日期里面。。
        $time = time();


        //奖品数量
        $rewards = ShandongRenbaoRewards::find()->asArray()->all();
        $rewards_total  = ShandongRenbaoRewards::find()->sum('number');



        $numbers = $this->getNumbers();



        //考虑中奖概率
        $setting = Settings::find()->where([
            'key'=>'winning_probability'
        ])->one();
        $probalility = $setting->setting_value;
        $rand = rand(0,99);

        if($rand>=0 and $rand<=$probalility){
            $rand = rand(0,$rewards_total);
            $begin = 0;
            foreach($rewards as $reward){

                $last = $begin+$reward['number'];
                //分区间中奖
                if($numbers[$reward['id']]<$reward['number'] and $rand>=$begin and $rand<$last){
                    return $reward['id'];
                }
                $begin = $begin+$reward['number'];
            }
            return 0;

        }else{
            return 0;
        }
    }

    public function actionRewards(){
        return $this->render('rewards',[

        ]);
    }


    public function actionDelete()
    {
        $request = Yii::$app->request;
        $mobile = $request->get('mobile');
        $car = ShandongRenbaoArmy::deleteAll([
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

    public function actionDownload(){
        $filename ="data_export_" . date("Y-m-d") . ".csv";
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        $request = Yii::$app->request;
        $begin = $request->get('begin');
        $last = $request->get('last');
        $array = ShandongRenbaoArmy::find()->select([
            'mobile','rewards_id'
        ])->where([
            '>=','created_at',$begin
        ])->andWhere([
            '<','created_at',$last
        ])->asArray()->all();
        echo $this->array2csv($array);
        die();
    }

    public function array2csv(array &$array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
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

    public function actionAddressSave(){

        $request = Yii::$app->request;
        $rewards_id = $request->post('rewards_id');
        $mobile = $request->post('mobile');
        $name = $request->post('name');
        $address = $request->post('address');


        //检测地址是否正确，不正确，报错
        $army = ShandongRenbaoArmy::find()->where([
            'mobile'=>$mobile,
            'rewards_id'=>$rewards_id,
        ])->one();
        if(!$army){
            $return = [
                'status'=>0,
                'message'=>'中奖号码不对，请检查'
            ];
            echo \GuzzleHttp\json_encode($return);
        }else{
            $army->address = $address;
            $army->name = $name;
            $army->save();
            $return = [
                'status'=>1,
                'message'=>'您的地址保存成功'
            ];
            echo \GuzzleHttp\json_encode($return);
        }
    }



    public function actionApi(){
        $this->layout =false;
        $cars = ShandongRenbaoArmy::find()->all();

        return $this->render('api',[
            'cars'=>$cars
        ]);
    }

    public function actionTotal(){

        echo "以下为抽奖记录<br>";
        
        $total = ShandongRenbaoArmy::find()->where([
            'rewards_id'=>0
        ])->count();
        echo '没中奖人数为'.$total;
        echo "<br>";
        $rewards = ShandongRenbaoRewards::find()->asArray()->all();

        $numbers= [] ;
        $totals = ShandongRenbaoArmy::find()->select(['rewards_id','count(1) as count'])->groupBy('rewards_id')->asArray()->all();
        foreach ($totals as $total){
            $numbers[$total['rewards_id']] = $total['count'];
        }


        foreach($rewards as $reward){
            echo $reward['name'].'数量为：'.$numbers[$reward['id']];
            echo "<br>";
        }

        echo "以下为分享赠送记录<br>";
        $totals = ShandongRenbaoArmyShare::find()->select(['rewards_id','count(1) as count'])->groupBy('rewards_id')->asArray()->all();
        foreach ($totals as $total){
            echo self::$share_rewards[$total['rewards_id']].'数量为：'.$total['count'];
            echo "<br>";
        }

    }


    public function actionGroupTotal(){
        $totals = ShandongRenbaoArmy::find()->select(['group_id', 'counted' => 'count(*)']) ->groupBy('group_id')->asArray()->all();
        foreach($totals as $total){
            echo $total['group_id']."->".$total['counted'];
            echo "<br>";
        }


    }

    public function actionTest(){
        $this->getRewards(4545);
        die();

        $rewards_id = Yii::$app->session['shandong_renbao_group_id'] ;
        echo $rewards_id;
        die();
        $totals = ShandongRenbaoArmy::find()->select(['rewards_id','count(1) as count'])->groupBy('rewards_id')->asArray()->all();
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

    public function actionVerify(){
        return $this->render('verify',[

        ]);
    }

    public function actionSetTotal()
    {
        $request = Yii::$app->request;
        $army_base_count = $request->post('army_base_count');
        $army_multiple_count = $request->post('army_multiple_count');
        $army_base_count_settings = Settings::find()->where([
            'key'=>'army_base_count'
        ])->one();
        $army_multiple_count_settings = Settings::find()->where([
            'key'=>'army_multiple_count'
        ])->one();
        $army_base_count_settings->setting_value = $army_base_count;
        $army_base_count_settings->save();
        $army_multiple_count_settings->setting_value = $army_multiple_count;
        $army_multiple_count_settings->save();
    }

    public function actionSettings()
    {
        $army_base_count = Settings::find()->where([
            'key'=>'army_base_count'
        ])->one();
        $army_multiple_count = Settings::find()->where([
            'key'=>'army_multiple_count'
        ])->one();
        $total = ShandongRenbaoArmy::find()->count();
        $showTotal = $army_base_count->setting_value+$army_multiple_count->setting_value*$total;

        return $this->render('total',[
            'total' => $total,
            'army_base_count' => $army_base_count->setting_value,
            'army_multiple_count' => $army_multiple_count->setting_value,
            'showTotal' => $showTotal
        ]);
    }
}