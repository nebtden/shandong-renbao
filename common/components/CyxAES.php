<?php

namespace common\components;


class CyxAES
{
    public static $block_size = 32;
    private $key;
    private $iv="\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
    private $algorithm;
    private $mode;

    public function __construct($key, $iv="\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0", $mode = MCRYPT_MODE_CBC, $algorithm = MCRYPT_RIJNDAEL_128)
    {
        $keykey = hash('sha256', $key);
        $ccccc = self::str2bin($keykey);
        $this->key =substr($ccccc,0,32);
        $this->iv = $iv;
        $this->algorithm = $algorithm;
        $this->mode = $mode;
    }
    //aes加密
    public function AesEncrypt($toEncrypt)
    {
        $content = self::addPKCS7Padding($toEncrypt);
        $Encrypt = mcrypt_encrypt($this->algorithm, $this->key, $content, $this->mode, $this->iv);
        return base64_encode($Encrypt);
    }
    //PKCS7填充
    public static function addPKCS7Padding($source)
    {
        $source = trim($source);
        $block = mcrypt_get_block_size('rijndael-128', 'cbc');
        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block)
        {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }

    public function encrypt($orig_data)
    {
        $encrypter = mcrypt_module_open($this->algorithm, '',
            $this->mode, '');
        $orig_data = $this->pkcs7padding($orig_data, mcrypt_enc_get_block_size($encrypter));
        mcrypt_generic_init($encrypter, $this->key, $this->iv);
        $ciphertext = mcrypt_generic($encrypter, $orig_data);
        mcrypt_generic_deinit($encrypter);
        mcrypt_module_close($encrypter);
        return base64_encode($ciphertext);
    }

    public static function str2bin($hexdata)
    {
        $bindata="";
        for ($i=0;$i < strlen($hexdata);$i+=2) {
            $bindata.=chr(hexdec(substr($hexdata,$i,2)));
        }
        return $bindata;
    }

    public function decrypt($ciphertext)
    {
        $encrypter = mcrypt_module_open($this->algorithm, '', $this->mode, '');
        $ciphertext = base64_decode($ciphertext);
        mcrypt_generic_init($encrypter, $this->key, $this->iv);
        $orig_data = mdecrypt_generic($encrypter, $ciphertext);
        mcrypt_generic_deinit($encrypter);
        mcrypt_module_close($encrypter);
        return $this->pkcs7unPadding($orig_data);
    }

    public function pkcs7padding($data, $blocksize)
    {
        $padding = $blocksize - strlen($data) % $blocksize;
        $padding_text = str_repeat(chr($padding), $padding);
        return $data . $padding_text;
    }

    public function pkcs7unPadding($data)
    {
        $length = strlen($data);
        $unpadding = ord($data[$length - 1]);
        return substr($data, 0, $length - $unpadding);
    }


    private static function pkcs7_pad($text)
    {
        $block_size = self::$block_size;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = $block_size - ($text_length % $block_size);
        if ($amount_to_pad == 0)
            $amount_to_pad = $block_size;
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++)
            $tmp .= $pad_chr;
        return $text . $tmp;
    }

    /**
     * 加密
     * @param $input
     * @param $key
     * @return string
     */
    public static function encrypt1($message, $key)
    {
        $input = self::pkcs7_pad($message);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = '0000000000000000';
//        $iv = self::toStr([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
//        $blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//        $len = strlen($message); //取得字符串长度
//        $pad = $blocksize - ($len % $blocksize); //取得补码的长度
//        $message .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
//        $xcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $message, MCRYPT_MODE_CBC, $iv);
//        $xcrypt = base64_encode($xcrypt);
//        return $xcrypt;
    }

    public static function getBytes($string)
    {
        $bytes = array();
        for ($i = 0; $i < strlen($string); $i++) {
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    public static function toStr($bytes)
    {
        $str = '';
        foreach ($bytes as $ch) {
            $str .= chr($ch);
        }
        return $str;
    }

    /**
     * 解密
     * @param $sStr
     * @param $sKey
     * @return bool|string
     */
    public static function decrypt1($sStr, $sKey)
    {
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, base64_decode($sStr), MCRYPT_MODE_CBC);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
}