<?php

use yii\helpers\Url;

?>
<body class="cailiaoPage">
<div class="yuyue-tip">请您在线上传年检材料进行审核，审核通过后可直接办理</div>
<form action="" id="cailiao" method="post" enctype="multipart/form-data">
    <div class="mail-cailiao">
        <ul class="mail-cailiao-ul">
            <?= $htmlStr?>
        </ul>
        <p>注：请确认违章处理完毕后，再预约年检；<br /> 图片大小每张控制在  M以内，可参照示例图后进行上传</p>
        <div class="blue failed-reason">
            <div>?</div>
            <span>年检可能失败的原因</span>
        </div>
    </div>
</form>

<!--<div class="banli-info">-->
<!--    <div class="address">-->
<!--        <span class="up">-->
<!--            <i>违章查询</i>-->
<!--            <em class="icon-cloudCar2-jiantou"></em>-->
<!--        </span>-->
<!--    </div>-->
<!--</div>-->
<div class="order-details-wrapper year-testing-details-wrapper send-back-wrap">
    <span class="title">年检资料回寄地址</span>
    <ul class="order-details-ul year-testing-details-ul">
        <li>
            <i>收件人</i>
            <span><?= isset($useraddr['name']) ? $useraddr['name'] : '' ?>    <?= isset($useraddr['mobile']) ? $useraddr['mobile'] : '' ?></span>
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
    </ul>
</div>
<?php if ($info['status'] != ORDER_CANCELING):?>
<div class="commom-submit btn-wrap">
<!--    <button type="button" class="btn-block cancelorder">取消订单</button>-->
    <?php if ($reqListStatus==0):?>
    <button type="button" class="btn-block check-submit">提交审核</button>
    <?php elseif ($reqListStatus==2):?>
<!--        <button type="button" class="btn-block check-submit">提交审核</button>-->

    <?php elseif ($reqListStatus==3):?>
        <button type="button" class="btn-block sure-submit">确认派单</button>
    <?php elseif ($reqListStatus==4):?>
        <button type="button" class="btn-block check-submit">重新提交</button>
    <?php endif;?>
</div>
<?php endif;?>

<p class="cancel-tips">如需取消订单，请拨打 <i class="blue">020-62936789</i></p>
<!-- 行驶证示例图弹框 -->
<div class="commom-popup-outside examples-popup-outside " style="display:none;" >
    <div class="commom-popup">
        <div class="title"><span>行驶证正本和副本原件示例</span><i class="icon-error"></i></div>
        <div class="content">
            <div class="same-examples commom-img examples-xingshi-front"><img src="/frontend/web/cloudcarv2/img/yc-xingshi-front.jpg" ></div>
            <div class="same-examples commom-img examples-xingshi-behind"><img src="/frontend/web/cloudcarv2/img/yc-xingshi-behind.jpg" ></div>
            <div class="same-examples commom-img examples-jiaoqiang"><img src="/frontend/web/cloudcarv2/img/yc-jiaoqiang.jpg" ></div>
            <div class="same-examples commom-img examples-carid"><img src="/frontend/web/cloudcarv2/img/yc-carid.jpg" ></div>
            <div class="same-examples commom-img examples-idcard-front"><img src="/frontend/web/cloudcarv2/img/yc-idcard-front.jpg" ></div>
            <div class="same-examples commom-img examples-idcard-behind"><img src="/frontend/web/cloudcarv2/img/yc-idcard-behind.jpg" ></div>
            <div class="same-examples commom-img examples-cheshui"><img src="/frontend/web/cloudcarv2/img/yc-cheshui.jpg" ></div>
            <div class="same-examples commom-img examples-benrenidcard"><img src="/frontend/web/cloudcarv2/img/yc-benrenidcard.jpg" ></div>
        </div>
    </div>
</div>
<!-- 提示弹窗 -->
<div class="commom-popup-outside  small-popup-outside" style="display:none;" >
    <div class="commom-popup">
        <div class="title title-nobg"><i class="icon-error"></i></div>
        <div class="content">
            <div class="up">您的在线年检资料已提交审核，<br />请耐心等待审核结果，<br />审核通过后可直接办理</div>
            <div class="commom-submit need-submit">
                <a class="btn-block btn-primary small-popup-btn to_shenhe_banli" href="javascript:;">是</a>
            </div>
        </div>
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    //取消订单
    var isSubmit = false;
    var id = <?php echo $info['id'];?>;
    $('.cancelorder').click(function () {
        if (isSubmit) {
            return false;
        } else {
            YDUI.dialog.confirm('提示', '确定要取消订单吗?', function () {
                isSubmit = true;
                YDUI.dialog.loading.open('提交中...');
                $.post("<?php echo Url::to(['cancelorderimg'])?>", {
                    id: id,
                }, function (json) {
                    YDUI.dialog.loading.close();
                    isSubmit = false;
                    if (json.status == <?php echo SUCCESS_STATUS;?>) {
                        window.location.replace("<?php echo Url::to(['index'])?>");
                    } else {
                        YDUI.dialog.toast(json.msg, 'none', 15000);
                    }
                }, 'json');
            });
        }
    });
    //示例弹框显示
    $('.mail-cailiao-ul>li>.see-exam').on('click',function(){
        var str1 = '行驶证原件（正本）',
            str2 = '行驶证原件（副本）',
            str3 = '交强险保单（副本，有效期内）',
            str4 = '车辆登记证复印件',
            str5 = '身份证复印件（正面）',
            str6 = '身份证复印件（反面）',
            str7 = '车船税发票（交强险缴税记录）',
            str8 = '手持身份证正面照片',
            $popup = $('.examples-popup-outside'),
            $examples = $('.same-examples'),
            name =$(this).attr('data-name');
        $examples.hide();
        switch (name) {
            case 'xingshi-front':
                $popup.find('.title>span').text(str1);
                $('.examples-xingshi-front').show();
                break;
            case 'xingshi-behind':
                $popup.find('.title>span').text(str2);
                $('.examples-xingshi-behind').show();
                break;
            case 'jiaoqiang':
                $popup.find('.title>span').text(str3);
                $('.examples-jiaoqiang').show();
                break;
            case 'carid':
                $popup.find('.title>span').text(str4);
                $('.examples-carid').show();
                break;
            case 'idcard-front':
                $popup.find('.title>span').text(str5);
                $('.examples-idcard-front').show();
                break;
            case 'idcard-behind':
                $popup.find('.title>span').text(str6);
                $('.examples-idcard-behind').show();
                break;
            case 'cheshui':
                $popup.find('.title>span').text(str7);
                $('.examples-cheshui').show();
                break;
            case 'benrenidcard':
                $popup.find('.title>span').text(str8);
                $('.examples-benrenidcard').show();
                break;
        }
        $popup.show();
    });
    //示例弹框关闭
    $('.commom-popup>.title>i').on('click',function(){
        $('.commom-popup-outside').hide();
    });

    //上传图片
    function handleFiles(obj) {
        var f = obj.files;
        for (var i = 0; i < f.length; i++) {
            var reader = new FileReader();
            reader.readAsDataURL(f[i]);
            reader.onload = function (e) {

                if(isSubmit){
                    return false;
                }
                $(obj).parent().find('img').attr('src', e.target.result);

                YDUI.dialog.loading.open('上传中...');
                isSubmit = true;
                var attrName = $(obj).attr('name');
                var picValue = e.target.result;
                $.post("<?php echo Url::to(['uploadimg'])?>",{id:id,attrName:attrName,picValue:picValue}, function (json) {
                    YDUI.dialog.loading.close();
                    isSubmit = false;
                    if (json.status == 1) {
                        $(obj).parents('li').find('input[type="hidden"]').val(json.msg);
                    } else {
                        YDUI.dialog.toast(json.msg, 'none', 1500);
                    }
                }, 'json');

            }
        }
    }

    //提交审核提示弹窗
    $('.commom-submit>.check-submit').on('click',function(e){
        var isAll = true;
        $('ul').find('input[type="hidden"]').each(function (i) {
            if($(this).data('confirm')==1 && !$(this).val()){
                YDUI.dialog.toast('请填全所需上传资料', 700);
                isAll = false;
                return false;
            }
        });
        if(isAll){
            $('.small-popup-outside').show();
        }

    });
    //关闭弹窗
    $('.commom-popup>.title>i').on('click',function(e){
        $('.small-popup-outside').hide();
    });
    //提交
    $('.to_shenhe_banli').click(function () {
        $('.small-popup-outside').hide();
        if(isSubmit){
            return false;
        }
        YDUI.dialog.loading.open('提交中...');
        isSubmit = true;
        $.post("<?php echo Url::to(['updateimg'])?>"+'?id='+id,$('#cailiao').serialize(), function (json) {
            YDUI.dialog.loading.close();
            isSubmit = false;
            if (json.status == 1) {
                window.location.href = '<?php echo Url::to(["preorderres"]);?>' + '?mid=<?= $info['m_id']?>';
            } else {
               YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>
</body>
