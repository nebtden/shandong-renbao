<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/15 0015
 * Time: 9:37
 */
namespace backend\controllers;

use backend\util\BController;
use Yii;
use common\models\Fans;
use common\models\Fan_cashApply;
use common\models\Fans_cashType;
use common\models\Paylog;
use common\components\W;
use common\components\CExcel;
use common\models\AlxgFans;
use common\models\FansAccount;
use common\models\CarUserCarno;
use common\models\Car_type;

class MemberController extends  BController {
    public function actionDownload(){
        $member = new Fans();
        $where = "token='" . Yii::$app->session ['token'] . "'";
        $url='member/importdata';
        $str="";
        if(! empty ($_REQUEST['keywords'])){
            $where .= " and  (locate('" . $_REQUEST['keywords'] . "',card)>0 or locate('" . $_REQUEST['keywords'] . "',realname)>0 or locate('" . $_REQUEST['keywords'] . "',telphone) >0 or locate('" . $_REQUEST['keywords'] . "',nickname) )";
            $str.='&keywords='.$_REQUEST['keywords'];
        }
        if($_REQUEST['status']>=0){
            $where.=' and status='.$_REQUEST['status'];
            $str.='&status='.$_REQUEST['status'];
        }
        if(!empty($_REQUEST['card'])){
            $where.=' and pid ="' . $_REQUEST ['card']. '" ';
            $str.='&card='.$_REQUEST['card'];
        }
        $cot = $member->getData ( 'count(*) cot', 'one', $where );
        if($cot['cot']>0){
            $res='';
            $s=ceil($cot['cot']/5000);
            for($i=0;$i<$s;$i++) {
                $x=$i*5000;
                $impurl='http://'.$_SERVER['HTTP_HOST'].Yii::$app->params['baseUrl'].$url.'.html'.'?'.'p='.$x.$str;
                $res.='<a href="'.$impurl.'" >下载分数据-'.($i+1).'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
                if($i>0 && ($i+1)%3==0)$res.='</br>';
            }
            echo $res;exit;
        }
        else{
            echo '没有相匹配的用户信息提供下载';exit;
        }
    }

    public function actionList()
    {
        $member = new Fans();
        if(Yii::$app->request->isAjax){
            $request = Yii::$app->request;
            $where=" id<>0 ";
            $pageSize = intval ( $_GET ['limit'] ) ? intval ( $_GET ['limit'] ) : 10;
            $offset = intval ( $_GET ['pageNumber'] )  > 1? intval ( $_GET ['pageNumber'] ) - 1 : 0;
            $order = "id desc";
            $limit = $offset * $pageSize.','. $pageSize;
            $search = array();
            $_REQUEST['status'] = !isset($_REQUEST['status']) ? -1 : $_REQUEST['status'];
            if (!empty ($_REQUEST['keywords'])) {
                $where .= " and  (locate('" . $_REQUEST['keywords'] . "',card)>0 or locate('" . $_REQUEST['keywords'] . "',realname)>0 or locate('" . $_REQUEST['keywords'] . "',telphone) >0 or locate('" . $_REQUEST['keywords'] . "',nickname) )";
                $search['keywords'] = $_REQUEST['keywords'];
            }
            if ($_REQUEST['status'] != '') {
                if ($_REQUEST['status'] >= 0) {
                    $where .= ' and status=' . $_REQUEST['status'];
                }
                $search['status'] = $_REQUEST['status'];
            }
            if ($_REQUEST['card']) {
                $where .= ' and pid="' . $_REQUEST['card'] . '"';
            }
            //添加时间搜索许雄泽20101030修改
            $s_time = $request->get('s_time',null);
            $e_time = $request->get('e_time',null);
            $where=$this->getTimeWhere($where,'subscribe_time',$s_time,$e_time);


            $row = $member->getData('*', 'all', $where, $order, $limit);
            $a = $member->find()->createCommand()->getRawSql();
            $pids = W::createKeyStr($row, 'pid');
            $nickname = $member->getnkname($pids);
            foreach ($row as $k => $v) {
                $row [$k] ['nick_name'] = $nickname [$v ['pid']];
                if($v['status']==1)   $row [$k]['status']=  '已关注';
                elseif($v['status']==0)  $row [$k]['status']='未关注';
                else   $row [$k]['status']= '取消关注';
                $row [$k] ['subscribe_time'] = !empty($v['subscribe_time'])?date('Y-m-d H:i:s',$v['subscribe_time']):'--';
            }

            $cot = $member->getData('count(*) cot', 'one', $where);
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode( $row) . ' }';
            exit ();

        }
        return   $this->render ( 'list');

    }
    public  function actionEdit(){
        $member = new Fans ();
        $where = 'id=' . $_REQUEST ['id'];
        $data = $member->getData ( '*', 'one', $where );
        if (Yii::$app->request->post()) {
            $arr = array (
                'realname' => $_POST ['realname'],
                'sex' => $_POST ['sex'],
                'telphone' => $_POST ['telphone'],
                'address' => $_POST ['address'],
                'email' => $_POST ['email']
            );
            $member->upData ( $arr, $where );
            $this->redirect ( 'list.html' );
        }
       return $this->render ( 'edit', array ('row' => $data,'act' => 'edit') );

    }

    public function  actionImportdata()
    {

        $where="token='" . Yii::$app->session ['token'] . "'";

        if (! empty ( $_REQUEST['keywords'] ) ) {
            $where .= " and  (locate('" . $_REQUEST['keywords'] . "',card)>0 or locate('" . $_REQUEST['keywords'] . "',realname)>0 or locate('" . $_REQUEST['keywords'] . "',telphone) >0 or locate('" . $_REQUEST['keywords'] . "',nickname) )";

        }
        if ($_REQUEST['status']!='') {
            if($_REQUEST['status']>=0) {
                $where.=' and status='.$_REQUEST['status'];
            }
        }
        if(!empty($_REQUEST['card'])){
            $where.=' and pid ="' . $_REQUEST ['card']. '" ';
        }
        // 浏览器下载excel
        $fileName = "用户分数据-";
        $member=new Fans();
        $cur=$_REQUEST['p'];
        $row = $member->getData ( '*', 'all', $where,'id desc',$cur.',5000');

        $pids = W::createKeyStr ( $row, 'pid' );
        $nickname = $member->getnkname ( $pids );
        $headArr = array (
            "会员名称",
            "推荐人姓名",
            "会员卡号",
            "手机",
            "省份",
            "城市",
            "会员关注状态",
            "会员关注来源",
            "关注时间",
            "余额",
            "总收益",
            "未确认金额￥",
            "是否标准客户"
        ); // 标题 第一行内容
        $data = array(); // 文档内容
        foreach ( $row as $k => $v ) {
            if($v['status']==1)
                $status= '已关注';
            elseif($v['status']==0)
                $status='未关注';
            else
                $status= '取消关注' ;

            $isstandard=$v['isstandard']==1?'标准':'非标准';
            $data[]=array($v['nickname'],$nickname[$v ['pid']],$v['card'],$v['telphone'],$v['province'],$v['city'],
                $status,'个人二维码扫描',date('Y-m-d H:i:s',$v['subscribe_time']),round($v['yue'],2),
                round($v['total'],2),round($v['lock'],2),$isstandard);
        }
        CExcel::getExcel()->getExcel ( $fileName, $headArr, $data );
        exit ();
    }

   public function actionCash_apply() {
        if(Yii::$app->request->isAjax){
            $fan = new Fans();
            $fancashType = new Fans_cashType();
            $cashinfo = $fancashType->Cashinfo ();
            $cassapply = new Fan_cashApply ();
            $fanpaylog = new Paylog();
            $pageSize = intval ( $_GET ['limit'] ) ? intval ( $_GET ['limit'] ) : 10;
            $offset = intval ( $_GET ['pageNumber'] )  > 1? intval ( $_GET ['pageNumber'] ) - 1 : 0;
            $order = "id desc";
            $limit = $offset * $pageSize.','. $pageSize;
            $where = 'token="' . Yii::$app->session ['token'] . '"';
            $search=array();
            $_REQUEST['status']=!isset($_REQUEST['status'])?-1:$_REQUEST['status'];
            if (! empty ( $_REQUEST['keywords'] )) {
                $infoIf = $fan->getData ( 'count(*) cot,openid', 'one', 'locate("' . $_REQUEST['keywords'] . '",realname)>0' );
                $infoIct = $fancashType->getData ( 'count(*) cot,openid', 'one', 'locate("' .  $_REQUEST['keywords'] . '",account)>0' );
                if ($infoIct ['cot'] > 0) {
                    $where .= " and openid='" . $infoIct ['openid'] . "'";
                } elseif ($infoIf ['cot'] > 0) {
                    $where .= " and openid='" . $infoIf ['openid'] . "'";
                }

                $search['keyword'] = $_REQUEST['keywords'];
            }


            if ( $_REQUEST['status'] =='') {
                if($_REQUEST['status']>=0) {
                    $where.=' and status='.$_REQUEST['status'];
                }
                $search['status'] =$_REQUEST['status'];
            }

            $arr = $cassapply->getData ( '*', 'all', $where, 'applytime desc', $limit );
            $openids=W::createKeyStr($arr,'openid');
            $cids=W::createKeyStr($arr,'id');
            $yues=$fan->getYue($openids);
            foreach ( $arr as $k => $v ) {
                $arr [$k] ['nickname'] = $cashinfo[$v ['openid']][3];
                $arr [$k] ['yue'] = $yues [$v ['openid']] ;
                $arr [$k] ['account'] = $cashinfo [$v ['openid']] [2];
                $arr [$k] ['cash_type'] = $cashinfo [$v ['openid']] [0];
                $arr [$k] ['idCard'] = $cashinfo [$v ['openid']] [1];
                $arr [$k] ['cash_name'] = $cashinfo [$v ['openid']] [4];
            }

            $cot = $cassapply->getData ( 'count(*) cot', 'one', $where, 'applytime desc' );
            $paylogs=$fanpaylog::paylogs($cids);
            echo '{"IsOk":1,"total": ' . $cot['cot'] . ',"rows":' . json_encode( $arr) . ' }';
            exit ();
        }


        return  $this->render ( 'cashapply');
    }
    /**
     * vip列表
     * @return string
     */
    public function actionVip(){
        $request = Yii::$app->request;
        $status = FansAccount::$userstatus;
        $useris_web = FansAccount::$useris_web;
        if($request->isAjax){
            $model = new FansAccount();
            $uname = $request->get('uname',null);
            $status = $request->get('status');
            $where = '`id` <> 0';
            if($uname !== null){
                $uname = addslashes(trim($uname));
                $where .= " AND (realname LIKE '%".$uname."%' OR mobile LIKE '%".$uname."%')";
            }
            if($status || $status == '0'){
                $where .= " AND status = '".$status."'" ;
            }
            //添加时间搜索许雄泽20101030修改
            $s_time = $request->get('s_time',null);
            $e_time = $request->get('e_time',null);
            $where=$this->getTimeWhere($where,'c_time',$s_time,$e_time);
            $res = $model->page_list("*",$where);
            $list = $res['rows'];
            if($list){
                foreach ($list as $key=>$val){
                    $val['is_vip'] = FansAccount::$level[$val['is_vip']];
                    $val['status'] = FansAccount::$userstatus[$val['status']];
                    $val['is_web'] = $useris_web[$val['is_web']];
                    $val['vip_time'] = $val['vip_time'] ? date("Y-m-d H:i:s",$val['vip_time']):'--';
                    $val['c_time'] = date("Y-m-d H:i:s",$val['c_time']);
                    $val['u_time'] = date("Y-m-d H:i:s",$val['u_time']);
                    $list[$key] = $val;
                }
            }
            $res['rows'] = $list;
            return json_encode($res);
        }
            return $this->render('vip',['status'=>$status]);

    }

    public function actionEditvip(){
        set_time_limit(0);
        $request = Yii::$app->request;
        if($request->isPost){
            $db = Yii::$app->db;
            $data = $request->post();
            $uid = $data['uid'];
            $id = null;
            $accountinfo = (new FansAccount())->select("id,pid,uid",['id' => $uid])->one();
            if(isset($data['id'])){
                $id = $data['id'];
                unset($data['id']);
            }else{
                if($accountinfo) return '此用户已添加会员，不可重复添加';
            }
            $data['u_time'] = time();
            if(!$id) $data['c_time'] = $data['u_time'];
            if($data['is_vip']) $data['vip_time'] = $data['u_time'];
            //获得用户本身
            $fans = (new AlxgFans())->select("id,pid,tparent,salesman",['id' => $uid])->one();
            $is_vip_2 = ($uid == $fans['tparent']) ? 1:0;//是否是超级会员
            $is_vip_3 = ($uid == $fans['salesman']) ? 1:0;//是否是业务员
            if($data['is_vip'] == 2){
                //超级会员与业务员不能直接互转
                if($is_vip_3) return '用户已经是业务员，不能再转为超级会员';
                //超级会员之上不能再有超级会员
                if($fans['tparent'] && !$is_vip_2) return '一条关系链中仅允许有且仅有一个超级会员';
                //超级会员之下不能有超级会员与业务员
                $msg = 'ok';
                $uids = $this->getRelation($uid,5,$msg);
                if($uids === false) return '用户下线会员中'.$msg;
                $uids[] = $uid;
                $uids = array_unique($uids);
                $trans = $db->beginTransaction();
                try{
                    //更新或新增FansAccount
                    if($id){
                        (new FansAccount())->myUpdate($data,['id' => $id]);
                    }else{
                        (new FansAccount())->myInsert($data);
                    }
                    //更新关系
                    (new AlxgFans())->myUpdate(['tparent' => $uid],['id' => $uids]);
                    $trans->commit();
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $e->getMessage();
                }
            }elseif($data['is_vip'] == 3){
                //业务员与超级会员之间不能直接互转
                if($is_vip_2) return '用户已经是超级会员，不能再转为业务员';
                //业务员上不能有业务员和超级会员
                if($fans['tparent']) return '用户上级已存在超级会员';
                if($fans['salesman'] && !$is_vip_3) return '用户上级已存在业务员';
                //业务员下不能有业务员
                $msg = 'ok';
                $uids = $this->getRelation($uid,3,$msg);
                if($uids === false) '用户下线会员中'.$msg;
                $uids[] = $uid;
                $uids = array_unique($uids);
                $trans = $db->beginTransaction();
                try{
                    //更新或新增FansAccount
                    if($id){
                        (new FansAccount())->myUpdate($data,['id' => $id]);
                    }else{
                        (new FansAccount())->myInsert($data);
                    }
                    //更新关系
                    (new AlxgFans())->myUpdate(['salesman' => $uid],['id' => $uids]);
                    $trans->commit();
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $e->getMessage();
                }
            }else{
                //回退关系
                $trans = $db->beginTransaction();
                try{
                    //更新或新增FansAccount
                    if($id){
                        (new FansAccount())->myUpdate($data,['id' => $id]);
                    }else{
                        (new FansAccount())->myInsert($data);
                    }
                    //回退关系
                    (new AlxgFans())->myUpdate(['tparent'=>0],['tparent'=>$uid]);
                    (new AlxgFans())->myUpdate(['salesman'=>0],['salesman'=>$uid]);
                    $trans->commit();
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $e->getMessage();
                }
            }
            return $this->redirect(['member/vip']);
        }
        $id = $request->get("id",null);
        $data = null;
        if($id && is_numeric($id) && $id > 0){
            $data = (new FansAccount())->select("*",['id' => $id])->one();
        }
        $level = FansAccount::$level;
        $acstatus = FansAccount::$userstatus;
        return $this->render('editvip',['data' => $data,'level' => $level,'status'=>$acstatus]);
    }
    /**
     * 绑定车牌列表查询条件
     * @return string
     */

    protected function setBindingWhere(){
        $request = Yii::$app->request;
        $card_province = trim($request->get('card_province',null));
        $card_char = $request->get('card_char',null);
        $card_brand = $request->get('card_brand',null);
        $user_id = $request->get('user_id',0);
        $where=' id<>0 ';
        if(!empty($card_province)){
            $where.=' AND  card_province ="'.$card_province.'"';
        }

        if(!empty($card_char)){
            $where.=' AND  card_char ="'.$card_char.'"';
        }

        if(!empty($card_brand)){
            $where.=' AND  card_brand ="'.$card_brand.'"';
        }

        if(!empty($user_id)){
            $where.=' AND  uid ="'.$user_id.'"';
        }
        return $where;

    }
    /**
     * 绑定车牌列表
     * @return string
     */

    public function actionBindinglist(){
        if (Yii::$app->request->isAjax) {
            $model=new CarUserCarno();
            $where=$this->setBindingWhere();
            $list=$model->page_list('*',$where, 'id DESC');
            $listrows=$list['rows'];
            $cartype=CarUserCarno::$car_type;
            $status=['-1'=>'管理员删除','0'=>'用户已删除','1'=>'正常'];
            foreach ($listrows as $key=>$val){
                $listrows[$key]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
                $listrows[$key]['u_time'] = !empty($val['u_time'])?date("Y-m-d H:i:s",$val['u_time']):'--';
                $listrows[$key]['card_type'] = $cartype[$val['card_type']];
                $listrows[$key]['status'] =$status[$val['status']];
            }
            $list['rows']=$listrows;
            return json_encode($list);

        }
        $car_brand=(new Car_type())->select('id,name',[])->cache(true,3600)->all();
        $car_province=CarUserCarno::$car_province;
        $car_zimu=CarUserCarno::$car_zimu;
        $car_type=CarUserCarno::$car_type;
        return      $this->render ('bindinglist',['car_brand'=>$car_brand,'cartype'=>$car_type,'province'=>$car_province,'zimu'=>$car_zimu]);
    }


    public function actionBindingedit() {

        $id = $_REQUEST['id'] ? $_REQUEST['id'] : "";
        $Model = new CarUserCarno();
        $cates = $Model->table()->select('*')->where(['id'=>intval($id)])->one();
        if (Yii::$app->request->post()) {
            if(empty($_POST['uid']) || empty($_POST['card_type']) || empty($_POST['card_province']) ||
                empty($_POST['card_char']) || empty($_POST['card_no'])|| empty($_POST['card_brand'])
            ) exit('数据有误');
            if($_POST['status']=='0')exit('你是管理员，不能选此项');
            $arr = array(
                'uid' => $_POST['uid'],
                'card_type' => $_POST['card_type'],
                'card_province' => $_POST['card_province'],
                'card_char' => $_POST['card_char'],
                'card_no' => $_POST['card_no'],
                'card_brand' => $_POST['card_brand'],
                'status' => $_POST['status']
            );

            if(! empty($id)) $arr['id'] = $id;
            $Model->update_card($arr);
            $this->redirect('bindinglist.html');
        }
        $car_brand=(new Car_type())->select('id,name',[])->cache(true,3600)->all();
        $car_province=CarUserCarno::$car_province;
        $car_zimu=CarUserCarno::$car_zimu;
        $car_type=CarUserCarno::$car_type;
        return      $this->render ('bindingedit',['data'=>$cates,'car_brand'=>$car_brand,'cartype'=>$car_type,'province'=>$car_province,'zimu'=>$car_zimu]);
    }
}