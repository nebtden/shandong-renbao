<?php

use yii\helpers\Url;

?>
<?php $this->beginBlock('hStyle'); ?>
<style>
    .commom-submit>.btn-block {
        width: 40%;
        display: inline-block;
        margin: 1rem 4%;
    }
    .commom-submit>.btn-block.nosubmit{
        background-color: #cccccc;
        box-shadow: none;
    }
    .use-instructions-ol>li {
        font-size: .24rem;
        color: #959595;
        list-style: none;
        margin-top: .2rem;
    }
    .ctip{
        display: flex;
        flex-flow: column;
        align-items: center;
        justify-content: center;
        margin-top: .4rem;
        padding-bottom: .12rem;
        font-size: .26rem;
        color: #808080;
    }

</style>
<?php $this->endBlock('hStyle'); ?>
<?php if ($info['status'] > 0 && $info['status'] != ORDER_FAIL): ?>
    <div class="commom-order-header finish">
        <div class="left commom-img order-details-img">
            <img src="/frontend/web/cloudcarv2/images/agency-yearly-inspection.png">
        </div>
        <div class="right">
            <?php if ($info['status'] == ORDER_SUCCESS): ?>
                办理成功，车检材料已经寄回<br>
            <?php elseif ($info['status'] == ORDER_UNSURE): ?>

                <?php if (!$info['expresscom'] || !$info['expressno']): ?>
<!--                    订单已生成<br>请尽快将所需材料寄出<br>填写单号-->
                    订单已生成<br>请在确认以下信息后<br>提交订单
                <?php else:?>
                    您已填写单号<br>请提交订单
                <?php endif;?>
            <?php elseif ($info['status'] == ORDER_HANDLING || $info['status'] == ORDER_LOCK): ?>
                下单成功<br>正在办理中
<!--                材料已寄出<br>正在办理中-->
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="commom-order-header cancel">
        <div class="left commom-img order-details-img">
            <img src="/frontend/web/cloudcarv2/images/agency-yearly-inspection.png">
        </div>
        <div class="right yearTesting-fail">
            <?php if ($info['status'] < 0): ?>
                <span>订单已取消</span>
            <?php elseif ($info['status'] == ORDER_FAIL): ?>
                <span>办理失败，车检材料已寄回</span>
                <!--            <a href="javascript:;">可能办理失败的原因</a>-->
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<div class="order-details-wrapper">
    <ul class="order-details-ul year-testing-details-ul">
        <li>
            <i>年检车辆：</i>
            <span><?= $info['carnum'] ?></span>
        </li>
        <li>
            <i>年检类型：</i>
            <span><?= $info['inspectionType'] ?></span>
        </li>
        <li>
            <i>年检城市：</i>
            <span><?= $info['carcity'] ?></span>
        </li>
        <li>
            <i>车辆所有人手机号：</i>
            <span><?= $info['carphone'] ?></span>
        </li>
        <li>
            <i>接收办理进度手机号：</i>
            <span><?= $info['carresp'] ?></span>
        </li>
        <li>
            <i>使用券：</i>
            <span>车辆年检服务券
<!--                <i class="price">抵扣&yen;--><?//= $info['couponprice'] ?><!--</i>-->
            </span>
        </li>
    </ul>
</div>
<div class="order-details-wrapper youji-address">
    <span class="title">邮寄地址</span>
    <ul class="order-details-ul">
        <li>
            <i>收件人:</i>
            <span><?= $info['contactName'] ?>    <?= $info['contactPhone'] ?></span>
        </li>
        <li>
            <i>收件地址：</i>
            <span><?= $info['shopAddress'] ?></span>
        </li>
        <?php if ($info['status'] < 0): ?>
            <?php if ($info['express'] && $info['expressno']): ?>
                <li class="express">
                    <i>单号信息：</i>
                    <span><?= $info['express'] ?>    <?= $info['expressno'] ?></span>
                    <em class="icon-cloudCar2-jiantou_down"></em>
                </li>
                <div class="explain-content">
                    <ul class="use-instructions-ol">
                        <?php if ($expressinfo['result']):?>
                        <?php foreach ($expressinfo['result']['list'] as $v): ?>
                                <li><?= $v['remark'] ?></br><?= $v['datetime'] ?></li>
                        <?php endforeach; ?>
                        <?php else:?>
                            <li><?= $expressinfo['reason'] ?></li>
                        <?php endif;?>
                    </ul>
                </div>
            <?php else: ?>
                <li class="wuliudanhao">
                    <i>物流单号：</i>
                    <div class="wuliudanhao">
                        <a href="###" class="btn">填写单号</a>
<!--                        <a href="--><?php //echo Url::to(['express', 'id' => $info['id']]) ?><!--" class="btn">填写单号</a>-->
                    </div>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="youji-tip" style="padding: .3rem">注：因年检政策调整，平台暂无需邮寄材料即可提交订单。到时客服会和您联系告知具体办理方式</div>
</div>
<div class="mail-cailiao ">
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
<!--    <div class="youji-tip">注：寄出时邮寄费用请自理，否则无法办理</div>-->
</div>
<div class="order-details-wrapper youji-address">
    <span class="title">回寄地址</span>
    <ul class="order-details-ul">
        <li>
            <i>收件人:</i>
            <span><?= $info['userAddress']['name'] ?>    <?= $info['userAddress']['phone'] ?></span>
        </li>
        <li>
            <i>收件地址：</i>
            <span><?= $info['userAddress']['province'] ?><?= $info['userAddress']['city'] ?><?= $info['userAddress']['country'] ?><?= $info['userAddress']['address'] ?></span>
        </li>
        <li>
            <i>单号信息：</i>
            <span><?= $info['backEmsCompany'] ?>    <?= $info['backEmsNumber'] ?></span>
        </li>
    </ul>
</div>
<div class="youji-address">
    <ul class="order-details-ul">
        <li>
            <i>订单类型：</i>
            <span>年检服务</span>
        </li>
        <li>
            <i>订单编号：</i>
            <span><?= $info['orderid'] ?></span>
        </li>
        <li>
            <i>创建时间：</i>
            <span><?= $info['c_time'] ?></span>
        </li>
    </ul>
</div>
<div class="ctip">
    <span>订单若有疑问，请拨打:<strong>0571-87392970</strong></span>
</div>

<?php if ($info['status'] ==ORDER_UNSURE): ?>
    <div class="commom-submit year-testing-submit">
        <button type="button" class="btn-block cancelorder">取消订单</button>
<!--        --><?php //if ($info['express'] && $info['expressno']):?>
<!--            <button type="button" class="btn-block cansubmit">提交订单</button>-->
<!--        --><?php //else: ?>
<!--            <button type="button" class="btn-block nosubmit" disabled>提交订单</button>-->
<!--        --><?php //endif;?>
        <button type="button" class="btn-block cansubmit">提交订单</button>
    </div>
<?php endif; ?>

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
    window.addEventListener('pageshow', function(e) {
        // 通过persisted属性判断是否存在 BF Cache
        if (e.persisted) {
            location.reload();
        }
    });
    var express = '<?php echo $info['express'];?>';
    var expressno = '<?php echo $info['expressno'];?>';
    var status = '<?php echo $info['status'];?>';
    // if(status>0 && (express==''||expressno=='')){
    //     YDUI.dialog.alert('寄出材料后，请务必正确填写物流单号，以确保材料安全，否则</br><span style="color: #fc4f5c">对可能造成的后果以及额外费用，需自行承担</span>');
    // }
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
                        window.location.reload();
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
                    window.location.reload();
                } else {
                    YDUI.dialog.toast(json.msg, 'none', 1500);
                }
            }, 'json');
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
    //展开收起说明
    $(document).on('click','.express',function(e){
        e.stopPropagation();
        var isShow = $(this).attr('data-show');
        if(isShow=='true'){
            $(this).attr('data-show','fasle')
            $(this).next('.explain-content').hide(10);
            $(this).find('em').removeClass('icon-cloudCar2-jiantou_up').addClass('icon-cloudCar2-jiantou_down');
        }else{
            $(this).attr('data-show','true')
            $(this).next('.explain-content').show(10);
            $(this).find('em').removeClass('icon-cloudCar2-jiantou_down').addClass('icon-cloudCar2-jiantou_up');
        }
    });
</script>
<?php $this->endBlock('script'); ?>
