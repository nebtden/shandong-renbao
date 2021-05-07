<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 8:49  tpy_travel_list_date
 */
namespace common\models;

use Yii;

class TravelListDate extends Base_model
{
    public $tablePK = 'id';//设置本表我主键

    public static $status = [
        1 => '上架',
        2 => '下架'
    ];
    public static function tableName()
    {
        return '{{%travel_list_date}}';
    }
    public function  getDateAll($where=[],$res=true){

        $datelist = $this->select('*',$where)->all();
        if($res)$datelist =  array_column($datelist,'date','id');
        return $datelist;
    }
    public function  getDateOne($where=[]){

        $dateinfo = $this->select('*',$where)->one();
        return $dateinfo;
    }

}