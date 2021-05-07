<?php
/**
 * Openssl加解密
 * @time: 2018-11-01
 */

namespace common\components;

use Yii;

class Openssl
{
    private $key = "";
    private $iv = "";
    private $method = '';

    /**
     *
     *
     * @param string $key
     * @param string $iv
     */
    public function __construct($key,$iv,$method)
    {

        if (empty($key)) {
            echo 'key is not valid';
            exit();
        }

        $this->key = $key;
        $this->iv = $iv;
        $this->method = $method;
    }


    /**
     * 加密
     * @param $input
     * @return string
     */
    public function encrypt($input)
    {
        $data = openssl_encrypt($input,$this->method, $this->key, OPENSSL_RAW_DATA, $this->iv);
        $data = base64_encode($data);
        return $data;
    }

    /**
     * 解密
     * @param $input
     * @return string
     */
    public function decrypt($input)
    {
        $decrypted = openssl_decrypt(base64_decode($input), $this->method, $this->key, OPENSSL_RAW_DATA,$this->iv);
        return $decrypted;
    }

}