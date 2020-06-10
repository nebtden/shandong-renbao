<?php 
use yii\helpers\Url;
?>

<?php echo $this->context->renderPartial('../layouts/mall_header',array('adinfo'=>$adinfo)); ?>
<section class="shopCont boxSizing DetailsColor showHide shopPd0">
    <h1 class="none_">鼎翰易购</h1>
<ul class="iconDiv boxSizing">
        <li>
            <a href="<?php echo Url::toRoute('catlist')?>">
                <span><img src="/static/mobile/images/quanbushangping.png"></span>
                <em>全部商品</em>
            </a>
            <a onclick='taiping();'>
                <span><img src="/static/mobile/images/sirendingzhi.png"></span>
                <em>太平专场</em>
            </a>
            <a href="<?php echo Url::toRoute('preferential')?>">
                <span><img src="/static/mobile/images/youhuihuodong.png"></span>
                <em>优惠活动</em>
            </a>
        </li>
        <li>
            <a href="<?php echo Url::toRoute(['myorder','tag'=>'group'])?>">
                <span><img src="/static/mobile/images/tuangou.png"></span>
                <em>我的团购</em>
            </a>
            <a href="<?php echo Url::toRoute('fangan')?>">
                <span><img src="/static/mobile/images/lipingfangan.png"></span>
                <em>营销方案</em>
            </a>
            <a href="<?php echo Url::toRoute('member')?>">
                <span><img src="/static/mobile/images/gerenzhongxin.png"></span>
                <em>个人中心</em>
            </a>
        </li>
    </ul>
    <?php $i=1; foreach($catArr as $k=>$v){?>
    <dl class="ListTop boxSizing clearfix">
        <dt><?php echo $v?></dt>
        <dd><a href="<?php echo Url::toRoute(['listdetail','catid'=>$k]);?>">查看更多</a></dd>
    </dl>
    
    <div class="ulDiv boxSizing">
    <div class="shopPDDiv webkitbox boxSizing">
    <?php foreach($proArr[$k] as $val){?>
        <div class="dlDiv">
            <a href="<?php echo Url::toRoute(['prodetail','pid'=>$val['id']]);?>">
                <dl>
                    <dt>
                    <img src="<?php echo $val['pic']?>">
                    <?php if($val['sign']==22)echo '<div class="hotDiv">热销</div>';?>
                    </dt>
                    <dd>
                        <p><?php echo $val['proname'] ?></p>
                        <p>积分：<i><?php echo $val['bargain_price']*10 ?></i></p>
                    </dd>
                </dl>
            </a>
        </div>
        <?php }?>
            </div>
            </div>
        <?php $i++;}?>
</section>
<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/Inputsearch.js"></script>
<script src="/static/mobile/js/swiper.min.js"></script>
<script src="/static/mobile/js/alert.js"></script>

<script>
          
         $('.shopFooter a').eq(0).css('background-image','url(/static/mobile/images/a1.png)').css('color','#ff5500');  
</script>

<script>
    var swiper = new Swiper('.swiper-container.TGContainer', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
//        spaceBetween: 30,
        centeredSlides: true,
        autoplay: 2500,
        autoplayDisableOnInteraction: false
    });
</script>

<script>
      function taiping(){
            var tag='<?php echo $tag;?>';
            if(tag=='you'){
            	location.href='<?php echo Url::toRoute(['listdetail','catid'=>$v['id'],'tpy'=>1]);?>';
                }else{
                	$().tanchu('对不起，你不是太平会员！');
                    }
          }
</script>
