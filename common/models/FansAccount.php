<?php
namespace common\models;

use Yii;
class FansAccount extends Base_model
{
    public $tablePK = 'id';//设置本表我主键

    public static function tableName()
    {
        return '{{%fans_account}}';
    }

    public static $level = [
        '0' => '普通会员',
        '1' => '认证会员',
    ];

    public static $userstatus = [
        '0' => '禁用',
        '1' => '正常',
        '2' => '风控'
    ];
    public static $useris_web = [
        '0' => '微信',
        '1' => 'web网页',
    ];
    /**
     * 返回用户是否是vip的信息
     * @param int $uid              用户uid
     * @return array|mixed|null     用户vip信息
     */
    public function isVip($uid = 0){
        if(!$uid) return null;
        $vip = $this->select("*",['uid' => $uid,'status' => 1])
                    ->one();
        if(empty($vip)) return null;
        return $vip['is_vip'] ? $vip : null;
    }
}