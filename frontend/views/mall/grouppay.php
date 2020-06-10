<?php 
use yii\helpers\Url;
?>
<section class="MyOrderCont DetailsColor">
    <h1 class="none_">邀请团员</h1>
    <div class="Purchase">
        <span>购买说明</span>
    </div>
   
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>收货人信息</span></div>
        <div class="tijiaoTell boxSizing">
            <div class="TellCont">
               <?php echo $orInfo['receiver'].'&nbsp;&nbsp;'.$orInfo['telphone'].'&nbsp;&nbsp;'.$orInfo['address']?>       
            </div>
        </div>
    </div>
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>团购订单</span></div>
        <div class="DDBot">
            <dl class="dlDiv linebot boxSizing webkitbox clearfix">
                <dt><img src="<?php echo $proInfo['pic']?>"></dt>
                <dd>
                    <div class="p1"><?php echo $proInfo['proname']?></div>
                    <div class="p3 webkitbox boxSizing">
                    <?php if($proInfo['id']==149 ||$proInfo['id']==150){?>
                        <span class="sp"><b>&yen;</b><?php echo $proInfo['bargain_price']?></span>
                    <?php }else{?>  
                        <span class="sp"><b>积分</b><?php echo $proInfo['bargain_price']*10?></span>
                    <?php }?>  
                        <span class="i"></span>
                        <div class="em webkitbox">
                            <i  id='proNum'><?php echo $orInfo['num_total']?></i>
                        </div>
                    </div>
                </dd>
            </dl>
        </div>
    </div>
   
    <div class="allMessage boxSizing">
        <div class="MessDiv boxSizing">
            <dl class="clearfix">
            <?php if($proInfo['id']==149  || $proInfo['id']==150){?>
                <dt>商品金额</dt>
                <dd><i>&yen;</i><?php echo $proInfo['bargain_price']?>元</dd>
            <?php }else{?>    
                <dt>商品积分</dt>
                <dd><i>积分</i><?php echo $proInfo['bargain_price']*10?></dd>
            <?php }?>
            </dl>
            <dl class="clearfix">
                <dt>购买数量</dt>
                <dd id='buyNum'><?php echo $orInfo['num_total'].$proInfo['unit']?></dd>
            </dl>
            <dl class="clearfix">
            <?php if($proInfo['id']==149 || $proInfo['id']==150){?>
                <dt>实际付款</dt>
                <dd id='totalmoney'><i>&yen;</i><?php echo $orInfo['amount_total']?>元</dd>
            <?php }else{?>  
                <dt>需要积分</dt>
                <dd id='totalmoney'><i>积分</i><?php echo $orInfo['amount_total']*10?></dd>
            <?php }?>  
            </dl>
        </div>
    </div>
    <div class="kaituanBT" ><span onclick='tijiao();'>提交付款</span></div>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>



<div class="ReceiptZZ"></div>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/alert.js"></script>
<script>

    $(function () {
        $('.fapiaoCont').children('span').click(function(i){
       	    $(this).siblings().removeClass('cur'); 
       	    $(this).addClass('cur');
            })
   })
    
    function tijiao(){
            location.href='<?php echo Url::toRoute(['/payment/jsapi','product_id'=> $orInfo ['order_code']]);?>';   
     }
</script>

     



