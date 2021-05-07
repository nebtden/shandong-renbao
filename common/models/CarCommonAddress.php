<?php
namespace common\models;

use Yii;
use common\components\AlxgBase;

class CarCommonAddress extends AlxgBase{
    protected $currentTable = "{{%car_common_address}}";

    /**
     * 更新或添加用户常用地址
     * @param array $data
     * @return $this|bool|int|string|\yii\db\Command
     */
    public function update($data = []){
        $now = time();
        $data['u_time'] = $now;
        if(isset($data['id'])){
            return $this->myUpdate($data);
        }else{
            $data['c_time'] = $now;
            return $this->myInsert($data);
        }
    }

    /**
     * 获取用户的常用地址
     * @param int $uid
     * @param int $limit
     * @return mixed
     */
    public function get_user_address($uid = 0,$limit = 2){
        $list = $this->table()->where(['uid' => $uid])->limit($limit)->orderBy("sort ASC")->all();
        return $list;
    }
}