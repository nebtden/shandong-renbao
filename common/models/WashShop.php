<?php

namespace common\models;

use common\components\AlxgBase;
use common\components\DianDianWash;
use Yii;

class WashShop extends AlxgBase
{

     protected $currentTable = '{{%car_wash_shoplist}}';

    /**
     * 根据shopId查询门店
     * @param $shopId
     * @return bool
     */
    public function getShop($shopId)
    {
        $shopdetail = $this->table()->select()->where(['shopId' => $shopId])->one();
        if($shopdetail){
            return $shopdetail;
        }
        return false;
    }

    /**
     * 通过典典接口获取门店图片
     * @param $shopId
     * @return mixed
     */
    public function getImages($shopId)
    {
        $DianDianWash = new DianDianWash();
        $getData['shopId'] = $shopId;
        $res = $DianDianWash->offlineShopDetail($getData);
        if(!isset($res['data']['images'])){
            $res['data']['images'][0] = '/frontend/web/images/qiche.jpg';
        }

        return $res['data']['images'][0];

    }

    /**
     * 插入或者更新门店
     * @param $insertData
     * @return bool|mixed
     */
    public function insertOrUpdate($insertData,$column,$where)
    {

        $res = $this->table()->select()->where([''.$column.'' => $where])->one();;

        if(!$res){
            $result = self::table()->myInsert($insertData);
        }else {
            unset($insertData['shopId'],$insertData['score']);
            $result = self::table()->myUpdate($insertData,[''.$column.''=>$where]);
        }

        return $insertData;
    }









}