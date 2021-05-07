<?php
/**
 * 免登录
 * 免手机绑定
 */

namespace common\components;

use Yii;

class NoLogin
{
    private $apk;
    private $secret;
    private $data;
    private $errno = 0;
    private $errmsg = 'success';



    public function __construct($apk, $secret)
    {
        $this->apk = $apk;
        $this->secret = $secret;
    }

    /**
     * 接收数据
     */
    protected function get_data()
    {
        $content = file_get_contents("php://input");
        $this->log($content);
        $data = json_decode($content, true);
        $this->data = $data;
    }

    /**
     * 获得接收到的数据
     * @return mixed
     */
    public function reciever(){
        //$data = [];
        if(!$this->data){
            $this->get_data();
        }
        return $this->data;
    }

    /**
     * 签名生成
     * @param $data
     * @return string
     */
    public function make_sign($data)
    {
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= $k . $v;
        }
        $str = $this->apk . $str . $this->secret;
        $sign = md5($str);
        return $sign;
    }

    /**
     * 签名验证
     * @param $sign
     * @param $data
     * @return bool
     */
    public function check_sign($sign, $data)
    {
        $mk_sign = $this->make_sign($data);
        return ($sign === $mk_sign);
    }

    /**
     * 生成标识符
     * @param $keys
     * @return string
     */
    public function generate_key($keys){
        $str = '';
        if($keys){
            $str = http_build_query($keys);
        }
        $str .= uniqid();
        $key = md5($str);
        return $key;
    }

    /**
     * 返回错误信息
     */
    public function get_error()
    {
        return ['errno' => $this->errno, 'errmsg' => $this->errmsg];
    }
    /**
     * 写日志
     */
    private function log($data)
    {
        $path = Yii::$app->basePath.'/web/log/sxgs/'.date('Y-m').'/';
        if(!is_dir($path)){
            mkdir($path,0755);
        }
        $file = fopen($path.date('Y-m-d').'.log','a+');
        $content = '======================'.date('Y-m-d H:i:s').'======================'.PHP_EOL;
        $content.= 'data:'.$data.PHP_EOL;
        fwrite($file,$content);
        fclose($file);
    }
}