<?php

use yii\helpers\Url;

?>
<style>
    .banli-info-ul > li > input {
        font-size: .28rem;
    }
</style>
<div class="yuyue-tip">请输入代办年检所需信息，您的信息将严格保密</div>
<div class="yuyue-info">
    <ul class="banli-info-ul">
        <li>
            <i>联系电话:</i>
            <input type="tel" maxlength="11" name="carphone" placeholder="请输入手机号">
        </li>
        <li class="car-info">
            <i>年检代办车辆：</i>
            <div class="car-regulate">
                <i><?= $carinfo['card_province'].$carinfo['card_char'].$carinfo['card_no'] ?></i>
                <!--button type="button" onclick="location.href='<?php echo Url::to(['peccancy', 'carId' => $carinfo['id'], 'type' => 0]) ?>'">
<!--                    查询违章-->
<!--                </button>-->
            </div>
        </li>
        <li class="send-address">
            <i>年检资料回寄地址</i>
            <em class="icon-cloudCar2-jiantou2">请选择</em>
        </li>
        <li>
            <i>收件人 </i>
            <span><?= isset($useraddr['name']) ? $useraddr['name'] : '' ?></span>
        </li>
        <li>
            <i>收件地址</i>
            <span>
                <?= isset($useraddr['province']) ? $useraddr['province'] : '' ?>
                <?= isset($useraddr['city']) ? $useraddr['city'] : '' ?>
                <?= isset($useraddr['region']) ? $useraddr['region'] : '' ?>
                <?= isset($useraddr['street']) ? $useraddr['street'] : '' ?>
            </span>
        </li>
        <input type="hidden" name="uaddrid" value="<?= isset($useraddr['id']) ? $useraddr['id'] : 0 ?>">
    </ul>
</div>

<div class="commom-submit">
    <button type="button" class="btn-block">预约办理</button>
</div>

<div class="commom-tabar-height"></div>
<!-- 提示弹窗 -->
<div class="commom-popup-outside  small-popup-outside" style="display:none;">
    <div class="commom-popup">
        <div class="title title-nobg"><i class="icon-error"></i></div>
        <div class="content">
            <div class="up">请先确保违章全部消除后再进行预约办理， 否则会造成年检代办不成功。</div>
            <div class="commom-submit need-submit">
                <a class="btn-block btn-primary small-popup-btn to_yuyue_banli" href="javascript:;">是</a>
            </div>
        </div>
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>

    window.addEventListener('pageshow', function (e) {
        // 通过persisted属性判断是否存在 BF Cache
        if (e.persisted) {
            location.reload();
        }
    });

    /**
     * 去选择回寄地址
     */
    $('.send-address').on('click', function () {
        window.location.href = "<?php echo Url::to(['useraddr'])?>";
    });

    //提示弹窗
    $('.commom-submit').click(function () {
        carphone = $('input[name="carphone"]').val();
        uaddrid = $('input[name="uaddrid"]').val();
        var pattern = /^1[34578]\d{9}$/;
        if (!pattern.test(carphone)) {
            YDUI.dialog.toast('请填写正确格式的手机号', 700);
            return false;
        }
        if (!uaddrid) {
            YDUI.dialog.toast('请选择回寄地址', 700);
            return false;
        }
        $('.small-popup-outside').show();
    });
    //关闭弹窗
    $('.commom-popup>.title>i').on('click',function(e){
        $('.small-popup-outside').hide();
    });

    //提交
    $('.to_yuyue_banli').click(function () {
        $('.small-popup-outside').hide();
        YDUI.dialog.loading.open('预约中...');
        isSubmit = true;
        $.post("<?php echo Url::to(['presureorder'])?>", {
            carphone: carphone,
            carId: '<?= $carinfo['id']?>',
            transactType: '<?= $carinfo['transactType']?>',
        }, function (json) {
            YDUI.dialog.loading.close();
            isSubmit = false;
            if (json.status == 1) {
                window.location.href = '<?php echo Url::to(["preorderres"]);?>' + '?mid=' + json.data.id;
            } else {
                YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>
