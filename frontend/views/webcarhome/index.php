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
    </style>
    <div class="swiper-container swiper-poster">
        <div class="swiper-wrapper ">
            <div class="swiper-slide ">
                <img src="/frontend/web/cloudcarv2/images/index-lunbo2.png" >
            </div>
            <div class="swiper-slide ">
                <img src="/frontend/web/images/banner.png" >
            </div>
            <div class="swiper-slide ">
                <img src="/frontend/web/cloudcarv2/images/index-lunbo2.png" >
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <div class="commom-yc-wrapper">
        <ul class="yc-servise-ul">
            <li>
                <a href="javascript:;" class="wash-service">
                    <!--                    <img src="/frontend/web/cloudcarv2/images/wash-car.png">-->
                    <img src="/frontend/web/cloudcarv2/images/wash_car.png">
                    <span>洗车服务</span>
                </a>
            </li>
            <li>
                <a href="javascript:;" class="driving-service">
                    <!--                    <img src="/frontend/web/cloudcarv2/images/super-driving.png">-->
                    <img src="/frontend/web/cloudcarv2/images/super_driving.png">
                    <span>代驾服务</span>
                </a>
            </li>
            <li>
                <a href="<?=$disurl?>" >
                    <img src="/frontend/web/cloudcarv2/images/disinfectlog.png">
                    <span>臭氧杀菌</span>
                </a>
            </li>

            <?php foreach ($menulist as $menu): ?>
                <li>
                    <a href="javascript:;" class="service" data-url="<?=$menu['web_menu_url']?>">
                        <img src="<?=$menu['menu_img']?>">
                        <span><?=$menu['menu_name']?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php if($show != 'no'):?>
    <div class="commom-popup-outside  small-popup-outside" style="display:none;">
        <div class="commom-popup">
            <div class="title title-nobg">
                <p style="color: #666">请使用手机登录开启服务</p>
                <i class="icon-error"></i>
            </div>
            <div class="content">
                <div class="cell-item">
                    <div class="cell-left"><i class="cell-icon demo-icons-phone"></i></div>
                    <div class="cell-right">
                        <input type="tel" name="mobile" pattern="[0-9]*" data-url="" class="cell-input" placeholder="请输入手机号码" autocomplete="off" />
                        <button type="button" class="btn btn-warning" id="J_GetCode">获取短信验证码</button>
                    </div>
                </div>
                <div class="cell-item">
                    <div class="cell-right cell-arrow"><input type="text" name="code" class="cell-input" placeholder="请输入验证码" autocomplete="off" /></div>
                </div>
                <div class="commom-submit need-submit">
                    <a class="btn-block btn-primary login" href="javascript:;"  >登录</a>
                </div>
            </div>
        </div>
    </div>
<?php else:?>
    <!-- 洗车券弹窗 -->
    <?php if (empty($washCoupon )): ?>
        <!-- 没有此优惠券弹窗开始-->
        <div class="commom-popup-outside sell-popup-outside" style="display: none">
            <div class="commom-popup psell-popu">
                <div class="title title-nobg "><i class="icon-error"></i></div>
                <div class="content">
                    <p class="sell-tc-txt">您暂时还没有此类型的优惠券<br/>
                        无法使用权益
                    </p>
                    <div  class="sell-tc-iput">
                        <input type="text" name="wash-pwd" placeholder="请输入您的兑换码" >
                    </div>
                    <div class="sell-btn">
                        <a href="javascript:;" class="exchange-wash-submit">激活</a>
                        <a href='<?php echo Url::to(['carwash/shoplistnew'])?>'>跳过</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 没有此优惠券弹窗结束-->
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

                                        <?php if($val['w_status']==2): ?>
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
    <?php endif;?>
    <!-- 代驾券弹窗 -->
    <?php if(empty($drivingCoupon )): ?>
        <div class="commom-popup-outside  small-popup-outside" style="display: none;" >
            <div class="commom-popup psell-popu">
                <div class="title title-nobg "><i class="icon-error"></i></div>
                <div class="content">
                    <p class="sell-tc-txt">您暂时还没有此类型的优惠券<br/>
                        无法使用权益
                    </p>
                    <div  class="sell-tc-iput">
                        <input type="text" name="pwd" placeholder="请输入您的兑换码" >
                    </div>
                    <div class="sell-btn">
                        <a href="javascript:;" class="exchange-submit">激活</a>
                        <a href='<?php echo Url::to(['carecarnew/index'])?>'>跳过</a>
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
<?php endif; ?>
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
<?php if($likelist):?>
    <!--<div class="guess-you-like mt10">-->
    <!--    <div class="guess-you-like-title">猜你喜欢</div>-->
    <!--    <ul class="guess-you-like-ul">-->
    <!--        --><?php //foreach ($likelist as $val):?>
    <!--        <li onclick="location='--><?//=$val['ad_url']?><!--'"-->
    <!--             <img src="<?//=$val['ad_pic']?><!--">-->
    <!--            <div class="li-right">-->
    <!--                <h3>--><?//=$val['ad_title']?><!--<em></em></h3>-->
    <!--                <div class="div-middle">已售<i>--><?//=$val['workoff_num']?><!--</i>好评率<i>--><?php //echo floatval($val['praise_rate'])?><!--%</i></div>-->
    <!--                <div class="div-down">-->
    <!--                    <i>--><?//=$val['discount']?><!--折</i>-->
    <!--                    <span><em class="list-del-price">--><?//=$val['market_price']?><!--</em><i>--><?//=$val['discount_price']?><!--</i></span>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </li>-->
    <!--        --><?php //endforeach;?>
    <!--    </ul>-->
    <!--</div>-->
<?php endif;?>

    <div style="height: 1.2rem"></div>
<?php $this->beginBlock('script'); ?>
    <script src="/frontend/web/cloudcarv2/js/swiper-3.0.4.min.js"></script>
    <script>


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
    <script>
        //公用弹窗函数调用
        var isShow='<?php echo $show;?>';
        var url = '';
        if(isShow == 'yes'){
            runLoginlayer(url);
        }
        <?php if($show != 'no'):?>
        $('.yc-servise-ul>li>a').on('click',function(){
            url = $(this).attr('data-url');
            runLoginlayer(url);
        });
        <?php else:?>
        //洗车弹窗
        $('.yc-servise-ul>li>a.wash-service').on('click',function(){
            <?php if(count($washCoupon) != 0): ?>
            window.location.href="<?php echo $washCoupon[0]['show_coupon_url']?>";
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
        //代驾 弹窗显示
        $('.yc-servise-ul>li>a.driving-service').on('click',function(){
            $('.commom-popup-outside').eq(1).show();
        });
        $('.yc-servise-ul>li>a.service').on('click',function(){
            url = $(this).attr('data-url');
            window.location.href = url;
        });
        //关闭优惠弹窗
        $('.sell-popup-outside>.commom-popup>.title>i').on('click',function(e){
            $('.sell-popup-outside').hide();
        });

        //关闭大弹窗
        $('body').on('click','.commom-popup>.title>i',function(e){
            $('.big-popup-outside').hide();
        });
        //洗车券激活
        var isSubmit = false;
        $('.exchange-wash-submit').on('touchstart', function () {
            if (isSubmit) return false;
            var pwd = $("input[name=wash-pwd]").val();
            if (!pwd.length) {
                YDUI.dialog.toast('请输入兑换码', 1000);
                return false;
            }
            isSubmit = true;
            YDUI.dialog.loading.open('正在提交');
            $.post("<?php echo Url::to(['webcaruser/accoupon'])?>", {pwd: pwd}, function (json) {
                YDUI.dialog.loading.close();
                isSubmit = false;
                if (json.status === 1) {
                    YDUI.dialog.alert('兑换成功！',function () {
                        window.location.href = "<?php echo Url::to(['webcaruser/coupon']);?>"
                    });

                } else if (json.status === 2) {
                    window.location.href = "<?php echo Url::to(['caruser/meal']);?>"
                } else {
                    YDUI.dialog.alert(json.msg);
                }
            }, 'json');
        });
        //代驾券激活
        var isSubmit1 = false;
        $('.exchange-submit').on('touchstart', function () {
            if (isSubmit1) return false;
            var pwd = $("input[name=pwd]").val();
            if (!pwd.length) {
                YDUI.dialog.toast('请输入兑换码', 1000);
                return false;
            }
            isSubmit1 = true;
            YDUI.dialog.loading.open('正在提交');
            $.post("<?php echo Url::to(['webcaruser/accoupon'])?>", {pwd: pwd}, function (json) {
                YDUI.dialog.loading.close();
                isSubmit1 = false;
                if (json.status === 1) {
                    YDUI.dialog.alert('兑换成功！',function () {
                        window.location.href = "<?php echo Url::to(['webcaruser/coupon']);?>"
                    });

                } else if (json.status === 2) {
                    window.location.href = "<?php echo Url::to(['caruser/meal']);?>"
                } else {
                    YDUI.dialog.alert(json.msg);
                }
            }, 'json');
        });
        <?php endif;?>


        var testMobile = function(m){
            var reg = /^1[0-9]{10}$/;
            return reg.test(m);
        };
        //公用弹窗函数 div拼接
        function runLoginlayer(url){
            $('.mobile').attr('data-url',url);
            $('.commom-popup-outside').show();
        }
        /* 定义参数 */
        var $getCode = $('#J_GetCode');
        $getCode.sendCode({
            disClass: 'btn-disabled ',
            secs: 59,
            run: false,
            runStr: '重新发送{%s}',
            resetStr: '重新获取'
        });

        $(document).on('touchstart','#J_GetCode' ,function () {
            var mobile = $("input[name=mobile]").val();
            if(!testMobile(mobile)){
                YDUI.dialog.toast('请输入正确的手机号码','none',1500);
                return false;
            }
            /* ajax 成功发送验证码后调用【start】 */
            YDUI.dialog.loading.open('发送中');
            $.post("<?php echo Url::to(['webcarlogin/smscode']);?>",{mobile:mobile},function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    $getCode.sendCode('start');
                    YDUI.dialog.toast('验证码已发送', 'none', 1500);
                }else{
                    YDUI.dialog.alert(json.msg);
                }
            },'json');
        });


        //关闭登录弹窗
        $(document).on('click','.small-popup-outside>.commom-popup>.title>i',function(){
            $('.small-popup-outside').hide();
        });

        //登录确认
        var isSubmit = false;
        $(document).on('click','.commom-submit> .login',function(){
            if(isSubmit) return false;
            var mobile = $("input[name=mobile]").val();
            var code = $("input[name=code]").val();

            if(!testMobile(mobile)){
                YDUI.dialog.toast('请输入正确的手机号码','none',1500);
                return false;
            }
            isSubmit = true;
            YDUI.dialog.loading.open('正在提交');
            $.post("<?php echo Url::to(['webcarlogin/login'])?>",{mobile:mobile,code:code,url:url},function(json){
                isSubmit = false;
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast(json.msg, 'none', 1000,function(){
                        window.location.href = json.url;
                    });

                }else{
                    YDUI.dialog.alert(json.msg);
                }
            },'json');
        });
    </script>
<?php $this->endBlock('script'); ?>