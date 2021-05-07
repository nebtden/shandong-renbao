<?php

namespace common\models;



class Car_washarea extends Base_model
{
    public  static function tableName()
    {
        return '{{%car_wash_area}}';
    }

    public function getProvince()
    {
        return $this->select("code,name,pid",'LENGTH(pid) = 3')->cache(true,3600)->all();
    }

    /**
     * @param string $code  省份的code
     */
    public function getCity($pid = '')
    {
        if(!$pid) return false;

        return $this->select('code,name,pid','LENGTH(pid) = 6 AND pid like "'.(string)$pid.'%"')->all();
    }
    /**
     * @param string $code  市级的code
     */
    public function getArea($pid = '')
    {
        if(!$pid) return false;

        return $this->select('code,name,pid','LENGTH(pid) = 10 AND pid like "'.(string)$pid.'%"')->all();
    }

    public function getLocation($address)
    {
        $pro = $address['province'];
        $ci = $address['city'];
        $ar = $address['district'];
        $province = $this->select("code,name,pid",'LENGTH(pid) = 3 AND name like "'.(string)$pro.'%"')->one();
        $city = $this->select("code,name,pid",'LENGTH(pid) = 6 AND name like "'.(string)$ci.'%"')->one();
        $area = $this->select("code,name,pid",'LENGTH(pid) = 10 AND name like "'.(string)$ar.'%"')->one();
        if(!$province && !$city && !$area){
            return false;
        }
        $cityList = $this->getCity($province['pid']);
        $areaList = $this->getArea($city['pid']);
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
        $province = $this->select("code,name,pid",'LENGTH(pid) = 3 AND pid = "'.$pro.'"')->one();
        $city = $this->select("code,name,pid",'LENGTH(pid) = 6 AND pid = "'.$ci.'"')->one();
        $area = $this->select("code,name,pid",'LENGTH(pid) = 10 AND pid = "'.$ar.'"')->one();
        if(!$province && !$city && !$area){
            return false;
        }
        $cityList = $this->getCity($province['pid']);
        $areaList = $this->getArea($city['pid']);
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
        $province = $this->select("code,name,pid",'LENGTH(pid) = 3 AND pid = "'.$pro.'"')->one();
        $city = $this->select("code,name,pid",'LENGTH(pid) = 6 AND pid = "'.$ci.'"')->one();
        $area = $this->select("code,name,pid",'LENGTH(pid) = 10 AND pid = "'.$ar.'"')->one();
        if(!$province && !$city && !$area){
            return false;
        }
        $cityList = $this->getCity($province['pid']);
        $areaList = $this->getArea($city['pid']);
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