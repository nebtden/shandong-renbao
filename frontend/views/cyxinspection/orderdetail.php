<?php

use yii\helpers\Url;

?>
<style>
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
    .mail-cailiao>ul{
        padding-left: .3rem;
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
</style>
<?php if ($info['status'] > 0 && $info['status'] != ORDER_FAIL): ?>
    <div class="commom-order-header finish">
        <div class="left commom-img order-details-img">
            <img src="/frontend/web/cloudcarv2/images/agency-yearly-inspection.png">
        </div>
        <div class="right">
            <?php if ($info['status'] == ORDER_SUCCESS): ?>
                <?php if ($info['transactType'] < 4): ?>
                    办理成功<br>车检材料已经寄回<br>本次服务已结束
                <?php else: ?>
                    在线审核通过<br>车检材料已经寄回<br>本次服务已结束
                <?php endif; ?>
            <?php elseif ($info['status'] == ORDER_HANDLING || $info['status'] == ORDER_LOCK): ?>
                <?php if ($info['transactType'] < 4): ?>
                    材料已寄出<br>正在办理中
                <?php else: ?>
                    在线审核已通过<br>请等待业务人员办理<br>
                    成功后将回寄年检标志
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <?php if ($info['status'] < 0): ?>
        <div class="commom-order-header finish">
            <div class="left commom-img order-details-img">
                <img src="/frontend/web/cloudcarv2/images/agency-yearly-inspection.png">
            </div>
            <div class="right">订单已取消</div>
        </div>
    <?php endif; ?>
    <?php if ($info['status'] == ORDER_FAIL): ?>
        <div class="commom-order-header cancel">
            <div class="left commom-img order-details-img">
                <img src="/frontend/web/cloudcarv2/images/agency-yearly-inspection.png">
            </div>
            <div class="right yearTesting-fail">
                <span>办理失败，车检材料已寄回</span>
                <a href="javascript:;">可能办理失败的原因</a>
            </div>
        </div>
    <?php endif; ?>
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
            <i>联系电话：</i>
            <span><?= $info['carphone'] ?></span>
        </li>
        <?php if ($info['status'] >= 0): ?>
            <li>
                <i>使用券：</i>
                <span>车辆年检服务券
                <i class="price">抵扣1次</i>
            </span>
            </li>
        <?php endif; ?>
    </ul>
</div>
<!--在线审核已通过、在线审核服务结束、订单已取消无此段-->
<?php if ($info['status'] >= 0): ?>
<div class="mail-cailiao ">
    <span class="title">本牌照所在省市办理年检要您邮寄如下材料：</span>
    <ul>
        <?php foreach ($info['requirementList'] as $k => $v): ?>
            <?php if ($v['requirementType'] == 'XSZ'): ?>
                <li><p>行驶证正本和副本原件</p></li>
            <?php endif; ?>
            <?php if ($v['requirementType'] == 'JQX'): ?>
                <li><p>在保险期间内的交强险保单副本原件</p></li>
            <?php endif; ?>
            <?php if ($v['requirementType'] == 'CCS'): ?>
                <li><p>车船税发票原件（如交强险含车船税、则无需提供车船税发票）</p></li>
            <?php endif; ?>
            <?php if ($v['requirementType'] == 'SFZ'): ?>
                <li><p>车主身份证正面和反面复印件</p></li>
            <?php endif; ?>
            <?php if ($v['requirementType'] == 'CLDJZ'): ?>
                <li><p>车辆登记证书复印件</p></li>
            <?php endif; ?>
        <?php endforeach; ?>
        <li><p>请确认违章处理完毕后，再预约年检</p></li>
    </ul>
    <div class="youji-tip">以上材料请确保齐全后按照给出的地址寄出，<br/>否则可能造成的办理不成功，后果自负</div>
</div>
<!--在线审核已通过、在线审核服务结束、订单已取消无此段-->
<div class="order-details-wrapper youji-address">
    <span class="title">资料寄送地址</span>
    <ul class="order-details-ul">
        <li>
            <i>收件人:</i>
            <span><?= $info['mailAddress']['name'] ?>    <?= $info['mailAddress']['phone'] ?></span>
        </li>
        <li>
            <i>收件地址：</i>
            <span><?= $info['mailAddress']['location'] ?></span>
        </li>
        <?php if ($info['status'] < 0): ?>
            <li>
                <i>单号信息：</i>
            </li>
            <li>
                <i><?= $info['express'] ?>：</i>
                <span><?= $info['expressno'] ?></span>
            </li>
        <?php endif; ?>
    </ul>
</div>
<!--订单已取消无此段-->
<div class="order-details-wrapper youji-address">
    <span class="title">年检资料回寄地址</span>
    <ul class="order-details-ul year-testing-details-ul">
        <li>
            <i>收件人:</i>
            <span><?= $useraddr['name'] ?>    <?= $useraddr['mobile'] ?></span>
        </li>
        <li>
            <i>收件地址：</i>
            <span><?= $useraddr['province'] ?><?= $useraddr['city'] ?><?= $useraddr['region'] ?><?= $useraddr['street'] ?></span>
        </li>
        <li>
            <i>单号信息：</i>
            <?php if (!$info['postSheetId'] || !$info['postCompany']): ?>
                <span class="blue">暂无回寄单号</span>
            <?php endif; ?>
        </li>
        <!--有单号显示-->
        <?php if ($info['postSheetId'] && $info['postCompany']): ?>
            <li>
                <i><?= $info['postCompany'] ?>：</i>
                <span><?= $info['postSheetId'] ?></span>
            </li>
        <?php endif; ?>
    </ul>
</div>
<?php endif;?>
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
        <!--成功办理、失败、在线审核服务结束-->
        <?php if ($info['s_time']): ?>
        <li>
            <i>服务完成时间：</i>
            <span><?php echo date('Y-m-d H:i:s',$info['s_time'])?></span>
        </li>
        <?php endif;?>
    </ul>
</div>
<?php if ($info['status'] == ORDER_SUCCESS): ?>
<!--<div class="commom-submit year-testing-submit">-->
<!--    成功办理状态-->
<!--    <button type="button" class="btn-block">评&nbsp;&nbsp;价</button>-->
<!--</div>-->
<?php endif;?>
<div class="ctip">
    <span>订单若有疑问，请拨打:<strong>0571-87392970</strong></span>
</div>

<?php if ($info['status'] == ORDER_UNSURE): ?>
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
<?php $this->beginBlock('script'); ?>
<script>
    window.addEventListener('pageshow', function (e) {
        // 通过persisted属性判断是否存在 BF Cache
        if (e.persisted) {
            location.reload();
        }
    });


</script>
<?php $this->endBlock('script'); ?>
