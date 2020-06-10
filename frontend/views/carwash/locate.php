<?php

use yii\helpers\Url;

?>
<?php $this->beginBlock('hScript'); ?>
<script type="text/javascript"
        src="http://api.map.baidu.com/api?v=3.0&ak=<?php echo 'Rwh2q8UoSllxKMNekOTrRefBddWpG21s';//Yii::$app->params['BmapWeb']; ?>"></script>
<?php $this->endBlock('hScript'); ?>
<?php $this->beginBlock('hStyle'); ?>


<?php $this->endBlock('hStyle'); ?>
<div class="map-wrapper commom-img" id="Bmap">

</div>


<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
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
        wx.openLocation({
            latitude: <?php echo $shop['shopLat']?>, // 纬度，浮点数，范围为90 ~ -90
            longitude: <?php echo $shop['shopLng']?>, // 经度，浮点数，范围为180 ~ -180。
            name: '<?php echo $shop['shopName']?>', // 位置名
            address: '<?php echo $shop['shopAddress']?>', // 地址详情说明
            scale: 23, // 地图缩放级别,整形值,范围从1~28。默认为最大
            infoUrl: 'www.yunche168.com' // 在查看位置界面底部显示的超链接,可点击跳转
        });
        wx.getLocation({
            type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
            success: function (res) {
                console.log(1);
                var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                var longitude = res.longitude ; // 经度，浮点数，范围为180 ~ -180。
                var speed = res.speed; // 速度，以米/每秒计
                var accuracy = res.accuracy; // 位置精度


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
</script>


<?php $this->endBlock('script'); ?>
