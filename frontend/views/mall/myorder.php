<?php 
use yii\helpers\Url;
?>

<section class="MyOrderCont DetailsColor">
    <h1 class="none_">我的订单</h1>
    <div class="orderTop boxSizing clearfix">
        <span>我的订单</span>
        
    </div>
    <ul class="shop_menu webkitbox boxSizing">
        <li class="opacity" tag='group'><a href="#"><em></em><span>我的团购</span></a><i><?php echo $groupNum?></i></li>
        <li tag=0><a href="#" ><em></em><span>待支付</span></a><i><?php echo $nopayNum?></i></li>
        <li tag=1><a href="#" ><em></em><span>待发货</span></a><i><?php echo $noshipNum?></i></li>
        <li tag=2><a href="#" ><em></em><span>待收货</span></a><i><?php echo $noreceivingNum?></i></li>
        <li tag=3><a href="#" ><em></em><span>已完成</span></a><i><?php echo $finishNum?></i></li>
    </ul>
    <div class='content'>
  
    </div>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>
<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script>
       $(function () {
             var tag='<?php echo $tag?>';
             $('.shop_menu').children('li').each(function(i){
                      
                    if ($(this).attr('tag')==tag){
                    	$(this).addClass('opacity');
                    	$(".content").load("<?php echo Url::toRoute('loadorder');?>",{status : tag});
                        }else{
                        	$(this).removeClass('opacity');
                            }
                 })

           })

           
        $('.shop_menu').children('li').click(function(i){
                 $(this).siblings().removeClass('opacity');
                 $(this).addClass('opacity');
                 var status=$(this).attr('tag')
                 $(".content").load("<?php echo Url::toRoute('loadorder');?>",{status : status});
            })

</script>
<script>
         $('.shopFooter a').eq(3).css('background-image','url(/static/mobile/images/a4.png)').css('color','#ff5500');  

         function cancel(ordercode){
             var order=ordercode;
        	 $.post("<?php echo Url::toRoute('cancelgroup');?>",{order:order},function(data){
						location.href="<?php echo Url::toRoute(['myorder','tag'=>'group'])?>";
             })

             }

         function cancelpersonal(ordercode){
             var order=ordercode;
        	 $.post("<?php echo Url::toRoute('cancelpersonal');?>",{order:order},function(data){
						location.href="<?php echo Url::toRoute(['myorder','tag'=>0])?>";
             })

             }
        
         
         function confirmorder(orderid){
              var orderid=orderid;
              $.post("<?php echo Url::toRoute('confirmorder');?>",{orderid:orderid},function(data){
					location.href="<?php echo Url::toRoute(['myorder','tag'=>'group'])?>";
              })
             }

         function confirmmemberorder(memberid){
        	 $.post("<?php echo Url::toRoute('confirmmemberorder');?>",{memberid:memberid},function(data){
					location.href="<?php echo Url::toRoute(['myorder','tag'=>'group'])?>";
              })
             }
</script>

