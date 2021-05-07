<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 8:47
 */
namespace common\models;
use Yii;
class TravelData extends Base_model
{
    public $tablePK = 'id';//设置本表我主键
    public static function tableName()
    {
        return '{{%travel_data}}';
    }
    public function  getUserNum($where=[]){
        $num = $this->select('id',$where)->count();
        return $num;
    }
}