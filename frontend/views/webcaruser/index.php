<?php

use yii\helpers\Url;

?>
<div class="my-header">
    <span>
        <img src="<?= $user['headimgurl'] ?>" onerror="this.src='/frontend/web/cloudcar/images/dhlogo.png'">
    </span>
    <i><?= $user['nickname'] ?></i>
    <i>积分：<?= floatval($vip['score']) ?></i>
</div>

<div class="card-wrapper">
    <a href="<?php echo Url::to(['webcaruser/coupon']); ?>" >
        <span class="icon-cloudCar2-wodeqiaquan"></span>
        <i>我的卡券</i>
    </a>
    <a href="<?php echo Url::to(['accoupon']); ?>" >
        <span class="icon-cloudCar2-duihuanfuwu"></span>
        <i>兑换服务</i>
    </a>
    <?php if($shoper['shop_status'] == 2):?>
        <a href="<?php echo Url::to(['car/recovery']); ?>" >
            <span class="icon-cloudCar2-duihuanfuwu"></span>
            <i>卡券核销</i>
        </a>
    <?php endif; ?>
</div>
<div class="my-links-list">
    <ul class="my-links-ul">
        <!--        <li>-->
        <!--            <a href="javascript:;">-->
        <!--                <i class="icon-cloudCar2-wodedingdan"></i>-->
        <!--                <span>我的订单</span>-->
        <!--                <em class="icon-cloudCar2-jiantou"></em>-->
        <!--            </a>-->
        <!--        </li>-->
        <li>
            <a href="<?php echo Url::to(['webcaruorder/index']) ?>">
                <i class="icon-cloudCar2-wodedingdan"></i>
                <span>我的订单</span>
                <em class="icon-cloudCar2-jiantou"></em>
            </a>
        </li>
        <li>
            <a href="<?php echo Url::to(['carlist']) ?>">
                <i class="icon-cloudCar2-wodecheku"></i>
                <span>我的车库</span>
                <em class="icon-cloudCar2-jiantou"></em>
            </a>
        </li>
        <li>
            <a href="<?php echo Url::to(['bindmobile']) ?>">
                <i class="icon-cloudCar2-wodeshouji"></i>
                <span>我的手机</span>
                <time><?php echo $vip['mobile'] ? substr($vip['mobile'], 0, 3) . '****' . substr($vip['mobile'], 7) : ''; ?></time>
                <em class="icon-cloudCar2-jiantou"></em>
            </a>
        </li>
        <li>
            <a href="<?php echo Url::to(['caroil/bind','id'=>$car_id,'from'=>'user']) ?>">
                <i class="icon-cloudCar2-wodejiayouqiahao"></i>
                <span>我的加油卡号</span>
                <em class="icon-cloudCar2-jiantou"></em>
            </a>
        </li>

        <li>
            <a href="tel:400-176-0899">
                <i class="icon-cloudCar2-kefurexian"></i>
                <span>客服热线</span>
                <time>400-176-0899</time>
            </a>
        </li>
    </ul>
</div>

<div style="height: 1.2rem"></div>