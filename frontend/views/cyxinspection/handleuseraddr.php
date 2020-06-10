<?php
use yii\helpers\Url;
?>
<div class="commom-address">
    <ul class="commom-address-ul">
        <li>
            <i>收件人</i>
            <input type="text" name="name"  placeholder="请填写姓名" value="<?= $addrinfo['name'] ?>">
        </li>
        <li>
            <i>手机号码</i>
            <input type="text" name="mobile"  placeholder="请填写手机号码" value="<?= $addrinfo['mobile'] ?>">
        </li>
        <li>
            <i>收件地区</i>
            <input type="text" readonly id="J_Address" value="<?= $addrinfo['province'] ?> <?= $addrinfo['city'] ?> <?= $addrinfo['region'] ?>" placeholder="请填写收件地区" >
            <input type="hidden" name="province" value="<?= $addrinfo['province'] ?>">
            <input type="hidden" name="city" value="<?= $addrinfo['city'] ?>">
            <input type="hidden" name="region" value="<?= $addrinfo['region'] ?>">
        </li>
        <li>
            <i>详细地址</i>
            <input type="text" name="street"  placeholder="请填写详细地址" value="<?= $addrinfo['street'] ?>">
        </li>
    </ul>
</div>
<div class="commom-submit commom-address-submit">
    <a href="javascript:;" class="btn-block">保&nbsp;存</a>
    <input type="hidden" name="id" value="<?= $addrinfo['id'] ?>">
</div>
<?php $this->beginBlock('script'); ?>
<script src="/frontend/web/cloudcarv2/js/city.js"></script>
<script>
    var $address = $('#J_Address');
    $address.citySelect();
    $address.on('click', function () {
        $address.citySelect('open');
    });
    $address.on('done.ydui.cityselect', function (ret) {
        /* 省：ret.provance */
        /* 市：ret.city */
        /* 县：ret.area */
        $(this).val(ret.provance + ' ' + ret.city + ' ' + ret.area);
        $('input[name="province"]').val(ret.provance);
        $('input[name="city"]').val(ret.city);
        $('input[name="region"]').val(ret.area);
    });
    //按钮切换
    $('.select-radio>span').on('click', function () {
        $(this).parent('.select-radio').find('i').removeClass('icon-cloudCar2-radioactive');
        $(this).find('i').addClass('icon-cloudCar2-radioactive');
        $(this).parent('.select-radio').find('input').val($(this).find('i').data('val'));
    });
    var isSubmit=false;
    $('.commom-submit').click(function () {
        if(isSubmit){
            return false;
        }
        if($('input[name="name"]').val()==''){
            YDUI.dialog.toast('请填写收件人', 'none', 1000);
            return false;
        }
        var reg_phone = /1\d{10}/;
        if(!reg_phone.test($('input[name="mobile"]').val())){
            YDUI.dialog.toast('请填写正确的手机号', 'none', 1000);
            return false;
        }
        if($address.val()=='' || $('input[name="street"]').val()==''){
            YDUI.dialog.toast('请填写完整的地址', 'none', 1000);
            return false;
        }
        YDUI.dialog.loading.open('提交中...');
        isSubmit = true;
        $.post("<?php echo Url::to(['handleuseraddr'])?>", {
            type:'addoredit',
            name: $('input[name="name"]').val(),
            id: $('input[name="id"]').val(),
            mobile: $('input[name="mobile"]').val(),
            province: $('input[name="province"]').val(),
            city: $('input[name="city"]').val(),
            region: $('input[name="region"]').val(),
            street: $('input[name="street"]').val(),
        }, function (json) {
            YDUI.dialog.loading.close();
            isSubmit = false;
            if (json.status == 1) {
                YDUI.dialog.toast(json.msg, 'none', 1000, function () {
                    window.history.go(-1);
                });
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>
