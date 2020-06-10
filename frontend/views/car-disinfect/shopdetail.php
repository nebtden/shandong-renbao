<?php
use yii\helpers\Url;
?>

<div class="shop-details-header commom-img" >
    <img src="<?= $shopDetail['images'] ?>" style="max-height: 421px;">
</div>
<div class="shop-info">
    <div class="title"><?= $shopDetail['shopName'] ?></div>
    <div class="time"><?= $shopDetail['serviceStartTime'] ?> - <?= $shopDetail['serviceEndTime'] ?> </div>
    <div class="icon-score">

        <?php for($i=1; $i<=5; $i++): ?>
           <?php if($shopDetail['score'] >= $i): ?>
               <i class="icon-cloudCar2-star-shi"></i>
           <?php else: ?>
               <i class="icon-cloudCar2-star-empty "></i>
           <?php endif;?>
        <?php endfor; ?>
        <span><?= $shopDetail['score'] ?>分</span>
    </div>
</div>
<div class="shop-address">
    <i class="icon-cloudCar2-dizhi"></i>
    <span><?= $shopDetail['shopAddress'] ?></span>
    <a href="javascript:;" onclick="locate()">
        导航<em class="icon-cloudCar2-jiantou"></em>
    </a>
</div>
<div class="wash-car-liucheng">
    <div class="title"><i class="icon-cloudCar2-liucheng"></i><span>本臭氧杀菌服务流程</span></div>
    <div class="commom-img liuchengtu"><img src="/frontend/web/images/dis-car-liuchengtu.png" ></div>
</div>
<div class="commom-submit service-code-submit">
    <button type="button" class="btn-block">出示服务码</button>
</div>
<div class="commom-tabar-height"></div>
<?php if(isset($this->context->webUrl)):?>
    <div class="m-actionsheet" id="J_ActionSheet">
        <a href="#" onclick="Navigate('bd')" class="actionsheet-item">百度地图</a>
        <a href="#" onclick="Navigate('gd')" class="actionsheet-item">高德地图</a>
        <a href="javascript:;" class="actionsheet-action" id="J_Cancel">取消</a>
    </div>
<?php endif;?>
<?php if($footer == 'hidden'){?>
    <?php $this->beginBlock('footer'); ?>
    <?php $this->endBlock('footer'); ?>
<?php }?>
<?php $this->beginBlock('script');?>
<!--<script src="/frontend/web/js/jquery-2.1.4.js"></script>-->
<script src="/frontend/web/cloudcarv2/js/qrcode.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=3.0&ak=<?php echo Yii::$app->params['BmapWeb'] ?> "></script>
<script src="/frontend/web/cloudcarv2/js/my.js"></script>
<script>
    //初始化sessionStorage拓展方法
    var sessions = new Sstorage();
    //将门店详情存入sessionStorage
    var shopDetail = {};
    shopDetail.shopName = '<?= $shopDetail['shopName'] ?>';
    shopDetail.shopLat = '<?= $shopDetail['shopLat'] ?>';
    shopDetail.shopLng = '<?= $shopDetail['shopLng'] ?>';
    shopDetail.shopAddress = '<?= $shopDetail['shopAddress'] ?>';
    sessions.set('shop',shopDetail);
    //出示服务码弹框显示
    $('.commom-submit>.btn-block').on('click',function(){
        var couponId = '<?php echo $couponId; ?>';
        var shopId = '<?php echo $shopDetail['shopId']; ?>';
        YDUI.dialog.loading.open('服务码获取中');
        $.ajax({
            url: '<?php echo Url::to(['car-disinfect/shengdagetcode'])?>',
            data: {couponId:couponId, shopId:shopId},
            type: 'POST',
            dataType: 'json',
            timeout:60000,
            success: function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                        showServiceCode(json.data);
                }else{
                    YDUI.dialog.toast(json.msg,'error', 1500);
                }
            },
            complete: function(XMLHttpRequest,status){
                if(status == 'timeout'){
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('请求错误'+status, 'error',1500 );
                }
            }
        });
    });
    //关闭弹窗
    $('body').on("click",'.commom-popup>.title>i',function(){
        $('.big-popup-outside').hide();
    });

    function showServiceCode(data){
        var html = '';
        html += '<div class="commom-popup-outside  big-popup-outside"  >'+
            '<div class="commom-popup">'+
            '<div class="title"><?= $coupon['name'] ?><i class="icon-error"></i></div>'+
            '<div class="content">'+
            '<div class="up">'+
            '<div id="qr-code" style="width:150px; height:150px; margin-top:15px; margin:0 auto;"></div>'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>';
        $('body').prepend(html);
        var qrcode = new QRCode(document.getElementById('qr-code'), {
            width : 150,
            height : 150
        });
        function makeCode (text) {
            qrcode.makeCode(text);
        }
        makeCode(data.consumerCode);

    }

    <?php if(isset($this->context->webUrl)):?>
    var shopInof='';
    //web端地图导航，弹出选择地图下拉菜单。
    function locate(){
        var $as = $('#J_ActionSheet');
        $as.actionSheet('open');
        $('#J_Cancel').on('click', function () {
            $as.actionSheet('close');
        });

        this.shopInof = sessions.get('shop');
    }

    //导航
    function Navigate(style) {
        var shop = this.shopInof;
        if (style == 'bd') {
            $.get('http://api.map.baidu.com/geoconv/v1/?coords='+ shop.shopLat + ',' + shop.shopLng  + '&from=5&to=3&ak=<?php echo Yii::$app->params['BmapServer']?>', {}, function(data) {
                if (data.status == 0) {
                    var point = data.result[0];
                    console.log(point);
                    window.location.href = 'https://api.map.baidu.com/direction?origin=latlng:' + sessions.get('navLat') + ',' + sessions.get('navLng')  + '|name:我的位置&destination=latlng:' + point.x + ',' + point.y   + '|name:'+shop.shopName+'&mode=driving&origin_region=' + sessions.get('navLat') + ',' + sessions.get('navLng')  + '&destination_region='+shop.city+'&output=html&coord_type=bd09ll&src=myAppName';
                }
            }, 'jsonp');

        } else if (style == 'gd') {
            //转换百度坐标为高德坐标 调高德导航
            $.get('http://api.map.baidu.com/geoconv/v1/?coords='+ sessions.get('navLat') + ',' + sessions.get('navLng')  + '&from=5&to=3&ak=<?php echo Yii::$app->params['BmapServer']?>', {}, function(data) {
                if (data.status == 0) {
                    var point = data.result[0];
                    console.log(point);
                    window.location.href = 'https://uri.amap.com/navigation?from=' + point.y + ',' + point.x + ',我的位置&to=' + + shop.shopLng + ',' + shop.shopLat + ','+ shop.shopName+'&mode=car&policy=0&coordinate=gaode&callnative=1';
                }
            }, 'jsonp');
        }
    }
    <?php else:?>

    function locate() {
        var shopInfo = sessions.get('shop');
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
                latitude: parseFloat(shopInfo.shopLat), // 纬度，浮点数，范围为90 ~ -90
                longitude: parseFloat(shopInfo.shopLng), // 经度，浮点数，范围为180 ~ -180。
                name: shopInfo.shopName, // 位置名
                address: shopInfo.shopAddress, // 地址详情说明
                scale: 23, // 地图缩放级别,整形值,范围从1~28。默认为最大
                infoUrl: 'www.yunche168.com' // 在查看位置界面底部显示的超链接,可点击跳转
            });
            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    console.log(1);
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度


                },
                error: function (e) {
                    console.log(1);

                },
                fail: function (e) {
                    console.log(23);

                },
                cancel: function (e) {
                    console.log(1);

                }
            });
        });
    }
    <?php endif?>
</script>
<?php $this->endBlock('script');?>
