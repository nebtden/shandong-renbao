<?php

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
        display: none;
        text-align: center;
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
        padding: .05rem .2rem .05rem .05rem;
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
        margin-left: .1rem;
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
        src="http://api.map.baidu.com/api?v=3.0&ak=<?php echo Yii::$app->params['BmapWeb']; ?>"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<div class="map-wrapper" id="Bmap" style="width: 100%; height: 100%;"></div>
<div class="place-order-fixed">
    <ul class="place-order-ul">
        <li class="service">
            <a href="javascript:;" data-ydui-actionsheet="{target:'#actionSheet',closeElement:'#cancel'}">
                <div class="div-left"><span class="dot-wrapper"><i class="dot"></i></span><i>服务项目</i></div>
                <div class="div-right"><span>请选择</span><i class="iconfont icon-car-jiantou"></i></div>
            </a>
            <input type="hidden" name="rescue_way" value="">
        </li>
        <li class="car">
            <a href="javascript:;" data-ydui-actionsheet="{target:'#actionCar',closeElement:'#cancel'}">
                <div class="div-left"><span class="dot-wrapper"><i class="dot"></i></span><i>故障车辆</i></div>
                <div class="div-right"><span>请选择</span><i class="iconfont icon-car-jiantou"></i></div>
            </a>
            <input type="hidden" name="car_id" value="">
        </li>
        <li class="rescue">
            <span><i></i></span>
            <input class="drive-end" type="text" name="fault_address" placeholder="请输入事故发生地" >
            <input type="hidden" name="lng">
            <input type="hidden" name="lat">
        </li>
        <li class="cardVolume">
            <a href="javascript:;">
                <div class="div-left"><img src="/frontend/web/cloudcar/images/myCardVolume.png"><i>服务券</i></div>
                <div class="div-right"><span>请选择</span><i class="iconfont icon-car-jiantou"></i></div>
            </a>
            <input type="hidden" name="coupon_id" value="">
        </li>
        <li class="beizhu">
            <i class="iconfont icon-car-dingdan1"></i><textarea maxlength="50" name="remark"
                                                                placeholder="请输入备注信息"></textarea>
        </li>
    </ul>
    <div class="send-comfirm oneKey-placeOrder">
        <button type="button" class="btn-block btn-primary">一键下单</button>
    </div>
    <!--    <div class="rescue-tip">注：如果救援过程中产生过路费、过桥费、停车费，需用户承担</div>-->
</div>
<!-- iframe -->
<iframe id="iframe" src="" frameborder="0" scrolling="auto"></iframe>
<!-- 派单中 -->
<div class="poupon-paidan">
    <div class="cancel-btn-wrapper">
        <button type="button" class="btn-block btn-hollow" id="cancel">取消订单</button>
    </div>
</div>
<!-- 已接到订单 -->
<div class="jiedaned-fixed">
    <div class="up">
        <img src="<?= $user['headimgurl'] ?>" onerror="this.src='/frontend/web/cloudcar/images/dhlogo.png'">
        <div class="up-middle">
            <span><?= $user['nickname'] ?></span>
            <i id="car_no"></i>
            <i id="car_model"></i>
        </div>
        <a class="up-down" href="javascript:;">
            <span id="order_sta">未受理</span>
        </a>
    </div>
    <div class="down">
        <span id="service_item"></span>
        <span id="remark"></span>
    </div>
</div>
<!-- 上拉菜单 选取救援服务项目 -->
<div class="m-actionsheet" id="actionSheet">
    <ul class="actionsheet-service-ul">
        <?php foreach ($rescueItems as $key => $item): ?>
            <li data-sid="<?= $key ?>"><?= $item ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<!-- 上拉菜单 选择车辆 -->
<div class="m-actionsheet" id="actionCar">
    <ul class="actionsheet-service-ul">
        <?php foreach ($carlist as $item): ?>
            <li data-cid="<?= $item['id'] ?>" data-brand="<?= $item['car_model_small_fullname'] ?>"><?php echo $item['card_province'] . $item['card_char'] . $item['card_no']; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<div id="mapIcon">
    <div style="display: none;">
        <span>
            <i>12</i>
            <em>分钟</em>
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
    var order_no = null;
    var order_sta = 0;//订单状态
    var sync_order_timer = null;
    //如果要使用此项服务需先绑定车牌信息，没有的话不允许使用
    var setRescueStart = function (title, point) {
        $("input[name=lng]").val(point.lng);
        $("input[name=lat]").val(point.lat);
        $(".drive-end").val(title);
    };
    var backFunc = function (res, result) {
        var r = result.addressComponents;
        setRescueStart(r.district + r.street + r.streetNumber, result.point);
    };
    <?php if(!$carlist):?>
    YDUI.dialog.alert('此项服务需要先完善您的车辆信息', function () {
        window.location.href = "<?php echo Url::to(['caruser/carlist']);?>"
    });
    <?php elseif(isset($this->context->webUrl)):?>
    var cMap = new myMap();
    cMap.init('Bmap', 125, $("#mapIcon"), backFunc);
    cMap.geolocation();

    <?php else:?>
    var cMap = new myMap();
    cMap.init('Bmap', 125, $("#mapIcon"), backFunc);
    // cMap.geolocation();
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
    //救援服务项目
    $('#actionSheet>.actionsheet-service-ul>li').on('touchstart', function () {
        var $rescueTxt = $('.place-order-ul>li.service>a>.div-right>span')
        $rescueTxt.text($(this).text());
        $("input[name=rescue_way]").val($(this).data('sid'));
        $("#service_item").text("服务项目："+$(this).text());
        if (!$rescueTxt.hasClass('volume-color')) {
            $rescueTxt.addClass('volume-color');
        }
        $('#actionSheet').on('click', function () {
            $(this).actionSheet('close');
        });
    });
    //故障车辆
    $('#actionCar>.actionsheet-service-ul>li').on('touchstart', function () {
        var $rescueTxt = $('.place-order-ul>li.car>a>.div-right>span')
        $rescueTxt.text($(this).text());
        $("input[name=car_id]").val($(this).data('cid'));
        $("#car_no").text($(this).text());
        $("#car_model").text($(this).data('brand'));
        if (!$rescueTxt.hasClass('volume-color')) {
            $rescueTxt.addClass('volume-color');
        }
        $('#actionCar').on('click', function () {
            $(this).actionSheet('close');
        });
    });
    //显示代金券页面
    $('.place-order-ul>li.cardVolume>a').on('touchstart', function (e) {
        e.preventDefault();
        //判断是否选择了服务项目
        var scene = $("input[name=rescue_way]").val();
        if (!scene.length) {
            YDUI.dialog.toast('请选择服务项目', 1000);
            return false;
        }
        var url = '<?php echo Url::to(['rescuecp'])?>' + '?scene=' + scene;
        $('#iframe').attr('src', url);
        $('#iframe').addClass('show');
    });

    //根据订单状态做出相应的反应
    var sync_back = function (sta, points) {
        //指示救援人位置
        if (points.length) {
            cMap.driversMk(points);
        }
        //根据状态处理
        sta = parseInt(sta);
        switch (sta) {
            case 0:
                break;
            case 1:
                //待受理
                if (order_sta !== 1) {
                    order_sta = 1;
                }
                break;
            case 2:
                //已受理
                if(order_sta !== 2){
                    $('.poupon-paidan').hide();
                    $('.jiedaned-fixed').show();
                    $("#order_sta").text('已受理');
                    order_sta = 2;
                }
                break;
            case 3:
                //已调派
                if(order_sta !== 3){
                    $('.poupon-paidan').hide();
                    $('.jiedaned-fixed').show();
                    $("#order_sta").text('已派车');
                    order_sta = 3;
                }
                break;
            case 4:
                //已回拨
                if(order_sta !== 4){
                    $('.poupon-paidan').hide();
                    $('.jiedaned-fixed').show();
                    $("#order_sta").text('已回拨');
                    order_sta = 4;
                }
                break;
            case 5:
                //已到达
                if(order_sta !== 5){
                    $('.poupon-paidan').hide();
                    $('.jiedaned-fixed').show();
                    $("#order_sta").text('已到达');
                    order_sta = 5;
                }
                break;
            case 6:
                //已完成
                if(order_sta !== 6){
                    $('.poupon-paidan').hide();
                    $('.jiedaned-fixed').show();
                    $("#order_sta").text('已完成');
                    window.clearInterval(sync_order_timer);
                    order_sta = 6;
                }
                break;
            default:
                //状态7，8，9，订单取消
                order_sta = sta;
                break;
        }
    };

    //同步订单状态及位置
    var sync_order = function () {
        sync_order_timer = setInterval(function () {
            $.post('<?php echo Url::to(["syncorder"])?>', {order_no: order_no}, function (json) {
                console.log(json.data.status);
                sync_back(json.data.status, json.data.lbs);
            }, 'json');
        }, 5000);
    };


    //下单，移除移动监听
    var isClickOrder = false;//是否点击下单

    //下救援单参数
    var paidan = function () {
        //服务项目
        var rescue_way = $("input[name=rescue_way]").val();
        if (!rescue_way.length) {
            YDUI.dialog.toast('请选择服务项目', 1000);
            return false;
        }
        //故障车辆
        var car_id = $("input[name=car_id]").val();
        if (!car_id.length) {
            YDUI.dialog.toast('请选择故障车辆', 1000);
            return false;
        }
        //事故发生地
        var address = $(".drive-end").val();
        var lng = $('input[name=lng]').val();
        var lat = $('input[name=lat]').val();
        //优惠券
        var coupon_id = $('input[name=coupon_id]').val();
        if (!coupon_id.length) {
            YDUI.dialog.toast('请选择服务券', 1000);
            return false;
        }
        //备注
        var remark = $("textarea[name=remark]").val();
        $("#remark").text(remark);
        return {
            rescue_way: rescue_way,
            car_id: car_id,
            fault_address: address,
            longitude: lng,
            latitude: lat,
            coupon: coupon_id,
            remark: remark
        };
    };

    $('.oneKey-placeOrder').on('touchstart', function () {
        if (isClickOrder) return false;
        //获得相关参数
        var data = paidan();
        if (!data) {
            return false;
        }
        isClickOrder = true;
        //移除地图移动监听
        cMap.dismove();
        $('.place-order-fixed').hide();
        $.post("<?php echo Url::to(['paidan'])?>", data, function (json) {
            isClickOrder = false;
            if (json.status === 1) {
                order_no = json.data.orderno;
                order_Id = json.data.m_id;
                // $("#mapIcon").hide();//定位图标隐藏
                // sync_order();
                // $('.poupon-paidan').show();
                window.location.href= "<?php echo Url::to(['caruorder/rescue']);?>"+'?id='+order_Id;
            } else {
                YDUI.dialog.alert(json.msg);
                cMap.move();
                $('.place-order-fixed').show();
            }
        }, 'json');
    });
    //取消订单
    var isCancelOrder = false;
    $('.cancel-btn-wrapper').on('touchstart', function () {
        if (isCancelOrder) return false;
        isCancelOrder = true;
        $.post("<?php echo Url::to(['cancel'])?>", {order_no: order_no}, function (json) {
            isCancelOrder = false;
            if (json.status === 1) {
                order_no = null;
                order_sta = 0;
                //重新监听地图
                cMap.move();
                cMap.removeMk(cMap.driversMarkers);
                $("#mapIcon").show();//定位图标隐藏
                //取消定时任务
                window.clearInterval(sync_order_timer);
                $('.poupon-paidan').hide();
                $('.place-order-fixed').show();
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
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
