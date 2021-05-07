<?php

namespace common\models;

use common\components\Oilcard;
use Yii;
use common\components\AlxgBase;

class CarUseroilcard extends AlxgBase
{
    protected $currentTable = '{{%car_useroilcard}}';

    public function bind($data){
        //验证卡号
        $msg = ['status' => 1,'msg' => 'ok'];
        $card_no = $data['oil_card_no'];
        $card_type = $data['oil_card_type'];
        $type = Oilcard::check_oil_card_no($card_no);
        if($type === false){
            $msg['status'] = 0;
            $msg['msg'] = '当前卡号暂无法提供服务！';
            return $msg;
        }else{
            if($card_type != $type){
                $msg['status'] = 0;
                $msg['msg'] = '输入的卡号与类型不匹配！';
                return $msg;
            }
        }
        $data['u_time'] = time();
        if(isset($data['id'])){
            $this->myUpdate($data);
        }else{
            $data['c_time'] = $data['u_time'];
            $this->myInsert($data);
        }
        return $msg;
    }
}