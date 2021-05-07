<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/2/22
 * Time: 15:35
 * 人脸核身 公共控制器
 */

namespace common\components;

use Yii;
use GuzzleHttp\Client;


class FaceKernel
{

    protected $secretId = '';
    protected $secretKey = '';
    protected $url;
    protected $geturl = 'GETfaceid.tencentcloudapi.com/?';
    public function __construct()
    {
        $this->secretId = Yii::$app->params['FaceKernel']['secretId'];
        $this->secretKey = Yii::$app->params['FaceKernel']['secretKey'];
        $this->url = Yii::$app->params['FaceKernel']['url'];

    }

    protected function httpPost($url,$data)
    {
        $url = $this->url.$url;
        $res = W::http_post($url,$data);
        return $res;
    }

    /**
     * 实名核身鉴权 详情见api文档 连接（https://cloud.tencent.com/document/product/1007/31816）
     * @param array
     * @return array
     */
    public function DetectAuth($getdata)
    {
        $client = new Client();
        $url = $this->url;
        $data = [];
        $data['RuleId']=0;
        $data['Action']='DetectAuth';
        $data['Version']='2018-03-01';
        $data['Region']='ap-guangzhou';
        $data['Nonce']='1'.$getdata['companyid'].rand(10000,99999).time();
        $data['SecretId']= $this->secretId;
        $data['IdCard']=$getdata['IdCard'];
        $data['Name']=$getdata['Name'];
        $data['RedirectUrl'] =$getdata['RedirectUrl'];
        $data['Extra']=$getdata['Extra'];
        $data['Timestamp']=time();
        $string = $this->getString($data);
        $value = $this->geturl.$string;
        $secretKey = $this->secretKey;
        $signStr = base64_encode(hash_hmac('sha1', $value, $secretKey, true));
        $data['Signature']=$signStr;
        $res = $client->request('GET', $url,  ['query' =>$data]);
        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        return $return['Response'];

    }

    /**
     * 获取实名核身结果信息 详情见api文档 连接（https://cloud.tencent.com/document/product/1007/33560）
     * @param array
     * @return array
     */
    public function GetDetectInfo($getdata)
    {
        $client = new Client();
        $url = $this->url;
        $data = [];
        $data['RuleId']=0;
        $data['Action']='GetDetectInfo';
        $data['Version']='2018-03-01';
        $data['Region']='ap-guangzhou';
        $data['Nonce']= '2'.$getdata['companyid'].rand(10000,99999).time();//
        $data['BizToken']=$getdata['BizToken'];
        $data['SecretId']= $this->secretId;
        $data['Timestamp']=time();
        $string = $this->getString($data);
        $value = $this->geturl.$string;
        $secretKey = $this->secretKey;
        $signStr = base64_encode(hash_hmac('sha1', $value, $secretKey, true));
        $data['Signature']=$signStr;
        $res = $client->request('GET', $url,  ['query' =>$data]);
        $return_data = $res->getBody()->getContents();
        $return_arr = \GuzzleHttp\json_decode($return_data, true);
        $return = \GuzzleHttp\json_decode($return_arr['Response']['DetectInfo'], true);
        return $return;

    }


    /**
     * 请求参数处理
     * @param array $header
     * @return string
     */
    protected function getString($data)
    {
        ksort($data);
        $string = '';
        $i = 0;
        foreach($data as $key =>$val){
            if($i == 0){
                $string.=$key.'='.$val;
            }else{
                $string.='&'.$key.'='.$val;
            }
            $i++;
        }
        return $string;
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

}
