<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <title>我的订单</title>
    <link rel="stylesheet" href="/frontend/web/hbtp/css/ydui.css"/>
    <link rel="stylesheet" href="/frontend/web/hbtp/css/common.css">
    <link rel="stylesheet" href="/frontend/web/hbtp/css/all.css">
    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="/frontend/web/hbtp/js/ydui.flexible.js"></script>
</head>
<body>
<div class="order-wrapper">
    <ul class="order-ul">
        <?php foreach ($package as $p): ?>
            <li>
                <div class="order-coupon">
                    <div class="left">
                        <div class="price">
                            <i>¥</i><span>15</span><em>¥30</em><span>洗车优惠券</span>
                        </div>
                        <div class="exchange-code">兑换码：<span id="exchange-content"><?= $p['package_pwd'] ?></span></div>
                        <?php if ($p['use_limit_time']): ?>
                            <div class="time">有效期至<?php echo date("Y年m月d日", $p['use_limit_time']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="right">
                        <?php if ($p['status'] == 1): ?>
                            <button type="button" class="alxg-to-active" data-oid="<?= $info['id'] ?>"
                                    data-id="<?= $p['id'] ?>">立即激活
                            </button>
                        <?php elseif ($p['status'] == 2): ?>
                            <button type="button">已激活</button>
                        <?php elseif ($p['status'] == 3): ?>
                            <button type="button">已过期</button>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<textarea class="copy-transfer-station" id="transfer-station-input" readonly></textarea>
<script src="/frontend/web/hbtp/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/hbtp/js/ydui.js"></script>
<script>
    //激活
    var isToActive = false;
    $('.alxg-to-active').on('click', function (e) {
        if (isToActive) return false;
        isToActive = true;
        var id = $(this).data('id');
        var oid = $(this).data('oid');
        YDUI.dialog.loading.open('正在激活');
        $.post("<?php echo \yii\helpers\Url::to(['acwash'])?>",{oid:oid,id:id},function(json){
            isToActive = false;
            YDUI.dialog.loading.close();
            if(json.status === 1){
                YDUI.dialog.alert('优惠券已激活，请到我的卡券中查看',function(){
                    window.location.reload();
                });
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });
</script>
</body>
</html>