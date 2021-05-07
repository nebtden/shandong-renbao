<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/21 0021
 * Time: 上午 9:46
 */
namespace common\models;

use Yii;
use common\components\AlxgBase;

class Car_tuhunotice extends AlxgBase
{
    protected $currentTable = '{{%car_tuhunotice}}';


    /*
     * 洗车卡券核销错误代码
     * 100000	成功
     * 400001	传入参数错误
     * 400002	缺少方法名参数
     * 400003	参数不合法
     * 400004	缺少Sign参数
     * 400005	签名验证错误
     * 400006	访问太频繁
     */
    public static $errmsg = [
        '100000' => '成功',
        '400001' => '传入参数错误',
        '400002' => '缺少方法名参数',
        '400003' => '参数不合法',
        '400004' => '缺少Sign参数',
        '400005' => '签名验证错误',
        '400006' => '访问太频繁',
    ];
}