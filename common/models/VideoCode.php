<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4 0004
 * Time: 下午 4:35
 */
namespace common\models;

use common\components\AlxgBase;
use common\components\W;
class VideoCode extends AlxgBase
{
    protected $currentTable = '{{%video_code}}';
    //1：进行中，0：失败，2：成功
    public static $status = [
        '0' => '禁止观看',
        '1' => '可观看',
        '2' => '观看次数用完'
    ];

    //检查卡编号的唯一性
    public function checkCardNo($fields,$code = '', $id = 0)
    {
        if (!$code) return false;
        $where = ' "' . $fields . '" = "' . $code . '" ';
        if ($id) $where .= ' and id <> ' . $id;
        $res = $this->table()->select('count(id) as cot')->where($where)->one();
        return $res['cot'];
    }

    public function generateCardNoxu($fields,$len = 8)
    {
        $code = W::createNonceViewcode($len);
        while ($this->checkCardNo($fields,$code)) {
            $code= W::createNonceViewcode($len);
        }
        return $code;
    }

}