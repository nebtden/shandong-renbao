<?php

use yii\helpers\Url;

?>
<?php $this->beginBlock('hScript'); ?>
<script type="text/javascript"
        src="http://api.map.baidu.com/api?v=3.0&ak=<?php echo 'Rwh2q8UoSllxKMNekOTrRefBddWpG21s';//Yii::$app->params['BmapWeb']; ?>"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="/frontend/web/js/laydate/laydate.js"></script>
<?php $this->endBlock('hScript'); ?>
<?php $this->beginBlock('hStyle'); ?>
<style>
    #mapIcon {
        position: fixed;
        top: 50%;
        left: 50%;
        -webkit-transform: translateX(-50%) translateY(-100%);
        -moz-transform: translateX(-50%) translateY(-100%);
        -ms-transform: translateX(-50%) translateY(-100%);
        -o-transform: translateX(-50%) translateY(-100%);
        transform: translateX(-50%) translateY(-100%);
        /*height: 36px;*/
        margin-top: -125px;
        /*display: none;*/
        text-align: center;
        display: none;
    }

    #mapIcon > img {
        height: 36px;
        margin: 0 auto;
    }

    #mapIcon > div {
        display: flex;
        /*width:100%;*/
        align-items: center;
        background: #fff;
        border-radius: 1rem;
        padding: 0.05rem;
        border: 1px solid #14a0ff;
        margin-bottom: .05rem;
    }

    #mapIcon > div > span {
        color: #14a0ff;
        font-size: .24rem;
        border: 1px solid #14a0ff;
        border-radius: 50%;
        padding: .1rem .15rem;
    }

    #mapIcon > div > i {
        font-size: .24rem;
        padding: 0.1rem 0 0.1rem 0;
        margin-left: .1rem;
        margin-right: .1rem;
    }

    #mapIcon > div > span > i {
        display: block;
    }

    #mapIcon > div > span > em {
        display: block;
        font-size: .1rem;
    }
    #url{
        position: fixed;right: 10%;bottom: 46%;z-index:10;padding: 6px;background: #fff;color:#3873eb;box-shadow:0 2px 0 #ccc;border-radius: 6px;
    }
    .bottom-place-order-fixed{
        width: 100%;
        padding:.16rem;
        box-sizing: border-box;
        position: fixed;
        bottom:0;
        left:50%;
        transform: translate3d(-50%,0,0);
    }
    .bottom-place-order-fixed .place-order-sle{width: 100%;min-height: .62rem;margin-bottom: .18rem}
    .place-order-fixed{

        z-index:1;
        width:100%;
        height:auto;
        padding: 0 .24rem ;
        background-color:#fff;
        border-radius: .08rem;
        border-left:1px solid #d9d9d9;
        border-right:1px solid #d9d9d9;

    }
    /*.bottom-place-order-fixed .place-order-sle p:nth-child(1){float: left}*/
    .bottom-place-order-fixed .place-order-sle p:nth-child(1){float: right;width: 1.66rem;color: #3873eb;text-align: center;line-height: .60rem}
    .bottom-place-order-fixed .place-order-sle p{border-radius: .06rem;background: #fff;box-shadow: 0 .06rem .06rem #D0CECF;font-size:.26rem;height: .60rem; }


    .bottom-place-order-fixed .place-order-sle p a{display: inline-block;width: 1.5rem;height: .60rem;line-height: .60rem;text-align: center;color: #808080}
    .bottom-place-order-fixed .place-order-sle p a:nth-child(1){color: #c5c4c4}
    .bottom-place-order-fixed .place-order-sle p a.active{color:  #3873eb!important; text-decoration: underline}

    /* æ²¡æœ‰åˆ¸æç¤º */
    .uncoupons-tip-text{
        padding: 0 .24rem;
        text-align: center;
        margin-top: .52rem;
    }
    .send-comfirm>button{
        background-color: #3873EB;
        height:.90rem;
        line-height: .90rem;
        border-radius: .45rem;

    }
    .driving-type{
        height: .8rem;
        margin-bottom: .12rem;
    }
    .driving-type .driving-type-p {
        width:50%;
        float: left;
        background:#fff;
        padding:.1rem 0;
        text-align:center;
        border-radius:.40rem;
        box-shadow:0 0.1rem 0.05rem #DDD;
    }
    .driving-type .driving-type-p span{
        font-size: .28rem;
        color: #333;
        display: inline-block;
        height: 100%;
        border: 1px solid rgb(204,204,204);
        padding: .03rem .1rem;
        border-radius: .16rem;
    }
    .driving-type .driving-type-p span:nth-child(n+2){
        margin-left: .20rem;
    }
    .dj-xc{
        float: right;
        width: 1.66rem;
        color: #3873eb;
        text-align: center;
        line-height: .60rem;

    }
    .dj-xc a{
        display: block;
        height: 100%;
        color:#3873eb;
        font-size: 0.3rem;
        text-align: center;
        background: #fff;
        border-radius: .40rem;
        box-shadow:0 0.1rem 0.05rem #DDD;
    }
    .commom-driving-ul .select-driving{
        padding-right:0.2rem;
    }
    .commom-driving-ul .select-driving i{
        flex:inherit!important;
        width:1.8rem;
    }
    .commom-driving-ul .select-driving .right{
        flex:1;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .commom-driving-ul .select-driving .icon{
        color: #e60012;
        margin-left: .12rem;
        margin-right:.16rem;
        font-size:0.26rem;
    }
    .icon-cloudCar2-jiantou{
        line-height: 0.94rem;
    }

</style>
<?php $this->endBlock('hStyle'); ?>
<div class="map-wrapper commom-img" id="Bmap">
    <!--    <img src="/frontend/web/cloudcarv2/images/driving.png">-->
</div>
<!-- 代驾-初始阶段 -->
<div class="bottom-place-order-fixed">
    <div class="driving-type">
        <p class="driving-type-p"><span class="normal">日常</span> <span class="reserve">预约</span>  </p>
        <p class="dj-xc"><a href="<?= Url::to(['carecarnew/list']) ?>" style="">代驾行程</a> </p>
    </div>
    <div class="commom-driving-popup init-popup place-order-fixed item-dailyLife" style="position: relative;bottom: 0px;padding-bottom: .6rem;">
        <ul class="commom-driving-ul">
            <li class="start-date" style="display: none">
                <input  id="begin-time" type="text" placeholder="请输入预定时间" >
            </li>
            <li class="start-place">
                <input type="text" placeholder="输入出发地" readonly>
            </li>
            <li class="end-place">
                <input type="text" placeholder="输入目的地" readonly>
            </li>
<!--            <li class="select-driving">-->
<!--                <i>选择代驾券</i>-->
<!--                <div class="right">-->
<!--                    <span>请选择</span>-->
<!--                    <i></i>-->
<!--                    <em class="icon-cloudCar2-jiantou"></em>-->
<!--                </div>-->
<!--            </li>-->
            <li class="select-driving">
                <i>选择代驾券</i>
                <div class="right">请选择</div>
                <span class="icon"></span>
                <em class="icon-cloudCar2-jiantou"></em>
            </li>
            <li class="tel">
                <i>联系电话</i>
                <input type="tel" name="mobile" id="call_mobile" placeholder="请输入手机号码" value="<?=$mobile?>">
            </li>
        </ul>
        <div class="commom-submit comfirm-order-submit">
            <button type="button" class="btn-block">呼叫代驾</button>
        </div>
    </div>
</div>

<!-- 司机信息 -->
<div class="commom-driving-popup driver-info-popup" style="display:none">
    <i class="icon-cloudCar2-siji"></i>
    <span></span>
    <i class="icon-cloudCar2-jialing"></i>
    <span></span>
</div>
<!-- 代驾 正在派单 已接单 已完成 -->
<div class="commom-driving-popup paidaning-popup " style="display:none">
    <div class="box">
        <div class="left">
            <span>优惠券抵扣后预估费用：<i>￥0.0</i></span>
            <i>具体费用请以司机实际驾驶为准</i>
        </div>
        <button type="button" class="btn">取消订单</button>
    </div>
    <div class="notice">
        司机接单后，如需取消订单，请拨打<a href="tel:400-810-3939">400-810-3939</a>
    </div>
</div>
<!-- 输入出发地 弹层 -->
<div class="commom-input-place start-place-popup">
    <div class="input-place-header">
        <div class="place start">
            <i class="icon-cloudCar2-sousuo"></i>
            <input type="text" placeholder="请输入代驾出发地">
        </div>
        <div class="place end">
            <i class="icon-cloudCar2-sousuo"></i>
            <input type="text" placeholder="请输入代驾目的地">
        </div>
    </div>
    <div class="usually-address">
        <?php if ($common_address[0]): ?>
            <div class="same" data-sort="1" data-addr="<?= $common_address[0]['name'] ?>"
                 data-lat="<?= $common_address[0]['lat'] ?>" data-lng="<?= $common_address[0]['lng'] ?>">
                <span><?= $common_address[0]['title'] ?></span>
                <i><?= $common_address[0]['name'] ?></i>
            </div>
        <?php else: ?>
            <div class="same" data-sort="1" data-addr="" data-lat="" data-lng="">
                <span>添加</span>
                <i>设置常用地址</i>
            </div>
        <?php endif; ?>
        <?php if ($common_address[1]): ?>
            <div class="same" data-sort="2" data-addr="<?= $common_address[1]['name'] ?>"
                 data-lat="<?= $common_address[1]['lat'] ?>" data-lng="<?= $common_address[1]['lng'] ?>">
                <span><?= $common_address[1]['title'] ?></span>
                <i><?= $common_address[1]['name'] ?></i>
            </div>
        <?php else: ?>
            <div class="same" data-addr="" data-lat="" data-lng="" data-sort="2">
                <span>添加</span>
                <i>设置常用地址</i>
            </div>
        <?php endif; ?>
    </div>
    <div class="address-list">
        <ul class="address-ul search-place-1">

        </ul>
    </div>
</div>
<!-- 设置常用地址 -->
<div class="commom-input-place set-place-popup">
    <ul class="set-place-ul">
        <li>
            <span>地址命名</span>
            <input type="text" class="place-title" maxlength="5" placeholder="请给地址命名，最多5个字，如“家”“公司”">
        </li>
        <li>
            <span>定位</span>
            <input class="location" type="text" placeholder="请输入地址，精准定位" data-addr="" data-lat="" data-lng=""
                   data-sort="">
        </li>
    </ul>
    <div class="commom-submit address-comfirm">
        <button type="button" class="btn-block btn-primary">确认</button>
    </div>
    <div class="address-list">
        <ul class="address-ul search-place-2">

        </ul>
    </div>
</div>
<!-- 选择代驾券 -->
<?php  if($coupons):?>
    <div class="commom-input-place select-driving-popup" id="selected_coupon">

        <ul class="card-list-ul" data-selected_coupon="<?= $coupon_id  ?>">
            <?php foreach ($coupons as $coupon): ?>
                <li class="service-daijia">
                    <div class="title ">
                        <i><?= $coupon['name'] ?></i>
                        <span><?= floatval($coupon['amount']) ?>公里</span>
                    </div>
                    <div class="content">
                        <div class="up">
                            <div class="left">
                                <span>服务码: <?= $coupon['coupon_sn'] ?></span>
                                <span>有效期：<?= date("Y-m-d", $coupon['use_limit_time']) ?></span>
                            </div>
                            <div class="right">
                                <a class="btn " href="javascript:;" data-amount="<?= floatval($coupon['amount']) ?>公里"
                                   data-name="<?= $coupon['name'] ?>" data-id="<?= $coupon['id'] ?>"
                                   data-sn="<?= $coupon['coupon_sn'] ?>">选择</a>
                            </div>
                        </div>
                        <div class="down">
                            <div class="card-explain">
                                <em class="icon-cloudCar2-qiaquanshuoming"></em>
                                <span>卡券说明</span>
                                <i class="icon-cloudCar2-jiantou_down"></i>
                            </div>
                            <div class="explain-content">
                                <ol class="use-instructions-ol">
                                    <?php foreach ($use_text[$coupon['coupon_type']] as $txt): ?>
                                        <li><?= $txt ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
<?php else: ?>
    <div class="uncoupons-tip-wrapper commom-input-place select-driving-popup">
        <div class="uncoupons-tip-text">您暂时还没有此类型的优惠券哦</div>
        <div class="send-comfirm uncoupons-back">
            <button type="button" class="btn-block btn-primary" id="none_driving_counpous">
                返回
            </button>
        </div>
    </div>
<?php endif; ?></div>
<div id="mapIcon">
    <div>
        <span style="display: none;">
            <i>12</i>
            <em>公里</em>
        </span>
        <i>从这里出发</i>
    </div>
    <img src="/frontend/web/cloudcar/images/mapicon.png">
</div>
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>
<script src="/frontend/web/cloudcarv2/js/my.js"></script>

<script>
    //时间选择器
    laydate.render({
        elem: '#begin-time'
        ,type: 'datetime'
    });
    var driving_type = 'normal';
    $('.reserve').on('click', function () {
        $('li.start-date').show();
        driving_type = 'reserve'
    });
    $('.normal').on('click', function () {
        $('li.start-date').hide();
        driving_type = 'normal'
    });
    window.addEventListener('pageshow', function(e) {
        $('.end-place input').val('')
        // 通过persisted属性判断是否存在 BF Cache
        /*var endAdd = 	sessionStorage.getItem('endAdd')
         if(endAdd != '' ||  endAdd != underfined){
             sD.setEndPoint(endAdd[0], endAdd[1], endAdd[2]);
              panel.setEndPlace(endAdd[0]);
        }*/

    });

    var search = new searchPlace();
    search.init('<?php echo Url::to(["search"])?>');
    search.bind($(".input-place-header>.place.start>input"), $(".address-ul.search-place-1"));
    search.bind($(".input-place-header>.place.end>input"), $(".address-ul.search-place-1"));
    search.bind($(".set-place-ul>li>.location"), $(".address-ul.search-place-2"));
    var panel = {
        setStartPlace: function (title) {
            $(".start-place>input").val(title);
        },
        setEndPlace: function (title) {
            $(".end-place>input").val(title);
        }
    };
    var driverInfo = {
        el: $(".driver-info-popup"),
        show: function (name, driveage) {
            this.el.find('span').eq(0).text(name);
            this.el.find('span').eq(1).text('驾龄：' + driveage+'年');
            this.el.show();
        },
        hide: function () {
            this.el.hide();
        }
    };
    var feePanel = {
        el: $(".paidaning-popup"),
        show: function () {
            this.el.show();
        },
        hide: function () {
            this.el.hide();
        },
        text: function (fee) {
            this.el.find('.box>.left>span>i').text('￥' + fee);
        }
    };
    var sD = new SubstituteDriving();
    var orderCommit = '<?php echo Url::to(["placeorder"])?>',
        orderPolling = '<?php echo Url::to(["polling"])?>',
        orderCostestimate = '<?php echo Url::to(["costestimate"])?>',
        nearbyDriver = "<?php echo Url::to(['getnearbydrivers']);?>",
        orderDriverPosition = '<?php echo Url::to(["position"])?>',
        orderPay = '<?php echo Url::to(["fee"])?>',
        orderReserveUrl = '<?php echo Url::to(["reserve"])?>',
        orderCancel = '<?php echo Url::to(["cancel"])?>';

    sD.init(orderCommit, orderPolling, orderCostestimate, nearbyDriver, orderDriverPosition, orderPay, orderCancel,orderReserveUrl);

    var cMap = new myMap();
    var nearByCallback = function(json){
        if (json.data.length) {
            $("#mapIcon>div>i").text('从这里出发');
            if (cMap.GetVer == cMap.DragVer) {
                cMap.driversMk(json.data);
            }
        } else {
            $("#mapIcon>div>i").text('附近暂无空闲司机');
        }
    };
    cMap.init('Bmap', 125, $("#mapIcon"), function (res, result) {
        sD.setStartPoint(res.title, res.point.lat, res.point.lng);
        sD.setLocalCity(result.addressComponents.city);
        search.setRegion(sD.city);
        panel.setStartPlace(res.title);
        sD.getNearbyDrivers(res.point.lat, res.point.lng, cMap, nearByCallback);
    });
    // web端用户采用百度定位
    <?php if(isset($this->context->webUrl) && $is_weixin === false):?>
    cMap.geolocation();
    <?php else: ?>
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo $alxg_sign['appId']; ?>', // 必填，公众号的唯一标识
        timestamp: <?php echo $alxg_sign['timestamp'] ?> , // 必填，生成签名的时间戳
        nonceStr: '<?php echo $alxg_sign['noncestr'] ?>', // 必填，生成签名的随机串
        signature: '<?php echo $alxg_sign['signature'] ?>',// 必填，签名，见附录1
        jsApiList: [
            'checkJsApi',
            'chooseWXPay',
            'checkJsApi',
            'openLocation',//使用微信内置地图查看地理位置接口
            'getLocation' //获取地理位置接口
        ]
    });
    wx.ready(function () {
        wx.getLocation({
            type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
            success: function (res) {
                console.log('云车驾到欢迎您');
                console.log(res);

                // point = {"lng":res.longitude,"lat":res.latitude};
                point = new BMap.Point(res.longitude,res.latitude)
                //坐标转换完之后的回调函数
                translateCallback = function (data){
                    if(data.status === 0) {
                        cMap.location = data.points[0];
                        cMap.geocoder(data.points[0]);
                        cMap.setCenter(data.points[0]);
                        cMap.localIcon.show();
                        cMap.move();
                    }
                }


                var convertor = new BMap.Convertor();
                var pointArr = [];
                pointArr.push(point);
                convertor.translate(pointArr, 1, 5, translateCallback)

            },
            error: function(e){
                console.log(1);

            },
            fail:function(e){
                console.log(23);

            },
            cancel:function(e){
                console.log(1);

            }
        });
    });
    <?php endif;?>
</script>
<script>
    var curentPoint = 'start';
    $('.commom-driving-ul>li.start-place>input').on('click',function(){
        $('.commom-input-place').hide();
        $('.start-place-popup').show();
        $('.input-place-header>.place').hide();
        $('.input-place-header>.start').show();
        curentPoint = 'start';
    });
    //输入目的地
    $('.commom-driving-ul>li.end-place>input').on('click', function () {
        $('.commom-input-place').hide();
        $('.start-place-popup').show();
        $('.input-place-header>.place').hide();
        $('.input-place-header>.end').show();
        curentPoint = 'end';
    });
    //选择目的地
    $(".address-ul.search-place-1").on('click', 'li', function () {
        var lng = $(this).data('lng'),
            lat = $(this).data('lat'),
            address = $(this).find(".right>span").text();
        if(curentPoint === 'start'){
            sD.setStartPoint(address, lat, lng);
            panel.setStartPlace(address);
            cMap.setCenter(new BMap.Point(lng,lat));
            sD.getNearbyDrivers(lat, lng, cMap, nearByCallback);
        }else{
            sessionStorage.setItem('endAdd',[address, lat, lng])
            sD.setEndPoint(address, lat, lng);
            panel.setEndPlace(address);
        }
        $('.commom-input-place').hide();
        $('.start-place-popup').hide();
        $('.input-place-header>.end').hide();
        $('.input-place-header>.start>input').val('');
        $('.input-place-header>.end>input').val('');
        $(".address-ul.search-place-1").html('');
    });
    $(".address-ul.search-place-2").on('click', 'li', function () {
        var lng = $(this).data('lng'),
            lat = $(this).data('lat'),
            name = $(this).find(".right>span").text(),
            address = $(this).find(".right>i").text();
        $(".address-ul.search-place-2").html('');
        var input = $(".set-place-ul>li>.location");
        input.val(name);
        input.data('addr', address);
        input.data('lat', lat);
        input.data('lng', lng);
    });
    //地址选择
    $('.address-ul>li>.right').on('click', function () {
        $('.commom-input-place').hide();
    });
    //设置地址 显示
    $('.usually-address>.same').on('click', function (e) {
        $('.commom-input-place').hide();
        var address = $(this).data('addr');
        var lat, lng, sort;
        if (address) {
            lat = $(this).data('lat');
            lng = $(this).data('lng');
            if(curentPoint === 'start'){
                sD.setStartPoint(address, lat, lng);
                panel.setStartPlace(address);
                cMap.setCenter(new BMap.Point(lng,lat));
                sD.getNearbyDrivers(lat, lng, cMap, nearByCallback);
            }else{
                sessionStorage.setItem('endAdd',[address, lat, lng])
                sD.setEndPoint(address, lat, lng);
                panel.setEndPlace(address);
            }
        } else {
            sort = $(this).data('sort');
            $(".set-place-ul>li>.location").data('sort', sort);
            $('.set-place-popup').show();
        }
    });

    //地址设置完成
    var commit_address = false;
    $('.address-comfirm').on('click', function () {
        if (commit_address) return false;
        var titleInput = $('.set-place-ul>li>.place-title');
        var title = titleInput.val();
        var locationInput = $(".set-place-ul>li>.location");
        var name = locationInput.val(),
            address = locationInput.data('addr'),
            lat = locationInput.data('lat'),
            lng = locationInput.data('lng'),
            sort = locationInput.data('sort');
        if (!title) {
            YDUI.dialog.toast('请输入地址命名', 'none', 1500);
            return false;
        }

        if (!name || !address || !lat || !lng || !sort) {
            YDUI.dialog.toast('请输入地址定位', 'none', 1500);
            return false;
        }
        var data = {title: title, name: name, address: address, lng: lng, lat: lat, sort: sort};
        commit_address = true;
        $.post('<?php echo Url::to(["editloc"])?>', data, function (json) {
            commit_address = false;
            if (json.status === 1) {
                locationInput.val('');
                locationInput.data('lat', '');
                locationInput.data('lng', '');
                locationInput.data('sort', '');
                titleInput.val('');
                var box = $(".start-place-popup>.usually-address>div[data-sort=" + sort + "]");
                box.data('addr', name);
                box.data('lat', lat);
                box.data('lng', lng);
                box.find('span').text(title);
                box.find('i').text(name);
                $('.commom-input-place').hide();
                $('.start-place-popup').show();
            } else {
                YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
    });
    //点击代驾券
    $('.commom-driving-ul>li.select-driving').on('click', function () {
        $('.commom-input-place').hide();
        $('.select-driving-popup').show();
    });

    //当没有代驾券的时候，用
    $('#none_driving_counpous').on('click', function () {
        $('.select-driving-popup').hide();
    });




    //展开收起说明
    $('.card-explain').on('click', function (e) {
        e.stopPropagation();
        var isShow = $(this).attr('data-show');
        if (isShow == 'true') {
            $(this).attr('data-show', 'fasle');
            $(this).next('.explain-content').hide(10);
            $(this).find('i').removeClass('icon-zjlt-jiantou_up').addClass('icon-zjlt-jiantou_down');
        } else {
            $(this).attr('data-show', 'true');
            $(this).next('.explain-content').show(10);
            $(this).find('i').removeClass('icon-zjlt-jiantou_down').addClass('icon-zjlt-jiantou_up');
        }
    });
    //选择代驾券
    $('.card-list-ul>li>.content .btn').on('click', function (e) {
        e.stopPropagation();
        var name = $(this).data('name'),
            amount = $(this).data('amount'),
            id = $(this).data('id'),
            sn = $(this).data('sn');
        $(".commom-driving-ul>li.select-driving>.right").text(name);
        $(".commom-driving-ul>li.select-driving>.icon").text(amount);
        sD.setCoupon(id, sn);
        $('.commom-input-place').hide();
    });


    //处理代金券，默认选择,从用户处进来选择。。
    $(function () {
        var coupon_id = $('.card-list-ul').data('selected_coupon');
        if(coupon_id){
            $('.card-list-ul>li').each(function (index ) {

                var id = $(this).find('.content .btn').data('id');
                if(id == coupon_id){
                    var name = $(this).find('.content .btn').data('name'),
                        amount = $(this).find('.content .btn').data('amount'),
                        id = $(this).find('.content .btn').data('id'),
                        sn = $(this).find('.content .btn').data('sn');
                    $(".commom-driving-ul>li.select-driving>.right").text(name);
                    $(".commom-driving-ul>li.select-driving>.icon").text(amount);
                    sD.setCoupon(id, sn);
                    $('.commom-input-place').hide();
                }
            })
        }
    });

    //处理代金券，默认选择
    $(function () {
        var coupon_id = $('.card-list-ul').data('selected_coupon');
        if(coupon_id){

        }
    })

    function driverPosition(pollingCount) {
        $.post('<?php echo Url::to(["position"])?>', {
            booking_id: sD.bookingId,
            driver_id: sD.driverId,
            order_id: sD.orderId,
            polling_count: pollingCount
        }, function (json) {
            pollingCount++;
            var next = parseInt(json.data.next);
            var state = parseInt(json.data.driver.orderStateCode);
            sD.orderState = state;
            var lng = parseFloat(json.data.driver.longitude);
            var lat = parseFloat(json.data.driver.latitude);
            var points = [{lng: lng, lat: lat}];
            cMap.driversMk(points);
            if (state === 403 || state === 404) {
                //取消订单,流程结束
                if (state === 403) {
                    // alert('订单已取消');
                    // window.location.reload();
                } else {
                    alert('司机销单');
                    window.location.reload();
                }

            } else if (state === 501) {
                getOrderFee();
                //订单完成
                alert('本次代驾服务结束');
            } else {
                setTimeout(driverPosition, next * 1000, pollingCount);
                if (state === 304) {
                    $('title').text('到达目的地');
                    //获得金额
                    getOrderFee();
                } else if (state === 301) {
                    $('title').text('司机已接单,正在赶来');
                    driverInfo.show(json.data.driver.name + '(' + sD.driverId + ')', json.data.driver.year);
                } else if (state === 302) {
                    $('title').text('司机已就位');
                    $('.paidaning-popup>.box>.btn').hide();
                } else if (state === 303) {
                    $('title').text('正在前往目的地');
                }
            }
        }, 'json');
    }

    function getOrderFee() {
        $.post('<?php echo Url::to(["fee"])?>', {order_id: sD.orderId}, function (json) {
            $(".paidaning-popup>.left>i").hide();
            $(".paidaning-popup>.left>span").html('优惠券抵扣后需支付费用：<i>￥' + json.data.cast + '</i>');
        }, 'json');
    }

    //确认订单 下一步 正在派单
    var isPaidan = false;
    $('.comfirm-order-submit').on('click', function () {
        if (isPaidan) return false;
        if (!sD.checkStartPoint()) {
            YDUI.dialog.toast('请选择出发地', 'none', 1000);
            return false;
        }
        if (!sD.checkEndPoint()) {
            YDUI.dialog.toast('请选择目的地', 'none', 1000);
            return false;
        }
        if (!sD.checkCoupon()) {
            YDUI.dialog.toast('请选择代驾券', 'none', 1000);
            return false;
        }
        var testMobile = function(m){
            var reg = /^1[0-9]{10}$/;
            return reg.test(m);
        };
        var mobile = $("#call_mobile").val();
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码', 'none', 1000);
            return false;
        }else{
            sD.mobile = mobile;
        }
        isPaidan = true;
        var costCount = 0;
        var costCallback = function(json){
            if (json.status === 1) {
                feePanel.text(json.data.fee + '(券抵扣'+json.data.deduct_money+'元)');
                sD.fee = json.data.fee;
            } else {
                if(costCount < 5){
                    costCount++;
                    sD.costestimate(costCallback);
                }
            }
        };
        //执行预估费用
        sD.costestimate(costCallback);

        //派单
        cMap.dismove();//移除监听
        $("#mapIcon").hide();
        $(".init-popup").hide();

        //根据情况进行判断。。
        if(driving_type=='normal'){
            sD.placeOrder(function (json) {
                isPaidan = false;
                if (json.status === 1) {
                    sD.bookingId = json.data.bookingId;
                    sD.bookingType = json.data.bookingType;
                    feePanel.show();
                    //拉取订单信息
                    var pollingLambda = function (json, pollingCount) {
                        if (json.status === 1) {
                            var next = parseInt(json.data.next);
                            var state = parseInt(json.data.pollingState);
                            sD.pollingState = state;
                            if (state === 2) {
                                //派单成功
                                $("title").text('正在派单');
                                sD.driverId = json.data.driverId;
                                sD.orderId = json.data.orderId;
                                driverPosition(1);

                            } else if (state === 1) {
                                //派单失败
                                YDUI.dialog.alert('派单失败', function () {
                                    window.location.reload();
                                });
                            } else {
                                var func = function (pollingLambda, pollingCount) {
                                    sD.polling(pollingLambda, pollingCount);
                                };
                                setTimeout(func, next * 1000, pollingLambda, pollingCount);
                            }
                        } else {
                            // YDUI.dialog.toast('', 'none', 1000);
                        }
                    };
                    sD.polling(pollingLambda, 1);
                } else {
                    YDUI.dialog.toast(json.msg, 'none', 1500);
                    $("title").text('代驾服务');
                    $("#mapIcon").show();
                    $('.commom-driving-popup').hide();
                    $(".init-popup").show();
                    cMap.move();
                }
            })
        }else if(driving_type=='reserve'){
            var begin = $('#begin-time').val();
            if(!begin){
                YDUI.dialog.toast('请选择预约时间', 'none', 1000);
                return false;
            }
            sD.begin = begin;
            sD.reserveOrder(function (json) {
                isPaidan = true;
                if (json.status === 1) {
                    YDUI.dialog.alert('预约成功', function () {
                        window.location.reload();
                    });

                } else {
                    YDUI.dialog.toast(json.msg, 'none', 1500);
                    $("title").text('预约成功');
                    $("#mapIcon").show();
                    $('.commom-driving-popup').hide();
                    $(".init-popup").show();
                    cMap.move();
                }
            })
        }


        // YDUI.dialog.toast('请输入出发地', 'none', 1500);
        // setTimeout(function () {
        //     $('.commom-driving-popup').hide();
        //     $('.paidaning-popup').show();
        //     $('.driver-info-popup').show();
        // }, 1500)
    });
    //取消订单
    var isCancel = false;
    $('.paidaning-popup>.box>.btn').on('click', function (e) {
        if (isCancel) return false;
        isCancel = true;
        $.post("<?php echo Url::to(['cancel']);?>", {booking_id: sD.bookingId, order_id: sD.orderId}, function (json) {
            isCancel = false;
            if (json.status === 1) {
                $('.commom-driving-popup').hide();
                $('.init-popup').show();
                sD.reset();
                cMap.move();
                $('title').text('代驾服务');
                $("#mapIcon").show();
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });
</script>

<?php $this->endBlock('script'); ?>
