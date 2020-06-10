<?php

use frontend\controllers\SegwayController;
use yii\helpers\Url;

?>
<div class="daibu-wrapper">
    <ul class="year-testing-ul daibu-ul">
        <li>
            <i>选择地区</i>
            <div class="right">
                <select id="province">
                    <option value="0">省份</option>
                    <?php foreach ($provinceList as $k => $v): ?>
                        <option value="<?= $v->provinceCode ?>"><?= $v->provinceName ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
            <div class="right">
                <select id="city" name="city">
                    <option value="0">城市</option>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
            <div class="right">
                <select id="region" name="region">
                    <option value="0">区县</option>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
        <li>
            <i>用车网点</i>
            <div class="right">
                <select id="prestore" name="prestore">
                    <option value="0">请选择</option>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
        <li>
            <i>用车车型</i>
            <div class="right">
                <select id="smallService" name="smallService">
                    <option value="0">请选择</option>
                    <option value="SLS0006">经济型(出险车价值20万以内)</option>
                    <option value="SLS0007">舒适型(出险车价值20万以上)</option>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
        <li>
            <i>预约用车时间</i>
            <div class="right">
                <input type="datetime-local" name="datetime-local" id="pre_u_time">
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
        <li>
            <i>预计还车时间</i>
            <div class="right">
                <input type="datetime-local" name="datetime-local" id="pre_r_time">
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>

        <li>
            <i>联系人</i>
            <div class="right">
                <input type="text" placeholder="请输入联系人姓名" id="liaison"/>
            </div>
        </li>
        <li>
            <i>联系电话</i>
            <div class="right">
                <input type="tel" placeholder="在这里输入手机号" id="phoneNum"
                       value="<?= Yii::$app->session['wx_user_auth']['mobile'] ?>"/>
            </div>
        </li>
    </ul>
    <div class="yuyue-tips">
        <p>请保持您的手机畅通</p>
        <label><input type="checkbox"><i class="cell-checkbox-icon"></i>我已同意<a href="#">《代步车服务条款》</a></label>
    </div>

</div>
<div class="commom-submit">
    <button class="btn-block btn-primary">下一步</button>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    $(function () {
//		checkbox点击
        var _i = $(".cell-checkbox-icon"),
            checkbox = $("input[type='checkbox']");
        _i.click(function () {
            if (checkbox.is(':checked')) {
                checkbox.removeAttr("checked");
            } else {
                checkbox.prop("checked", true);
            }
        });
    });
    var pcode = ccode = rcode = 0;
    //解决placeholder在微信端显示
    $("#date").on("input", function () {
        if ($(this).val().length > 0) {
            $(this).addClass("full");
        }
        else {
            $(this).removeClass("full");
        }
    });
    $('#province').on('change', function () {
        pcode = $(this).val();
        var str = '<option value="0">城市</option>';
        $('select[name="city"]').empty().append(str);
        $('select[name="prestore"]').empty().append('<option value="0">请选择</option>');

        $.post("<?php echo Url::to(['getcity'])?>", {
            pcode: pcode,
        }, function (res) {
            str += res;
            $('select[name="city"]').empty().append(str);
        }, 'html');
    });
    $('#city').on('change', function () {
        ccode = $(this).val();
        var str = '<option value="0">区县</option>';
        $.post("<?php echo Url::to(['getcounty'])?>", {
            ccode: ccode,
        }, function (res) {
            str += res;
            $('select[name="region"]').empty().append(str);
        }, 'html');
    });
    $('#region').on('change', function () {
        rcode = $(this).val();
        var str = '<option value="0">请选择</option>';
        $.post("<?php echo Url::to(['getstore'])?>", {
            pcode: pcode,
            ccode: ccode,
            rcode: rcode,
        }, function (res) {
            str += res;
            $('select[name="prestore"]').empty().append(str);
        }, 'html');
    });

    //下一步
    var isSubmit = false;
    $('.commom-submit>.btn-block').on('click', function () {
        if (isSubmit) {
            return false;
        }
        //验证...
        var prestorecode = $('#prestore').val();
        var pre_u_time = $('#pre_u_time').val();
        var pre_r_time = $('#pre_r_time').val();
        var liaison = $('#liaison').val();
        var phoneNum = $('#phoneNum').val();
        var smallService = $('#smallService').val();
        var pattern = /^1[34578]\d{9}$/;
        if (!pattern.test(phoneNum)) {
            YDUI.dialog.toast('请输入您正确的手机号', 'none', 1500);
            return false;
        }
        if ($("input[type='checkbox']").is(':checked') == false) {
            YDUI.dialog.toast('请同意《代步车服务条款》', 'none', 1500);
            return false;
        }
        if (!pcode || !ccode || !prestorecode || !pre_u_time || !pre_r_time || !liaison || !smallService) {
            YDUI.dialog.toast('请填写完整的资料', 'none', 1500);
            return false;
        }
        YDUI.dialog.loading.open('预约中...');
        isSubmit = true;
        $.post("<?php echo Url::to(['saveorder'])?>", {
            pcode: pcode,
            ccode: ccode,
            rcode: rcode,
            prestorecode: prestorecode,
            pre_u_time: pre_u_time,
            pre_r_time: pre_r_time,
            liaison: liaison,
            phoneNum: phoneNum,
            smallService: smallService,
        }, function (res) {
            YDUI.dialog.loading.close();
            isSubmit = false;
            if (res.status == 1) {
                location.href = '<?= Url::to(["segway/preorder"])?>' + '?id=' + res.data.id;
            } else {
                YDUI.dialog.toast(res.msg, 'none', 1500);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>
