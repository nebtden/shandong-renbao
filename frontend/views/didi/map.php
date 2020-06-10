<?php

use yii\helpers\Url;

?>

<?php $this->beginBlock('hScript'); ?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="https://webapi.amap.com/maps?v=1.4.10&key=a8ea95ef63658e29e619bf72899f12ab&plugin=AMap.Autocomplete"></script>
<script src="//webapi.amap.com/ui/1.0/main.js"></script>

<?php $this->endBlock('hScript'); ?>
<?php $this->beginBlock('hStyle'); ?>

<link rel="stylesheet" href="https://cache.amap.com/lbs/static/main1119.css"/>


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

</style>
<?php $this->endBlock('hStyle'); ?>
<div id="container" style="top:-40%;height:140%;"></div>

<div class="bottom-place-order-fixed" >
    <div class="place-order-sle">
        <p  ><a href="<?= Url::to(['carecarnew/list']) ?>" style="color:#3873eb;font-size: 0.3rem">代驾行程</a> </p>
    </div>

    <div class="commom-driving-popup init-popup place-order-fixed item-dailyLife" style="position: relative;bottom: 0px;padding-bottom: .6rem;">
        <ul class="commom-driving-ul">
            <li class="start-place">
                <input id='startAddress' type="text" placeholder="输入出发地" readonly>
            </li>
            <li class="end-place">
                <input type="text" id='endAddress' placeholder="输入目的地" readonly>
            </li>
            <li class="select-driving">
                <i>选择代驾券</i>
                <div class="right">
                    <span>请选择</span>
                    <i></i>
                    <em class="icon-cloudCar2-jiantou"></em>
                </div>
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
        <div class="place end ">
            <i class="icon-cloudCar2-sousuo"></i>
            <input type="text" id='tipinput' placeholder="请输入代驾目的地">
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
                <i>设置常用地址2</i>
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
                <i>设置常用地址2</i>
            </div>
        <?php endif; ?>
    </div>
    <div class="address-list">
        <ul id='address-ul' class="address-ul search-place-1">
            <li id='seacchResult'></li>
        </ul>
    </div>
</div>
<div id='msg' style='width:50%;height:200px;position:absolute;display:none;background:#fff;left:0;top:0;z-index:9999'></div>
<input type='hidden' name='lon'>
<input type='hidden' name ='lat'>

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

<script>

    //输入目的地
    $('.commom-driving-ul>li.end-place>input').on('click',function(){
        $('.commom-input-place').hide();
        $('.start-place-popup').show();
        $('.input-place-header>.place').hide();
        $('.input-place-header>.end').show();
    });
    //地址选择
    $('.address-ul>li>.right').on('click',function(){
        $('.commom-input-place').hide();
    });
    //设置地址 显示
    $('.usually-address>.same').on('click',function(e){
        $('.commom-input-place').hide();
        $('.set-place-popup').show();
    });
    //地址设置完成
    $('.address-comfirm').on('click',function(){
        YDUI.dialog.toast('请输入地址','none',1500);
        setTimeout(function(){
            $('.commom-input-place').hide();
            $('.start-place-popup').show();
        },1500);
    });


    //高德地图

    var map,marker;
    var geocoder;
    var placeSearch,autocomplete;
    var startEle = document.getElementById('startAddress');
    var endEle = document.getElementById('endAddress');
    var tempEle = startEle;
    var tempBoolean = true;

    //初始化地图
    map = new AMap.Map('container', {
        resizeEnable: true, //是否监控地图容器尺寸变化
        zoom:11, //初始化地图层级
        center: [28.19652, 112.977361] //初始化地图中心点
    });

    AMap.service('AMap.Geocoder',function(){//回调函数
        //实例化Geocoder
        geocoder = new AMap.Geocoder({
            city: "长沙"//城市，默认：“全国”
        });
        //TODO: 使用geocoder 对象完成相关功能
    });


    //初始定位

    map.plugin('AMap.Geolocation', function () {
        geolocation = new AMap.Geolocation({

            enableHighAccuracy: true,//是否使用高精度定位，默认:true
            timeout: 10000,          //超过10秒后停止定位，默认：无穷大
            maximumAge: 0,           //定位结果缓存0毫秒，默认：0
            convert: true,           //自动偏移坐标，偏移后的坐标为高德坐标，默认：true
            showButton: false,        //显示定位按钮，默认：true
            buttonPosition: 'LB',    //定位按钮停靠位置，默认：'LB'，左下角
            buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
            showMarker: true,        //定位成功后在定位到的位置显示点标记，默认：true
            showCircle: true,        //定位成功后用圆圈表示定位精度范围，默认：true
            panToLocation: true,     //定位成功后将定位到的位置作为地图中心点，默认：true
            zoomToAccuracy:true      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false

        });

        map.addControl(geolocation);
        geolocation.getCurrentPosition();
        AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
        AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息

    });

    //定位成功
    function onComplete(e){
        startEle.value = e.formattedAddress;
        addMarker([e.position.getLng(), e.position.getLat()])
    }
    //定位失败

    function onError(){
        console.log('定位失败')
        addMarker([112.59 , 28.12])
        map.setCenter([112.59 , 28.12])
        writeAddress([112.59 , 28.12],tempEle);

    }

    //为地图注册click事件获取鼠标点击出的经纬度坐标
    map.on('click', function(e) {
        map.setCenter([e.lnglat.lng,e.lnglat.lat])
        isWhoAddress()
        writeAddress([e.lnglat.lng,e.lnglat.lat],tempEle);
        addMarker([e.lnglat.lng,e.lnglat.lat]);
    });
    //为地图注册mapmove事件获取鼠标点击出的经纬度坐标
    map.on('mapmove',function(e){
        isWhoAddress()
        marker.setPosition(map.getCenter());
        writeAddress([map.getCenter().lng,map.getCenter().lat],tempEle);
    })

    // 实例化点标记
    function addMarker(lnglatXY) {
        map.clearMap();
        marker = new AMap.Marker({
            icon: '/frontend/web/cloudcar/images/mapicon.png',
            position: lnglatXY
        });
        marker.setMap(map);
        if(tempBoolean){
            marker.setLabel({
                //修改label相对于maker的位置
                offset: new AMap.Pixel(20, 20),
                content: "<div class='info'>从这里出发</div>"
            })
        }else{
            marker.setLabel({
                //修改label相对于maker的位置
                offset: new AMap.Pixel(20, 20),
                content: "<div class='info'>到这里去</div>"
            })
        }
    }

    //搜索功能
    AMap.plugin(['AMap.Autocomplete','AMap.PlaceSearch'],function(){//回调函数
        autocomplete = new AMap.Autocomplete({
            city: "长沙", //城市，默认全国
            input:"tipinput"//使用联想输入的input的id
        });
        placeSearch = new AMap.PlaceSearch({
            pageSize: 5,//每页显示多少行
            pageIndex: 1,//显示的下标从那个开始
            map: map,
            //panel:'seacchResult'
        });

        AMap.event.addListener(autocomplete, "select", function(e){
            console.log(e)
            tempBoolean = false ;
            placeSearch.setCity(e.poi.adcode);
            console.log(e.poi)
            placeSearch.search(e.poi.name); //关键字查询查询
            $('.commom-input-place').hide();
            endEle.value = e.poi.address;

            addMarker(e.poi.location)

        });
    });

    // 填写地址
    function writeAddress(lnglatXY,ele){
        geocoder.getAddress(lnglatXY, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                geocoder_CallBack(result,ele);
            }
        });
    }
    // 地址回调
    function geocoder_CallBack(data,ele) {
        var address = data.regeocode.formattedAddress; //返回地址描述
        ele.value = address;
    }


    //出发地or目的地
    function isWhoAddress(){
        tempBoolean ? tempEle = startEle : tempEle = endEle
    }
</script>


<?php $this->endBlock('script');?>

