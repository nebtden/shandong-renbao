<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/5 0005
 * Time: 上午 9:06
 */
namespace common\components;
use Yii;

class NationalLife
{

    protected $appkey = '';    //
    protected $secret = ''; //注册平台的手机号
    protected $company = 31; //客户公司名称
    protected $url;

    public function __construct()
    {
        $this->appkey = Yii::$app->params['national_life']['appkey'];
        $this->secret = Yii::$app->params['national_life']['secret'];
        $this->url = Yii::$app->params['national_life']['url'];

    }

    protected function httpPost($url,$data)
    {
        $url = $this->url.$url;
        $res = W::http_post($url,$data);
        return $res;
    }
    public function notice($data)
    {
        $data['sign'] = $this->sign($data);
        $url = '/api/order/order/ycjdOl/notify';
        return  $this->httpPost($url,$data);
    }
    /**
     * 设置请求头
     * @param array $header
     * @return array
     */
    protected function setHeaders($header=[])
    {
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        if($header){
            $headers = array_merge($headers,$header);
        }
        return $headers;
    }

    /**
     * 设置请求头
     * @param array $header
     * @return array
     */
    public function sign($data)
    {
        $str = '';
        $count = count($data);
        $i = 0;
        foreach ($data as $k => $v) {
            if($i == $count-1){
                $str .= $k .'='. $v ;
            }else{
                $str .= $k .'='. $v . '&';
            }
            $i++;
        }
        $sign = strtoupper(md5($str));
        return $sign;
    }

}