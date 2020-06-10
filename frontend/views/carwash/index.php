<?php

use yii\helpers\Url;

?>
<?php if (empty($list)): ?>
    <div class="uncoupons-tip-wrapper">
        <div class="uncoupons-tip-text">您暂时还没有此类型的优惠券哦</div>
        <div class="send-comfirm uncoupons-back">
            <button type="button" class="btn-block btn-primary" onclick="location='<?php echo Url::to(['caruser/accoupon'])?>'">去激活</button>
        </div>
    </div>
<?php endif; ?>
<div class="tab-panel-inner tab-panel-inner1">
    <div class="tab-panel-inner-item active">
<!--        <div class="no-data-tip">暂无数据噢</div>-->
        <ul class="card-list-ul">
            <?php foreach($list as $val): ?>
            <li class="service-wash-car ">
                <div class="title ">
                    <i><?= $val['show_coupon_company'] ?></i>
                    <span>剩余<?= $val['show_coupon_left'] ?>次</span>
                </div>
                <div class="content">
                    <div class="up">
                        <div class="left">
                            <span>服务码: <?= $val['servicecode']?:$val['coupon_sn'] ?></span>
                            <span>有效期至：<?= $val['show_coupon_endtime'] ?></span>
                            <span>共<?= intval($val['amount']) ?>次</span>
                        </div>
                        <div class="right">
                            <?php if($val['show_coupon_url']): ?>
                            <a class="btn " href="<?= $val['show_coupon_url'] ?>">使用</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="down">
                        <div class="card-explain">
                            <em class="icon-cloudCar2-qiaquanshuoming"></em>
                            <span>卡券说明</span>
                            <i class="icon-cloudCar2-jiantou_down"></i>
                        </div>
                        <div class="explain-content">
                            <ol class="use-instructions-ol">
                                <?php foreach($use_text as $txt): ?>
                                <li><?= $txt ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    //展开收起说明
    $('.card-explain').on('click',function(e){
        e.stopPropagation();
        var isShow = $(this).attr('data-show');
        if(isShow=='true'){
            $(this).attr('data-show','fasle')
            $(this).next('.explain-content').hide(10);
            $(this).find('i').removeClass('icon-zjlt-jiantou_up').addClass('icon-zjlt-jiantou_down');
        }else{
            $(this).attr('data-show','true')
            $(this).next('.explain-content').show(10);
            $(this).find('i').removeClass('icon-zjlt-jiantou_down').addClass('icon-zjlt-jiantou_up');
        }
    });


</script>
<?php $this->endBlock('script'); ?>

