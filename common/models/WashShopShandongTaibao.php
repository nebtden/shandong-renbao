<?php

namespace common\models;

use Yii;

class WashShopShandongTaibao extends Base_model
{

//    protected $currentTable = '{{%car_wash_shoplist_shandong_taibao}}';

    public static function tableName()
    {
        return '{{%car_wash_shoplist_shandong_taibao}}';
    }

    public function rules()
    {
        return [
            [['name','shop_id','prov','city','district','shengda_district',
                'shop_lng','shop_lat','distance','avator','images',
                'tel','address','service_starttime','service_endtime','score',
                'date','c_time','u_time','company_id','is_get'
                ], 'safe'],
        ];
    }



}