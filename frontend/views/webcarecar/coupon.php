<?php

use yii\helpers\Url;

?>
<?php if (empty($list)): ?>
    <div class="uncoupons-tip-wrapper">
        <div class="uncoupons-tip-text">您暂时还没有此类型的优惠券哦</div>
        <div class="send-comfirm uncoupons-back">
            <button type="button" class="btn-block btn-primary">返回</button>
        </div>
    </div>
<?php endif; ?>
<div class="discountCoupons-wrapper">
    <ul class="discountCoupons-ul">
        <?php foreach ($list as $val): ?>
            <li class="<?= $val['show_coupon_style'] ?>" data-content="<?=$val['name']?>" data-id="<?=$val['id']?>">
                <div class="discountCouponsbg <?php echo ($val['status'] > 1) ? 'graybg' : 'normalbg'; ?>">
                    <h3>服务码：<?= $val['coupon_sn'] ?></h3>
                    <?= $val['show_coupon_name'] ?>
                    <?php if ($val['status'] == 2): ?>
                        <img class="used" src="/frontend/web/webcloudcar/images/used.png">
                    <?php elseif ($val['status'] == 3): ?>
                        <img class="overdued" src="/frontend/web/webcloudcar/images/overdued.png">
                    <?php endif; ?>
                </div>
                <div class="discountCoupons-instructions">
                    <time>有效期至 <?= $val['show_coupon_endtime'] ?></time>
                    <div class="mianfei-wrapper">
                        <span class="span-mianfei"><?= $val['show_coupon_desc'] ?></span>
                        <?php if ($val['status'] < 2): ?>
                            <span class="open-unfold" data-show="false"><em>展开说明</em><i
                                    class="iconfont icon-car-jiantou_up"></i> </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($val['status'] < 2): ?>
                    <div class="open-content">
                        <div class="left">
                            <h3>使用说明：</h3>
                            <ol class="use-instructions-ol">
                                <?php foreach ($use_text[$val['coupon_type']] as $txt): ?>
                                    <li><?= $txt ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>
<script>
    //展开收起说明
    $('.open-unfold').on('click', function (e) {
        e.stopPropagation();
        var isShow = $(this).attr('data-show');
        if (isShow == 'false') {
            $(this).attr('data-show', 'true')
            $(this).closest('.discountCoupons-instructions').next('.open-content').show(10);
            $(this).find('em').text('收起说明');
            $(this).find('i').removeClass('icon-car-jiantou_up').addClass('icon-car-jiantou_down');
        } else {
            $(this).attr('data-show', 'false')
            $(this).closest('.discountCoupons-instructions').next('.open-content').hide(10);
            $(this).find('em').text('展开说明');
            $(this).find('i').removeClass('icon-car-jiantou_down').addClass('icon-car-jiantou_up');
        }
    });
    //点击代金券
    $('.discountCoupons-ul>li').on('click', function () {
        var content = $(this).attr('data-content');
        var id = $(this).data('id');
        daijinquan = $('.place-order-ul>li.cardVolume>a>.div-right>span', parent.document);
        $('#iframe', parent.document).removeClass('show');
        $('#iframe', parent.document).attr('src','');
        daijinquan.text(content);
        $("input[name=coupon_id]",parent.document).val(id);
        if (!daijinquan.hasClass('volume-color')) {
            daijinquan.addClass('volume-color');
        }

    });
    //点击返回
    $('.uncoupons-back>button').on('touchstart', function () {
        $('#iframe', parent.document).removeClass('show');
        $('#iframe', parent.document).attr('src','');
    });
</script>
<?php $this->endBlock('script'); ?>
