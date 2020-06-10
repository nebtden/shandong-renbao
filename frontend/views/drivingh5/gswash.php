<?php
use yii\helpers\Url;

?>
<!DOCTYPE html>
<html lang="en">
<head >
    <meta charset="UTF-8">
    <meta name="wap-font-scale" content="no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telphone=no, email=no"/>
    <title>代驾服务专区</title>
    <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/ydui.css" />
    <link rel="stylesheet" href="/frontend/web/drivingH5/css/public.css">
    <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/all.css">
    <link rel="stylesheet" href="/frontend/web/drivingH5/css/ciao.css">

</head>
<style>
    .commom-popup>.title {
        position: relative;
        display: block;
        width: 100%;
        color: #fff;
        font-size: .36rem;
        text-align: center;
        padding: .24rem 0;
        background-color: #0c55a7;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .card-popup-ul>li>.title {
        display: flex;
        align-items: center;
        font-size: .36rem;
        color: #0c55a7;
        font-weight: 700;
        padding-top: .3rem;
    }
    .card-popup-ul>li>.down>a {
        height: auto;
        line-height: inherit;
        color: #fff;
        font-size: .26rem;
        border-radius: 20px;
        padding: .05rem .26rem;
        background-color: #0c55a7;
    }
    .commom-submit>.btn-block {
        height: .9rem;
        line-height: .9rem;
        background-color: #0c55a7;
        border-radius: 30px;
        font-size: .36rem;
        color: #fff;
        box-shadow: 0px 6px 15px rgba(56, 115, 235, 0.43);
    }
</style>

<body>
<div class="imgWrap">
    <img src="/frontend/web/drivingH5/img/guoshou-xc-bg-1.jpg" />
    <div class="btn-item wash-click">
        <?php if(empty($user)):?>
            <a href="<?php echo Url::to(['drivingh5/login','url' =>'gswash'])?>"><img src="/frontend/web/drivingH5/img/guoshou-xc-btn-1.png"></a>
            <a href="<?php echo Url::to(['drivingh5/login','url' =>'gswash'])?>"><img src="/frontend/web/drivingH5/img/guoshou-xc-btn-2.png"></a>
            <a href="<?php echo Url::to(['drivingh5/login','url' =>'gswash'])?>"><img src="/frontend/web/drivingH5/img/guoshou-xc-btn-3.png"></a>
            <?php else:?>
            <a href="<?php echo Url::to(['webcaruser/accoupon'])?>"><img src="/frontend/web/drivingH5/img/guoshou-xc-btn-1.png"></a>
            <a href="<?php echo Url::to(['caruser/coupon'])?>"><img src="/frontend/web/drivingH5/img/guoshou-xc-btn-2.png"></a>
            <a href="javascript:;" class="wash-service"><img src="/frontend/web/drivingH5/img/guoshou-xc-btn-3.png"></a>
        <?php endif;?>
    </div>
</div>
<!-- 洗车券弹窗 -->
<?php if (empty($washCoupon )): ?>
    <div class="commom-popup-outside  small-popup-outside" style="display:none;" >
        <div class="commom-popup">
            <div class="title">
                您暂时还没有此类型的优惠券哦
                <i class="icon-error"></i>
            </div>
            <div class="content">
                <div class="up">
                </div>
                <div class="commom-submit need-submit">
                    <a class="btn-block btn-primary small-popup-btn" href="<?php echo Url::to(['caruser/accoupon'])?>" >去激活</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="commom-popup-outside big-popup-outside" style="display: none">
        <div class="commom-popup">
            <div class="title">请先选择洗车券<i class="icon-error"></i></div>
            <div class="content">
                <ul class="card-popup-ul">
                    <?php foreach($washCoupon as $val): ?>
                        <?php if($val['status'] < 3):?>
                            <li>
                                <div class="title"><span><?= $val['name'] ?></span>
                                    <i>剩余<?= $val['show_coupon_left'] ?>次</i>
                                </div>
                                <?php if(!empty($val['servicecode'])): ?>
                                    <div class="middle">服务码：<?= $val['servicecode'] ?></div>
                                <?php else: ?>
                                    <div class="middle">&nbsp;</div>
                                <?php endif; ?>

                                <div class="down">
                                    <time>有效期至：<?= $val['show_coupon_endtime'] ?></time>

                                    <?php if($val['w_status']==2 && $val['is_mensal']==1): ?>
                                        <a class="btn " href="javascript:;">本月已使用</a>
                                    <?php else: ?>
                                    <a href="<?= $val['show_coupon_url'] ?>" class="btn">选择</a></div>
                            <?php endif;?>
                            </li>
                        <?php endif;?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="imgWrap">
    <img src="/frontend/web/drivingH5/img/guoshou-xc-bg-2.jpg" />
</div>
<script src="/frontend/web/cloudcarv2/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.flexible.js"></script>
<script>


    //洗车服务 弹窗显示
    $('.wash-click>a.wash-service').on('click',function(){
        // runCommomLayer();
        <?php if(count($washCoupon)==1): ?>
        window.location.href="<?php echo $washCoupon[0]['show_coupon_url']?>";
        <?php else: ?>
        $('.commom-popup-outside').show();
        <?php endif; ?>
    });

    //关闭大弹窗
    $('body').on('click','.commom-popup>.title>i',function(e){
        $('.big-popup-outside').hide();
    });
    //关闭小弹窗
    $('.small-popup-outside>.commom-popup>.title>i').on('click',function(e){
        $('.small-popup-outside').hide();
    });
</script>
</body>
</html>