<?php

use yii\helpers\Url;

?>
<div class="yuyue-tip">请确认违章处理完毕后再预约年检</div>
<div class="operation-order">
    <ul class="operation-order-ul">
        <li class="active"><span>提交订单</span></li>
        <li><span>去邮寄</span></li>
        <li><span>办理中</span></li>
        <li><span>办理完成</span></li>
    </ul>
</div>
<div class="banli-info">
    <ul class="banli-info-ul">
        <li>
            <img src="<?= $carinfo['brandIcon'] ?>" style="height: 46px;margin-right: 10px">
            <i><?= $carinfo['carNum'] ?></i>
        </li>
        <li>
            <i>年检城市</i>
            <input type="text" readonly value="<?= $carinfo['inspectionCityName'] ?>">
        </li>
        <li>
            <i>车辆所有人手机号</i>
            <input type="text" name="carphone" autofocus maxlength="11" placeholder="手机号">
        </li>
        <li>
            <i>接收办理进度手机号</i>
            <input type="text" name="carresp" maxlength="11" placeholder="手机号">
        </li>
    </ul>
    <div class="address">
            <span class="up">
                <i>选择回寄地址</i>
                <em class="icon-cloudCar2-jiantou"></em>
            </span>
        <?php if ($useraddr): ?>
            <span class="middle">
                <i>收件人</i>
                <i><?= $useraddr['name'] ?></i>
                <i><?= $useraddr['mobile'] ?></i>
            </span>
            <span class="down">
                <i>收件地址</i>
                <i><?= $useraddr['province'] ?><?= $useraddr['city'] ?><?= $useraddr['region'] ?><?= $useraddr['street'] ?></i>
            </span>
        <?php endif; ?>
        <input type="hidden" name="uaddrid" value="<?= $useraddr['id'] ?>">
    </div>
</div>
<div class="mail-cailiao">
    <span class="title">邮寄所需材料</span>
    <ul class="mail-cailiao-ul">
        <li>
            <i>1</i>
            <span>行驶证正本<br>和副本原件</span>
            <div class="examples " data-name="xingshizheng">
                <i class="icon-cloudCar2-hangshizheng"></i>
                <span>查看示例图</span>
            </div>
        </li>
        <li>
            <i>2</i>
            <span>在保险期间内的交<br>强险保单副本原件</span>
            <div class="examples " data-name="qiangxian">
                <i class="icon-cloudCar2-qiangxianbaodan"></i>
                <span>查看示例图</span>
            </div>
        </li>
        <li>
            <i>3</i>
            <span>车主身份证正面<br>和反面复印件</span>
            <div class="examples " data-name="idcard">
                <i class="icon-cloudCar2-shenfenzhengfuyinjian"></i>
                <span>查看示例图</span>
            </div>
        </li>
    </ul>
    <div class="step-last">
        <i>4</i>
        <span>请确认违章处理完毕后，再预约年检</span>
    </div>
</div>
<div class="commom-submit">
    <button type="button" class="btn-block">生成订单</button>
</div>
<div class="commom-tabar-height"></div>
<!-- 行驶证示例图弹框 -->
<div class="commom-popup-outside examples-popup-outside " style="display:none;">
    <div class="commom-popup">
        <div class="title"><span>行驶证正本和副本原件示例</span><i class="icon-error"></i></div>
        <div class="content">
            <div class="same-examples commom-img examples-xingshizheng"><img
                        src="/frontend/web/cloudcarv2/images/xingshizheng-copy.png"></div>
            <div class="same-examples commom-img examples-qiangxian"><img
                        src="/frontend/web/cloudcarv2/images/qiangxian-copy.png"></div>
            <div class="same-examples commom-img examples-idcard"><img
                        src="/frontend/web/cloudcarv2/images/idcard-copy.png"></div>
        </div>
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    if (<?= $carinfo['status']?> == <?= ERROR_STATUS?>) {
        YDUI.dialog.toast('<?php echo $carinfo['msg']?:'维护';?>', 1500);
    }
    window.addEventListener('pageshow', function(e) {
        // 通过persisted属性判断是否存在 BF Cache
        if (e.persisted) {
            location.reload();
        }
    });
    //示例弹框显示
    $('.mail-cailiao-ul>li>.examples').on('click', function () {
        var str1 = '行驶证正本和副本原件示例',
            str2 = '交强险保单副本原件示例',
            str3 = '车主身份证正面和反面复印件示例',
            $popup = $('.examples-popup-outside'),
            $examples = $('.same-examples'),
            name = $(this).attr('data-name');
        $examples.hide();
        switch (name) {
            case 'xingshizheng':
                $popup.find('.title>span').text(str1);
                $('.examples-xingshizheng').show();
                break;
            case 'qiangxian':
                $popup.find('.title>span').text(str2);
                $('.examples-qiangxian').show();
                break;
            case 'idcard':
                $popup.find('.title>span').text(str3);
                $('.examples-idcard').show();
                break;
        }
        $popup.show();
    });
    //示例弹框关闭
    $('.commom-popup>.title>i').on('click', function () {
        $('.commom-popup-outside').hide();
    });

    /**
     * 去选择回寄地址
     */
    $('.address .up').on('click', function () {
        window.location.href = "<?php echo Url::to(['useraddr'])?>";
    })
    //提交订单
    var isSubmit = false;
    $('.commom-submit').click(function () {
        if(isSubmit){
            return false;
        }else{
            var carphone = $('input[name="carphone"]').val();
            var carresp = $('input[name="carresp"]').val();
            var uaddrid = $('input[name="uaddrid"]').val();
            var pattern = /^1[34578]\d{9}$/;
            if (!pattern.test(carphone) || !pattern.test(carresp)) {
                YDUI.dialog.toast('请填写正确格式的手机号', 1000);
                return false;
            }
            if (uaddrid == '') {
                YDUI.dialog.toast('请选择回寄地址', 1000);
                return false;
            }
            YDUI.dialog.loading.open('生成中...');
            isSubmit = true;
            $.post("<?php echo Url::to(['sureorder'])?>", {
                carphone: carphone,
                carresp: carresp,
                uaddrid: uaddrid,
            }, function (json) {
                YDUI.dialog.loading.close();
                isSubmit = false;
                if (json.status == 1) {
                    window.location.href = '<?php echo Url::to(["orderdetail"]);?>' + '?mid=' + json.data.id;
                } else {
                    YDUI.dialog.toast(json.msg, 'none', 1500);
                }
            }, 'json');
        }
    });
</script>
<?php $this->endBlock('script'); ?>
