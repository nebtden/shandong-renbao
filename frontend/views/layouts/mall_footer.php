<?php 
use yii\helpers\Url;
?>

<footer class="shopFooter webkitbox">
    <a href="<?php echo Url::toRoute('catlist');?>" class="a2">首页</a>
    <a href="<?php echo Url::toRoute('procat');?>">分类</a>
    <a href="<?php echo Url::toRoute('shopcart');?>">购物车</a>
    <a href="<?php echo Url::toRoute(['myorder','tag'=>0]);?>">个人中心</a>
</footer>

<script type="text/javascript">
    $(function(){
        var lhb1 = {
            a1 : "url(/static/mobile/images/a1.png)",
            a2 : "url(/static/mobile/images/a2.png)",
            a3 : "url(/static/mobile/images/a3.png)",
            a4 : "url(/static/mobile/images/a4.png)"
        }
        var lhb2 = {
            b1 : "url(/static/mobile/images/b1.png)",
            b2 : "url(/static/mobile/images/b2.png)",
            b3 : "url(/static/mobile/images/b3.png)",
            b4 : "url(/static/mobile/images/b4.png)"
        }
    });
</script>