<?php

use yii\helpers\Url;

?>

<div class="select-oilcard-type">
    <i>请选择类型</i>
    <span data-name="zhongshiyou" class="zhongshiyou">
            <i class="icon-cloudCar2-radio "></i>
            <em>中石油</em>
        </span>
    <span data-name="zhongshihua" class="zhongshihua">
            <i class="icon-cloudCar2-radio "></i>
            <em>中石化</em>
        </span>
    <input type="hidden" id="oil_card_type" name="oil_card_type" value="<?php echo $info ? $info['oil_card_type'] : 1; ?>">
</div>
<div class="input-oilcard-info">
    <ul class="oilcard-info-ul">
        <li class="shiyou zhongshiyou">
            <i class="icon-cloudCar2-zhongshiyou"></i>
            <em>中石油卡号</em>
            <input type="tel" name="oil_card_no" placeholder="请输入16位加油卡号" value="<?php if($info['oil_card_type']==1){ echo $info['oil_card_no']; }  ?>">
        </li>
        <li class="shiyou zhongshihua">
            <i class="icon-cloudCar2-zhongshihua"></i>
            <em>中石化卡号</em>
            <input type="tel" name="oil_card_no" placeholder="请输入19位加油卡号" value="<?php if($info['oil_card_type']==2){ echo $info['oil_card_no']; }  ?>">
        </li>
        <li class="shiyou zhongshiyou">
            <i class="icon-cloudCar2-querenqiahao"></i>
            <em>确认卡号</em>
            <input type="tel" name="oil_card_no_repeat" placeholder="再次输入加油卡号" value="<?php if($info['oil_card_type']==1){ echo $info['oil_card_no']; }  ?>">
        </li>
        <li class="shiyou zhongshihua">
            <i class="icon-cloudCar2-querenqiahao"></i>
            <em>确认卡号</em>
            <input type="tel" name="oil_card_no_repeat" placeholder="再次输入加油卡号" value="<?php if($info['oil_card_type']==2){ echo $info['oil_card_no']; }  ?>">
        </li>
        <li class="shiyou zhongshiyou">
            <i class="icon-cloudCar2-shenfenzheng"></i>
            <em>身份证号</em>
            <input type="text" name="identify_no" placeholder="请输入身份证号" value="<?php if($info['oil_card_type']==1){ echo $info['identify_no']; }  ?>">
        </li>
        <li class="shiyou zhongshihua">
            <i class="icon-cloudCar2-shenfenzheng"></i>
            <em>身份证号</em>
            <input type="text" name="identify_no" placeholder="请输入身份证号" value="<?php if($info['oil_card_type']==2){ echo $info['identify_no']; }  ?>">
        </li>
    </ul>
    <div class="recharge-tip zhongshiyou">
        注：中石油用户：请在充值前确保您的加油卡是在有效期内的 <i class="price">个人记名卡</i>，不记名卡、司机卡（车队卡）、过有效期均无法 充值成功！
    </div>
    <div class="recharge-tip zhongshihua">
        注：中石化用户：请在充值前确保您的加油卡是<i class="price">主卡</i>，副卡、增票加油卡无法进行正常充值！
    </div>
    <input type="hidden" name="id" value="<?= $info['id'] ?>">
    <div class="commom-submit comfirm-recharge-submit" style="bottom: 1.7rem">
        <a href="javascript:;" class="btn-block">确定</a>
    </div>
</div>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script'); ?>
<script>
    $(function () {
        var type = $("#oil_card_type").val();
        console.log(type);
        // $('.select-oilcard-type>span').find('i').removeClass('icon-cloudCar2-radioactive');
        $('.input-oilcard-info').show();
        $('.recharge-tip').hide();
        if (type == '1') {
            $('.select-oilcard-type').find('.zhongshiyou').find('i').addClass('icon-cloudCar2-radioactive');
            $('.oilcard-info-ul>li.shiyou').hide();
            $("input[name=oil_card_type]").val(1);
            $('.oilcard-info-ul>li.zhongshiyou').show();
            $('.recharge-tip').hide();
            $('.recharge-tip.zhongshiyou').show();
        } else if (type == '2') {
            $('.select-oilcard-type').find('.zhongshihua').find('i').addClass('icon-cloudCar2-radioactive');
            $('.oilcard-info-ul>li.shiyou').hide();
            $("input[name=oil_card_type]").val(2);
            $('.oilcard-info-ul>li.zhongshihua').show();
            $('.recharge-tip').hide();
            $('.recharge-tip.zhongshihua').show();
        }

    })

</script>
<script>

    //按钮切换
    $('.select-oilcard-type>span').on('click', function(){
        var name = $(this).attr('data-name');
        $('.select-oilcard-type>span').find('i').removeClass('icon-cloudCar2-radioactive');
        $(this).find('i').addClass('icon-cloudCar2-radioactive');
        $('.input-oilcard-info').show();
        if (name == 'zhongshiyou') {
            $('.oilcard-info-ul>li.shiyou').hide();
            $("input[name=oil_card_type]").val(1);
            $('.oilcard-info-ul>li.zhongshiyou').show();
            $('.recharge-tip').hide();
            $('.recharge-tip.zhongshiyou').show();
        } else if (name == 'zhongshihua') {
            $('.oilcard-info-ul>li.shiyou').hide();
            $("input[name=oil_card_type]").val(2);
            $('.oilcard-info-ul>li.zhongshihua').show();
            $('.recharge-tip').hide();
            $('.recharge-tip.zhongshihua').show();
        }
    });
    //确认充值
    var isSubmit = false;
    $('.comfirm-recharge-submit').on('click', function () {
        if (isSubmit) return false;
        var oil_card_type = $("input[name=oil_card_type]").val();
        if (oil_card_type==1){
            var   oil_card_no = $("input[name=oil_card_no]:eq(0)").val();
            var  oil_card_no_repeat = $("input[name=oil_card_no_repeat]:eq(0)").val();
            var  identify_no = $("input[name=identify_no]:eq(0)").val();
        }else{
            var   oil_card_no = $("input[name=oil_card_no]:eq(1)").val();
            var  oil_card_no_repeat = $("input[name=oil_card_no_repeat]:eq(1)").val();
            var  identify_no = $("input[name=identify_no]:eq(1)").val();
        }



        if (!oil_card_no) {
            YDUI.dialog.toast('请输入加油卡号', 1000);
            return false;
        }
        if (oil_card_no != oil_card_no_repeat) {
            YDUI.dialog.toast('两次输入卡号不一致', 1000);
            return false;
        }
        if (!identify_no || identify_no.length < 15) {
            YDUI.dialog.toast('请输入身份证号码', 1000);
            return false;
        }
        isSubmit = true;
        var id = $("input[name=id]").val();
        var data = {
            oil_card_type: oil_card_type,
            oil_card_no: oil_card_no,
            identify_no: identify_no
        };
        if (id) data.id = id;

        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['bind'])?>", data, function (json) {
            isSubmit = false;
            YDUI.dialog.loading.close();
            if (json.status === 1) {
                YDUI.dialog.toast('绑定成功', 'success', 1000, function () {
                    window.location.href = json.url;
                });
            } else {
                YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
    });

</script>
<?php $this->endBlock('script'); ?>
