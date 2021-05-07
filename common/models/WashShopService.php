<?php

namespace common\models;

use common\components\AlxgBase;

class WashShopService extends AlxgBase
{
    protected $currentTable = '{{%car_wash_shoplist_service}}';

    /**
     * 门店服务列表
     * @param $id
     * @return bool
     */

    public function checkService($serviceId, $shopId)
    {
        $map['lv2ServiceTypeId'] = $serviceId;
        $map['shopId'] = $shopId;
        $service = $this->table()->select()->where($map)->one();
        if(!$service){
            return false;
        }
        return $service;
    }


}