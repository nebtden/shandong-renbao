<?php 
use yii\helpers\Url;
?>
<section class="MyOrderCont DetailsColor">
    <h1 class="none_">优惠活动</h1>
    <div class="Purchase">
        <span>优惠活动</span>
    </div>
    <?php if(!$pros){?>
    <p style="text-align: center;font-size:20px;margin-top:30px;">暂无优惠活动！</p>
    <?php }else{?>
    <?php foreach($pros as $v){?>
    <div class="shopTitle boxSizing"><?php echo $v['desc']?></div>
    <div class="shopYHBaner"><img src="<?php echo $v['pic']?>"></div>
    <div class="YHDesc boxSizing">
    <?php if($v['pack_num']){?>
        <span><?php echo $v['pack_num'].$v['unit']?>/件，整件发货,
    <?php }?>    
        <?php echo $v['proname']?></span>
    </div>
    <div class="YHTellDiv boxSizing">
        <?php echo $v['content']?>
    </div>
    <div class="YHBotton"><a href="<?php echo Url::toRoute(['prodetail','pid'=>$v['id']]);?>">点击参加</a></div>
    <?php }?>
    <?php }?>
</section>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<?php echo $this->context->renderPartial('../layouts/mall_footer');?>
