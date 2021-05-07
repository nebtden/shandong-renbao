<?php

namespace common\components;


class RSA
{

    /**
     * 公钥加密
     * @param string $data 要加密的数据
     * @param string $key 公钥
     * @return bool|string
     */
    public static function encryptByPublicKey($data, $key)
    {
        $key = self::get_public_key($key);
        if (!$key) return false;
        $crypto = '';
        $originalData = str_split($data,117);
        foreach ($originalData as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $key);
            $crypto .= $encryptData;
        }
        return base64_encode($crypto);
    }

    /**
     * 私钥加密
     * @param string $data 要加密的数据
     * @param string $key 私钥
     * @param int $padding 填充方式
     * @param int $blocksize 块大小
     * @return bool|string
     */
    public static function encryptByPrivateKey($data, $key, $padding = OPENSSL_NO_PADDING, $blocksize = 128)
    {
        $key = self::get_private_key($key);
        if (!$key) return false;
        $data = base64_encode($data);
        $data = self::checkPadding($data, $padding, $blocksize);
        $flag = openssl_private_encrypt($data, $crypted, $key, $padding);
        if (!$flag) return false;
        return base64_encode($crypted);
    }

    public static function decryptByPrivateKey($data, $key)
    {
        $key = self::get_private_key($key);
        if (!$key) return false;
        $data = base64_decode($data);
        $maxlength = 128;
        $crt = '';
        while($data){
            $input = substr($data,0,$maxlength);
            $data = substr($data,$maxlength);
            $ok = openssl_private_decrypt($input,$crypted,$key);

            if($ok){
                $crt .= $crypted;
            }else{
                //return false;
                var_dump(openssl_error_string());
            }
        }
        return base64_decode($crt);
    }

    /**
     * 加密时要检查填充方式，如果是OPENSSL_NO_PADDING，则要进行手动填充
     * @param string $data 要加密的数据
     * @param int $padding 填充方式
     * @param int $blocksize 块的大小
     * @return string
     */
    public static function checkPadding($data, $padding, $blocksize)
    {
        switch ($padding) {
            case OPENSSL_PKCS1_PADDING:
                break;
            case OPENSSL_NO_PADDING:
                $pad = $blocksize - (strlen($data) % $blocksize);
                $data .= str_repeat("\0", $pad);
                break;
        }
        return $data;
    }

    /**
     * 获得公钥
     * @param string $content 公钥字符串
     * @return bool|resource
     */
    protected static function get_public_key($content)
    {
        if ($content === false) return false;
        $rsa_pubkey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($content, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        $key = openssl_get_publickey($rsa_pubkey);
        return $key;
    }

    /**
     * 获得私钥
     * @param string $content 私钥字符串
     * @return bool|resource
     */
    protected static function get_private_key($content)
    {
        if ($content === false) return false;
        $rsa_prikey = "-----BEGIN PRIVATE KEY-----\n" .
            wordwrap($content, 64, "\n", true) .
            "\n-----END PRIVATE KEY-----";
        $key = openssl_get_privatekey($rsa_prikey);
        return $key;
    }
}