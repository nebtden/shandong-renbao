<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 8:48
 */
namespace common\models;
use Yii;
class TravelList extends Base_model
{
    public $tablePK = 'id';//设置本表我主键
    public static function tableName()
    {
        return '{{%travel_list}}';
    }
    public function  getLuxianAll($where=[]){

        $luxianlist = $this->select(['id','title'],$where)->all();
        $luxianlist =  array_column($luxianlist,'title','id');
        return $luxianlist;
    }

}