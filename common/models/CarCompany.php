<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 下午 4:40
 */

namespace common\models;

use Yii;
use common\components\AlxgBase;
use common\components\W;

class CarCompany extends AlxgBase
{
    protected $currentTable = '{{%car_company}}';

    public static $status = [
        '0' => '禁用',
        '1' => '正常'
    ];

    public function getCompany($fields=[],$where=[]){
        $companys=  $this->table()->select($fields)->where($where)->all();
        return $companys;
    }
}