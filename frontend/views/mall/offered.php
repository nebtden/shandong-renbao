<?php 
use yii\helpers\Url;
?>

<header class="shopHeader">
    <img src="<?php echo $proInfo['pic']?>">
</header>

<section class="shopDetails DetailsColor boxSizing">
    <h1 class="none_">物品详情</h1>
    <div class="ItemDesc boxSizing">
        <span><?php echo $proInfo['pack_num'].'套/件，整件发货，'.$proInfo['proname']?></span>
    </div>
    <dl class="PriceOrder boxSizing clearfix">
    <?php if($proInfo['id']==149 || $proInfo['id']==150){?>
        <dt>&yen;<i><?php echo $proInfo['bargain_price']?></i>/<?php echo $proInfo['unit']?></dt>
        <?php }else{?>
        <dt>积分：<i><?php echo $proInfo['bargain_price']*10?></i>/<?php echo $proInfo['unit']?></dt>
        <?php }?>
        <dd><!--<a href="#">加入购物车</a>--></dd>
    </dl>
    <dl class="OrderNum LaunchTuan Tsingle boxSizing clearfix">
        <dt>团长:<?php echo $orInfo['receiver']?></dt>
        <dd><i><?php echo $num?></i>人参团/整件成团<br>剩余时间<i><?php echo $yuday?></i>天内</dd>
    </dl>
    <?php if($orInfo['order_status']==0){?>
    <div class="cantuanButton"><a href="<?php echo Url::toRoute(['memberpro','orderId'=>$orInfo['id']]);?>">参团</a></div>
    <?php }else{?>
    <div class="cantuanButton" style="text-align:center;height:40px;line-height:40px;color:#fff;">不好意思，此团已过期！</div>
    <?php }?>
    <div class="zuTuanPlay paddBot20">
        <div class="PlayTop clearfix"><span>组团玩法</span><a href="#">规则详情</a></div>
        <div class="PlayBot boxSizing webkitbox">
            <dl>
                <dt>开团</dt>
                <dd>选择商品<br>并参团/开团</dd>
            </dl>
            <div class="chaIcon"></div>
            <dl>
                <dt><img src="/static/mobile/images/yaoqinghaoyou.png"></dt>
                <dd>邀请好友<br>参加</dd>
            </dl>
            <div class="chaIcon"></div>
            <dl>
                <dt><img src="/static/mobile/images/couqirenshu.png"></dt>
                <dd>凑齐三人<br>成团购买</dd>
            </dl>
        </div>
        <div class="ruleButton"><a href="#">规则详情</a></div>
        <div class="ruleButton"><a href="#">图文详情</a></div>
    </div>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>

