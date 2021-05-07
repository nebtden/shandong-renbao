<?php
/**
 * 3Des加解密
 * @time: 2018-11-01
 */

namespace common\components;

use Yii;

class Encrypt3Des
{
    private $key = "";
    private $iv = "";

    /**
     *
     *
     * @param string $key
     * @param string $iv
     */
    public function __construct($key,$iv)
    {
        if (empty($key) || empty($iv)) {
            echo 'key and iv is not valid';
            exit();
        }
        $this->key = $key;
        $this->iv = $iv;
    }

    public function urlSafeCode($str, $isEncrypt= true)
    {
        $str1 = ['=','/','+'];
        $str2 = ['.','_','-'];

        if(!$isEncrypt){
            list($str1, $str2)= [$str2,$str1];
        }

        return str_replace($str1,$str2,$str);

    }

    /**
     *加密
     * @param <type> $value
     * @return <type>
     */
    public function encrypt($value)
    {
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        $value = $this->PaddingPKCS7($value);
        @mcrypt_generic_init($td, $this->key, $this->iv);
        $ret = mcrypt_generic($td, $value);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $ret = $this->urlSafeCode(base64_encode($ret));
        return $ret;
    }

    /**
     *解密
     * @param <type> $value
     * @return <type>
     */
    public function decrypt($value)
    {
        $value = $this->urlSafeCode($value, $isEncrypt=false);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');

        mcrypt_generic_init($td, $this->key, $this->iv);
        $ret = trim(mdecrypt_generic($td, base64_decode($value)));
        $ret = $this->UnPaddingPKCS7($ret);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $ret;
    }


//    /**
//     * 加密
//     * @param $input
//     * @return string
//     */
//    public function encrypt($input)
//    {
//        $data = openssl_encrypt($input, 'DES-EDE3', $this->key, OPENSSL_RAW_DATA,$this->iv);
//        $data = base64_encode($data);
//        return $data;
//    }
//
//    /**
//     * 解密
//     * @param $input
//     * @return string
//     */
//    public function decrypt($input)
//    {
//        $input = $this->urlSafeCode($input, $isEncrypt=false);
//        $decrypted = openssl_decrypt(base64_decode($input), 'DES-EDE3', $this->key, OPENSSL_RAW_DATA,$this->iv);
//        return $decrypted;
//    }


    function PaddingPKCS7($data)
    {
        $block_size   = mcrypt_get_block_size('tripledes', MCRYPT_MODE_ECB);
        $padding_char = $block_size - (strlen($data) % $block_size);
        $data .= str_repeat(chr($padding_char), $padding_char);
        return $data;
    }

    function UnPaddingPKCS7($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

}