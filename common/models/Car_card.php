<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2 0002
 * Time: 下午 2:35
 */
namespace common\models;

use Yii;
use common\components\W;
class Car_card extends Base_model
{


    public static $status=[
        '1'=>'可兑换',
        '2'=>'已兑换',
        '4'=>'已申请兑换'
    ];
    //优惠券类型状态 0已删除，1可兑换，2已兑换，3已过期，4已申请兑换

    public static $is_bisable = [
        '0' => '正常',
        '1' => '禁用'
    ];
    public static function tableName()
    {
        return '{{%car_card}}';
    }
    //检查卡编号的唯一性
    public function checkCardNo($card_no = '',$id = 0){
        if(!$card_no) return false;
        $where = 'card_num = :card_num';
        $bind[':card_num'] = $card_no;
        if($id){
            $where .= ' and id <> :id';
            $bind[':id'] = $id;
        }
        $res = $this->select('count(*) as cot',$where,$bind)->one();
        return $res['cot'];
    }
    public function generateCardNo($len = 8){
        $card_no = W::createNoncestr($len);
        while ($this->checkCardNo($card_no)){
            $card_no = W::createNoncestr($len);
        }
        return $card_no;
    }

}