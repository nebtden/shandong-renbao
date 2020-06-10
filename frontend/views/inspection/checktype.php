<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle'); ?>
<style>
    .annualSurvey-btn{
        width: 16%;
        background:transparent;
        height: 16%;
        right: 0.8rem;
        top: 1.4rem;
        position: absolute;
        z-index: 999;
    }
    .result-header>div.canNotReason{
        top: 57%;
        font-size: .4rem;
        color: #fff;
    }
</style>
<?php $this->endBlock('hStyle'); ?>
<!-- 可预约办理 -->
<?php //if($carinfo['isTransact'] && $carinfo['isPerfect'] && ($carinfo['inspectionType']==1) && $carinfo['isOpen']):?>
<a href="<?php echo Url::to(['car-news/detail','id'=>INSNEWS])?>" class=" annualSurvey-btn"></a>
<?php if($carinfo['isTransact'] && $carinfo['isPerfect'] && ($carinfo['inspectionType']==1)):?>
<div class="result-header-wrapper result-header-may" >
    <div class="result-header  commom-img">
        <img src="/frontend/web/cloudcarv2/images/yuyuebanli-may.png"  >
        <div class="up"><?= $carinfo['carNum'] ?></div>
        <div class="middle">
            <span>车辆注册日期：<?= $carinfo['registerDate'] ?> </span>
            <span>年检类型：<?= $carinfo['insType'] ?></span>
        </div>
        <div class="down">年检时间：<?= $carinfo['inspectionTime'] ?></div>
    </div>
    <!-- 有劵 -->
    <?php if($coupon):?>
    <div class="year-testing-voucher voucher-yes ">
        <a href="<?php echo Url::to(['caruser/coupon','coupon_type'=>7])?>">
            <span>年检劵</span>
            <?php if ($couponinfo):?>
                <i>年检（免上线服务）</i>
<!--                <i class="price">抵扣¥--><?//= $couponinfo['amount'] ?><!--</i>-->
            <?php else:?>
                <i>有可用劵</i>
            <?php endif;?>

            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </div>
    <!-- 无劵 -->
    <?php else:?>
    <div class="year-testing-voucher voucher-no">
        <a href="javascript:;">
            <span>年检劵</span>
            <i>无可用劵</i>
<!--            <em class="icon-cloudCar2-jiantou"></em>-->
        </a>
    </div>
    <?php endif;?>
    <div class="commom-submit">
        <a href="javascript:;" class="btn-block <?php if(!$coupon){ echo 'charge-submit';} ?>">去预约办理</a>
    </div>
    <div class="commom-tabar-height"></div>
</div>
<?php else:?>
<!-- 不可预约办理 -->
<div class="result-header-wrapper">
    <div class="result-header  commom-img" style="background: #F5F5F5;">
        <img src="/frontend/web/cloudcarv2/images/yuyuebanli-no.png" >
        <div class="up"><?= $carinfo['carNum'] ?></div>
        <div class="middle">
            <span>车辆注册日期：<?= $carinfo['registerDate'] ?> </span>
            <span>年检类型：<?= $carinfo['insType'] ?></span>
        </div>
        <div class="canNotReason">
            <?php if ($carinfo['inspectionType']==2):?>
            上线年检暂未开放
            <?php else:?>
            <?= $carinfo['inspectionStatusString'] ?>
            <?php endif;?>
        </div>
        <div class="down">年检时间：<?= $carinfo['inspectionTime'] ?></div>
    </div>
</div>
<?php endif;?>
<!-- 提示弹窗 -->
<div class="commom-popup-outside  small-popup-outside" style="display:none;" >
    <div class="commom-popup">
        <div class="title title-nobg"><i class="icon-error"></i></div>
        <div class="content">
            <div class="up"></div>
            <div class="commom-submit need-submit">
                <a class="btn-block btn-primary small-popup-btn" href="" >是</a>
            </div>
        </div>
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    //兑换服务提示弹窗
    $('.commom-submit>.btn-block').on('click',function(e){
        if($(this).hasClass('charge-submit')){
            $('.small-popup-outside').find('.up').html('您还没有可用年检券，<br>是否现在兑换服务？');
            $('.small-popup-outside').find('.small-popup-btn').attr('href',"<?php echo Url::to(['caruser/accoupon'])?>");
            $('.small-popup-outside').show();
        }else {
            window.location.href = "<?php echo Url::to(['preorder'])?>"+"?carId=<?php echo $carinfo['carId']?>";
        }

    });
    //关闭弹窗
    $('.commom-popup>.title>i').on('click',function(e){
        $('.small-popup-outside').hide();
    });


</script>
<?php $this->endBlock('script'); ?>
