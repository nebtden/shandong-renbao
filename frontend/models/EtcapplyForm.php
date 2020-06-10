<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\5\23 0023
 * Time: 17:43
 */

namespace frontend\models;


use yii\base\Model;

class EtcapplyForm extends Model
{
    public $username;
    public $cert_no;
    public $cert_front;
    public $cert_back;
    public $user_hold_cert;
    public $contact;
    public $link_phone;
    public $link_address;
    public $plate_no;
    public $driving_license_front;
    public $driving_license_back;
    public $car_head;

    public function rules()
    {
        return [
            [['username','cert_no','cert_front','cert_back','user_hold_cert','contact','link_phone','link_address'],'trim'],
            ['username','match', 'pattern' => '/^[\x{4e00}-\x{9fa5}]{2,6}$/u','message'=>'持卡人姓名格式不正确'],
            ['cert_no','match', 'pattern' => '/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/','message'=>'身份证格式不正确'],
            ['cert_front','required', 'message'=>'请上传身份证正面照'],
            ['cert_back','required', 'message'=>'请上传身份证反面照'],
            ['user_hold_cert','required', 'message'=>'请上传手持身份照'],
            ['contact','match','pattern'=>'/^[\x{4e00}-\x{9fa5}]{2,6}$/u', 'message'=>'联系人姓名格式不正确'],
            ['link_phone','match','pattern'=>'/^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/', 'message'=>'联系人手机号码格式不正确'],
            ['link_address','match','pattern'=>'/^[x{4e00}-\x{9fa5}]{1,50}$/u', 'message'=>'联系人地址格式不正确'],
            ['plate_no','match','pattern'=>'/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{5}$/u', 'message'=>'车牌格式不正确'],
            ['driving_license_front','required', 'message'=>'请上传行驶证正面照'],
            ['driving_license_back','required', 'message'=>'请上传行驶证反面照'],
            ['car_head','required', 'message'=>'请上传车头正面照'],
        ];
    }

}