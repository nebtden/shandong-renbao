<?php
namespace common\models;
use Yii;
use yii\behaviors\TimestampBehavior;

class News_category extends Base_model
{
	public static  function tableName()
	{
		return '{{%news_category}}';
	}

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }
	//读取可用类别
	public function readCates($token){

		return $this->getData('*','all',"`status` = 1 and `token`='".$token."'");
	}
	
	public static function getCategroys($token){
		$model = new self();
		$cates = $model ->readCates($token);
		$cateArr = array();
		foreach ($cates as $v){
			$cateArr[$v['id']] = $v['name'];
		}
		return $cateArr;
	}
    public function readScates($pid='0',$token=''){
        $where='and pid ='.$pid  .' and token="'.$token.'"';
        return $this->getData('*','all',"`status` = 1 $where");
    }

}
