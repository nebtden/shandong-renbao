<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/17
 * Time: 14:35
 */
namespace common\components;

class AESUtil
{
    private function _pkcs5Pad($text, $blockSize)
    {
        $pad = $blockSize - (strlen($text) % $blockSize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function _pkcs5Unpad($text)
    {
        $end = substr($text, -1);
        $last = ord($end);
        $len = strlen($text) - $last;

        if(substr($text, $len) == str_repeat($end, $last))
        {
            return substr($text, 0, $len);
        }
        return false;
    }

    //加密
    public function encrypt($encrypt, $key)
    {

        $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $paddedData = $this->_pkcs5Pad($encrypt, $blockSize);
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $key2 = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key2, $paddedData, MCRYPT_MODE_ECB, $iv);
        return base64_encode($encrypted);
    }


    //解密
    public function decrypt($decrypt, $key)
    {
        $decoded = base64_decode($decrypt);
        $blockSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($blockSize, MCRYPT_RAND);
        $key2 = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key2, $decoded, MCRYPT_MODE_ECB, $iv);
        return $this->_pkcs5Unpad($decrypted);
    }

    /**
     * 参数排序
     * @param $data 数组 $signkey 密匙
     * @return $str 字符串
     */
    public function sign($data,$signkey)
    {
        $str = '';
        $count = count($data);
        $i = 0;
        foreach ($data as $k => $v) {
            if($i == $count-1){
                $str .= $k . $v . $signkey;
            }else{
                if(is_array($v)){
                    foreach ($v as $key => $val){
                        $str .= $key . $val ;
                    }
                }else{
                    $str .= $k . $v ;
                }
            }
            $i++;
        }
        return $str;
    }
    /**
     * 参数排序
     * @param $str 字符串
     * @return $str 加密后字符串
     */
    public function md5str($str)
    {
        $data = unpack('C*',$str);//获取字符串的UTF-8字节数组
        sort($data);//数组排序
        $str = implode('',$data);
        $sign = strtoupper(md5($str));
        return $sign;
    }

    /**
     * 参数排序
     * @param
     * @return 数字 时间戳毫秒数
     */

    public  function getMillisecond()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = (int)substr($msectime, 0, 13);
    }

}