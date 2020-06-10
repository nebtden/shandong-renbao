<?php

use yii\helpers\Url;

?>
<?php $this->beginBlock('hScript'); ?>
<script type="text/javascript"
        src="http://api.map.baidu.com/api?v=3.0&ak=<?php echo 'Rwh2q8UoSllxKMNekOTrRefBddWpG21s';//Yii::$app->params['BmapWeb']; ?>"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
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


    .send-comfirm>button{
        background-color: #3873EB;
        height:.90rem;
        line-height: .90rem;
        border-radius: .45rem;

    }

</style>
<?php $this->endBlock('hStyle'); ?>

<div class="map-wrapper commom-img" id="Bmap">

</div>
<!-- 司机信息 -->
<?php if($info['driveryear']):?>
<div class="commom-driving-popup driver-info-popup">
    <i class="icon-cloudCar2-siji"></i>
    <span><?php echo $info["drivername"]?></span>
    <i class="icon-cloudCar2-jialing"></i>
    <span><?php echo $info["driveryear"]?></span>
</div>
<?php  endif;?>

<!-- 代驾 正在派单 已接单 已完成 -->
<div class="commom-driving-popup paidaning-popup"  >
    <div class="box">
        <div class="left">
            <span>优惠券抵扣后预估费用：<i>￥0.00</i></span>
            <i>具体费用请以司机实际驾驶为准</i>
        </div>
        <button type="button" class="btn">取消订单</button>
    </div>
    <div class="notice">
        司机接单后，如需取消订单，请拨打<a href="tel:400-810-3939">400-810-3939</a>
    </div>

</div>



<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>

<script src="/frontend/web/cloudcarv2/js/my.js"></script>
<script>
    var cMap = new myMap();
    cMap.init('Bmap', 125, $("#mapIcon"), function (res, result) {
        sD.setStartPoint(res.title, res.point.lat, res.point.lng);
        sD.setLocalCity(result.addressComponents.city);
        // search.setRegion(sD.city);
        // panel.setStartPlace(res.title);
        // sD.getNearbyDrivers(res.point.lat, res.point.lng, cMap, nearByCallback);
    });
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

    $(function () {
        var data = {
            startlat: '<?php echo $info["start_lat"]?>',
            startlng: '<?php echo $info["start_lng"]?>',
            endlat: '<?php echo $info["end_lat"]?>',
            endlng: '<?php echo $info["end_lng"]?>',
            bonus_sn: '<?php echo $info["coupon_sn"]?>'
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

        $.post('<?php echo Url::to(["carecarnew/costestimate"])?>', data, function (json) {
            $(".driver-info-popup").show();
            if (json.status === 1) {
                feePanel.text(json.data.fee + '(券抵扣'+json.data.deduct_money+'元)');

            } else {
                if(costCount < 5){
                    costCount++;
                    sD.costestimate(costCallback);
                }
            }
        }, 'json');
    });



    function getOrderFee() {
        $.post('<?php echo Url::to(["fee"])?>', {order_id: sD.orderId}, function (json) {
            $(".paidaning-popup>.left>i").hide();
            $(".paidaning-popup>.left>span").html('优惠券抵扣后需支付费用：<i>￥' + json.data.cast + '</i>');
        }, 'json');
    }


    //取消订单
    var isCancel = false;
    $('.paidaning-popup>.box>.btn').on('click', function (e) {
        if (isCancel) return false;
        isCancel = true;
        $.post("<?php echo Url::to(['carecarnew/cancel']);?>", {booking_id: "<?php echo $info['booking_id']; ?>", order_id: "<?php echo $info['order_id']; ?>",orderid: "<?php echo $info['orderid']; ?>"}, function (json) {
            isCancel = false;
            if (json.status === 1) {
                YDUI.dialog.alert("取消成功");
                window.location.reload();
            } else {

                YDUI.dialog.alert("取消失败,原因为:"+json.msg);

            }
        }, 'json');
    });
</script>

<?php $this->endBlock('script'); ?>
