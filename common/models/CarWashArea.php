<?php

namespace common\models;



class CarWashArea extends Base_model
{
    public  static function tableName()
    {
        return '{{%car_wash_area_new}}';
    }

    public function getProvince()
    {
        return $this->select("*",'type_id = 1')->cache(true,3600)->all();
    }

    /**
     * @param string $code  省份的code
     */
    public function getCity($pid = '')
    {
        if($pid)$where['parent_id'] = $pid ;
        $where['type_id'] = 2;
        return $this->select('*',$where)->all();
    }
    /**
     * @param string $code  市级的code
     */
    public function getArea($pid = '')
    {
        if($pid)$where['parent_id'] = $pid ;
        $where['type_id'] = 3;
        return $this->select('*' , $where)->all();
    }

    public function getLocation($address)
    {
        $pro = $address['province'];
        $ci = $address['city'];
        $ar = $address['district'];
        $provinceall = $this->select("*",'type_id = 1')->all();
        $city = $this->select("code,name,parent_id,id",'type_id = 2 AND name like "'.(string)$ci.'%"')->one();
        $area = $this->select("code,name,parent_id,id",'type_id = 3 AND name like "'.(string)$ar.'%"')->one();
        $province = [];
        foreach ($provinceall as $k=>$v ){
            if(strpos($pro,$v['name']) !== false){
                $province = $v;
                break;
            }
        }

        if(!$province && !$city && !$area){
            return false;
        }
        $cityList = $this->getCity($province['id']);
        $areaList = $this->getArea($city['id']);
        $data = [
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'cityList' => $cityList,
            'areaList' => $areaList,
        ];
        return $data;
    }

    public function getLocationById($pid)
    {
        $pro = sprintf('%03d',$pid['provIds']);
        $ci = $pro.sprintf('%03d',$pid['cityIds']);
        $ar = $ci.sprintf('%04d',$pid['districtIds']);
        $province = $this->select("code,name,parent_id,id",'type_id = 1 AND id = "'.$pro.'"')->one();
        $city = $this->select("code,name,parent_id,id",'type_id = 2 AND id = "'.$ci.'"')->one();
        $area = $this->select("code,name,parent_id,id",'type_id = 3 AND id = "'.$ar.'"')->one();
        if(!$province && !$city && !$area){
            return false;
        }
        $cityList = $this->getCity($province['id']);
        $areaList = $this->getArea($city['id']);
        $data = [
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'cityList' => $cityList,
            'areaList' => $areaList,
        ];
        return $data;
    }

    public function getLocationByPid($pid)
    {
        $pro = $pid['province'];
        $ci = $pid['city'];
        $ar = $pid['district'];
        $province = $this->select("code,name,parent_id,id",'type_id = 1 AND id = "'.$pro.'"')->one();
        $city = $this->select("code,name,parent_id,id",'type_id = 2 AND id = "'.$ci.'"')->one();
        $area = $this->select("code,name,parent_id,id",'type_id = 3 AND id = "'.$ar.'"')->one();
        if(!$province && !$city && !$area){
            return false;
        }
        $cityList = $this->getCity($province['id']);
        $areaList = $this->getArea($city['id']);
        $data = [
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'cityList' => $cityList,
            'areaList' => $areaList,
        ];
        return $data;
    }
}