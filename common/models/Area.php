<?php

namespace common\models;
use Yii;

class Area extends Base_model
{
    public  static function tableName()
    {
        return '{{%area}}';
    }
    public function getProvince(){
        return $this->select("code,name",'LENGTH(code) = 3')->cache(true,3600)->all();
    }

    /**
     * @param string $code  省份的code
     */
    public function getCity($code = ''){
        if(!$code) return false;
        return $this->select('code,name','LENGTH(code) = 6 AND code like "'.(string)$code.'%"')->all();
    }
    /**
     * @param string $code  市级的code
     */
    public function getArea($code = ''){
        if(!$code) return false;
        return $this->select('code,name','LENGTH(code) = 9 AND code like "'.(string)$code.'%"')->all();
    }
}

?>