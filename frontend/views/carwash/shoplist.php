<?php

use yii\helpers\Url;

?>

<div class="m-gridstitle">查询您所在的省市的服务网点</div>
<div class="m-grids-3">
        <a href="#" class="grids-item province">
            <div class="grids-txt"><span data-code="">请选择省</span></div>
        </a>
        <a href="#" class="grids-item city-name" >
            <div class="grids-txt"><span data-code="">请选择市</span></div>
        </a>
        <a href="#" class="grids-item area-name">
            <div class="grids-txt"><span data-code="">请选择区域</span></div>
        </a>
</div>
<br>

<div class="m-actionsheet ActionProvince"  style="height: 60%">
    <div id="getpro" style="overflow-y: scroll ;height: 85%;">
        <?php foreach($province as $val): ?>
            <a href="#" class="actionsheet-item" data-pid="<?= $val['id']?>" data-name="<?= $val['name']?>" data-code="<?= $val['code']?>"><?php echo $val['name'] ?></a>
        <?php endforeach;?>
    </div>
    <a href="javascript:;" class="actionsheet-action" >取消</a>
</div>

    <div class="m-actionsheet ActionCity"  style="height: 60%">
        <div class="city-list" style="overflow-y: scroll ;height: 85%;">

        </div>
        <a href="javascript:;" class="actionsheet-action" >取消</a>
    </div>

    <div class="m-actionsheet ActionArea"  style="height: 60%;">
        <div class="area-list" style="overflow-y: scroll ;height: 85%;">

        </div>

        <a href="javascript:;" class="actionsheet-action" >取消</a>
    </div>
    <div class="shop-list" >
        <ul class="shop-ul" id="shop-list">

        </ul>
    </div>

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
<?php if(isset($this->context->webUrl) && $is_weixin === false):?>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=3.0&ak=<?php echo Yii::$app->params['BmapWeb'] ?> "></script>
<?php else:?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<?php endif;?>
<script src="/frontend/web/cloudcarv2/js/my.js"></script>
<script type="text/javascript">

    //初始化sessionStorage拓展方法
    var sessions = new Sstorage();

    <?php if(isset($this->context->webUrl) && $is_weixin === false):?>
        var shopInof='';
        //是否有门店缓存

            var geolocation = new BMap.Geolocation();
            geolocation.getCurrentPosition(function(r){
                if(this.getStatus() == BMAP_STATUS_SUCCESS){
                    sessions.set('navLat',r.point.lat);
                    sessions.set('navLng',r.point.lng);
                    getshop(r.point.lng,r.point.lat);
                }
                else {
                    YDUI.dialog.toast('没有权限，无法获取当前位置','error',1500);
                }
            });


        //web端地图导航，弹出选择地图下拉菜单。
        function locate(name,address,lng,lat,city){
            var $as = $('#J_ActionSheet');
            $as.actionSheet('open');
            $('#J_Cancel').on('click', function () {
                $as.actionSheet('close');
            });
            this.shop = {'name':name,'address':address,'lng':lng,'lat':lat,'city':city};
        }

        //导航
        function Navigate(style) {
            var shop = this.shop;
            if (style == 'bd') {
                $.get('http://api.map.baidu.com/geoconv/v1/?coords='+ shop.lat + ',' + shop.lng  + '&from=5&to=3&ak=<?php echo Yii::$app->params['BmapServer']?>', {}, function(data) {
                    if (data.status == 0) {
                        var point = data.result[0];
                        window.location.href = 'https://api.map.baidu.com/direction?origin=latlng:' + sessions.get('navLat') + ',' + sessions.get('navLng')  + '|name:我的位置&destination=latlng:' + point.x + ',' + point.y   + '|name:'+shop.name+'&mode=driving&origin_region=' + sessions.get('navLat') + ',' + sessions.get('navLng')  + '&destination_region='+shop.city+'&output=html&coord_type=bd09ll&src=myAppName';
                    }
                }, 'jsonp');

            } else if (style == 'gd') {
                //转换百度坐标为高德坐标 调高德导航
                $.get('http://api.map.baidu.com/geoconv/v1/?coords='+ sessions.get('navLat') + ',' + sessions.get('navLng')  + '&from=5&to=3&ak=<?php echo Yii::$app->params['BmapServer']?>', {}, function(data) {
                    if (data.status == 0) {
                        var point = data.result[0];
                        window.location.href = 'https://uri.amap.com/navigation?from=' + point.y + ',' + point.x + ',我的位置&to=' +  shop.lng + ',' + shop.lat + ','+ shop.name+'&mode=car&policy=0&coordinate=gaode&callnative=1';
                    }
                }, 'jsonp');
            }
        }
    <?php else:?>
    //是否有门店缓存



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
                type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                     // alert('云车驾到欢迎您');
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude ; // 经度，浮点数，范围为180 ~ -180。
                    getshop(longitude,latitude);
                },
                error: function(e){
                    console.log(1);
                    YDUI.dialog.notify('定位失败，请开启微信定位服务', 3000, function(){
                        console.log('定位失败，请开启微信定位服务');
                    });

                },
                fail:function(e){
                    console.log(23);
                    YDUI.dialog.notify('定位失败，请开启微信定位服务', 3000, function(){
                        console.log('定位失败，请开启微信定位服务');
                    });

                },
                cancel:function(e){
                    console.log(1);

                }
            });
        });


        //门店导航
        function locate(name,address,lng,lat,city=''){
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
                    latitude: parseFloat(lat), // 纬度，浮点数，范围为90 ~ -90
                    longitude: parseFloat(lng), // 经度，浮点数，范围为180 ~ -180。
                    name: name, // 位置名
                    address: address, // 地址详情说明
                    scale: 21, // 地图缩放级别,整形值,范围从1~28。默认为最大
                    infoUrl: 'www.yunche168.com' // 在查看位置界面底部显示的超链接,可点击跳转
                });
            });
        }
        <?php endif;?>


    //按照经纬度查询门店
    function getshop(lng,lat){
          // var lng = 120.19947900;
           //var lng = 116.327805;
        //   var lat = 30.22914400;//
          // var lat = 39.901209;
        
        var company = '<?= $company;?>';
        YDUI.dialog.loading.open('洗车门店加载中');
        $.ajax({
            url: '<?php echo Url::to(['carwash/getshoplist']) ?>',
            data: {lng:lng,lat:lat,company:company},
            type: 'POST',
            dataType: 'json',
            timeout:6000,
            success: function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    getShopList(json.data['shopList']);
                    var location = json.data['location'];
                    if(location){
                        eachcity('city-list',location['cityList']);
                        eachcity('area-list',location['areaList']);
                        setdata('province',location['province']['name'],location['province']['code'],location['province']['pid']);
                        setdata('city-name',location['city']['name'],location['city']['code'],location['province']['pid']);
                        setdata('area-name',location['area']['name'],location['area']['code'],location['province']['pid']);
                    }
                } else {
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast(json.msg,'error',1500);
                }

            },
            complete: function(XMLHttpRequest,status){
                if(status == 'timeout'){
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('请求超时', 'error',1500);
                }
            },
            error: function(XMLHttpRequest) {
                YDUI.dialog.loading.close();
                YDUI.dialog.toast('错误状态' + XMLHttpRequest.status,'error', 1500);
            }
        })
    }

    //按照地区查询门店列表
    function getareashop(pid,cid,aid){
        var province = pid;
        var city = cid;
        var area = aid;
        var company = '<?= $company;?>';
        YDUI.dialog.loading.open('洗车门店加载中');
        $.ajax({
            url: '<?php echo Url::to(['carwash/getareashoplist']) ?>',
            data: {province:province,city:city,area:area,company:company},
            type: 'POST',
            dataType: 'json',
            timeout:6000,
            success: function(json){

                $("#shop-list").empty();
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    getShopList(json.data['shopList']);
                } else {
                    YDUI.dialog.toast(json.msg, 'error',1500);
                }
            },
            complete: function(XMLHttpRequest,status){
                if(status == 'timeout'){
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('请求超时', 'error',1500);
                }
            },
            error: function(XMLHttpRequest) {
                YDUI.dialog.loading.close();
                YDUI.dialog.toast('错误状态' + XMLHttpRequest.status,'error', 1500);
            }

        })
    }

    //循环输出门店
    function getShopList(data){
        var html = '';
        $("#shop-list").html(' ');
        $.each(data, function(key, val){
            html += '<li>';
            if(val['shopAvator']){
                html += '<div class="left commom-img"><img src="'+val['shopAvator'] +'"></div>';
            }else{
                html += '<div class="left commom-img"><img src="/frontend/web/cloudcarv2/images/wash-car.png"></div>';
            }
            html += '<div class="middle"><span>'+ val['shopName'] +'</span> <i> '+ val['shopAddress'] +' </i> </div>';
            html += '<div class="right"><a href="<?php echo Url::to(['carwash/shopdetail', 'couponId' => $couponId,'company'=>$company ]) ?>&shopId='+ val['shopId'] +'" >洗车</a>' +
                '<p style="padding-top:7px;text-align:center;color:#808080">';
            if(!val['distance']){
                html += ' ';
            } else if(val['distance']>100){
                html+='>100';
            } else {
                html+=val['distance'].toFixed(2);
            }
            html += 'km</p>'+
            '<span onclick="locate(`'+val['shopName']+'`,`'+val['shopAddress']+'`,`'+val['shopLng']+'`,`'+val['shopLat']+'`,`'+val['city']+'`)" style="cursor:pointer;">导航 <i class="icon-cloudCar2-jiantou"></i></span></div>'+
            '</li>';
        });

        $("#shop-list").html(html);
    }


    $('.province').on('click', function () {
        $('.ActionProvince').actionSheet('open');
        $("#shop-list").html('');
    });
    $('.city-name').on('click', function () {
        $('.ActionCity').actionSheet('open');
    });
    $('.area-name').on('click', function () {
        $('.ActionArea').actionSheet('open');
    });

    $('.actionsheet-action').on('click', function (){
        $(this).parent(".m-actionsheet").actionSheet('close');
    });

    $('.ActionProvince .actionsheet-item').on('click', function () {
        var pid = $(this).attr('data-pid');
        var name = $(this).attr('data-name');
        var code = $(this).attr('data-code');

        setdata('province',name,code,pid);
        setdata('area-name','请选择区域');
        $('.ActionProvince').actionSheet('close');
        getCity(pid);

    });

    $('.ActionCity').on('click','.actionsheet-item', function () {
        var pid = $(this).attr('data-pid');
        var name = $(this).attr('data-name');
        var code = $(this).attr('data-code');

        setdata('city-name',name,code,pid);
        setdata('area-name','请选择区域');
        $('.ActionCity').actionSheet('close');
        getArea(pid);
    });

    $('.ActionArea').on('click','.actionsheet-item', function () {
        var name = $(this).attr('data-name');
        var code = $(this).attr('data-code');
        var pid = $(this).attr('data-pid');
        setdata('area-name',name,code,pid);

        var province_id = $('.province span').attr('data-code');
        var city_id = $('.city-name span').attr('data-code');
        var area_id = $('.area-name span').attr('data-code');

        $('.ActionArea').actionSheet('close');
        getareashop(province_id,city_id,area_id);
    });
    //设置省市区的值
    function setdata(area,name=null,code=null,pid=null)
    {
        $('.'+ area +' span').attr('data-code',code);
        $('.'+ area +' span').attr('data-name',name);
        $('.'+ area +' span').attr('data-pid',pid);
        $('.'+ area +' span').html(name);
    }

    //获取市级
    function getCity(pid){

        $.ajax({
            url:'<?php echo Url::to(['carwash/city']) ?>',
            type:'POST',
            dataType: 'json',
            data:{pid:pid},

            success: function (data) {
                console.log(data);
                $(".city-name span").html(data['city'][0]['name']);
                $(".city-name span").attr('data-code',data['city'][0]['code']);
                $(".city-name span").attr('data-name',data['city'][0]['name']);
                $(".city-name span").attr('data-pid',data['city'][0]['id']);
                eachcity('city-list',data['city']);
                eachcity('area-list',data['area']);
            }
        })
    }

    //获取地区级
    function getArea(pid){
        $.ajax({
            url:'<?php echo Url::to(['carwash/area']) ?>',
            type:'POST',
            dataType: 'json',
            data:{pid:pid},
            success: function (data) {
                eachcity('area-list',data);
            }
        })
    }

    //循环输出城市和地区
    function eachcity(city,data){
        var html = '';
        $('.'+ city +'').empty();
        $.each(data, function(index, val){
            html+='<a href="#" class="actionsheet-item" data-code="'+ val['code'] +'" data-pid="'+ val['id'] +'" data-name="'+ val['name']+'">' +
             val['name'] +'</a>';
        });
        $('.'+ city +'').html(html);
    }

</script>

<?php $this->endBlock('script');?>