<?php

namespace common\models;

class Goldeneggactivity extends Active
{
    public static function tableName()
    {
        return '{{%golden_egg_activity}}';
    }

    public static function getPrizeName($num)
    {
         $prize = self::findOne(1);
         switch ($num){
             case 1:
                 $name = $prize['first'];
                 break;
             case 2:
                 $name = $prize['second'];
                 break;
         }
         return $name;
    }
}