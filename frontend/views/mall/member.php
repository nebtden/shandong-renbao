<?php 
use yii\helpers\Url;
?>

<header class="meMess">
    <div class="imgDiv"><img src="/static/mobile/images/gerenxinxibaner.jpg"></div>
    <dl class="messImg boxSizing webkitbox">
        <dt><img src="<?php echo $fansinfo['headimgurl']?>"></dt>
        <dd><?php echo $fansinfo['nickname']?><i>手机号(<?php echo $fansinfo['telphone']?>)</i></dd>
    </dl>
</header>

<section class="MyOrderCont DetailsColor">
    <h1 class="none_">个人信息</h1>
    <div class="orderTop whiteColor boxSizing clearfix">
        <span>我的订单</span>
        <a href="#">查看全部订单</a>
    </div>
    <ul class="shop_menu webkitbox boxSizing">
        <li class="opacity"><a href="<?php echo Url::toRoute(['myorder','tag'=>'group'])?>"><em></em><span>我的团购</span></a><i><?php echo $groupnum;?></i></li>
        <li><a href="<?php echo Url::toRoute(['myorder','tag'=>0])?>"><em></em><span>待支付</span></a><i><?php echo $nopayNum;?></i></li>
        <li><a href="<?php echo Url::toRoute(['myorder','tag'=>1])?>"><em></em><span>待发货</span></a><i><?php echo $noshipNum;?></i></li>
        <li><a href="<?php echo Url::toRoute(['myorder','tag'=>2])?>"><em></em><span>待收货</span></a><i><?php echo $noreceivingNum;?></i></li>
        <li><a href="<?php echo Url::toRoute(['myorder','tag'=>3])?>"><em></em><span>已完成</span></a><i><?php echo $finishNum;?></i></li>
    </ul>
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>我的积分信息</span><a href="#">积分使用详情</a></div>
        <div class="jifenBot boxSizing">
            <div class="jifenCont boxSizing">

            </div>
        </div>
    </div>
    <div class="kefuTel boxSizing">
        <div class="kefuCont boxSizing">
            <a href="tel:4000499886">客服中心<i>4000499886</i></a>
        </div>
    </div>
</section>
<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script>
          
         $('.shopFooter a').eq(3).css('background-image','url(/static/mobile/images/a4.png)').css('color','#ff5500');  
</script>