<?php

namespace common\models;

use Yii;
use common\components\AlxgBase;

class CarUserCarno extends AlxgBase
{
    protected $currentTable = "{{%car_usercarno}}";

    public static $car_type = [
        '1' => '普',
        '2' => '警',
        '3' => '学',
        '4' => '军',
        '5' => '武'
    ];

    public static $car_zimu = ['A','B','C','D','E','F','G','H','I','J','k','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

    public static $car_province = ['京','沪','浙','苏','粤','鲁','晋','冀','豫','川',
                               '渝','辽','吉','黑','皖','鄂','湘','赣','闽','陕',
                               '甘','宁','蒙','津','贵','云','桂','琼','青','新',
                               '藏'];

    /**
     * 获得用户绑定的车牌列表
     * @param int $uid
     * @return mixed
     */
    public function get_user_bind_car($uid = 0)
    {
        $map['uid'] = $uid;
        $map['status'] = 1;
        $list = $this->table()->where($map)->orderBy('id DESC')->all();
        return $list;
    }

    /**
     * 新增或更新车牌
     * @param array $data
     * @return $this|bool|int|string|\yii\db\Command
     */
    public function update_card($data = []){
        $update = false;
        $now = time();
        if(isset($data['id']) && $data['id']) $update = true;
        $data['u_time'] = $now;
        if($update){
            return $this->myUpdate($data);
        }else{
            unset($data['id']);
            $data['c_time'] = $now;
            return $this->myInsert($data);
        }
    }
}