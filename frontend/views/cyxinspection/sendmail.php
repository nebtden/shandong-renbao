<?php

use yii\helpers\Url;

?>
<style>
    .mail-cailiao>ul{
        padding-left: .3rem;
        overflow: hidden;
    }
    .mail-cailiao>ul>li{
        list-style-type:decimal;
        font-size: .24rem;
        color: #808080;
        line-height: .38rem;
        padding-bottom: .2rem;
    }
    .mail-cailiao>ul>li:last-child{
        color: #3873eb;
    }
    .mail-cailiao>ul>li>p{
        float: left;
    }
    .mail-cailiao>ul>li>em{
        font-size: .24rem;
        color: #3873eb;
        float: right;
    }
    .express-num{
        justify-content: space-between;
    }
    .express-num>button{
        font-size: .28rem;
        color: #3873eb;
        border: solid 1px #3873eb;
        height: .54rem;
        line-height: .54rem;
        padding: 0 .2rem;
        border-radius: .27rem;
    }
</style>
<body class="submitPage">
<div class="yuyue-tip">请您邮寄办理年检所需材料至如下地址</div>
<div class="order-details-wrapper year-testing-details-wrapper">
    <span class="title">资料寄送地址</span>
    <ul class="order-details-ul year-testing-details-ul">
        <li>
            <i>收件人</i>
            <span><?= $mailAddress['name']?>    <?= $mailAddress['phone']?></span>
        </li>
        <li>
            <i>收件地址</i>
            <span><?= $mailAddress['location']?></span>
        </li>
        <li class="express-num">
            <i>物流单号</i>
            <?php if (!$info['express'] || !$info['expressno']): ?>
            <button type="button" onclick="location.href='<?php echo Url::to(['express', 'id' => $info['id']]) ?>'">填写单号</button>
            <?php endif; ?>
        </li>
        <?php if ($info['express'] && $info['expressno']): ?>
        <li>
            <i><?= $info['express'] ?></i>
            <span><?= $info['expressno'] ?></span>
        </li>
        <?php endif; ?>
    </ul>
</div>
<div class="wuliu-car-info">
    <i>年检代办车辆：</i><br />
    <div class="car-regulate">
        <i><?= $carinfo['card_province'].$carinfo['card_char'].$carinfo['card_no']?></i>
        <!--button type="button" onclick="location.href='<?php echo Url::to(['peccancy', 'carId' => $carinfo['id'], 'type' => 0]) ?>'">查询违章</button-->
    </div>
</div>
<div class="mail-cailiao">
    <span class="title">本牌照所在省市办理年检要您邮寄如下材料：</span>
    <ul>
        <?php foreach ($requirementList as $k=>$v):?>
            <?php if ($v['requirementType']=='XSZ'):?>
                <li><p>行驶证正本和副本原件</p><em class="icon-cloudCar2-jiantou2 examples" data-name="xingshi">查看示例图</em></li>
            <?php endif; ?>
            <?php if ($v['requirementType']=='JQX'):?>
                <li><p>在保险期间内的交强险保单副本原件</p><em class="icon-cloudCar2-jiantou2 examples" data-name="jiaoqiang">查看示例图</em></li>
            <?php endif;?>
            <?php if ($v['requirementType']=='CCS'):?>
                <li><p>车船税发票原件</p><em class="icon-cloudCar2-jiantou2 examples" data-name="cheshui">查看示例图</em></li>
            <?php endif;?>
            <?php if ($v['requirementType']=='SFZ'):?>
                <li><p>车主身份证正面和反面复印件</p><em class="icon-cloudCar2-jiantou2 examples" data-name="idcard">查看示例图</em></li>
            <?php endif;?>
            <?php if ($v['requirementType']=='CLDJZ'):?>
                <li><p>车辆登记证书复印件</p><em class="icon-cloudCar2-jiantou2 examples" data-name="carid">查看示例图</em></li>
            <?php endif;?>
        <?php endforeach;?>
        <li><p>请确认违章处理完毕后，再预约年检</p></li>
    </ul>

    <div class="youji-tip">以上材料请确保齐全后按照给出的地址寄出，<br />否则可能造成的办理不成功，后果自负</div>
</div>
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
<div class="commom-submit btn-wrap">
    <?php if ($info['express'] && $info['expressno']): ?>
        <button type="button" class="btn-block cansubmit">确认寄出材料</button>
    <?php else:?>
        <button type="button" class="btn-block cancelorder">取消订单</button>
        <button type="button" class="btn-block" disabled style="background: #bbb">确认寄出材料</button>
    <?php endif;?>
</div>
<div class="commom-tabar-height"></div>
<!-- 行驶证示例图弹框 -->
<div class="commom-popup-outside examples-popup-outside " style="display:none;" >
    <div class="commom-popup">
        <div class="title"><span>行驶证正本和副本原件示例</span><i class="icon-error"></i></div>
        <div class="content">
            <div class="same-examples commom-img examples-xingshi"><img src="/frontend/web/cloudcarv2/img/xingshizheng-copy.png" ></div>
            <div class="same-examples commom-img examples-jiaoqiang"><img src="/frontend/web/cloudcarv2/img/yc-jiaoqiang.jpg" ></div>
            <div class="same-examples commom-img examples-carid"><img src="/frontend/web/cloudcarv2/img/yc-carid.jpg" ></div>
            <div class="same-examples commom-img examples-idcard"><img src="/frontend/web/cloudcarv2/img/idcard-copy.png" ></div>
            <div class="same-examples commom-img examples-cheshui"><img src="/frontend/web/cloudcarv2/img/yc-cheshui.jpg" ></div>
        </div>
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    window.addEventListener('pageshow', function(e) {
        // 通过persisted属性判断是否存在 BF Cache
        if (e.persisted) {
            location.reload();
        }
    });
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
                $.post("<?php echo Url::to(['cancelorder'])?>", {
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
    //确认订单
    $('.cansubmit').click(function () {
        if (isSubmit) {
            return false;
        } else {
            YDUI.dialog.loading.open('提交中...');
            isSubmit = true;
            $.post("<?php echo Url::to(['submitorder'])?>", {
                id: id,
            }, function (json) {
                YDUI.dialog.loading.close();
                isSubmit = false;
                if (json.status == <?php echo SUCCESS_STATUS;?>) {
                    window.location.replace("<?php echo Url::to(['orderdetail','mid'=>$info['m_id']])?>");
                } else {
                    YDUI.dialog.toast(json.msg, 'none', 1500);
                }
            }, 'json');
        }
    });

    //示例弹框显示
    $('.mail-cailiao .examples').on('click',function(){
        var str1 = '行驶证原件（正副本）',
            str2 = '交强险保单（副本，有效期内）',
            str3 = '车辆登记证复印件',
            str4 = '身份证复印件（正反面）',
            str5 = '车船税发票（交强险缴税记录）',
            $popup = $('.examples-popup-outside'),
            $examples = $('.same-examples'),
            name =$(this).attr('data-name');
        $examples.hide();
        switch (name) {
            case 'xingshi':
                $popup.find('.title>span').text(str1);
                $('.examples-xingshi').show();
                break;
            case 'jiaoqiang':
                $popup.find('.title>span').text(str2);
                $('.examples-jiaoqiang').show();
                break;
            case 'carid':
                $popup.find('.title>span').text(str3);
                $('.examples-carid').show();
                break;
            case 'idcard':
                $popup.find('.title>span').text(str4);
                $('.examples-idcard').show();
                break;
            case 'cheshui':
                $popup.find('.title>span').text(str5);
                $('.examples-cheshui').show();
                break;
        }
        $popup.show();
    });
    //示例弹框关闭
    $('.commom-popup>.title>i').on('click',function(){
        $('.commom-popup-outside').hide();
    });
</script>
<?php $this->endBlock('script'); ?>
</body>
