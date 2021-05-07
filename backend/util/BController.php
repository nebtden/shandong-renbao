<?php

namespace backend\util;

use Yii;
use common\components\BaseController;
use backend\models\Admin;
use common\models\Group;

class BController extends BaseController {

    public $menu = array(
        '自定义菜单' => array(
            'cur' => 'admin',
            'icon' => 'kzt.png',
            'subs' => array('自定义菜单' => array('url' => 'admin/menulist', 'class' => 'glyphicon glyphicon-certificate'))
        ),
        '支付配置' => array(
            'cur' => 'payment',
            'icon' => 'kzt.png',
            'subs' => array('支付配置' => array('url' => 'payment/index', 'class' => 'glyphicon glyphicon-shopping-cart'))
        ),
        '微信自动回复' => array(
            'cur' => 'autoreply',
            'icon' => 'kzt.png',
            'subs' => array(
                '文字回复' => array('url' => 'autoreply/simplelist', 'class' => ''),
                '图文回复' => array('url' => 'autoreply/mixlist', 'class' => ''),
                '批量群发' => array('url' => 'autoreply/sendall', 'class' => '')
            )
        ),
        '广告管理' => array(
            'cur' => 'ad',
            'icon' => 'kzt.png',
            'subs' => array(
                '广告管理' => array('url' => 'ad/location', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),
        '首页管理' => array(
            'cur' => 'carmenu',
            'icon' => 'kzt.png',
            'subs' => array(
                '目录管理' => array('url' => 'carmenu/menulist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '推荐管理' => array('url' => 'carmenu/recommendlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '轮播管理' => array('url' => 'carmenu/bannerlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),
        '新闻资讯'=>array(
            'cur'=>'news',
            'icon'=>'kzt.png',
            'subs'=>array('新闻资讯'=>array('url'=>'carnews/newslist','class'=>'glyphicon glyphicon-shopping-cart'))
        ),
        '商铺管理' => array(
            'cur' => 'car',
            'icon' => 'kzt.png',
            'subs' => array(
                '商户管理' => array('url' => 'car/shop_list', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '兑换卡' => array('url' => 'car/card_list', 'class' => ''),
                '兑换记录' => array('url' => 'car/exchange_log', 'class' => ''),
                '提现申请记录' => array('url' => 'car/withdrawals_log', ''),
                '账号管理' => array('url' => 'car/account_list', ''),
            )
        ),
        '自营门店管理' => array(
            'cur' => 'selfshopcar',
            'icon' => 'kzt.png',
            'subs' => array(
                '自营门店管理' => array('url' => 'selfshopcar/shop_list', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '自营门店提现记录管理' => array('url' => 'selfshopcar/withdrawals_log', ''),
                '账号管理' => array('url' => 'selfshopcar/account_list', ''),
            )
        ),

        '会员管理' => array(
            'cur' => 'member',
            'icon' => 'kzt.png',
            'subs' => array(
                '会员管理' => array('url' => 'member/list', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '会员认证管理' => array('url' => 'member/vip', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '绑定车牌' => array('url' => 'member/bindinglist', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),
        '订单管理' => array(
            'cur' => 'order',
            'icon' => 'kzt.png',
            'subs' => array(
                '代驾订单管理' => array('url' => 'order/daicarorlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '道路救援订单' => array('url' => 'order/rescueorlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '途虎洗车券使用记录' => array('url' => 'order/carwashlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '油券订单管理' => array('url' => 'order/oilorlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '途虎在线洗车订单管理' => array('url' => 'order/washpaylist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '年检订单管理' => array('url' => 'order/asorderlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '盛大洗车订单管理' => array('url' => 'order/dianwashlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '代步车订单管理' => array('url' => 'order/segorderlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '平安洗车订单管理' => array('url' => 'order/pinganorlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '平安洗车网点管理' => array('url' => 'order/wangdianlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '太保推送记录管理' => array('url' => 'order/taibaoorlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),


        '权限管理' => array(
            'cur' => 'authority',
            'icon' => 'kzt.png',
            'subs' => array('权限管理' => array('url' => 'authority/index', 'class' => 'glyphicon glyphicon-shopping-cart'))
        ),
        '优惠券管理' => array(
            'cur' => 'coupon',
            'icon' => 'kzt.png',
            'subs' => array(
                '券包管理' => array('url' => 'coupon/packagelist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '申请管理' => array('url' => 'coupon/apply-list', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '审核管理' => array('url' => 'coupon/examine-list', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '券包生成' => array('url' => 'coupon/packagegenerate', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '优惠券管理' => array('url' => 'coupon/index', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '洗车券管理' => array('url' => 'coupon/carwashlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '优惠券说明模板' => array('url' => 'coupon/templatelist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '套餐管理' => array('url' => 'coupon/meallist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '套餐券管理' => array('url' => 'coupon/couponmeallist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '公司管理' => array('url' => 'coupon/companylist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '地区代码查询' => array('url' => 'coupon/arealist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '免兑换手机管理' => array('url' => 'coupon/mobilelist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '人保兑换码管理' => array('url' => 'coupon/codelist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '券包使用率查询' => array('url' => 'coupon/ratiocheck', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '优惠券使用率查询' => array('url' => 'coupon/ratiocouponcheck', 'class' => 'glyphicon glyphicon-shopping-cart'),
                'ETC设备码管理' => array('url' => 'coupon/etccodelist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '诚泰客户信息管理' => array('url' => 'coupon/ctlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                //'第三方需求管理' => array('url' => 'coupon/demandlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),
        '财务相关' => array(
            'cur' => 'finance',
            'icon' => 'kzt.png',
            'subs' => array(
                //'用户账号' => array('url' => 'finance/index', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '转账记录' => array('url' => 'finance/transferlog', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),
        '客服查询' => array(
            'cur' => 'service',
            'icon' => 'kzt.png',
            'subs' => array(
                '券包兑换码查询' => array('url' => 'service/packagelist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '套餐兑换码查询' => array('url' => 'service/couponmeallist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '优惠券查询' => array('url' => 'service/couponlist', 'class' => 'glyphicon glyphicon-shopping-cart'),
             )
        ),
        '太平洋旅游项目管理' => array(
            'cur' => 'travel',
            'icon' => 'kzt.png',
            'subs' => array(
                '旅游报名管理' => array('url' => 'travel/enroll-list', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '旅游日期管理' => array('url' => 'travel/date-list', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '营销员管理' => array('url' => 'travel/salesman-list', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),

    );

    public $menu_1 = array(
        '观看码管理' => array(
            'cur' => 'calendarad',
            'icon' => 'kzt.png',
            'subs' => array(
                '观看码管理' => array('url' => 'calendarad/codelist', 'class' => 'glyphicon glyphicon-shopping-cart'),
                '视频管理' => array('url' => 'calendarad/videolist', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        ),
        '公司管理' => array(
            'cur' => 'company',
            'icon' => 'kzt.png',
            'subs' => array(
                '公司列表' => array('url' => 'company/companylist', 'class' => 'glyphicon glyphicon-shopping-cart'),
            )
        )
    );
    public function init() {

        parent::init();
    }

    public function beforeAction($action = null){
        header('Content-type: text/html; charset=utf-8');
        parent::beforeAction($action);

        if ($this->id === 'site' && ($this->action->id === 'logout' ||  $this->action->id === 'login' ||  $this->action->id === 'yzcode' ||  $this->action->id === 'error')) {
            $this->layout = false;
        }
        if ($this->id === 'site' && ($this->action->id === 'login' || $this->action->id === 'yzcode')) {
            return true;
        } else {
            if( !Yii::$app->session ['UID'] ){
                $this->redirect(['site/login']);
                return false;
            }elseif(Yii::$app ->session['token']){
                if(Yii::$app->session ['UID'] != 1){
                    $group = (new Admin())->select('group_id',['id'=>Yii::$app->session ['UID']])->one();
                    $group=explode('|', $group['group_id']);
                    $modules = (new Group())->select('modules',['id'=>$group])->all();
                    $menu=[];
                    if(! empty($modules)){
                        $modulesall = array();
                        foreach ($modules as $key=>$val){
                            $modulesall[] = json_decode($val['modules'],true);
                        }
                        $modulesall = array_reduce($modulesall, 'array_merge', array());
                        $data_X=[];
                        $data_y=[];
                        foreach ($modulesall as $key=>$val){
                            if(in_array($val['modules'],$data_X)){
                                $modulesall[$data_y[$val['modules']]['key']]['modules'] = $val['modules'];
                                $modulesall[$data_y[$val['modules']]['key']]['subdirectory'] = array_unique(array_merge($val['subdirectory'],$data_y[$val['modules']]['val']));
                                unset($modulesall[$key]);
                            }else{
                                array_push($data_X,$val['modules']);
                                $data_y[$val['modules']]['key']=$key;
                                $data_y[$val['modules']]['val']=$val['subdirectory'];
                            }
                        }
                        //echo "<pre>";print_r($modulesall);echo "<pre>";die;
                        foreach ($modulesall as $key => $val ){
                            $menu[$val['modules']]=$this->menu[$val['modules']];
                            unset($menu[$val['modules']]['subs']);
                            foreach ($val['subdirectory'] as $value ){
                                $menu[$val['modules']]['subs'][$value]=$this->menu[$val['modules']]['subs'][$value];
                            }
                        }
                    }
                    $this->menu=$menu;
                    if (Yii::$app->session ['UID'] == 16){
                        $this->menu=$this->menu_1;
                    }
                }
            }
            return true;
        }
    }
    //返回json数据
    public function json($status = 1,$msg = '操作成功',$data = [],$url = '',$waiting = 0){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => $status,'msg' => $msg, 'data' => $data, 'url' => $url, 'waiting' => $waiting];

    }

    /**
     * @param null $model       获取列表的模型
     * @param string $field     要获取的字段
     * @param array $where      查询条件
     * @param string $order     排序字段
     * @return array|bool       返回数据列表
     */
    public function getPageData($model = null,$field = '*',$where = [],$order = 'id DESC'){
        if(!$model) return false;
        if(!$field) $field = '*';
        if(!$where) $where = [];
        if(!$order) $order = 'id DESC';

        $request = Yii::$app->request;
        $page = $request->get("pageNumber",1);
        $pagesize = $request->get("pageSize",10);
        $list = $model->select($field,$where)->page($page,$pagesize)->orderBy($order)->all();

        $cot = $model->select("count(*) as cot",$where)->one();
        return ['IsOk' => 1,'total'=> $cot['cot'],'rows'=>$list];
    }

    /**
     * 写日志
     */
    protected function write_log($arr)
    {

        $content = "--------" . date("Y-m-d H:i:s") . "-------" . PHP_EOL;
        $content .= json_encode($arr, JSON_UNESCAPED_UNICODE);
        $content .= PHP_EOL;
        $path = "log/operation_".date("Y_m_d") . '.log';
        error_log($content,3,$path);
    }
    /**
     * 获取时间查询条件
     */
    protected function getTimeWhere($where='',$filed,$start_time,$end_time){
        if(! empty($start_time) && empty($end_time)){
            $s_time=strtotime($start_time);
            $where.=' AND  '.$filed.' >= "'.$s_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $e_time=strtotime($end_time);
            $where.=' AND  '.$filed.' > 0 AND  '.$filed.' < "'.$e_time.'"';
        }elseif (! empty($start_time) && ! empty($end_time)){
            $st_time=strtotime($start_time);
            $en_time=strtotime($end_time);
            if($st_time > $en_time) {
                $s_time = $en_time;
                $e_time = $st_time;
            }else{
                $s_time = $st_time;
                $e_time = $en_time;
            }
            $where.=' AND  '.$filed.' >="'.$s_time.'" AND  '.$filed.' < "'.$e_time.'"';
        }
        return $where;
    }

    /**
     * 获取日期查询条件
     */
    protected function getDateWhere($where='',$filed,$start_time,$end_time){
        if(! empty($start_time) && empty($end_time)){
            $where.=' AND  '.$filed.' >= "'.$start_time.'"';
        }elseif (empty($start_time) && ! empty($end_time)){
            $where.=' AND  '.$filed.' > 0 AND  '.$filed.' <= "'.$end_time.'"';
        }elseif (! empty($start_time) && ! empty($end_time)){
            $st_time=$start_time;
            $en_time=$end_time;
            if($st_time > $en_time) {
                $s_time = $en_time;
                $e_time = $st_time;
            }else{
                $s_time = $st_time;
                $e_time = $en_time;
            }
            $where.=' AND  '.$filed.' >="'.$s_time.'" AND  '.$filed.' <= "'.$e_time.'"';
        }
        return $where;
    }

    /**
     * 时间处理
     */
    protected function handleTime($time){
        $time=! empty($time) ? date("Y-m-d H:i:s",$time) : '--';
        return $time;
    }
    /**
     * 查询处理
     */
    protected function getSqlWhere($url,$sqlwhere){
        array_shift($sqlwhere);
        if($sqlwhere){
            $sqlwherestr=http_build_query($sqlwhere);
            $url=Yii::$app->params['url'].$url.'?'.$sqlwherestr;
        }
        return $url;
    }
    /**
     * 获取用户发券权限
     */
    protected function getUserAuthority($uid){
        $info = (new Admin())->select('group_id',['id' => $uid])->one();
        $group_idarr = explode('|',$info['group_id']);
        $couponAuthority = Yii::$app->params['couponadmin'];
        $res = false;
        foreach ($group_idarr as $val){
            if(in_array($val,$couponAuthority)) $res = true;
        }
        return $res;
    }

}
