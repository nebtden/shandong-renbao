<?php
namespace backend\controllers;
use backend\util\BController;
use Yii;
use common\models\Reply;
use common\models\Keyword;
use common\components\W;
use common\models\Wxuser;
use common\models\Fans;
use yii\helpers\ArrayHelper;

class AutoreplyController  extends  BController{
    /* 文字回复 */
  public  function actionSimplereply() {
        $reply = new Reply ();
        if (Yii::$app->request->post()) {
            if (empty ( $_POST ['details'] ) || empty ( $_POST ['keywords'] )) {
                $this->redirect ('simplereply.html');
            }
            $reply_arr = array (
                'details' => trim ( $_POST ['details'] ),
                'keywords' => trim ( $_POST ['keywords'] ),
                'type' => 1,
                'token' => Yii::$app->session ['token']
            );
            $id = $reply->addData ( $reply_arr );
            $karr = array (
                'm_id' => $id,
                'module' => 'reply',
                'keyword' => trim ( $_POST ['keywords'] )
            );
            Keyword::dealKeyword ( $karr );
            $this->redirect ('simplelist.html');
        }
        $data = array ();
        if (! empty ( $_GET ['id'] )) {
            $where = 'id=' . $_GET ['id'];
            $data = $reply->getData ( '*', 'one', $where );
        return    $this->render ( 'simplereply', array (
                'data' => $data,
                'act' => 'edit'
            ) );
        } else {
         return    $this->render ( 'simplereply' );
        }
    }

    /* 文字回复列表 */
  public  function actionSimplelist() {
       $reply = new Reply ();
       if(Yii::$app->request->isAjax){
           $pageSize = intval ( $_GET ['limit'] ) ? intval ( $_GET ['limit'] ) : 5;
           $offset = intval ( $_GET ['pageNumber'] )  > 1? intval ( $_GET ['pageNumber'] ) - 1 : 0;
           $where = ' type=1 and status=1  and token="' . Yii::$app->session['token'] . '"';
           if ($_GET['keyword'])
               $where .= " and  locate('".$_GET['keyword']."',keywords) >0";
           $order = "id desc";
           $limit = $offset * $pageSize.','. $pageSize;
           $data = $reply->getData ( '*', 'all', $where, $order, $limit );
           $cot = $reply->getData ( 'count(*) cot', 'one', $where );
           echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode ( $data ) . ' }';
           exit ();
       }
       return $this->render('simplelist');
    }
    public  function actionSimpleupt() {
        $reply = new Reply ();
        $where = 'id=' . $_POST ['id'];
        $arr = array (
            'keywords' => $_POST ['keywords'],
            'details' => $_POST ['details']
        );
        $reply->upData ( $arr, $where );
        $this->redirect ( 'simplelist.html' );
    }
    //
  public  function actionSimpledelete() {
        $reply = new Reply ();
        $id = implode ( ',', Yii::$app->request->get ( 'params' ) );
        $where = "id in ($id)";
        $arr = array (
            'status' => '2'
        );
        $reply->upData ( $arr, $where );
        if (isset ( $_GET ['type'] ) && $_GET ['type'] == 2) {
            $this->redirect ( 'mixlist.html' );
        }
    }

  public  function actionMixlist() {
        $reply = new Reply ();
        if(Yii::$app->request->isAjax){
            $order = "id desc";
            $where = 'type=2 and pid=0 and status=1 and token="' . Yii::$app->session ['token'] . '"';
            $pageSize = intval ( $_GET ['limit'] ) ? intval ( $_GET ['limit'] ) : 5;
            $offset = intval ( $_GET ['pageNumber'] )  > 1? intval ( $_GET ['pageNumber'] ) - 1 : 0;
            $limit = $offset * $pageSize.','. $pageSize;
            if ($_GET['keyword'])
                $where .= " and  locate('".$_GET['keyword']."',keywords) >0";
            $data = $reply->getData ( '*', 'all', $where, $order, $limit );
            foreach($data as $k=> $v){
                $data[$k]['imageurl']="<img  src='".$v['imageurl']."' width='50px' height='50px' />";
            }
            $cot = $reply->getData ( 'count(*) cot', 'one', $where );
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode ( $data ) . ' }';
            exit;
        }
        return  $this->render ( 'mixlist' );
    }
    //
  public  function actionMixedit() {
        $reply = new Reply ();
        $id = 0;
        $rows = $reply->getData ( "count(*) as s0", "one", " id=" . $_GET ['id'] . " or pid=" . $_GET ['id'] . ' and token="' . Yii::$app->session ['token'] . '"' );
        if (Yii::$app->request->post()) {
            $prows = count ( $_POST ['id'] );
            foreach ( $_POST ['title'] as $k => $v ) {
                $details = isset ( $_POST ['details'] ) ? $_POST ['details'] : '';
                $arr = array (
                    'title' => $_POST ['title'] [$k],
                    'details' => $details,
                    'imageurl' => ! empty ( $_POST ['hadimg' . $k] ) ? W::dealPic ( $_POST ['hadimg' . $k] ) : '',
                    'order' => $_POST ['order'] [$k],
                    'type' => 2,
                    'keywords' => $_POST ['keyword'],
                    'pid' => $_POST ['pid'] [$k],
                    'token' => Yii::$app->session ['token'],
                    'url' => $_POST ['url'] [$k]
                );

                if ($prows > $rows ['s0'] && $k >= $rows ['s0']) {
                    $arr ['pid'] = $_POST ['id'] [$k];
                    $reply->addData ( $arr );
                } else {
                    $where = 'id=' . $_POST ['id'] [$k] . ' and token="' . Yii::$app->session ['token'] . '"';
                    $reply->upData ( $arr, $where );
                }
            }
            $this->redirect ( 'mixlist.html' );
        }
        $data = $reply->getData ( "*", "all", " id=" . $_GET ['id'] . " or pid=" . $_GET ['id'] . ' and token="' . Yii::$app->session ['token'] . '"', ' `order` asc' );
        $res ['act'] = 'edit';
        $res ['id'] = $_GET ['id'];
        $res ['rows'] = $data;
       return $this->render ( 'mixreply', $res );
    }

    //
    public  function actionMixreply() {
        // $this->layout = '//layouts/shop_frame';
        $reply = new Reply ();
        if (Yii::$app->request->post()) {

            foreach ( $_POST ['title'] as $k => $v ) {

                $details = isset ( $_POST ['details'] ) ? $_POST ['details'] : '';
                $arr = array (
                    'title' => $_POST ['title'] [$k],
                    'details' => $details,
                    'imageurl' => ! empty ( $_POST ['hadimg' . $k] ) ? W::dealPic ( $_POST ['hadimg' . $k] ) : '',
                    'order' => $_POST ['order'] [$k],
                    'type' => 2,
                    'keywords' => $_POST ['keyword'],
                    'pid' => 0,
                    'token' => Yii::$app->session ['token'],
                    'url' => $_POST ['url'] [$k]
                );
                if ($k > 0) {
                    $arr ['pid'] = \Yii::$app->cache->get ( 'reply_pid' . Yii::$app->session ['token'] );
                }

                $id = $reply->addData ( $arr );
                if ($k == 0) {
                    \Yii::$app->cache->set ( 'reply_pid' . Yii::$app->session ['token'], $id );
                }
                $karr = array (
                    'm_id' => $id,
                    'module' => 'reply',
                    'keyword' => trim ( $_POST ['keyword'] )
                );
                Keyword::dealKeyword ( $karr );
            }
            \Yii::$app->cache->delete ( 'reply_pid' . Yii::$app->session ['token'] );
            $this->redirect ( 'mixlist.html' );
        }

     return    $this->render ( 'mreply' );
    }
    public function actionSendall(){

        header('content-type:text/html;charset=utf8');
        exit('因为每月只有四次群发机会，暂时关闭此功能');
        $reply = new Reply ();
        $wxuser=new Wxuser();
        $data=array();
        $access_token=W::getAccessToken(Yii::$app->session['token']);
        $url='https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token='.$access_token;
        $where = 'type=2 and pid=0 and status=1 and token="' . Yii::$app->session ['token'] .'"';
        $order='id desc';
        if(Yii::$app->request->post())
        {
            $str='{"articles": [';
            $where = 'type=2 and pid='.$_REQUEST['rid'].' and status=1 and token="' . Yii::$app->session ['token'] . '" or id='.$_REQUEST['rid'];
            $data = $reply->getData( '*', 'all', $where ,$order);
            $wxuser=$wxuser->getUsrByToken(Yii::$app->session['token']);
            $mediaids='';
            foreach($data as  $k=> $v)
            {
                $len=count($data);

                $res = W::uploadImg(Yii::$app->session['token'],$v['imageurl'],'image');
                $showpic=($k==0)?1:0;
                $str.=' {
                        "thumb_media_id":"'.$res['media_id'].'",
                        "author":"'.$wxuser['wxname'].'",
			 			"title":"'.$v['title'].'",
						"content_source_url":"'.$v['url'].'",
						"content":"'.$v['details'].'",
						"digest":"'.$v['keywords'].'",
			            "show_cover_pic":"'.$showpic.'"
					 }';

                if($len-1>$k){
                    $str.=',';
                }
            }
            $str.=']}';

            $res = W::http_post($url,$str);
            $res = json_decode($res,true);
            $openidjosnstr='';
            $userarr=(new Fans())->select('openid',['status'=>'1','token'=>Yii::$app->session ['token']])->limitAndoffset('0,2')->all();
            if($userarr){
                $user_arr=ArrayHelper::getColumn($userarr,'openid');
                $openidjosnstr=json_encode($user_arr);
            }

            $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$access_token;
            $fdata='{
				   "touser":'.$openidjosnstr.',
				   "mpnews":{
				      	"media_id":"'.$res['media_id'].'"
				   },
				   "msgtype":"mpnews"
				}';
            $res = W::http_post($url,$fdata);
            echo $res;
            exit;
        }
        $data = $reply->getData( '*', 'all', $where ,$order);
        return   $this->render('sendall',array('data'=>$data));

    }
}