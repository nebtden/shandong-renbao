<?php
namespace common\models;

use Yii;
class Menu extends  Base_model   {

    public static function tableName()
    {
        return '{{%menu}}';
    }
    //读取可用的菜单
    public static function readMenus($token){
        $modle=new  self();
        $menus = $modle->getData('*','all',"`token`='{$token}' and status = 1");
        $menuArr = array();
        foreach ($menus as $v){
            $menuArr[$v['pid']][] = $v;
        }
        return $menuArr;
    }
}