<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 8:50  tpy_travel_users
 */
namespace common\models;
use Yii;
class TravelUsers extends Base_model
{
    public $tablePK = 'id';//设置本表我主键
    public static function tableName()
    {
        return '{{%travel_users}}';
    }
    //
    public function  getUserAll($filde=[],$where=[]){

        $userlist = $this->select($filde,$where)->all();
        return $userlist;
    }
    public function  getUserOne($where){
        $userlist = $this->select('*',$where)->one();
        return $userlist;
    }

}