<?php

namespace common\models;

use Yii;
use common\components\AlxgBase;


class CarApk extends AlxgBase
{

    protected $currentTable = '{{%car_apk}}';

    public function info($apk){
        return $this->table()->where(['appkey'=>$apk])->one();
    }
}