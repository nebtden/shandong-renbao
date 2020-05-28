<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\6\20 0020
 * Time: 14:55
 */

namespace console\controllers;

use common\components\DianDian;
use common\components\ShengDaCarNewApi;
use common\models\AreaTaibao;
use common\models\Car_wash_order_taibao;
use common\models\Car_washarea;
use common\models\CarWashArea;
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
        echo 'begin';
        echo 'begin';
        WashShopShandongTaibao::updateAll(['is_get'=>0]);
        echo 'begin';
        CarWashArea::updateAll(['is_get'=>0]);

        echo 'cash begin!';

        $cities = CarWashArea::find()->select(['id','name','code'])
            ->where([
                'type_id'=>2,
                'parent_id'=>46, //山东省
            ])
            ->andWhere([
                '!=','id','47'  //排查青岛市
            ])
            ->asArray()
            ->all();

        echo 'city begin!';

        foreach ($cities as $city){

            //查找地区，
            echo 1;

            $areas = CarWashArea::find()->where([
                'type_id'=>3,
                'parent_id'=>$city['id']
            ])->asArray()->all();


            echo 2;

            foreach ($areas as $area){
                try{
                    echo $area['name'];
                    $this->area($city,$area);

                }catch (\Exception $exception){
                    echo 'message';
                    echo $exception->getMessage();

                }

            }

        }


    }

    //更新
    public function area($city,$area){
        // $trans = Yii::$app->db->beginTransaction();

        $postData = [];

        $postData['sourceCode'] = Yii::$app->params['shengda_sourceApp_new'];
        $postData['serviceId'] = 5044;
        $postData['cityName'] = $city['name'];
        $postData['cityNumber'] = $city['code'];
        $postData['areaNumber'] = $area['code'];
        $postData['pageNo'] = 1;
        $postData['pageSize'] = 50;

        $conditions = [
            'prov' =>'山东省',
            'city' =>$city['name'],
            'shengda_district' =>$area['name'],
        ];


        $shengDa = new ShengDaCarNewApi();
        $result = $shengDa->merchantDistanceList($postData);

        $res=$result['data'];
        $resultarr = json_decode( $res,true);
        $resultJson = $shengDa->decrypt($resultarr['encryptJsonStr']);// ($resultarr['encryptJsonStr'],'|',true);
        $resultdata = explode('|',$resultJson);
        $shoparr = json_decode( $resultdata[0],true);
        $shopList = $shoparr['coEnterprises'];


        if(!$shopList){
            $area_model = AreaTaibao::findOne($area['id']);
            $area_model->is_get = -1;  //这个地区的-1，表示没数据，要小心
            $area_model->save();
            //$trans->commit();
            return false;
        }

        $shengDa->log('',\GuzzleHttp\json_encode($postData,JSON_UNESCAPED_UNICODE),\GuzzleHttp\json_encode($shopList,JSON_UNESCAPED_UNICODE));
//                    print_r($shopList);
        $now = time();
        $date = date('Y-m-d');
        echo 3;
        foreach($shopList as $val){

            $coordinate = $val['coordinate'];
            $point = explode(',',$coordinate);
            $point = $this->changePoint($point[0],$point[1]);

            //根据name更新，如果有，则添加到数据库，没有则删除
            $name = $val['shopName'];

            echo 4;

            $shop = WashShopShandongTaibao::find()->where([
                'name'=>$name,
                'prov' => '山东省',
                'city' => $city['name'],
                'shengda_district' => $area['name'],
            ])->one();
            echo $name.'---';
            echo $city['name'].'--';
            echo $area['name'].'--';
            echo '--5';
            if($shop){
                if($shop->is_del==1){
//                                $shop->is_del=2;
                    $shop->is_post=0;
                }
                $shop->is_del=0;
                $shop->is_get= 1;
                $shop->tel = $val['telephone'];
                $shop->address =  $val['address'];
                $shop->shop_lng = $point['0'];
                $shop->shop_lat = $point['1'];
                $shop->save();
                echo 6;
            }else{
                $newData = [
                    'name' => $name,
                    'shop_id' =>intval($point[0]*1000000),
                    'prov' => '山东省',
                    'city' => $city['name'],
                    'district' => $area['taibao_name'],
                    'shengda_district' => $area['name'],
                    'shop_lng' => $point['0'],
                    'shop_lat' => $point['1'],
                    'distance' => 0,
                    'avator' => $val['logoImgPath'],
                    'images' => $val['logoImgPath'],
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
                echo 7;
                $shop = new  WashShopShandongTaibao();
                $shop->attributes = $newData;
                $res=$shop->save();
                echo 8;
                print_r($res);

            }
            echo 9;
            echo "\n\r";
        };

        //更新区域
        $area_model = CarWashArea::findOne($area['id']);
        $area_model->is_get = 1;
        $area_model->save();

        //批量更新，对于没有获取到的，is_get = -1;
        $conditions['is_get']=0;

        print_r('$conditions');
        print_r($conditions);
        WashShopShandongTaibao::updateAll(['is_get'=>-1,'is_del'=>1],$conditions);


        // $trans->commit();


        echo $area['name'].'success!';
        echo "\n\r";
        echo "\n\r";
        echo "\n\r";
        echo "\n\r";
    }



    public function actionUpdate(){
        $map = [
            'prov' =>'山东省',
            'city' =>'烟台市',
            'is_post'=>0
        ];

        //首先所有数据删除一次 //  1 新增  2 更新 3删除
        $shopStatus = 3;
        //$map['is_get']=-1;
        $res = $this->delete($map,$shopStatus);

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
        $data['0'] = round($gg_lon, 8);
        $data['1'] = round($gg_lat, 8);
        return $data;
    }


    public function actionPostAll()
    {

        $cities = CarWashArea::find()->select(['id','name','code'])
            ->where([
                'type_id'=>2,
                'parent_id'=>46, //山东省
            ])
            ->andWhere([
                '!=','id','47'  //排查青岛市
            ])
            ->asArray()
            ->all();

        foreach ($cities as $city){
            echo $city['name'].'begin !';
            $map = [
                'prov' =>'山东省',
                'city' =>$city['name'],
                'is_post'=>0
            ];

            //首先所有数据删除一次 //  1 新增  2 更新 3删除
            $shopStatus = 3;
//            $map['is_get']=-1;
            $res = $this->delete($map,$shopStatus);
            if($res){
                Yii::$app->db->createCommand()
                    ->update(WashShopShandongTaibao::tableName(),
                        [ 'is_post'=>1 ], //columns and values
                        $map) //condition, similar to where()
                    ->execute();
            }

            //  1 新增  2 更新 3删除
            $shopStatus = 1;
            $map['is_get']=1;  //表示删除了，重新上传
            $res = $this->send($map,$shopStatus);


            print_r($res);
            if($res){
                if($res['ReturnCode']==1){
                    //数据库，是否推送
                    Yii::$app->db->createCommand()
                        ->update(WashShopShandongTaibao::tableName(),
                            [ 'is_post'=>1 ], //columns and values
                            $map) //condition, similar to where()
                        ->execute();

                }else{
                    // continue;
                    //die('111');
                }
            }

            //  1 新增  2 更新 3删除
            $shopStatus = 1;
            $map['is_get']=2;
            $res = $this->send($map,$shopStatus);


            print_r($res);
            if($res){
                if($res['ReturnCode']==1){
                    //数据库，是否推送
                    Yii::$app->db->createCommand()
                        ->update(WashShopShandongTaibao::tableName(),
                            [ 'is_post'=>1 ], //columns and values
                            $map) //condition, similar to where()
                        ->execute();

                }else{
                    // continue;
                    //die('111');
                }
            }


        }
    }

    private function send($map,$shopStatus){
        $shopList = WashShopShandongTaibao::find()->where($map)->asArray()->all();

        if(!$shopList){
            return false;
        }
        $counNum = count($shopList);
        if($counNum==0){
            return [];
        }
        $spCode = Yii::$app->params['shandongtaibao']['spCode'];
        $areasList = [];
        foreach($shopList as $val){
            ///
            if($val['avator_local']){
                $valavator = $val['avator_local'];
            }else{
                $valavator = 'taibao_images/logo.jpg';
            }

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
                'storePictures' => 'http://www.yunche168.com/'.$valavator,
                'serverType' => '05',

            ];
        }
        if(!$areasList){
            return false;
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
//        print_r($repuestData);

        $url = Yii::$app->params['shandongtaibao']['url'];
        $params = json_encode($repuestData,JSON_UNESCAPED_UNICODE);
        $res = W::http_post($url,$params);
        $res = json_decode($res,true);

        return $res;
    }

    private function delete($map,$shopStatus){
        $shopList = WashShopShandongTaibao::find()->where($map)->asArray()->all();
        if(!$shopList){
            return false;
        }
        $counNum = count($shopList);
        if($counNum==0){
            return [];
        }
        $spCode = Yii::$app->params['shandongtaibao']['spCode'];

        foreach($shopList as $val){
            ///
            if($val['avator_local']){
                $valavator = $val['avator_local'];
            }else{
                $valavator = 'taibao_images/logo.jpg';
            }
            $areasList = [];
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
                'storePictures' => 'http://www.yunche168.com/'.$valavator,
                'serverType' => '05',

            ];

            $params = [
                'sjAreasNum' => 1,
                'status' => $shopStatus,
                'sjAreasList' => $areasList
            ];

            $repuestData = [
                'RequestCode' => 'DW01007',
                'AppId' => 'cpic_e_rescue',
                'RequestBodyJson' => $params
            ];


            $url = Yii::$app->params['shandongtaibao']['url'];
            $params = json_encode($repuestData,JSON_UNESCAPED_UNICODE);
            $res = W::http_post($url,$params);
            $res = json_decode($res,true);

            if($res['ReturnCode']!=1){
                echo '删除失败！';

            }
        }
        return true;
    }


    public function actionTest(){
        WashShopShandongTaibao::updateAll(['is_get'=>0]);
    }

}