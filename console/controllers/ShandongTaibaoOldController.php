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

        WashShopShandongTaibao::updateAll(['is_get'=>0,'is_del'=>0]);
        AreaTaibao::updateAll(['is_get'=>0]);

        $cities = AreaTaibao::find()->select(['city'])
            ->where([
                'is_get'=>0
            ])
            ->andWhere([
                '!=','city','青岛市'
            ])
            ->groupBy('city')
            ->asArray()
            ->all();


        foreach ($cities as $city){

            //查找地区，
            echo 1;

            $areas = AreaTaibao::find()->select(['id','district','shengda_district']) ->where([
                'city'=>$city['city'],
                'is_get'=>0,
            ])->asArray()->all();


            echo 2;
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


                    $shengDa = new ShengDaCarNewApi();
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
                    echo 3;
                    foreach($shopList as $val){
                        $point = $this->changePoint($val['map']['longitude'],$val['map']['latitude']);

                        //根据name更新，如果有，则添加到数据库，没有则删除
                        $name = $val['name'];

                        echo 4;

                        $shop = WashShopShandongTaibao::find()->where([
                            'name'=>$name,
                            'prov' => '山东省',
                            'city' => $city['city'],
                            'shengda_district' => $area['shengda_district'],
                        ])->one();
                        echo 5;
                        if($shop){
                            if($shop->is_del==1){
//                                $shop->is_del=2;
                                $shop->is_post=0;
                            }
                            $shop->tel = $val['telephone'];
                            $shop->address =  $val['address'];
                            $shop->is_get = 1;
                            $shop->save();
                            echo 6;
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
                            echo 7;
                            $shop = new  WashShopShandongTaibao();
                            $shop->attributes = $newData;
                            $res=$shop->save();
                            echo 8;
                            print_r($res);

                        }
                        echo 5;
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
            'city' =>'潍坊市',
            'district' =>'昌邑市',
        ];
        $postData['city'] = '潍坊市';
        $postData['area'] = '昌邑市';

        $location = (new Car_washarea())->getLocation($address);
        $shopStatus = 1;
        $shengDa = new ShengDaCarNewApi();
        $result = $shengDa->merchantDistanceList($postData);
        $result = strstr($result['encryptJsonStr'],'|',true);
        $result = json_decode($result,true);
        $shopList = $result['coEnterprises'];
        print_r($shopList);

    }

    public function actionUpdate(){

        $map = [
            'prov' =>'山东省',
            'city' =>'潍坊市',
        ];
        //  1 新增  2 更新 3删除
        $shopStatus = 1;
        $map['is_get']=3;
//        $map['is_post']=0;
//        $map['is_post']=0;
        $res = $this->send($map,$shopStatus);
        print_r($res);
        if($res){
            if($res['ReturnCode']==1){
                //数据库，是否推送
                Yii::$app->db->createCommand()
                    ->update(WashShopShandongTaibao::tableName(),
                        [ 'is_get'=>1 ], //columns and values
                        $map) //condition, similar to where()
                    ->execute();

            }else{
                die('111');
            }
        }
    }


    /**
     * @throws \Exception
     */
    public function actionTbOrderStatusTest(){
        $tbObj = new Car_wash_order_taibao();

        $code ='KlFjl6bhtekaJQghjcgpuA==';
        $phone = '15123442221';
        $tbOrder = $tbObj->table()->where([
            'consumer_code'=>$code,
            'apply_phone'=>$phone,
            'status' => 3])->one();
        if(!$tbOrder){
            throw new \Exception('订单不存在');
        }
        $result = $this->tbOrderStatus($tbOrder,'DW01002','1006','0');
        if(!$result){
            throw new \Exception('人保接口调用失败');
        }
        if($result['ReturnCode'] == 0){
            throw new \Exception('错误代码:'.$result['ErrorCode'].',错误描述:'.$result['ErrorMessage']);
        }
        $tbOrder['status'] = ORDER_SUCCESS;
        $tbOrder['equity_status'] = ORDER_SUCCESS;
        $tbOrder['u_time'] = time();
        $res = (new Car_wash_order_taibao())->myUpdate($tbOrder);
        if(!$res){
            throw new \Exception('订单状态写入失败');
        }
    }

    private function tbOrderStatus($washOrder,$code,$status,$type)
    {
        $params = [
            'TicketId' => $washOrder['ticket_id'],
            'ServiceType' => $washOrder['service_type'],
            'SurveyUnitCode' => $status,
            'TicketType' => $type,
            'StatusTime' => date('Y-m-d H:i:s',time()),
            'TheTimeStamp' => (string)sprintf('%.0f', microtime(true)*1000),
        ];

        $repuestData = [
            'RequestCode' => $code,
            'AppId' => "cpic_e_rescue",
            'RequestBodyJson' => $params
        ];

        $url = Yii::$app->params['shandongtaibao']['url'];
        $params = json_encode($repuestData);
        $res = W::http_post($url,$params);
        (new DianDian())->requestlog($url,$params,json_encode($res,JSON_UNESCAPED_UNICODE),'taibaowash',$status,'taibaowash');

        return json_decode($res,true);
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

        //$cities  = $this->cities;
		$cities = AreaTaibao::find()->select(['city']) ->where([
            '!=','city','青岛市'
        ])->groupBy('city')->asArray()->all();

        foreach ($cities as $city){
            $map = [
                'prov' =>'山东省',
                'city' =>$city,
            ];

            //首先所有数据删除一次 //  1 新增  2 更新 3删除
            $shopStatus = 3;
//            $map['is_del']=0;  //表示删除了，重新上传
            $map['is_post']=0;
            $result = $this->delete($map,$shopStatus);
            if(!$result){
                continue;
            }


            //  1 新增  2 更新 3删除
            $shopStatus = 1;
            $map['is_get']=1;  //表示删除了，重新上传
            $map['is_post']=0;
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
                    die('111');
                }
            }

            //  1 新增  2 更新 3删除
            $shopStatus = 1;
            $map['is_get']=2;
            $map['is_post']=0;
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
                    die('111');
                }
            }


//            $shopStatus = 1;
//            $map['is_del']=1;  //表示删除了，重新上传
//            $map['is_post']=0;
//            $res = $this->delete($map,$shopStatus);

//            $shopStatus = 3;
//            $map['is_del']=1;
//            $map['is_post']=1;
//            $res = $this->send($map,$shopStatus);
//            if($res){
//                if($res['ReturnCode']==1){
//                    //数据库，是否推送
//                    Yii::$app->db->createCommand()
//                        ->update(WashShopShandongTaibao::tableName(),
//                            [ 'is_post'=>2 ], //columns and values
//                            $map) //condition, similar to where()
//                        ->execute();
//
//                }else{
//                    die('333');
//                }
//            }


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
                //数据库，是否推送
//                Yii::$app->db->createCommand()
//                    ->update(WashShopShandongTaibao::tableName(),
//                        [ 'is_post'=>1], //columns and values
//                        $map) //condition, similar to where()
//                    ->execute();

            }
        }
        return true;


    }



    public function actionDeleteAll()
    {

        $cities = ['烟台市'];
        foreach ($cities as $city){
            $map = [
                'prov' =>'山东省',
                'city' =>$city,
            ];
            $shopStatus = 3;
            $map['is_del']=3;
            $map['is_post']=0;


            $shopList = WashShopShandongTaibao::find()->where($map)->asArray()->all();
            $counNum = count($shopList);
            if($counNum==0){
                continue;
            }
            $spCode = Yii::$app->params['shandongtaibao']['spCode'];
            $areasList = [];
            foreach($shopList as $val){
                print_r($val);
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
                        [ 'is_del'=>1,'is_post'=>1 ], //columns and values
                        $map) //condition, similar to where()
                    ->execute();

            }else{
                print_r($res);

            }


        }
    }

}