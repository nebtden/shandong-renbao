<?php
/**
*  基本功能(IP获取及处理)
*/
namespace common\components;
use yii\base\Component;

class IPAdr extends Component {
	public static $realip = '';
	public static $unknown = 'unknown';
	
	static function dealAddress(){
		$res = $_SESSION['IPInfo'];
		if(!$res){
			$ip = self::getIp();
			$res = self::getIpLookup($ip);
			if(!$res) {
				$ip = self::getRealIP();
				$res = self::getIpLookup($ip);
			}
			$_SESSION['IPInfo'] = $res;
		}
		return $res;
	}
	
	static function getIp(){
		if (isset($_SERVER)){
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], self::$unknown)){
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				foreach($arr as $ip){
					$ip = trim($ip);
					if ($ip != 'unknown'){
						self::$realip = $ip;
						break;
					}
				}
			}else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], self::$unknown)){
				self::$realip = $_SERVER['HTTP_CLIENT_IP'];
			}else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], self::$unknown)){
				self::$realip = $_SERVER['REMOTE_ADDR'];
			}else{
				self::$realip = self::$unknown;
			}
		}else{
			if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), self::$unknown)){
				self::$realip = getenv("HTTP_X_FORWARDED_FOR");
			}else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), self::$unknown)){
				self::$realip = getenv("HTTP_CLIENT_IP");
			}else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), self::$unknown)){
				self::$realip = getenv("REMOTE_ADDR");
			}else{
				self::$realip = self::$unknown;
			}
		}
		return self::$realip = preg_match("/[\d\.]{7,15}/", self::$realip, $matches) ? $matches[0] : self::$unknown;
	}
	
	static function getIpLookup($ip = ''){
		if(empty($ip)){
			$ip = self::getIp();
		}
		$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
		if(empty($res)){ return false; }
		$jsonMatches = array();
		preg_match('#\{.+?\}#', $res, $jsonMatches);
		if(!isset($jsonMatches[0])){ return false; }
		$json = json_decode($jsonMatches[0], true);
		if(isset($json['ret']) && $json['ret'] == 1){
			$json['ip'] = $ip;
			unset($json['ret']);
		}else{
			return false;
		}
		return $json;
	}
	
	static function getRealIP(){
		$res = @file_get_contents("http://www.ip138.com/ips1388.asp");
		return self::$realip = preg_match("/[\d\.]{7,15}/", $res, $matches) ? $matches[0] : self::$unknown;
	}
}