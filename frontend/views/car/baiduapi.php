<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/15 0015
 * Time: 上午 9:47
 */
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>地址解析</title>
    <link rel="stylesheet" href="/frontend/web/css/shopcommon.css" type="text/css">
    <link rel="stylesheet" href="/frontend/web/css/css.css" type="text/css">
    <link rel="stylesheet" href="/frontend/web/css/lCalendar.css" type="text/css">
    <style type="text/css">
        *{
            margin:0px;
            padding:0px;
        }
        body, button, input, select, textarea {
            font: 12px/16px Verdana, Helvetica, Arial, sans-serif;
        }
        p{
            width:603px;
            padding-top:3px;
            margin-top:10px;
            overflow:hidden;
        }
        input#address{
            width:300px;
        }
        #container {
            min-width:603px;
            min-height:767px;
        }
    </style>
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=7OOBZ-WTZWK-GTFJX-ABI6E-LB7JF-5RBNK"></script>
    <script src="/frontend/web/js/jquery-1.10.1.js"></script>
    <script>
        var geocoder,map,marker = null;
        var init = function() {
            var center = new qq.maps.LatLng(28.235193 ,112.931375);
            map = new qq.maps.Map(document.getElementById('container'),{
                center: center,
                zoom: 15
            });
            //调用地址解析类
            geocoder = new qq.maps.Geocoder({
                complete : function(result){
                    map.setCenter(result.detail.location);
                    var marker = new qq.maps.Marker({
                        map:map,
                        position: result.detail.location
                    });
                }
            });

            codeAddress();
        }
        function codeAddress() {
            var address = '<?php echo $address;?>';
            //通过getLocation();方法获取位置信息值
            geocoder.getLocation(address);
        }

    </script>
</head>
<body onload="init()">
<div id="container"></div>
<footer class="footMenu webkitbox boxSizing">
    <ul class="right webkitbox">
        <li>
            <a href="<?php echo Url::to(['car/shop_list']);?>">回收点</a>
        </li>
        <li>
            <a href="<?php echo Url::to(['car/recovery']);?>">回收</a>
        </li>
        <li>
            <a href="<?php echo Url::to(['car/shop_core']);?>">商户中心</a>
        </li>
    </ul>
</footer>
</body>
</html>
