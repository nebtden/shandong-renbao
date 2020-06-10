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

<!-- 弹框 某内容显示 -->
<div class="commom-popup-outside " style="display:none;">
    <div class="commom-popup">
        <div class="title">提&nbsp;示<i class="icon-error"></i></div>
        <div class="content">
            <div class="up">您还未绑定手机<br>请先绑定</div>
            <div class="commom-submit">
                <a href="javascript:;" class="btn-block">确&nbsp;定</a>
            </div>
        </div>
    </div>
</div>




<!--<div class="bind-phone-wrapper">
    <ul class="bind-phone-ul">
        <li>
            <span>兑换码</span>
            <input type="text" name="pwd" placeholder="请输入兑换码">
        </li>
    </ul>
    <div class="jihuo-tip">注：每张卡券对应一个兑换码，只能使用一次</div>
</div>
<div class="send-comfirm">
    <button type="button" class="btn-block btn-primary">兑换服务</button>
</div>-->




<?php $this->beginBlock('script'); ?>
<script>
    // js防止安卓手机软键盘弹出挤压页面导致变形
    $('.exchange-input>input').on('focus',function(e){
        $('body').height($('body')[0].clientHeight);
    });
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
                // $('.commom-popup-outside').show();
                YDUI.dialog.alert('兑换成功！',function () {
                    window.location.href = "<?php echo Url::to(['coupon']);?>"
                });

            } else if (json.status === 2) {
                window.location.href = "<?php echo Url::to(['meal']);?>"
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });


    //关闭卡卷弹窗
    $('.btn-close-wrapper>i').on('touchstart', function () {
        $('.popup-outer').hide();
        window.location.href = '<?php echo Url::to(["coupon"])?>';
    })
</script>
<?php $this->endBlock('script'); ?>
