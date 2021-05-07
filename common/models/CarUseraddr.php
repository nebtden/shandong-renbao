<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\9\6 0006
 * Time: 17:41
 */

namespace common\models;


use common\components\AlxgBase;

class CarUseraddr extends AlxgBase
{
    protected $currentTable = '{{%car_useraddr}}';

    public function getUserAddr($uid, $map = '', $type = 'all')
    {
        $where = ' uid = ' . $uid;
        if ($map) {
            $where .= ' and ' . $map;
        }
        $res = $this->table()->select('*')->where($where)->$type();
        return $res;
    }

    public function getUserDefaultAddr($uid)
    {
        $res = $this->table()->select('*')->where('uid =' . $uid . ' and isdefault =1 ')->one();
        return $res;
    }

    public function clearUserDefault($uid)
    {
        $res = $this->table()->myUpdate(['isdefault' => 0], ['uid' => $uid]);
        return $res;
    }

    public function setUserDefault($id,$uid)
    {
        $this->clearUserDefault($uid);
        $res = $this->table()->myUpdate(['isdefault' => 1], ['id' => $id]);
        return $res;
    }

    public function addUserAddr($data)
    {
        if ($data['id']) {
            $res = $this->table()->myUpdate($data);
        } else {
            unset($data['id']);
            $res = $this->table()->myInsert($data);
        }
        return $res;
    }

    public function delUserAddr($id)
    {
        $res = $this->table()->myDel(['id' => $id]);
        return $res;
    }
}