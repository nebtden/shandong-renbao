<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 8:46  tpy_travel_company
 */
namespace common\models;
use Yii;
class TravelCompany extends Base_model
{
    public $tablePK = 'id';//设置本表我主键
    public static function tableName()
    {
        return '{{%travel_company}}';
    }
    public function  getCompanyAll($where=[],$res=true){

        $companylist = $this->select(['id','name','pid'],$where)->all();
        if($res)$companylist =  array_column($companylist,'name','id');
        return $companylist;
    }
    public function  getCompanyOne($where=[]){

        $companyinfo = $this->select('*',$where)->one();
        return $companyinfo;
    }

}