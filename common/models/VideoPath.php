<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4 0004
 * Time: 下午 4:35
 */
namespace common\models;

use common\components\AlxgBase;

class VideoPath extends AlxgBase
{
    protected $currentTable = '{{%video_path}}';


    public function getvideolist($companyid = 0){
        $where['status'] = 1;
        if(! empty($companyid)) $where['company_id'] = $companyid;
        $list = $this->table()->where($where)->orderBy(' sort ASC ')->all();
        return $list;
    }
    //是否显示简介
    public  static  $show_desc = [
        0 => '否',
        1 => '是'
    ];

    public  static  $status = [
        0 => '禁止观看',
        1 => '可观看'
    ];
}