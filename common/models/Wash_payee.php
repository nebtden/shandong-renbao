<?php

namespace common\models;



class Wash_payee extends Base_model
{
    public static function tableName()
    {
        return "{{%wash_payee}}";
    }

    //状态
    public static $status = [0=>'删除',1=>'正常',2=>'禁用'];
}