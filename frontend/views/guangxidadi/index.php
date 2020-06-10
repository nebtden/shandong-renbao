<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>大地保险尊享代驾</title>
  <link rel="stylesheet" type="text/css" href="/frontend/web/cloudcarv2/css/ydui.css">
  <link rel="stylesheet" type="text/css" href="/frontend/web/guangxidadi/css/common.css">
  <link rel="stylesheet" type="text/css" href="/frontend/web/guangxidadi/css/style.css">
  <script src="/frontend/web/cloudcarv2/js/ydui.flexible.js"></script>
</head>
<body>
<section class="contentContain">
	<div class="imgContain">
		<img src="/frontend/web/guangxidadi/images/1.jpg">
	</div>
	<ul class="listContain">
		<li>
			<a href="<?php echo Url::to(['guangxidadi/active'])?>">
				<img src="/frontend/web/guangxidadi/images/jihuo.jpg">
				<p>激活新服务</p>
			</a>
		</li>
		<li>
            <?php if($user):?>
			<a href="http://www.yunche168.com/frontend/web/carecarnew/index.html">
                <?php else:?>
                <a href="javascript:active()">
                    <?php endif;?>
				<img src="/frontend/web/guangxidadi/images/liebiao.jpg">
				<p>呼叫代驾</p>
			</a>
		</li>
		<li>
            <?php if($user):?>
            <a href="http://www.yunche168.com/frontend/web/caruser/coupon.html">
                <?php else:?>
                <a href="javascript:active()">
                    <?php endif;?>
				<img src="/frontend/web/guangxidadi/images/fuwubao.jpg">
				<p>我的卡券</p>
			</a>
		</li>
	</ul>
</section>
</body>
<script src="/frontend/web/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>
<script>
    function active() {
        YDUI.dialog.alert('请先激活服务后使用',function(){
            window.location.href =  '<?php echo Url::to(['guangxidadi/active'])?>';
        })
    }
</script>
</html>