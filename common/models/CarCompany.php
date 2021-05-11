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
use yii\db\Expression;
use common\components\W;

class CarCompany extends AlxgBase
{
    protected $currentTable = '{{%car_company}}';

    public static $status = [
        '0' => '禁用',
        '1' => '正常'
    ];

    public function getCompany($fields=[],$where=[]){
        $companys=  $this->table()->select($fields)->where($where)->orderBy('browse_num DESC')->all();
        return $companys;
    }
    //累加客户公司查询次数
    public function browseNum($id){
        if(empty($id)) return false ;
        $this->myUpdate(['browse_num' => new Expression("browse_num + 1")],['id'=>$id]);
        return true;
    }

}