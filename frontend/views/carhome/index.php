<?php

use yii\helpers\Url;

?>
<style>
    .m-confirm {
        width: 85%;
        background-color: #fff;
        border-radius: 2px;
        font-size: 15px;
        -webkit-animation: zoomIn .15s ease forwards;
        animation: zoomIn .15s ease forwards;
    }
    .confirm-ft>a.confirm-btn.primary {
        color: red;
    }
    .yc-servise-ul>li:nth-of-type(n+5){
        margin-top: .4rem;
    }
    .car-ul{
        display: flex;
        flex-flow: wrap;
        align-items: center;
    }
    .car-ul>li {
        flex: 0 0 33%;
        text-align: center;
        background-color: #739df2;
        margin-right: 0.5%;
        padding: .36rem 0;
    }
    .car-ul>li:nth-of-type(3n){
        margin-right: 0;
    }
    .car-ul>li>a {
        display: block;
    }
    .car-ul>li>a>img {
        display: inline-block;
        width: .58rem;
        height: .58rem;
    }
    .car-ul>li>a>span {
        display: block;
        font-size: .28rem;
        line-height: .36rem;
        color: #fff;
        margin-top: .3rem;
    }
    .car-ul>li:nth-of-type(n+4) {
        margin-top: .05rem;
    }
</style>
    <!-- 洗车券弹窗 -->
<?php if (empty($washCoupon )): ?>
    <div class="commom-popup-outside  small-popup-outside" style="display:none;" >
        <div class="commom-popup">
            <div class="title title-nobg">
                <i class="icon-error"></i>
            </div>
            <div class="content">
                <div class="up">
                    您暂时还没有此类型的优惠券哦
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
                                    <a data-url="<?= $val['show_coupon_url'] ?>"  href="javascript:;"  class="btn wash-click">选择</a></div>
                            <?php endif;?>
                            </li>
                        <?php endif;?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

    <!-- 代驾券弹窗 -->
<?php if (empty($drivingCoupon )): ?>
    <div class="commom-popup-outside  small-popup-outside" style="display:none;" >
        <div class="commom-popup">
            <div class="title title-nobg">
                <i class="icon-error"></i>
            </div>
            <div class="content">
                <div class="up">
                    您暂时还没有此类型的优惠券哦
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


    <div class="swiper-container swiper-poster">
        <div class="swiper-wrapper ">
            <?php if ($bannerList):?>
            <?php foreach ($bannerList as $banner): ?>
            <div class="swiper-slide " onclick="window.location.href='<?= $banner['url']?>'">
                <img src="<?= $banner['b_pic']?>" >
            </div>
            <?php endforeach; ?>
            <?php else:?>
                <div class="swiper-slide ">
                    <img src="/frontend/web/images/banner.png" >
                </div>
                <div class="swiper-slide ">
                    <img src="/frontend/web/images/banner_1.jpg" >
                </div>
                <div class="swiper-slide ">
                    <img src="/frontend/web/cloudcarv2/images/index-lunbo2.png" >
                </div>
            <?php endif;?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <div class="commom-nav"><span>车主服务</span></div>
    <div class="commom-yc-wrapper">
        <ul class="yc-servise-ul">
            <li>
                <a href="javascript:;" class="wash-service">
                    <img src="/frontend/web/cloudcarv2/images/wash_car.png">
                    <span>洗车服务</span>
                </a>
            </li>
            <li>
                <a href="javascript:;" class="driving-service">
                    <img src="/frontend/web/cloudcarv2/images/super_driving.png">
                    <span>代驾服务</span>
                </a>
            </li>
            <?php foreach ($menulist as $menu): ?>
                <li>
                    <a href="<?=$menu['menu_url']?>">
                        <img src="<?=$menu['menu_img']?>">
                        <span><?=$menu['menu_name']?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="commom-nav"><span>影音娱乐</span></div>
    <div class="commom-yc-wrapper">
        <ul class="yc-servise-ul">
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/aiqiyi.png">
                    <span>爱奇艺会员</span>
                </a>
            </li>
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/tengxun.png">
                    <span>腾讯会员</span>
                </a>
            </li>
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/youku.png">
                    <span>优酷会员</span>
                </a>
            </li>
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/dianying.png">
                    <span>电影票演出票</span>
                </a>
            </li>

        </ul>
    </div>
    <div class="commom-nav"><span>便利生活</span></div>
    <div class="commom-yc-wrapper">
        <ul class="yc-servise-ul">
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/liuliang.png">
                    <span>流量</span>
                </a>
            </li>
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/huafei.png">
                    <span>话费</span>
                </a>
            </li>
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/cafei.png">
                    <span>咖啡券</span>
                </a>
            </li>
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/dianying.png">
                    <span>外卖优惠券</span>
                </a>
            </li>

        </ul>
    </div>
    <div class="commom-nav"><span>礼品卡券</span></div>
    <div class="commom-yc-wrapper">
        <ul class="yc-servise-ul">
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/jingdong.png">
                    <span>京东e卡</span>
                </a>
            </li>

            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/zhongliang.png">
                    <span>中粮礼品卡</span>
                </a>
            </li>
            <li>
                <a href="/frontend/web/caruser/accoupon.html" >
                    <img src="/frontend/web/cloudcarv2/images/menu/suning.png">
                    <span>苏宁礼品卡</span>
                </a>
            </li>

        </ul>
    </div>
<!--    <div class="commom-nav"><span>汽车服务</span></div>-->
<!--    <div class="common-car-wrapper">-->
<!--        <div class="car-ul">-->
<!--            <li>-->
<!--                <a href="--><?php //echo Url::to(['/caruser/accoupon'])?><!--">-->
<!--                    <img src="/frontend/web/cloudcarv2/img/1-save.png" >-->
<!--                    <span>安全检测</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="--><?php //echo Url::to(['/caruser/accoupon'])?><!--">-->
<!--                    <img src="/frontend/web/cloudcarv2/img/2-site.png" >-->
<!--                    <span>四轮定位</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="--><?php //echo Url::to(['/caruser/accoupon'])?><!--">-->
<!--                    <img src="/frontend/web/cloudcarv2/img/3-clean.png" >-->
<!--                    <span>空调清洗</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="--><?php //echo Url::to(['/caruser/accoupon'])?><!--">-->
<!--                    <img src="/frontend/web/cloudcarv2/img/4-care.png" >-->
<!--                    <span>常规保养</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="--><?php //echo Url::to(['/caruser/accoupon'])?><!--">-->
<!--                    <img src="/frontend/web/cloudcarv2/img/5-oil.png" >-->
<!--                    <span>钣金油漆</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="--><?php //echo Url::to(['/caruser/accoupon'])?><!--">-->
<!--                    <img src="/frontend/web/cloudcarv2/img/6-cosme.png" >-->
<!--                    <span>汽车美容</span>-->
<!--                </a>-->
<!--            </li>-->
<!--        </div>-->
<!--    </div>-->
<?php  if($new_list): ?>
    <div class="commom-nav">
        <span>云车说</span>
        <a href="<?= Url::to(['car-news/index'])  ?>">全部</a>
    </div>
    <div class="yc-shuo-wrapper">
        <ul class="yc-shuo-ul">
            <?php foreach ($new_list as $val):?>
                <li>
                    <a href="<?php echo Url::to(['car-news/detail','id'=>$val['id']])?>">
                        <span><?=$val['title'] ?></span>
                        <i class="icon-cloudCar2-jiantou"></i>
                    </a>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
<?php endif; ?>


    <!--<div class="commom-popup-outside  small-popup-outside" >
        <div class="commom-popup">
            <div class="title title-nobg"><i class="icon-error"></i></div>
            <div class="content">
                <div class="up">您还没有可用服务码，<br>是否现在激活服务码？</div>
                <div class="commom-submit need-submit">
                    <a class="btn-block btn-primary" href="javascript:;" >是</a>
                </div>
            </div>
        </div>
    </div>-->


<?php if($likelist):?>
    <div class="guess-you-like mt10">
        <div class="guess-you-like-title">猜你喜欢</div>
        <ul class="guess-you-like-ul">

            <?php foreach ($likelist as $val):?>
                <li onclick="location='<?=$val['ad_url']?>'">
                    <img src="<?=$val['ad_pic']?>">
                    <div class="li-right">
                        <h3><?=$val['ad_title']?><em></em></h3>
                        <div class="div-middle">已售<i><?=$val['workoff_num']?></i>好评率<i><?php echo floatval($val['praise_rate'])?>%</i></div>
                        <div class="div-down">
                            <i><?=$val['discount']?>折</i>
                            <span><em class="list-del-price"><?=$val['market_price']?></em><i><?=$val['discount_price']?></i></span>
                        </div>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
<?php endif;?>

    <!--<div style="height: 1.2rem"></div>-->

<?php $this->beginBlock('script') ?>
    <script src="/frontend/web/cloudcarv2/js/swiper-3.0.4.min.js"></script>
    <script>


        //洗车服务 弹窗显示
        $('.yc-servise-ul>li>a.wash-service').on('click',function(){
            // runCommomLayer();
            <?php if(count($washCoupon)==1): ?>
            $.get('<?php echo Url::to(['carwash/checklimit'])?>',{},function(json){
                if(json.status == 1){
                    window.location.href = json.url?json.url:"<?php echo $washCoupon[0]['show_coupon_url']?>";
                }else{
                    YDUI.dialog.toast(json.msg,1500);
                }
            });

            <?php else: ?>
            $('.commom-popup-outside').eq(0).show();
            <?php endif; ?>
        });

        //滴滴代驾点击
        $('.didi').on('click',function(){
            var url = $(this).data('url');
            $.get(url,{},function (json) {
                if(json.status == 1){
                    window.location.href = json.url;
                }else{
                    YDUI.dialog.alert(json.msg);
                }
            })
        });

        //洗车券点击
        $('.wash-click').on('click',function(){
            var url = $(this).data('url');
            $.get('<?php echo Url::to(['carwash/checklimit'])?>',{},function(json){
                if(json.status == 1){

                   window.location.href = json.url?json.url:url;


                }else{
                    YDUI.dialog.toast(json.msg,1500);
                }
            })
        });


        //代驾 弹窗显示
        $('.yc-servise-ul>li>a.driving-service').on('click',function(){
            $('.commom-popup-outside').eq(1).show();
        });

        //关闭大弹窗
        $('body').on('click','.commom-popup>.title>i',function(e){
            $('.big-popup-outside').hide();
        });
        //关闭小弹窗
        $('.small-popup-outside>.commom-popup>.title>i').on('click',function(e){
            $('.small-popup-outside').hide();
        });
        //初始化Swiper
        var mySwiper = new Swiper(".swiper-container", {
            slidesPerView: 'auto',
            centeredSlides: true,
            watchSlidesProgress: true,
            spaceBetween:35,
            autoplay : 3000,
            speed:300,
            autoplayDisableOnInteraction:false,
            initialSlide:1,
            pagination: ".swiper-pagination",
            paginationClickable: true,
            onProgress: function(a) {
                var b, c, d;
                for (b = 0; b < a.slides.length; b++) c = a.slides[b],
                    d = c.progress,
                    scale = 1 - Math.min(Math.abs(.2 * d), 1),
                    es = c.style,
                    es.opacity = 1 - Math.min(Math.abs(d / 2), 1),
                    es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = "translate3d(0px,0," + -Math.abs(150 * d) + "px)"
            },
            onSetTransition: function(a, b) {
                for (var c = 0; c < a.slides.length; c++) es = a.slides[c].style,
                    es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = b + "ms"
            },
        });
    </script>
<?php $this->endBlock('script'); ?>