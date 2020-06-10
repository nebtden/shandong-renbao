<?php

use yii\helpers\Url;

?>
<div class="exchange-header commom-img">
    <img src="/frontend/web/cloudcarv2/images/exchange-bg.png" >
</div>
<div class="exchange-input">
    <input type="text" name="pwd" placeholder="请输入兑换码">
</div>
<div class="commom-submit exchange-submit">
    <a class="btn-block btn-primary" href="javascript:;" >兑&nbsp;&nbsp;换</a>
</div>
<div class="commom-tabar-height"></div>

<?php if($footer == 'hidden'){?>
    <?php $this->beginBlock('footer'); ?>
    <?php $this->endBlock('footer'); ?>
<?php }?>

<?php $this->beginBlock('script'); ?>
<script>
    <?php if(!$isbindmobile):?>
    YDUI.dialog.alert('需要绑定手机号码才可以激活卡券哦！', function () {
        window.location.href = "<?php echo Url::to(['bindmobile']);?>"
    });
    <?php endif;?>
    //提交确认
    var isSubmit = false;
    $('.exchange-submit').on('touchstart', function () {
        if (isSubmit) return false;
        var pwd = $("input[name=pwd]").val();
        if (!pwd.length) {
            YDUI.dialog.toast('请输入兑换码', 1000);
            return false;
        }
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['accoupon'])?>", {pwd: pwd}, function (json) {
            YDUI.dialog.loading.close();
            isSubmit = false;
            if (json.status === 1) {
                YDUI.dialog.alert('兑换成功！',function () {
                    window.location.href = "<?php echo Url::to(['coupon']);?>"
                });
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });

    var renderCarPopbox = function (coupons) {
        var box = $(".cardCoupon-popup");
        var len = coupons.length;
        var coupon_info;
        //写入优惠券张数
        box.find(".popup-inner>h3>span").text(len);
        //重构列表
        var li = '<div class="cardCoupon-tip">卡券已领取，请在「我的卡券」中查看</div>';
        $.each(coupons, function (i, c) {
            //coupon_info = get_coupon_info(c);
            li += '<li><div class="cardCouponbg ' + c.show_coupon_style + '"><div class="bg-left"><span>';
            li += c.show_coupon_name;
            li += '</span><i>';
            li += c.show_coupon_short;
            li += '</i></div><div class="bg-middle"><span>';
            li += c.show_coupon_desc;
            li += '</span><time>有效期&nbsp;';
            li += c.show_coupon_endtime;
            li += '</time></div><div class="bg-right"><span>';
            li += c.show_coupon_type_text;
            li += '</span></div></div></li>';
        });
        box.find(".coupons-ul").html(li);
        box.show();
    };
    //关闭卡卷弹窗
    $('.btn-close-wrapper>i').on('touchstart', function () {
        $('.popup-outer').hide();
        window.location.href = '<?php echo Url::to(["coupon"])?>';
    })
</script>
<?php $this->endBlock('script'); ?>
