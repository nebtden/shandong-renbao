<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\6\20 0020
 * Time: 14:55
 */

namespace console\controllers;

use common\components\ShengDaCarWash;
use common\models\AreaTaibao;
use common\models\Car_washarea;
use common\models\WashShop;
use common\models\WashShopShandongTaibao;
use yii\console\Controller;
use common\components\W;
use Yii;

class ShandongTaibaoController extends Controller
{

//    public $cities =   ["枣庄市","淄博市","济南市","青岛市","东营市","烟台市","潍坊市","济宁市","泰安市","威海市","日照市","莱芜市","临沂市","德州市","聊城市","滨州市","菏泽市"];


    //获取订单信息
    //php yii shandong-taibao/get-all
    public function actionGetAll(){

        $cities = AreaTaibao::find()->select(['city']) ->groupBy('city')->asArray()->all();

        WashShopShandongTaibao::updateAll(['is_get'=>0]);
        AreaTaibao::updateAll(['is_get'=>0]);

        foreach ($cities as $city){

            //查找地区，

            $areas = AreaTaibao::find()->select(['id','district','shengda_district']) ->where([
                'city'=>$city['city'],
                'is_get'=>0,
            ])->asArray()->all();



            foreach ($areas as $area){
                $trans = Yii::$app->db->beginTransaction();

                //批量更新
                try{

                    $postData = [
                        'source' => Yii::$app->params['shengda_sourceApp'],
                        'isMap' => 1,
                        'page' => '1',
                        'pageSize' => '100'
                    ];

                    $postData['city'] = $city['city'];
                    $postData['area'] = $area['shengda_district'];

                    $conditions = [
                        'prov' =>'山东省',
                        'city' =>$city['city'],
                        'district' =>$area['shengda_district'],
                    ];


                    $shengDa = new ShengDaCarWash();
                    $result = $shengDa->merchantDistanceList($postData);
                    $result = strstr($result['encryptJsonStr'],'|',true);
                    $result = json_decode($result,true);
                    $shopList = $result['coEnterprises'];
                    if(!$shopList){
                        $area_model = AreaTaibao::findOne($area['id']);
                        $area_model->is_get = -1;  //这个地区的-1，表示没数据，要小心
                        $area_model->save();
                        $trans->commit();
                        continue;
                    }
//                    print_r($shopList);
                    $now = time();
                    $date = date('Y-m-d');
                    foreach($shopList as $val){
                        $point = $this->changePoint($val['map']['longitude'],$val['map']['latitude']);

                        //根据name更新，如果有，则添加到数据库，没有则删除
                        $name = $val['name'];

                        $shop = WashShopShandongTaibao::find()->where([
                            'name'=>$name,
                            'prov' => '山东省',
                            'city' => $city['city'],
                            'shengda_district' => $area['shengda_district'],
                        ])->one();
                        if($shop){
                            $shop->is_get = 1;
                            $shop->save();
                        }else{
                            $newData = [
                                'name' => $val['name'],
                                'shop_id' =>intval($val['map']['latitude']*1000000),
                                'prov' => '山东省',
                                'city' => $city['city'],
                                'district' => $area['district'],
                                'shengda_district' => $area['shengda_district'],
                                'shop_lng' => $point['lng'],
                                'shop_lat' => $point['lat'],
                                'distance' => round($val['distance'],2),
                                'avator' => $val['logo'],
                                'images' => $val['logo'],
                                'tel' => $val['telephone'],
                                'address' => $val['address'],
                                'service_starttime' => '09:00',
                                'service_endtime' => '18:00',
                                'score' => 4,
                                'date'=>$date,
                                'c_time' => $now,
                                'u_time' => $now,
                                'company_id' => 2,
                                'is_get' => 2,
                            ];
                            $shop = new  WashShopShandongTaibao();
                            $shop->attributes = $newData;
                            $res=$shop->save();
                            print_r($res);

                        }
                    };

                    //更新区域
                    $area_model = AreaTaibao::findOne($area['id']);
                    $area_model->is_get = 1;
                    $area_model->save();

                    //批量更新，对于没有获取到的，is_get = -1;
                    $conditions['is_get']=0;

                    print_r('$conditions');
                    print_r($conditions);
                    WashShopShandongTaibao::updateAll(['is_get'=>-1,'is_del'=>1],$conditions);


                    $trans->commit();

                    echo $area['district'].'success!';
                    echo "\n\r";
                }catch (\Exception $exception){
                    echo 'msge';
                    echo $exception->getMessage();
                    echo "\n\r";
                    $trans->rollBack();
                }

            }

//            die();
        }


    }

    //更新
    public function actionTest(){
        $postData = [
            'source' => Yii::$app->params['shengda_sourceApp'],
            'isMap' => 1,
            'page' => '1',
            'pageSize' => '100'
        ];

        $address = [
            'province' =>'山东',
            'city' =>'东营市',
            'district' =>'东城区',
        ];
        $postData['city'] = '东营市';
        $postData['area'] = '东城区';

        $location = (new Car_washarea())->getLocation($address);
        $shopStatus = 1;
        $shengDa = new ShengDaCarWash();
        $result = $shengDa->merchantDistanceList($postData);
        $result = strstr($result['encryptJsonStr'],'|',true);
        $result = json_decode($result,true);
        $shopList = $result['coEnterprises'];
        print_r($shopList);

//        WashShopShandongTaibao::updateAll(['is_get'=>-1],$conditions);
    }


    public function changePoint($lng, $lat)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        // $data['gg_lon'] = $z * cos($theta);
        // $data['gg_lat'] = $z * sin($theta);
        $gg_lon = $z * cos($theta);
        $gg_lat = $z * sin($theta);
        // 保留小数点后六位
        $data['lng'] = round($gg_lon, 8);
        $data['lat'] = round($gg_lat, 8);
        return $data;
    }


    public function actionPostAll()
    {

        $cities  = $this->cities;
        foreach ($cities as $city){
            $map = [
                'prov' =>'山东省',
                'city' =>$city,
            ];
            $shopStatus = 1;
            $map['is_del']=0;
            $map['is_post']=0;


            $shopList = WashShopShandongTaibao::find()->where($map)->asArray()->all();
            $counNum = count($shopList);
            if($counNum==0){
                continue;
            }
            $spCode = Yii::$app->params['shandongtaibao']['spCode'];
            $areasList = [];
            foreach($shopList as $val){
                $areasList[] = [
                    'branchName' => '山东分公司',
                    'provinceName' => $val['prov'],
                    'cityName' => $val['city'],
                    'areaName' => $val['district'],
                    'addressDetail' => $val['address'],
                    'commissionPoint' => $val['name'],
                    'contactPhone' => $val['tel'],
                    'inspectionType' => 16,
                    'sjId' => $spCode.$val['id'],
                    'spCode' => $spCode,
                    'serverType' => '05',

                ];
            }
            $params = [
                'sjAreasNum' => $counNum,
                'status' => $shopStatus,
                'sjAreasList' => $areasList
            ];

            $repuestData = [
                'RequestCode' => 'DW01007',
                'AppId' => 'cpic_e_rescue',
                'RequestBodyJson' => $params
            ];
            print_r($repuestData);

            $url = Yii::$app->params['shandongtaibao']['url'];
            $params = json_encode($repuestData,JSON_UNESCAPED_UNICODE);
            $res = W::http_post($url,$params);
            print_r($res);

            $res = json_decode($res,true);
            print_r($res);
            if($res['ReturnCode']==1){
                //数据库，是否推送
                Yii::$app->db->createCommand()
                    ->update(WashShopShandongTaibao::tableName(),
                        [ 'is_post'=>1 ], //columns and values
                        $map) //condition, similar to where()
                    ->execute();

            }else{
                die();
            }

        }
    }

    public function actionDeleteAll()
    {

        $cities  = $this->cities;
//        $cities = ['滨州市'];
        foreach ($cities as $city){
            $map = [
                'prov' =>'山东省',
                'city' =>$city,
//            'district' =>$postBody['district'],
            ];
            $shopStatus = 3;
            $map['is_del']=1;
            $map['is_post']=0;


            $shopList = WashShopShandongTaibao::find()->where($map)->asArray()->all();
            $counNum = count($shopList);
            if($counNum==0){
                continue;
            }
            $spCode = Yii::$app->params['shandongtaibao']['spCode'];
            $areasList = [];
            foreach($shopList as $val){
                $areasList[] = [
                    'branchName' => '山东分公司',
                    'provinceName' => $val['prov'],
                    'cityName' => $val['city'],
                    'areaName' => $val['district'],
                    'addressDetail' => $val['address'],
                    'commissionPoint' => $val['name'],
                    'contactPhone' => $val['tel'],
                    'inspectionType' => 16,
                    'sjId' =>  $val['shop_id'],
                    'spCode' => $spCode,

                ];
            }
            $params = [
                'sjAreasNum' => $counNum,
                'status' => $shopStatus,
                'sjAreasList' => $areasList
            ];

            $repuestData = [
                'RequestCode' => 'DW01007',
                'AppId' => 'cpic_e_rescue',
                'RequestBodyJson' => $params
            ];
            print_r($repuestData);

            $url = Yii::$app->params['shandongtaibao']['url'];
            $params = json_encode($repuestData,JSON_UNESCAPED_UNICODE);
            $res = W::http_post($url,$params);
            print_r($res);

            $res = json_decode($res,true);
//            print_r($res);
            if($res['ReturnCode']==1){
                //数据库，是否推送
                Yii::$app->db->createCommand()
                    ->update(WashShopShandongTaibao::tableName(),
                        [ 'is_post'=>1 ], //columns and values
                        $map) //condition, similar to where()
                    ->execute();

            }else{
                die();
            }


        }
    }

}