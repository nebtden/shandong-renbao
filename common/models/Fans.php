<?php
namespace common\models;
use Yii;

class Fans extends  Base_model   {

	static $statusArr=array(
		'SUCCESS' => 1, //关注
		'CANNEL'  => 2, //取消关注
		'FAIL'    => 0  //未关注
	);
	
	public static function tableName()
	{
		return '{{%fans}}';
	}
	
	public static function checkFans($token,$openid,$time=0){
		($token&&$openid) || exit(1);
		$model = new self();
		$key=md5($token.$openid);
		$fans = Yii::$app->cache->get($key);
		if(!$fans){
			$fans = $model->getData('*','one',"`token`='".$token."' and `openid`='".$openid."'");
			if($time > 0){
				Yii::$app->cache->set($key, $fans, $time);
			}
		}
		return $fans;
	}
	
	public static function checkcard($token,$card){
		($token&&$card) || exit(1);
		$model = new self();
		$fans = $model->getData('`id`,openid,nickname','one','`pid`="'.$card.'" and `token`="'.$token.'"');
		return $fans;
	}
	
	public static function addFans($fans=array(),$upFields=array()){
		$model = new self();
		return $model->addData($fans,$upFields);
	}
	
	public static function updateFans($fans=array(),$where){
		$model = new self();
		return $model->upData($fans,$where);
	}

    public  function getnameByopenid($openids)
    {

        $arr=array();
        if($openids){
            $openidsArr = explode(',', $openids);
            $openidsArr = array_flip(array_flip($openidsArr));
            $openids = implode(',', $openidsArr);
            $data=$this->getData('nickname,openid','all','token="'.Yii::$app->session['token'].'" and openid in('.$openids.')');
            foreach($data as $v)
            {
                $arr[$v['openid']]=$v['nickname'];
            }
        }
        return $arr;
    }

    //
    /**
     * 根据openid读取realname
     */
    public static function getrealname($openid){
        $model = new self();
        $where='token="'.Yii::$app->session['token'].'" and openid="'.$openid.'"';
        $realname=$model->getData('realname','one',$where);
        return $realname['realname'];
    }


    public  function getnkname($cards)
    {
        $arr=array();
        if($cards)
        {
            $data=$this->getData('nickname,card','all','token="'.Yii::$app->session['token'].'" and card in('.$cards.')');
            foreach($data as $v)
            {
                $arr[$v['card']]=$v['nickname'];
            }
        }

        return $arr;
    }
}