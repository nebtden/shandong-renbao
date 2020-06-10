<?php
use yii\helpers\Url;
?>

<div class="quanyi-select-wrapper">
    <span class="title">权益任选一</span>
<!--    <span class="quanyi-tip">请在今日00:00前选择领取，过期未领取则失效</span>-->
    <ul class="taocan-ul">
        <?php foreach ($info as $key => $meal): ?>

            <li <?php if($key==0): ?>  class="active" <?php else: ?>  class="up" <?php endif; ?> data-id="<?= $meal['id'] ?>">
                <div class="up">
                    <i class="icon-cloudCar2-taocan"></i>
                    <span><?= $key+1 ?></span>
                </div>
                <div class="down"><?= $meal['name'] ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="self-quanyi-title">本权益包包含：</div>
<div class="quanyi-list">
    <?php foreach ($info as $index=>$meal): ?>
    <ul class="quanyi-ul" data-id="<?= $meal['id'] ?>"  <?php if($index==0): ?>    <?php else: ?>  style="display: none" <?php endif; ?>  >
        <?php foreach ($meal['meal_info'] as $val): ?>
                <?php if ($val['type'] == 1): ?>
                <li class="daijia">
                    <i></i>
                    <span>20公里代驾服务(<?php echo $val['amount'];  ?> * <?php echo $val['num'];  ?>次)</span>
                </li>
                <?php elseif ($val['type'] == 2): ?>
                <?php elseif ($val['type'] == 4): ?>
                <li class="wash-car">
                    <i></i>
                    <span>洗车服务(<?php echo $val['amount'];  ?> * <?php echo $val['num'];  ?>次）</span>
                </li>
                <?php elseif ($val['type'] == 5): ?>

                <li class="oil-card">
                    <i></i>
                    <span>油卡充值服务（<?php echo $val['amount'];  ?> * <?php echo $val['num'];  ?>次）</span>
                </li>
                <?php endif; ?>

        <?php  endforeach; ?>

            <li style="height: auto;padding-top: .33rem;padding-bottom: .33rem;">说明：<?= $meal['desc'] ?> </li>

    </ul>
    <?php endforeach; ?>
</div>
<div class="commom-submit lingqu-submit">
    <a href="javascript:;" class="btn-block">确认领取</a>
</div>
<div class="commom-tabar-height"></div>
<!-- 领取提示弹窗 -->
<div class="commom-popup-outside taocan-popup-outside"  style="display:none">
    <div class="commom-popup">
        <div class="title">提&nbsp;示<i class="icon-error"></i></div>
        <div class="content">
            <div class="tancan-text">套餐权益一经领取，将不能修改，<br>确定要领取本套餐吗？</div>
            <div class="tancan-btn-wrapper">
                <span class="comfirm">确&nbsp;定</span>
                <i></i>
                <span class="cancel">取&nbsp;消</span>
            </div>
        </div>
    </div>
</div>


<?php $this->beginBlock('script'); ?>
<script>
    //权益选择
    //套餐选择
    $('.taocan-ul>li').on('click',function(e){
        $('.taocan-ul>li').removeClass('active');
        var id = $('.taocan-ul>li').index(this);
        $('.quanyi-list>ul').hide();
        $('.quanyi-list>ul').eq(id).show();
        $(this).addClass('active');
    });
    //领取提示弹窗
    $('.lingqu-submit>.btn-block').on('click',function(e){
        $('.taocan-popup-outside').show();
    });
    //确认
    var isSubmit = false;
    $('.tancan-btn-wrapper>.comfirm').on('click',function(e){
        if (isSubmit) return false;
        isSubmit = true;
        //关闭弹窗
        $('.taocan-popup-outside').hide();
        var id = $(".taocan-ul>li.active").data('id');
        YDUI.dialog.loading.open('正在提交');
        console.log(id)
        $.post("<?php echo Url::to(['domeal']);?>", {id: id}, function (json) {
            YDUI.dialog.loading.close();
            isSubmit = false;
            if (json.status === 1) {
                YDUI.dialog.toast('兑换成功', 'none', function () {
                    window.location.href = '<?php echo Url::to(["coupon"])?>';
                });
            } else {
                YDUI.dialog.alert(json.msg);
            }
        });
    });
    //取消
    $('.commom-popup-outside .title>i,.tancan-btn-wrapper>.cancel').on('click',function(e){
        $('.taocan-popup-outside').hide();
    });

</script>
<?php $this->endBlock('script'); ?>
