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
    <link rel="stylesheet" href="/frontend/web/drivingH5/css/ydui.css" />
    <link rel="stylesheet" href="/frontend/web/drivingH5/css/public.css">

    <link rel="stylesheet" href="/frontend/web/drivingH5/css/ciao.css">

    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="/frontend/web/cloudcarv2/js/ydui.flexible.js"></script>


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
        background-color: #e8340c;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .card-popup-ul>li>.title {
        display: flex;
        align-items: center;
        font-size: .36rem;
        color: #e8340c;
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
        background-color: #e8340c;
    }
    .commom-submit>.btn-block {
        height: .9rem;
        line-height: .9rem;
        background-color: #e8340c;
        border-radius: 30px;
        font-size: .36rem;
        color: #fff;
        box-shadow: 0px 6px 15px rgba(56, 115, 235, 0.43);
    }
</style>

<body>
<div class="imgWrap">
    <img src="/frontend/web/drivingH5/img/bg-1.jpg" />
    <div class="btn-item driver-click">
        <?php if(empty($user)):?>
            <a href="<?php echo Url::to(['drivingh5/login','url' =>'index'])?>" ><img src="/frontend/web/drivingH5/img/btn-1.png"></a>
            <a href="<?php echo Url::to(['drivingh5/login','url' =>'index'])?>" ><img src="/frontend/web/drivingH5/img/btn-2.png"></a>
            <a href="<?php echo Url::to(['drivingh5/login','url' =>'index'])?>" ><img src="/frontend/web/drivingH5/img/btn-4.png"></a>
            <a href="<?php echo Url::to(['drivingh5/login','url' =>'index'])?>"><img src="/frontend/web/drivingH5/img/btn-3.png"></a>
            <?php else:?>
            <a href="<?php echo Url::to(['webcaruser/accoupon'])?>"><img src="/frontend/web/drivingH5/img/btn-1.png"></a>
            <a href="<?php echo Url::to(['caruser/coupon'])?>"><img src="/frontend/web/drivingH5/img/btn-2.png"></a>
            <a href="<?php echo Url::to(['webcaruorder/index'])?>" ><img src="/frontend/web/drivingH5/img/btn-4.png"></a>
            <a href="javascript:;" class="driving-service"><img src="/frontend/web/drivingH5/img/btn-3.png"></a>
        <?php endif;?>
    </div>
</div>
<!-- 代驾券弹窗 -->
<?php if (empty($drivingCoupon )): ?>
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
            <div class="title">请先选择代驾券<i class="icon-error"></i></div>
            <div class="content">
                <ul class="card-popup-ul">
                    <?php foreach($drivingCoupon as $val): ?>
                    <?php if($val['status'] < 3):?>
                    <li>
                        <div class="title"><span><?= $val['name'] ?></span>
                            <i><?= intval($val['amount']) ?>公里</i>
                        </div>
                        <?php if(!empty($val['coupon_sn'])): ?>
                            <div class="middle">优惠券码：<?= $val['coupon_sn'] ?></div>
                        <?php else: ?>
                            <div class="middle">&nbsp;</div>
                        <?php endif; ?>

                        <div class="down">
                            <time>有效期至：<?= $val['show_coupon_endtime'] ?></time>

                            <?php if($val['w_status']==2): ?>
                                <a class="btn " href="javascript:;">本月已使用</a>
                            <?php else: ?>
                            <?php if($val['company']==0): ?>
                            <a href="<?= $val['show_coupon_url'] ?>" class="btn">选择</a></div>
                        <?php else: ?>
                        <a data-url=<?= $val['show_coupon_url'] ?> href="javascript:;" class="btn didi">选择</a></div>
            <?php endif;?>
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
    <img src="/frontend/web/drivingH5/img/bg-2.jpg" />
</div>
<?php if(empty($user)):?>
    <!--	登录弹框-->
    <div class="login-wrap" style="display: none;">
        <div class="login-content">
            <div class="commom-input-list">
                <ul class="commom-input-ul">
                    <li>
                        <i>手机号</i>
                        <input type="tel" name="mobile" pattern="[0-9]*" >
                    </li>
                    <li>
                        <i>验证码</i>
                        <input type="text" name="code">
                        <button type="button" class="btn send-code " id="J_GetCode">获取验证码</button>
                    </li>
                </ul>
            </div>
            <div class="commom-submit">
                <a class="btn-block btn-primary identify-btn" href="javascript:;">立即验证</a>
            </div>
        </div>
    </div>
<?php endif;?>
<script src="/frontend/web/cloudcarv2/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>

<script>
    //滴滴代驾点击
    $('.didi').on('click',function(){
        var url = $(this).data('url');
        $.get(url,{},function (json) {
            if(json.status == 1){
                YDUI.dialog.toast(json.msg, 'none', 1000,function(){
                    window.location.href = json.url;
                });
            }else{
                YDUI.dialog.alert(json.msg);
            }
        })
    })

    //代驾 弹窗显示
    $('.driver-click>a.driving-service').on('click',function(){
        // runCommomLayer();
        $('.commom-popup-outside').show();
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