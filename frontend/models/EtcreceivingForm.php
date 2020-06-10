<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\5\21 0021
 * Time: 13:58
 */

namespace frontend\models;


use yii\base\Model;
use Yii;

class EtcreceivingForm extends Model
{
    public $name;
    public $mobile;
    public $province;
    public $city;
    public $district;
    public $address;
    public $couponId;

    public function rules()
    {
        return [
            [['name','mobile','province','city','district','address'],'trim'],
            ['name','required','message'=>'收货人不能为空'],
            ['name', 'match', 'pattern' => '/^[x{4e00}-\x{9fa5}]{2,6}$/u','message'=>'收货人格式不正确'],
            ['mobile','match','pattern' => '/^1[0-9]\d{9}$/i','message'=>'手机号码格式不正确'],
            [['province','city','district','address'],'required','message'=>'收货地址不能为空'],
            ['address','required','message'=>'收货地址不能为空'],
            ['couponId','required','message'=>'优惠券号码不能为空'],
            ['couponId','integer','message'=>'优惠券号码格式不正确'],
        ];
    }

}