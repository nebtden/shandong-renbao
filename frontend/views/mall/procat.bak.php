<?php 
use yii\helpers\Url;
?>

<?php echo $this->context->renderPartial('../layouts/mall_header'); ?>

<section class="shopCont boxSizing showHide">
    <ul class="ShopCategories boxSizing">
    <?php for($i=0;$i<count($cats);$i++){?>
      <?php if($i%2==0 && $i==count($cats)-1){?>
        <li>
            <a href="<?php echo Url::toRoute(['listdetail','catid'=>$cats[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $cats[$i]['pic']?>"></dt>
                    <dd><?php echo $cats[$i]['another_name']?></dd>
                </dl>
            </a>
           </li> 
        <?php }?>
        <?php if($i%2==0 && $i!=count($cats)-1){?>
        <li>
           <a href="<?php echo Url::toRoute(['listdetail','catid'=>$cats[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $cats[$i]['pic']?>"></dt>
                    <dd><?php echo $cats[$i]['another_name']?></dd>
                </dl>
            </a>
        <?php }?> 
         <?php if($i%2==1){?>
        
           <a href="<?php echo Url::toRoute(['listdetail','catid'=>$cats[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $cats[$i]['pic']?>"></dt>
                    <dd><?php echo $cats[$i]['another_name']?></dd>
                </dl>
            </a>
            </li>
        <?php }?>  
       
        <?php }?>  
    </ul>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/Inputsearch.js"></script>
<script src="/static/mobile/js/swiper.min.js"></script>
<script>
          
         $('.shopFooter a').eq(1).css('background-image','url(/static/mobile/images/a2.png)').css('color','#ff5500');  
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
