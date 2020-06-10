<?php 
use yii\helpers\Url;
?>
<section class="MyOrderCont showHide FenleiPD">
    <h1 class="none_">商品分类</h1>
    <div class="Purchase">
        <span>商品分类</span>
    </div>
    <?php foreach ($cats as $v){?>
    <div class="shopFenLei huozMarg boxSizing webkitbox">
        <p></p><div class="text"><?php echo $v['another_name']?></div><p></p>
    </div>
    <div class="shopImgShow boxSizing">
        <a href="<?php echo Url::toRoute(['listdetail','catid'=>$v['id']]);?>"><img src="<?php echo $v['pic']?>"></a>
    </div>
    <?php }?>
</section>
<?php echo $this->context->renderPartial('../layouts/mall_footer');?>
<script>
    $('.shopFooter a').eq(1).css('background-image','url(/static/mobile/images/a2.png)').css('color','#ff5500');  
</script>