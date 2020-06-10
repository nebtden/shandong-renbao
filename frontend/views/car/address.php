
<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        body, html,#allmap {width: 100%;height: 100%;overflow: hidden;margin:0;font-family:"微软雅黑";}
    </style>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=4GDQSBMXINbpSt1zr1n85HNX"></script>
    <title>地址解析</title>
    <link rel="stylesheet" href="/frontend/web/css/shopcommon.css" type="text/css">
    <link rel="stylesheet" href="/frontend/web/css/css.css" type="text/css">
    <link rel="stylesheet" href="/frontend/web/css/lCalendar.css" type="text/css">
</head>
<body>
<div id="allmap"></div>
<footer class="footMenu webkitbox boxSizing">
    <ul class="right webkitbox">
        <li>
            <a href="<?php echo Url::to(['car/shop_list']);?>">服务点</a>
        </li>
        <li>
            <a href="<?php echo Url::to(['car/recovery']);?>">兑换</a>
        </li>
        <li>
            <a href="<?php echo Url::to(['car/shop_core']);?>">商户中心</a>
        </li>
    </ul>
</footer>
</body>
</html>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("allmap");
    var point = new BMap.Point(28.235193 ,112.931375);
    map.centerAndZoom(point,12);
    // 创建地址解析器实例
    var myGeo = new BMap.Geocoder();

    // 将地址解析结果显示在地图上,并调整地图视野
    myGeo.getPoint("<?php echo $address['address']?>", function(point){
        var marker = new BMap.Marker(point);  // 创建标注
        map.addOverlay(marker);
        var label = new BMap.Label("<?php echo $address['shop_name']?>",{offset:new BMap.Size(20,-10)});
        if (point) {
            map.centerAndZoom(point, 16);
            map.addOverlay(new BMap.Marker(point));
            marker.setLabel(label);
        }else{
            alert("您选择地址没有解析到结果!");
        }
    }, "北京市");

//    var start = "湖南省长沙市天心区黑石铺";
//    var end = "<?php //echo $address['address']?>//";
//    //三种驾车策略：最少时间，最短距离，避开高速
//    var routePolicy = [BMAP_DRIVING_POLICY_LEAST_TIME,BMAP_DRIVING_POLICY_LEAST_DISTANCE,BMAP_DRIVING_POLICY_AVOID_HIGHWAYS];
//    function luxian(){
//        map.clearOverlays();
//        var i=0;
//        search(start,end,routePolicy[i]);
//        function search(start,end,route){
//            var driving = new BMap.DrivingRoute(map, {renderOptions:{map: map, autoViewport: true},policy: route});
//            driving.search(start,end);
//        }
//    };
//    luxian();
</script>