<?php
namespace frontend\util;

use Yii;
use common\components\W;

class Msg {
	
	private static $code_msg=array(
    	'1001'=>'没有权限',
    	'2001'=>'参数不正确',
    	'3001'=>'文件上传失败',
        '4001'=>'请求的数据不存在',
        '5001'=>'写入失败',
        '6001'=>'更新失败',
		'8001'=>'操作成功'
    );

    public static function error($code,$style = 'json'){
        $data=array();
        $data['status'] = array();
        $data['status']['code'] = 1;
        if(in_array(self::$code_msg[$code],self::$code_msg)){
            $data['status']['msg'] = self::$code_msg[$code];
        }
        else{
            $data['status']['msg'] = 'NULL';
        }
        
        if($style=='json'){
        	$res = json_encode($data);
        }elseif($style=='xml'){
        	$res = W::obj2xml($data);
        }
        echo $res;
        exit;
    }
    
    public static function success($return,$style = 'json'){
    	$data=array();
    	$data['status'] = array();
    	$data['status']['code'] = 0;
    	$data['status']['msg'] = self::$code_msg['8001'];
    	if(is_array($return))$data['status'] = array_merge($data['status'],$return);
    	if($style=='json'){
    		$res = json_encode($data);
    	}elseif($style=='xml'){
    		$res = W::obj2xml($data);
    	}
    	echo $res;
    	exit;
    }
}
