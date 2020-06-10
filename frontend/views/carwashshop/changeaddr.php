<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">
<style>
    .uploadFace  input[type="file"]{
        display: block;
        width: 100%;
        height: 70px;
        position: absolute;
        z-index: 2;
        left: 0px;
        top: 0px;
        opacity: 0;
    }
    #filePicker{
        text-align: right;
        font-size: .28rem;
        padding:.3rem;
        color:#707070
    }
    .cell-select {
        -webkit-flex: 1;
        -ms-flex: 1;
        flex: 1;
        color: #535353;
        margin-left: -.08rem;
        font-size: .28rem;
    }
    .cell-arrow:after {
        margin-left: -.05rem;
        margin-right: 0.1rem;
        font-size: .28rem;
        color: #C9C9C9;
        content: '\e608';
    }
    .common-dl>dd:first-child{
        position: initial;
        display: flex;
        align-items: center;
        min-height: .93rem;
    }
    .baidulocation{
        position: fixed;
        bottom:0;
        left: 0;
        z-index: 101;
        width: 100%;
        height: 100%;
        display: flex;
        flex-flow: column;
        justify-content: center;
        align-items: center;
        background-color: rgba(0,0,0,.72);
    }
    .baidulocation>div{
        background: #fff4f4;
        width: 100%;
        height: 100%;
    }
    .search-btn{
        display: block;
        padding: 0 .2rem;
        background-color: #3873eb;
        color: #fff;
        border-radius: .1rem;
        height: .55rem;
        line-height: .55rem;
        font-size: .24rem;
    }
    .search-site{
        margin-top:10px;
        font-size: .3rem;
        color: #535353;
    }
    .search-input{
        display: flex;
        margin-top: .1rem;
    }
    .search-input>input{
        flex: 1;
        height: .55rem;
        border: 1px solid #eee;
        padding: 0 .1rem;
    }
    .close-btn{
        position: fixed;
        top: .2rem;
        right: .2rem;
        font-size: .64rem;
        z-index: 500;
    }

</style>
<?php $this->endBlock('hStyle')?>

<div class="info-header">
    <span><?php if($washShop['shop_pic']):?>
            <img id="shop_av" src="<?= $washShop['shop_pic']?>">
        <?php else:?>
            <img id="shop_av" src="/frontend/web/images/qiche.jpg">
        <?php endif;?></span>
</div>
<div class="line-wrap">
    <dl class="common-dl">
        <dd class="common-select">
            <i>地址选择</i>
            <div class="cell-item">
                <label class="cell-left cell-arrow">
                    <select class="cell-select province" name="province" onchange="getCity()">
                        <?php if($location):?>
                            <option value="<?= $location['province']['pid']?>" selected><?= $location['province']['name']?></option>
                        <?php else:?>
                            <option value="">请选择省份</option>
                        <?php endif?>
                        <?php foreach ($province as $val):?>
                            <option value="<?php echo $val['pid']?>"><?php echo $val['name']?></option>
                        <?php endforeach;?>
                    </select>
                </label>

                <label class="cell-left cell-arrow">
                    <select class="cell-select city" name="city" onchange="getArea()">
                        <?php if($location):?>
                            <option value="<?= $location['city']['pid']?>" selected><?= $location['city']['name']?></option>
                        <?php endif?>
                        <?php foreach ($location['cityList'] as $val):?>
                            <option value="<?php echo $val['pid']?>"><?php echo $val['name']?></option>
                        <?php endforeach;?>
                    </select>
                </label>
                <label class="cell-left cell-arrow">
                    <select class="cell-select area" name="area">
                        <?php if($location):?>
                            <option value="<?= $location['area']['pid']?>" selected><?= $location['area']['name']?></option>
                        <?php endif?>
                        <?php foreach ($location['areaList'] as $val):?>
                            <option value="<?php echo $val['pid']?>"><?php echo $val['name']?></option>
                        <?php endforeach;?>
                    </select>
                </label>
            </div>

        </dd>

        <dd>
            <input type="text" name="shop_address" value="<?= $washShop['shop_address']?>" placeholder="请输入详细地址（如街道、小区、乡镇、村）">
        </dd>
        <dd>
            <i class="gps-site">导航定位</i>
            <i class="location"></i>
            <input type="hidden" name="lng" value="<?= $washShop['lng']?>">
            <input type="hidden" name="lat" value="<?= $washShop['lat']?>">
            <button type="button" class="btn site-btn" onclick="getLocation()"><i class="iconfont icon-cloudCar2-dizhi"></i>定位</button>
        </dd>
    </dl>
</div>
<div class="commom-submit">
    <button type="button" class="btn-block">保存</button>
</div>
<div class="m-actionsheet" id="J_ActionSheet">
    <a href="javascript:;" class="actionsheet-item" onclick="weixinLocation()">微信定位</a>
    <a href="javascript:;" class="actionsheet-item " onclick="baiduLocation()">地图选点</a>
    <a href="javascript:;" class="actionsheet-action" id="J_Cancel">取消</a>
</div>
<div class="baidulocation" style="display: none">
    <i class="close-btn icon-error"></i>
    <div>
        <div class="map-wrapper" id="l-map">
        </div>
        <div class="place-order-fixed">
            <div class="search-site">
                <p>请输入地址定位:</p>
                <div class="search-input">
                    <input type="text" name="search_info" size="30" value="" style="width:250px;" />
                    <a class="search-btn" href="javascript:;" onclick="baiduSearch()">查询</a>
                </div>
            </div>
            <ul class="r-result">
            </ul>

        </div>
    </div>
</div>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script type="text/javascript" src="/frontend/web/webuploader/webuploader.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=<?php echo Yii::$app->params['BmapWeb'] ?> "></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
    //选择省份时，循环出城市
    function getCity()  {
        var province = $('.province option:selected').val();
        if(province.length == 0){
            return false;
        }
        //根据选择option文字数量设置select宽度，
        var width = $('.province option:selected').text().length*15.33;
        $('.province').width(width);
        $.ajax({
            url:'<?php echo Url::to(['carwashshop/getdistrict'])?>',
            data:{province:province},
            type:'post',
            dataType:'json',
            success:function(json){
                $('select.city').empty();
                $('select.area').empty();
                var html = '<option value="" >请选择市区</option>';
                eachDistrict('city',html,json.data['city'])
            }
        })
    }
    //选择城市时，循环出区域
    function getArea() {
        var province = $('.province option:selected').val();
        var city = $('.city option:selected').val();

        //根据选择option文字数量设置select宽度，
        var width = $('.city option:selected').text().length*15.33;
        $('.city').width(width);
        if(province.length == 0 || city.length ==0){
            return false;
        }
        $.ajax({
            url:'<?php echo Url::to(['carwashshop/getdistrict'])?>',
            data:{city:city},
            type:'post',
            dataType:'json',
            success:function(json){
                $('select.area').empty();
                var area = '<option value="" >请选择区域</option>';
                eachDistrict('area',area,json.data['area'])
            }
        })

    }

    //循环输出城市和地区
    function eachDistrict(district,html,data){
        $('select.'+ district +'').empty();
        $.each(data, function(index, val){
            html+='<option value="'+val['pid']+'" >'+val['name']+'</option>';
        });
        $('.'+ district +'').html(html);
    }

    //选择定位方式
    var $myAs = $('#J_ActionSheet');
    function getLocation(){
        $myAs.actionSheet('open');
    }

    $('#J_Cancel').on('click', function () {
        $myAs.actionSheet('close');
    });

    //微信定位
    function weixinLocation(){
        $myAs.actionSheet('close');
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
                    $("input[name=lng]").val(longitude);
                    $("input[name=lat]").val(latitude);
                    $('dd>.location').html(longitude.toFixed(4)+','+latitude.toFixed(4));
                },
                error: function(e){
                    console.log(1);
                    YDUI.dialog.toast('定位失败', 1500);
                },
                fail:function(e){
                    console.log(23);
                    YDUI.dialog.notify('定位失败，请开启微信定位服务', 1500);
                },
                cancel:function(e){
                    console.log(1);
                }
            });
        });
    }

    //百度地图定位
    var map = new BMap.Map("l-map");
    function baiduLocation(){
        $myAs.actionSheet('close');
        $('.baidulocation').show();
        //百度地图定位
        var geolocation = new BMap.Geolocation();
        geolocation.getCurrentPosition(function(r){
            if(this.getStatus() == BMAP_STATUS_SUCCESS){
                map.centerAndZoom(new BMap.Point(parseFloat(r.point.lng),parseFloat(r.point.lat)), 14);
            }
            else {
                YDUI.dialog.toast('没有权限，无法获取当前位置','error',1500);
                $('.baidulocation').hide();
            }

        });
    }

    //选择位置
    $('.place-order-fixed').on('click','.r-result>li',function(){
        $(this).find('i').addClass('icon-cloudCar2-radioactive');
        $(this).siblings().children('i').removeClass('icon-cloudCar2-radioactive');
        var lng = $(this).attr('data-lng');
        var lat = $(this).attr('data-lat');
        //百度经纬度转换成高德经纬度并保留8位小数
        $.get('http://api.map.baidu.com/geoconv/v1/?coords='+ lng + ',' + lat  + '&from=5&to=3&ak=<?php echo Yii::$app->params['BmapServer']?>', {}, function(data) {
            if (data.status == 0) {
                var point = data.result[0];
                $("input[name=lng]").val(point.x.toFixed(8));
                $("input[name=lat]").val(point.y.toFixed(8));
                $('dd>.location').html(point.x.toFixed(4)+','+point.y.toFixed(4));
            }
        }, 'jsonp');

        $('.baidulocation').hide();

    });

    //百度地图关闭
    $(document).on('click','.close-btn',function(){
        $('.baidulocation').hide();
    });

    //检索POI
    function baiduSearch(){
        YDUI.dialog.loading.open('查询中');
        var searchInfo = $('input[name=search_info]').val();
        var options = {
            onSearchComplete: function(results){
                // 判断状态是否正确
                YDUI.dialog.loading.close();
                if (local.getStatus() == BMAP_STATUS_SUCCESS){
                    var s = [];
                    for (var i = 0; i < results.getCurrentNumPois(); i ++){
                        var css='';
                        //设置默认选中状态
                        if(i==0){
                            css = "class = 'icon-cloudCar2-radioactive'";
                        }
                        s.push("<li data-lng='"+results.getPoi(i).point['lng']+"' data-lat='"+results.getPoi(i).point['lat']+"'><div><h2>" + results.getPoi(i).title + "</h2><p>" + results.getPoi(i).address + "</div><i "+css+"></i></li>")
                    }
                    $('.r-result').html(s.join(''));
                }
            }
        };
        var local = new BMap.LocalSearch(map, options);
        local.search(searchInfo);
    }

    var is_sub = false;
    $(".btn-block").on('click',function(){
        var province = $('.province option:selected').val();
        var city = $('.city option:selected').val();
        var area = $('.area option:selected').val();
        var shop_address = $("input[name=shop_address]").val();
        var lng = $("input[name=lng]").val();
        var lat = $("input[name=lat]").val();

        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        if(province.length ==0 || city.length==0 || area.length==0){
            YDUI.dialog.toast('请选择省市区', 1000);
            return false;
        }
        if (shop_address.length == 0 || shop_address.length > 150) {
            YDUI.dialog.toast('请输入合理的详细地址', 1000);
            return false;
        }
        if(lng.length ==0 || lat.length==0 ){
            YDUI.dialog.toast('请定位导航地址', 1000);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/changeaddr'])?>',
            data:{province:province,city:city,area:area,shop_address:shop_address,lng:lng,lat:lat,},
            type:'post',
            dataType:'json',
            timeout:6000,
            success:function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast('修改成功', 'success',1000,function () {
                        window.location.href =  '<?php echo Url::to(['carwashshop/shopdetail'])?>'
                    });
                }else {
                    YDUI.dialog.toast(json.msg,'error',1000);
                    is_sub = false;
                }
            },
            complete: function(XMLHttpRequest,status){
                if(status == 'timeout'){
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('请求超时', 'error',1000);
                    is_sub = false;
                }
            },
            error: function(XMLHttpRequest) {
                YDUI.dialog.loading.close();
                YDUI.dialog.toast('错误代码' + XMLHttpRequest.status, 1500);
                is_sub = false;
            }
        })
    })
</script>
<script type="text/javascript">

    var uploader = WebUploader.create({
        // 选完文件后，是否自动上传。
        auto: true,
        // swf文件路径
        swf: '/frontend/web/webuploader/Uploader.swf',
        // 文件接收服务端。
        server: '/frontend/web/server/fileupload.php',

        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#filePicker',

        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        }
    });
    //////////////////////
    uploader.on( 'fileQueued', function( file ) {
        // 创建缩略图
        // 如果为非图片文件，可以不用调用此方法。
        // thumbnailWidth x thumbnailHeight 为 100 x 100
        uploader.makeThumb( file, function( error, src ) {
            if ( error ) {
                $('#shop_av').replaceWith('<span>不能预览</span>');
                return;
            }

            $('#shop_av').attr( 'src', src );
        }, 70, 70 );
    });

    /////////////////////////
    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $li = $( '#'+file.id ),
            $percent = $li.find('.progress span');

        // 避免重复创建
        if ( !$percent.length ) {
            $percent = $('<p class="progress"><span></span></p>')
                .appendTo( $li )
                .find('span');
        }

        $percent.css( 'width', percentage * 100 + '%' );
    });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on( 'uploadSuccess', function( file,response ) {
        var img_url = '/static/upfile/'+response.result ;
        $('#shop_av').attr( 'src', img_url );
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {

        var $li = $( '#'+file.id ),
            $error = $li.find('div.error');

        // 避免重复创建
        if ( !$error.length ) {
            $error = $('<div class="error"></div>').appendTo( $li );
        }

        $error.text('上传失败');
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        $( '#'+file.id ).find('.progress').remove();

    });

</script>
<?php $this->endBlock('script') ?>
