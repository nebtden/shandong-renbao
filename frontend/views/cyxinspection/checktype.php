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
    .year-testing-type{
        display: flex;
        align-items: center;
        background-color: #fff;
        padding: .1rem .35rem .1rem .41rem;
        position: relative;
        min-height: .9rem;
        margin-top: .1rem;
    }
    .year-testing-type>i{
        flex: 0 0 1.64rem;
        font-size: .3rem;
        color: #535353;
    }
    .year-testing-type>div{
        flex: 1;
        position: relative;
    }
    .year-testing-type>div>select{
        width: 100%;
        height: .9rem;
        padding: 0 .26rem;
        color: #808080;
        font-size: .26rem;
        border: 1px solid #dcdcdc;
    }
    .year-testing-type>div>.icon{
        position: absolute;
        top: .22rem;
        right: .1rem;
        z-index: 10;
        font-size: .42rem;
        color: #808080;
    }
</style>
<?php $this->endBlock('hStyle'); ?>
<!-- 可预约办理 -->
<?php //if($carinfo['isTransact'] && $carinfo['isPerfect'] && ($carinfo['inspectionType']==1) && $carinfo['isOpen']):?>
<a href="<?php echo Url::to(['car-news/detail','id'=>INSNEWS])?>" class=" annualSurvey-btn"></a>
<?php if($carinfo['state']==1 && $carinfo['canProcess']==1 && ($carinfo['inspectionType']==1)):?>
<div class="result-header-wrapper result-header-may" >
    <div class="result-header  commom-img">
        <img src="/frontend/web/cloudcarv2/images/yuyuebanli-may.png"  >
        <div class="up"><?= $carinfo['carNumber'] ?></div>
        <div class="middle">
            <span>车辆注册日期：<?= $carinfo['registerDate'] ?> </span>
            <span>检验有效期至：<?= $carinfo['checkDate'] ?> </span>
            <span>年检类型：<?= $carinfo['insType'] ?></span>
        </div>
        <div class="down">年检时间：<?= $carinfo['insTime'] ?></div>
    </div>
    <!-- 有劵 -->
    <?php if($coupon):?>
    <div class="year-testing-voucher voucher-yes ">
        <a href="<?php echo Url::to(['caruser/coupon','coupon_type'=>INSPECTION])?>">
            <span>年检劵</span>
            <?php if ($couponinfo):?>
                <i>年检（免上线服务）</i>
            <?php else:?>
                <i>有可用劵</i>
            <?php endif;?>

            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </div>
    <!-- 无劵 -->
    <?php else:?>
    <div class="year-testing-voucher voucher-no">
        <a href="<?php echo Url::to(['caruser/accoupon'])?>">
            <span>年检劵</span>
            <i class="no-voucher">无可用劵</i>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </div>
    <?php endif;?>
    <div class="year-testing-type">
        <i>年检类型：</i>
        <div>
            <select name="transactType">
                <option value="0">请选择</option>
                <?php foreach ($carinfo['transactTypeList'] as $k=>$v):?>
                    <?php if ($v['transactType']<4):?>
                        <option value="1">寄送资料年检</option>
                    <?php else:?>
                        <option value="4">上传材料年检</option>
                    <?php endif; ?>
                <?php endforeach;?>
            </select>
            <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
        </div>

    </div>
    <?php if ($carinfo['isOK']):?>
    <div class="commom-submit">
        <a href="javascript:;" class="btn-block <?php if(!$coupon){ echo 'charge-submit';} ?>">预约年检</a>
    </div>
    <?php endif;?>
    <div class="commom-tabar-height"></div>
</div>
<?php else:?>
<!-- 不可预约办理 -->
<div class="result-header-wrapper">
    <div class="result-header  commom-img" style="background: #F5F5F5;">
        <img src="/frontend/web/cloudcarv2/images/yuyuebanli-no.png" >
        <div class="up"><?= $carinfo['carNumber'] ?></div>
        <div class="middle">
            <span>车辆注册日期：<?= $carinfo['registerDate'] ?> </span>
            <span>检验有效期至：<?= $carinfo['checkDate'] ?> </span>
            <span>年检类型：<?= $carinfo['insType'] ?></span>
        </div>
        <div class="canNotReason">
            <?php if ($carinfo['inspectionType']==2):?>
            上线年检暂未开放
            <?php else:?>
            <?= $carinfo['inspectionStatusString'] ?>
            <?php endif;?>
        </div>
        <div class="down">年检时间：<?= $carinfo['insTime'] ?></div>
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
            var transactType = $('select[name="transactType"]').val();
            if(transactType=='0'){
                YDUI.dialog.toast('请选择年检类型', 700);
                return false;
            }
            window.location.href = "<?php echo Url::to(['preorder'])?>"+"?carId=<?= $carinfo['id'] ?>"+'&transactType='+transactType;
        }

    });
    //关闭弹窗
    $('.commom-popup>.title>i').on('click',function(e){
        $('.small-popup-outside').hide();
    });


</script>
<?php $this->endBlock('script'); ?>
