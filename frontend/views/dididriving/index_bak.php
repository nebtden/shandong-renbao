<?php

use Yii;
use yii\helpers\Url;

?>
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
</style>
<script type="text/javascript"
        src="http://api.map.baidu.com/api?v=3.0&ak=<?php echo 'Rwh2q8UoSllxKMNekOTrRefBddWpG21s';//Yii::$app->params['BmapWeb']; ?>"></script>
<div class="map-wrapper" id="Bmap"></div>
<div class="place-order-fixed">
    <ul class="place-order-ul">
        <li>
            <span><i></i></span>
            <input class="drive-start" type="text" placeholder="请输入出发地" readonly>
        </li>
        <li>
            <span><i></i></span>
            <input class="drive-end" type="text" placeholder="请输入目的地" readonly>
            <input type="hidden" name="end_lng" value="">
            <input type="hidden" name="end_lat" value="">
        </li>
        <li class="cardVolume">
            <a href="javascript:;">
                <div class="div-left"><img src="/frontend/web/cloudcar/images/myCardVolume.png"><i>优惠券抵扣</i></div>
                <div class="div-right"><span>请选择</span><i class="iconfont icon-car-jiantou"></i></div>
                <input type="hidden" name="coupon_id" value="">
                <input type="hidden" name="coupon_sn" value="">
            </a>
        </li>
    </ul>
    <div class="send-comfirm oneKey-placeOrder">
        <button type="button" class="btn-block btn-primary">一键下单</button>
    </div>
</div>
<!-- iframe -->
<iframe id="iframe" frameborder="0" scrolling="auto"></iframe>
<!-- 派单中 -->
<div class="poupon-paidan">
    <div class="cancel-btn-wrapper">
        <button type="button" class="btn-block btn-hollow" id="cancel">取消订单</button>
    </div>
</div>
<!-- 已接到订单 -->
<div class="jiedaned-fixed">
    <div class="up">
        <img src="./images/dhlogo.png">
        <div class="up-middle">
            <span>鼎师傅</span>
            <i>湘A 3A063</i>
            <i>丰田卡罗拉-白色</i>
        </div>
        <a class="up-down" href="javascript:;">
            <img src="./images/callPhone.png">
        </a>
    </div>
    <div class="down">
        <span>出发地：麓谷企业广场B4动鼎翰大厦</span>
        <img src="./images/bilateralArrow.png">
        <span>目的地：重点软件园二期</span>
    </div>
</div>
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
<script src="/frontend/web/cloudcar/js/my.js"></script>
<script>
    Lstorage().remove('driveStart');
    Lstorage().remove('curCity');
    var cMap, mapFunc, getNearbyDrivers, setDriveEnd, setDriveStart, setCurCity;
    setDriveStart = function (res) {
        Lstorage().set('driveStart', res);
        $(".drive-start").val(res.title);
    };
    //设置当前的省市
    setCurCity = function (res) {
        var component = res.addressComponents;
        Lstorage().set('curCity', component);
    };
    //获取附近的司机
    getNearbyDrivers = function (point) {
        var dragVer = cMap.DragVer;
        var func = function () {
            $.post("<?php echo Url::to(['getnearbydrivers']);?>", point, function (json) {
                cMap.GetVer++;
                if (json.data.length) {
                    $("#mapIcon>div>i").text('从这里出发');
                    if (cMap.GetVer == cMap.DragVer) {
                        cMap.driversMk(json.data);
                    }
                } else {
                    $("#mapIcon>div>i").text('附近暂无空闲司机');
                }
            }, 'json');
        };
        setTimeout(function (d) {
            //减少与后台的交互，在1秒内没有拖动位置才去后台获取附近司机的位置
            if (d == cMap.DragVer) {
                func();
            }
        }, 1000, dragVer);
    };

    cMap = new myMap();
    //回调函数
    mapFunc = function (res, result) {
        //将出发地存储起来
        setDriveStart(res);
        setCurCity(result);
        fireDestination();
        getNearbyDrivers(res.point);
    };
    cMap.init('Bmap', 125, $("#mapIcon"), mapFunc);
    cMap.geolocation();

    function getDriveStart() {
        var start = Lstorage().get('driveStart');
        if (start) return start;
        return false;
    }

    function getDriveEnd() {
        var title = $(".drive-end").val();
        if (!title.length) return false;
        var lng = $("input[name=end_lng]").val();
        var lat = $("input[name=end_lat]").val();
        return {title: title, point: {lng: lng, lat: lat}};
    }

    //设置完目的地后触发
    function fireDestination() {
        var pend = getDriveEnd();
        if (!pend) return false;
        var e_lng = pend.point.lng;
        var e_lat = pend.point.lat;
        var endPoint = new BMap.Point(e_lng, e_lat);
        var start = getDriveStart();
        var s_lng = start.point.lng;
        var s_lat = start.point.lat;
        var startPoint = new BMap.Point(s_lng, s_lat);
        var options = {
            onSearchComplete: function (result) {
                if (driving.getStatus() == BMAP_STATUS_SUCCESS) {
                    //驾车方案，直接选取第一条，因为是代驾，所以选择驾车的方案，当然，返回的也只有一条方案
                    var plan = result.getPlan(0);
                    //获取方案距离
                    var dis = plan.getDistance(false);
                    dis = (parseFloat(dis) / 1000).toFixed(0);
                    $("#mapIcon>div>span>i").text(dis);
                    $("#mapIcon>div>span").show();
                } else {
                    $("#mapIcon>div>span").hide();
                }
            }
        };
        var driving = new BMap.DrivingRoute(cMap.map, options);
        driving.search(startPoint, endPoint);
    }

    //下单
    var bookingId, bookingType, orderId, driverId;

    function place_an_order() {
        var startP = getDriveStart();
        if (!startP) {
            YDUI.dialog.toast('请选择出发地', 'none', 1000);
            return false;
        }
        var endP = getDriveEnd();
        if (!endP) {
            YDUI.dialog.toast('请输入目的地', 'none', 1000);
            return false;
        }
        var coupon_id = $('input[name=coupon_id]').val();
        if (!coupon_id.length) {
            YDUI.dialog.toast('请选择优惠券', 'none', 1000);
            return false;
        }
        var coupon_sn = $("input[name=coupon_sn]").val();
        if (!coupon_sn) coupon_sn = '';
        //Costestimate(startP.point.lat, startP.point.lng, endP.point.lat, endP.point.lng, coupon_sn);
        var data = {
            address: startP.title,
            lng: startP.point.lng,
            lat: startP.point.lat,
            bonus_sn: coupon_sn,
            daddress: endP.title,
            dlng: endP.point.lng,
            dlat: endP.point.lat
        };
        $.post('<?php echo Url::to(["placeorder"])?>', data, function (json) {
            bookingId = json.data.bookingId;
            bookingType = json.data.bookingType;
            polling(1);
        }, 'json');
    }

    //预估费用
    function Costestimate(startlat, startlng, endlat, endlng, bonus_sn) {
        var data = {
            startlat: startlat, startlng: startlng, endlat: endlat, endlng: endlng, bonus_sn: bonus_sn
        };
        $.post('<?php echo Url::to(["costestimate"])?>', data, function (json) {
            console.log(json);
        }, 'json');
    }

    //拉取订单信息
    function polling(pollingCount) {
        $.post('<?php echo Url::to(["polling"])?>', {
            booking_id: bookingId,
            booking_type: bookingType,
            polling_count: pollingCount
        }, function (json) {
            pollingCount++;
            var next = parseInt(json.data.next);
            var state = parseInt(json.data.pollingState);
            if (state === 2) {
                //派单成功
                driverId = json.data.driverId;
                orderId = json.data.orderId;
                driverPosition(1);
            } else if (state === 1) {
                //派单失败
            } else {
                setTimeout(polling, next * 1000, pollingCount);
            }
        }, 'json');
    }

    //获得订单的司机位置
    function driverPosition(pollingCount) {
        $.post('<?php echo Url::to(["position"])?>', {
            booking_id: bookingId,
            driver_id: driverId,
            order_id: orderId,
            polling_count: pollingCount
        }, function (json) {
            pollingCount++;
            var next = parseInt(json.data.next);
            var state = parseInt(json.data.driver.orderStateCode);
            var lng = parseFloat(json.data.driver.longitude);
            var lat = parseFloat(json.data.driver.latitude);
            var points = [{lng: lng, lat: lat}];
            cMap.driversMk(points);
            if (state === 403 || state === 404) {
                //取消订单,流程结束
                alert(state);
            } else if (state === 501) {
                //订单完成
                alert('订单结束');
            } else {
                setTimeout(driverPosition, next * 1000, pollingCount);
                if (state === 304) {
                    //获得金额
                    getOrderFee();
                }
            }
        }, 'json');
    }

    function getOrderFee() {
        $.post('<?php echo Url::to(["fee"])?>', {order_id: orderId}, function (json) {
            console.log(json);
        }, 'json');
    }

</script>
<script>
    //显示代金券页面
    $('.place-order-ul>li.cardVolume>a').on('click', function (e) {
        e.preventDefault();
        var url = "<?php echo Url::to(['coupon'])?>";
        $('#iframe').attr('src', url);
        $('#iframe').addClass('show');
    });
    // 显示目的地页面
    $('.place-order-ul>li>input.drive-end').on('click', function (e) {
        e.preventDefault();
        var url = '<?php echo Url::to(['destination'])?>';
        var city = Lstorage().get('curCity');
        city = city.city;
        url += '?region=' + city;
        $('#iframe').attr('src', url);
        $('#iframe').addClass('show');
    });
    //一键下单
    $('.oneKey-placeOrder').on('click', function () {
        place_an_order();
        // $('.place-order-fixed').hide();
        // $('.poupon-paidan').show();
        // setTimeout(function () {
        //     $('.poupon-paidan').hide();
        //     $('.jiedaned-fixed').show();
        // }, 1500);
    });
    //取消订单
    $('.cancel-btn-wrapper').on('click', function () {
        $('.poupon-paidan').hide();
        $('.place-order-fixed').show();
    });
    //解决输入框input获取焦点得时，虚拟键盘会把fixed元素顶上去
    $(function () {
        var winHeight = window.innerHeight; //获取初始可视窗口高度
        window.addEventListener('resize', function () { //监测窗口大小的变化事件
            var currHeight = window.innerHeight; //当前可视窗口高度
            if (winHeight > currHeight) { //可以作为虚拟键盘弹出事件
                $('body').css('position', 'fixed');
            } else { //可以作为虚拟键盘关闭事件
                $('body').css('position', 'static');
            }
            winHeight = currHeight;
        });
    });
</script>
<?php $this->endBlock('script'); ?>
