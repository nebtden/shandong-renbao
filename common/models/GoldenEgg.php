<?php
/**
 * @title:砸金蛋抽奖活动
 * @time:2018-11-26
 */
namespace common\models;

use common\components\AlxgBase;
use yii;

class GoldenEgg extends Active
{

   public static function tableName()
   {
       return '{{%golden_egg}}';
   }

    /**
     * 根据工号和姓名查询
     * @param $jobNum
     * @param $name
     * @return null|static
     */
   public function selectName($jobNum,$name)
   {
       return self::findOne(['job_num' => $jobNum, 'name' => $name, 'is_del' =>0]);
   }

    /**根据ID查询
     * @param $id
     * @return null|static
     */
   public function selectId($id)
   {
       return self::findOne(['id' => $id,'is_del' =>0]);
   }

   public function prizeNum()
   {
       $prize['1'] = 10-$this->countPrize('1');
       $prize['2'] = 4990-$this->countPrize('2');
       return $prize;
   }

   public function countPrize($num)
   {
       return self::find()->where(['prize' => $num])->count();
   }

   public function getPrizeInfo($id)
   {
       $user = self::findOne($id);
       if($user->prize){
           return false;
       }
       return true;
   }

   public function batchRemove($params)
   {
       if (count($params) == 0 || !is_array($params)) {
           return false;
       }

       $sql = '';
       foreach ($params as $val){
           $sql .=' or id = '.$val;
       }
       $sql = substr($sql,3);
       $sql = 'update '.self::tableName().' set `is_del`=1 where '.$sql.';';
       $res = Yii::$app->db->createCommand($sql)->execute();
       return $res;
   }
}