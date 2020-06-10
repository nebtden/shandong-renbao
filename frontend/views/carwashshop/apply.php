<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>

    <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">
    <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/lCalendar.css" />
    <link rel="stylesheet" href="/frontend/web/cloudcarv2/icons/iconfont.css">
<style>

    .uploadFace{
        padding: 10px 15px;
        background: url("../images/yarrow.png") -webkit-calc(100% - 15px) center no-repeat;
        background-size: 7px;
        position: relative;
        z-index: 2;
    }
    .uploadFace::after{
        border-bottom: 1px solid #E5E5E5;
    }
    .uploadFace dt{
        width: 70px;
        height: 70px;
    }
    .uploadFace dd{
        -webkit-box-flex: 1;
        -moz-box-flex: 1;
        box-flex: 1;
        font-size: .28rem;
        line-height: 70px;
        text-align: right;
        padding-right: 15px;
        width: 200px;
        color: #b2b2b2;
    }
    .uploadFace dt img{
        display: block;
        line-height: 0px;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 100px;
    }
    .uploadFace dd input[type="file"]{
        display: block;
        width: 100%;
        height: 70px;
        position: absolute;
        z-index: 2;
        left: 0px;
        top: 0px;
        opacity: 0;
    }
    .boxSizing{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .webkitbox{
        display: -webkit-box;
        display: -moz-box;
        display: box;
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
    #container{width:600px;height:400px}
    .label{margin-left:20px;font-weight:bold;font-size:14px}
    .lng-lat{
        margin: 0 0 30px 0px;
    }
    .lng-lat .item{
        margin: 10px;
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
    <dl class="common-dl">
        <dt><span>门店认证</span></dt>
        <dd class="common-select">
            <dl class="uploadFace webkitbox boxSizing afterFour">
                <dt>
                            <img id="shop_av" src="/frontend/web/images/qiche.jpg">
                </dt>
                <dd>
                    <div id="filePicker">上传商户LOGO</div>
                </dd>
            </dl>
        </dd>
        <dd>
            <i>门店名称</i>
            <input type="text" name="shop_name" placeholder="请输入商户全称">
        </dd>
        <dd class="common-select">
            <i>地址选择</i>
            <div class="cell-item">
                <label class="cell-left cell-arrow">
                    <select class="cell-select province" name="province" onchange="getCity()">
                        <option value="">请选择省份</option>
                        <?php foreach ($province as $val):?>
                        <option value="<?php echo $val['pid']?>"><?php echo $val['name']?></option>
                        <?php endforeach;?>
                    </select>
                </label>

                <label class="cell-left cell-arrow" onchange="getArea()">
                    <select class="cell-select city" name="city">
                    </select>
                </label>
                <label class="cell-left cell-arrow">
                    <select class="cell-select area" name="area">
                    </select>
                </label>
            </div>

        </dd>

        <dd>
            <input type="text" name="shop_address" placeholder="请输入详细地址（如街道、小区、乡镇、村）">
        </dd>
        <dd>
            <i class="gps-site">门店导航定位</i>
            <i class="location"></i>
            <input type="hidden" name="lng">
            <input type="hidden" name="lat">
            <button type="button" class="btn site-btn" onclick="getLocation()"><i class="iconfont icon-cloudCar2-dizhi"></i>定位</button>
        </dd>
        <dd class="social-code">
            <i>统一社会信用代码</i>
            <input type="text" name="shop_credit_code" placeholder="请输入统一社会信用代码">
        </dd>
        <dd class="common-select">
            <i>注册时间</i>
            <!--<span>请选择</span>-->
            <input type="text" name="shop_register_time" readonly="" id="regdate" placeholder="请选择">
            <em class="icon-cloudCar2-jiantou"></em>
        </dd>
        <dd>
            <i>联系电话</i>
            <input type="tel" name="shop_tel" placeholder="请输入店铺联系号码">
        </dd>
        <dd>
            <i>营业开始时间</i>
            <input type="text" name="start_time" placeholder="请输入营业开始时间，如：9:00">
        </dd>
        <dd>
            <i>营业结束时间</i>
            <input type="text" name="end_time" placeholder="请输入营业结束时间，如：18:00">
        </dd>
        <dd>
            <i>手机号</i>
            <input type="tel" name="mobile" class="telephone" placeholder="请输入手机号码">
        </dd>
        <dd>
            <i>验证码</i>
            <input type="tel" placeholder="请输入您收到的验证码" name="code">
            <button type="button" class="btn send-code" id="J_GetCode">发送验证码</button>
        </dd>
    </dl>
    <p class="tips">手机号涉及到提现等业务，请准确填写，并确保能收到验证码</p>
    <dl class="common-dl">
        <dt><span>银行账户</span></dt>
        <dd>
            <i>账户名称</i>
            <input type="text" name="bank_payee_name" placeholder="请输入开户人名称">
        </dd>
        <dd>
            <i>银行账号</i>
            <input type="text" name="bank_payee_account" placeholder="请输入银行账号">
        </dd>
        <dd>
            <i>开户行</i>
            <input type="text" name="payee_bank" placeholder="请输入开户行名称">
        </dd>
    </dl>
    <dl class="common-dl">
        <dt><span>支付宝账户</span></dt>
        <dd>
            <i>支付宝账号</i>
            <input type="text" name="aipay_payee_account" placeholder="请输入支付宝账号">
        </dd>
        <dd>
            <i>真实姓名</i>
            <input type="text" name="aipay_payee_name" placeholder="请输入支付宝实名认证姓名">
        </dd>
    </dl>
    <p class="tips">支付宝账户和对公账户涉及到提现业务，必需填写一个，请准确填写</p>
    <div class="commom-submit">
        <button type="button" class="btn-block" >申请认证</button>
    </div>
    <div class="commom-tabar-height"></div>
<?php $this->beginBlock('script') ?>
    <script src="/frontend/web/js/my.js"></script>
    <script src="/frontend/web/js/lCalendar.js"></script>
    <script src="/frontend/web/cloudcarv2/js/my.js"></script>
    <script type="text/javascript" src="/frontend/web/webuploader/webuploader.js"></script>
<!--    <script type="text/javascript" src="http://api.map.baidu.com/api?v=3.0&ak=--><?php //echo Yii::$app->params['BmapWeb'] ?><!-- "></script>-->
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script>
        //	初始化日期控件
        var calendar = new lCalendar();
        var _date = new Date();
        calendar.init({
            'trigger': '#regdate',
            'type': 'date',
            'minDate':'2000-1-1',
            'maxDate':(new Date().getFullYear()) + '-' + (new Date().getMonth()+1) + '-' + (new Date().getDate())
        });

        /**
         * 发送验证码
         * 定义参数
         */
        var $getCode = $('#J_GetCode');
        $getCode.sendCode({
            disClass: 'btn-disabled ',
            secs: 59,
            run: false,
            runStr: '重新发送{%s}',
            resetStr: '重新获取'
        });
        var testMobile = function(m){
            var reg = /^1[0-9]{10}$/;
            return reg.test(m);
        };
        //发送验证码
        $getCode.on('click', function () {
            /* ajax 成功发送验证码后调用【start】 */
            var mobile = $("input[name=mobile]").val();
            if(!testMobile(mobile)){
                YDUI.dialog.toast('请输入正确的手机号码','none',1500);
                return false;
            }
            YDUI.dialog.loading.open('发送中');
            $.post("<?php echo Url::to(['carwashshop/smscode']);?>",{mobile:mobile},function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    $getCode.sendCode('start');
                    YDUI.dialog.toast('已发送', 'none',1500);
                }else {
                    YDUI.dialog.alert(json.msg);
                }
            },'json');
        });

        //选择省份时，循环出城市
        function getCity() {
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

        //提交认证
        var is_sub =false;
        $('.btn-block').on('click',function () {
            if (is_sub) {
                YDUI.dialog.toast('数据提交中请稍后', 'none', 1500);
                return false;
            }
            var shop_pic = $("#shop_av").attr('src');
            var shop_name = $("input[name=shop_name]").val();
            var province = $('.province option:selected').val();
            var city = $('.city option:selected').val();
            var area = $('.area option:selected').val();
            var shop_address = $("input[name=shop_address]").val();
            var lng = $("input[name=lng]").val();
            var lat = $("input[name=lat]").val();
            var shop_credit_code = $("input[name=shop_credit_code]").val();
            var shop_register_time = $("input[name=shop_register_time]").val();
            var shop_tel = $("input[name=shop_tel]").val();
            var start_time = $("input[name=start_time]").val();
            var end_time = $("input[name=end_time]").val();
            var mobile = $("input[name=mobile]").val();
            var code = $("input[name=code]").val();
            var bank_payee_name = $("input[name=bank_payee_name]").val();
            var bank_payee_account = $("input[name=bank_payee_account]").val();
            var payee_bank = $("input[name=payee_bank]").val();
            var aipay_payee_account = $("input[name=aipay_payee_account]").val();
            var aipay_payee_name = $("input[name=aipay_payee_name]").val();

            if (shop_pic.length == 0) {
                YDUI.dialog.toast('请上传门店图片', 1000);
                return false;
            }
            if (shop_name.length == 0 || shop_name.length > 50) {
                YDUI.dialog.toast('请输入合理门店名称', 1000);
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
            if (shop_credit_code.length != 18) {
                YDUI.dialog.toast('请输入合理的统一社会信用代码', 1000);
                return false;
            }
            if (shop_register_time.length == 0) {
                YDUI.dialog.toast('请选择注册时间', 1000);
                return false;
            }
            if (shop_tel.length == 0 || shop_tel.length > 12) {
                YDUI.dialog.toast('请输入正确的联系电话', 1000);
                return false;
            }
            if (start_time.length == 0 || start_time.length > 20) {
                YDUI.dialog.toast('请输入合理的营业开始时间', 1000);
                return false;
            }
            if (end_time.length == 0 || end_time.length > 20) {
                YDUI.dialog.toast('请输入合理的营业结束时间', 1000);
                return false;
            }
            if (!/^1[0-9]\d{9}$/.test(mobile)) {
                YDUI.dialog.toast('请输入正确的手机号码', 1000);
                return false;
            }
            if (code.length == 0 || code.length > 6) {
                YDUI.dialog.toast('请输入合理的验证码', 1000);
                return false;
            }
            if(bank_payee_account.length !=0 && !/^([1-9]{1})(\d{15}|\d{18})$/.test(bank_payee_account)){
                YDUI.dialog.toast('请输入合理的银行账户', 1000);
                return false;
            }
            if (/^0?(13[0-9]|15[012356789]|18[0123456789]|14[0123456789]|17[0123456789])[0-9]{8}$/.test(aipay_payee_account)){
            } else if(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(aipay_payee_account)){
            } else {
                YDUI.dialog.toast('请输入正确的支付宝账号', 1000);
                return false;
            }
            is_sub = true;
            YDUI.dialog.loading.open('洗车门店加载中');
            $.post('<?php echo Url::to(['carwashshop/apply'])?>', {
                shop_pic: shop_pic,
                shop_name: shop_name,
                province:province,
                city:city,
                area:area,
                lng:lng,
                lat:lat,
                shop_address: shop_address,
                shop_credit_code: shop_credit_code,
                shop_register_time: shop_register_time,
                shop_tel: shop_tel,
                start_time: start_time,
                end_time: end_time,
                mobile: mobile,
                code: code,
                bank_payee_name: bank_payee_name,
                bank_payee_account: bank_payee_account,
                payee_bank: payee_bank,
                aipay_payee_account: aipay_payee_account,
                aipay_payee_name: aipay_payee_name,
            }, function (json) {
                YDUI.dialog.loading.close();
                if (json.status == 1) {
                    YDUI.dialog.toast('申请提交成功','success',1000,function(){
                        window.location.href = "<?php echo Url::to(['carwashshop/index']);?>";
                    })
                } else {
                    is_sub = false;
                    YDUI.dialog.toast(json.msg, 1000);
                }

            }, 'json')
        });

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
<script>
    var map = new BMap.Map("l-map");
    var lng;
    var lat;

    //百度地图定位
    function baiduLocation(){
        $myAs.actionSheet('close');
        $('.baidulocation').show();
        //百度地图定位
        var geolocation = new BMap.Geolocation();
        geolocation.getCurrentPosition(function(r){
            if(this.getStatus() == BMAP_STATUS_SUCCESS){
                lng = r.point.lng;
                lat = r.point.lat;
            }
            else {
                YDUI.dialog.toast('没有权限，无法获取当前位置','error',1500);
                $('.baidulocation').hide();
            }
            map.centerAndZoom(new BMap.Point(parseFloat(lng),parseFloat(lat)), 14);
            console.log(lng+','+lat);
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

</script>

<?php $this->endBlock('script') ?>