<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\5\21 0021
 * Time: 14:55
 */

namespace common\components;


class SegwayDES
{
    private $key;
    private $iv = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
    private $algorithm;
    private $mode;

    public function __construct($key, $iv = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0", $mode = MCRYPT_MODE_CBC, $algorithm = MCRYPT_RIJNDAEL_128)
    {
        $this->key = $key;
        $this->iv = $iv;
        $this->algorithm = $algorithm;
        $this->mode = $mode;
    }

    /**
     * 填充
     * @param $text
     * @param $blocksize
     * @return string
     */
    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    public function encrypt($input)
    {
        $size = mcrypt_get_block_size($this->algorithm, $this->mode);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open($this->algorithm, '', $this->mode, '');
        //获取密钥的最大长度
        $ks = mcrypt_enc_get_key_size($td);
        $key = substr($this->key, 0, $ks);
        //加密向量值
        mcrypt_generic_init($td, $key, $this->iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return base64_encode($data);
    }


    public function decrypt($ciphertext)
    {
        $td = mcrypt_module_open($this->algorithm, '', $this->mode, '');
        $ciphertext = base64_decode($ciphertext);
        /*获取密钥的最大长度*/
        $ks = mcrypt_enc_get_key_size($td);
        $key = substr($this->key, 0, $ks);
        mcrypt_generic_init($td, $key, $this->iv);
        $data = mdecrypt_generic($td, $ciphertext);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = $this->pkcs5_unpad($data);
        return $data;
    }
}