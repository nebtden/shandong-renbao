<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 下午 4:40
 */

namespace common\models;

use Yii;
use common\components\AlxgBase;
use common\components\W;

class VideoCompany extends AlxgBase
{
    protected $currentTable = '{{%video_company}}';

    public static $status = [
        '0' => '禁用',
        '1' => '正常'
    ];

    public  function getCompanyInfo($companyid = 0){
        $where['status'] = 1;
        if(! empty($companyid)) {
            $where['id'] = $companyid;
        }else{
            return [];
        }
        $info = $this->table()->where($where)->one();
        return $info;
    }
    public  function getCompanyAll(){
        $where['status'] = 1;
        $info = $this->table()->where($where)->all();
        return $info;
    }
}