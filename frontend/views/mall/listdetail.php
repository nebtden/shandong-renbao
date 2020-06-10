<?php 
use yii\helpers\Url;
?>

<?php echo $this->context->renderPartial('../layouts/mall_header',array('adinfo'=>$adinfo)); ?>
<section class="shopCont boxSizing DetailsColor showHide shopPd0">
    <h1 class="none_">鼎翰易购</h1>
   
    <dl class="ListTop boxSizing clearfix">
        <dt><?php echo $cat['another_name']?></dt>
    </dl>
    <?php for($i=0;$i<count($pros);$i++){?>
    <?php if($i%3==0 && $i==count($pros)-1){?>
    <div class="ulDiv boxSizing">
    <div class="shopPDDiv webkitbox boxSizing">
        <div class="dlDiv">
            <a href="<?php echo Url::toRoute(['prodetail','pid'=>$pros[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $pros[$i]['pic']?>">
                    <?php if($pros[$i]['sign']==22)echo '<div class="hotDiv">热销</div>';?>
                    </dt>
                    <dd>
                        <p><?php echo $pros[$i]['proname'] ?></p>
                        <p>积分：<i><?php echo $pros[$i]['bargain_price']*10 ?></i></p>
                    </dd>
                </dl>
            </a>
        </div>
            </div>
            </div>
      <?php }?>
      <?php if($i%3==0 && $i!=count($pros)-1){?>
       <div class="ulDiv boxSizing">
       <div class="shopPDDiv webkitbox boxSizing">
        <div class="dlDiv">
            <a href="<?php echo Url::toRoute(['prodetail','pid'=>$pros[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $pros[$i]['pic']?>">
                    <?php if($pros[$i]['sign']==22)echo '<div class="hotDiv">热销</div>';?>
                    </dt>
                    <dd>
                        <p><?php echo $pros[$i]['proname'] ?></p>
                       <p>积分：<i><?php echo $pros[$i]['bargain_price']*10 ?></i></p>
                    </dd>
                </dl>
            </a>
        </div>
            
      <?php }?>
        <?php if($i%3==1 && $i==count($pros)-1){?>
    
        <div class="dlDiv">
            <a href="<?php echo Url::toRoute(['prodetail','pid'=>$pros[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $pros[$i]['pic']?>">
                    <?php if($pros[$i]['sign']==22)echo '<div class="hotDiv">热销</div>';?></dt>
                    <dd>
                        <p><?php echo $pros[$i]['proname'] ?></p>
                        <p>积分：<i><?php echo $pros[$i]['bargain_price']*10 ?></i></p>
                    </dd>
                </dl>
            </a>
        </div>
       </div> 
       </div>
      <?php }?>
       <?php if($i%3==1 && $i!=count($pros)-1){?>
    
        <div class="dlDiv">
            <a href="<?php echo Url::toRoute(['prodetail','pid'=>$pros[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $pros[$i]['pic']?>">
                    <?php if($pros[$i]['sign']==22)echo '<div class="hotDiv">热销</div>';?></dt>
                    <dd>
                        <p><?php echo $pros[$i]['proname'] ?></p>
                        <p>积分：<i><?php echo $pros[$i]['bargain_price']*10 ?></i></p>
                    </dd>
                </dl>
            </a>
        </div>
       
      <?php }?>
      <?php if($i%3==2){?>
    
        <div class="dlDiv">
            <a href="<?php echo Url::toRoute(['prodetail','pid'=>$pros[$i]['id']]);?>">
                <dl>
                    <dt><img src="<?php echo $pros[$i]['pic']?>"><?php if($pros[$i]['sign']==22)echo '<div class="hotDiv">热销</div>';?></dt>
                    <dd>
                        <p><?php echo $pros[$i]['proname'] ?></p>
                        <p>积分：<i><?php echo $pros[$i]['bargain_price']*10 ?></i></p>
                    </dd>
                </dl>
            </a>
        </div>
        </div>
        </div>
       
      <?php }?>
            
            
       <?php }?>
</section>
<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/Inputsearch.js"></script>
<script src="/static/mobile/js/swiper.min.js"></script>
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
