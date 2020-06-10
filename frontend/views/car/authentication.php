<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/6 0006
 * Time: 下午 3:30
 */

use yii\helpers\Url;
?>

<section class="contentFull bgColor overFlow secPadd" style="height: 100%;overflow: hidden">
    <div class="TranslateDiv webkitbox">
        <div class="zhengwenDiv">
            <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot">
                <div class="renzhengTopTitle afterFour">
                    商户认证
                </div>
                <dl class="uploadFace webkitbox boxSizing afterFour">
                    <dt>
                        <?php if ($shopinfo['shop_pic']){?>
                            <img id="shop_av" src="<?php echo $shopinfo['shop_pic'];?>">
                        <?php }else{?>
                            <?php if ($userinfo['headimgurl']){?>
                                <img id="shop_av" src="<?php echo $userinfo['headimgurl'];?>">
                            <?php }else{?>
                                <img id="shop_av" src="/frontend/web/images/qiche.jpg">
                            <?php }?>
                        <?php }?>
                    </dt>
                    <dd>
                        <div id="filePicker">上传商户LOGO</div>

                    </dd>
                </dl>
                <ul class="renzhengMessUl boxSizing">
                    <li class="webkitbox afterFour">
                        <label class="before">公司名称</label>
                        <input id="shop_name" type="text" value="<?php echo $shopinfo['shop_name']?$shopinfo['shop_name']:'';?>" placeholder="请输入公司名称">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">注册地址</label>
                        <input id="register_address" type="text" value="<?php echo $shopinfo['register_address']?$shopinfo['register_address']:'';?>" placeholder="请输入公司注册地址">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">选择地区</label>
                        <div id="shop_address"  class="selectAreaDiv"><?php echo $shopinfo['shop_address']?$shopinfo['shop_address']:'请选择地区信息';?></div>
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">统一社会信用代码</label>
                        <input  id="shop_credit_code"  type="text" value="<?php echo $shopinfo['shop_credit_code']?$shopinfo['shop_credit_code']:'';?>" placeholder="请输入社会信用代码">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">注册时间</label>
                        <input  type="text" placeholder="请输入公司注册时间" value="<?php echo !empty($shopinfo['shop_register_time'])?date('Y-m-d',$shopinfo['shop_register_time']):'';?>" readonly id="zhuceTime" onfocus="this.blur()">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">联系人</label>
                        <input  id="shop_preson_name" type="text" value="<?php echo $shopinfo['shop_preson_name']?$shopinfo['shop_preson_name']:'';?>" placeholder="请输入联系人姓名">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">联系电话</label>
                        <input id="shop_tel"  type="tel" value="<?php echo $shopinfo['shop_tel']?$shopinfo['shop_tel']:'';?>" placeholder="请输入联系电话">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">手机号码</label>
                        <input id="mobile"  type="tel" value="<?php echo $shopinfo['mobile']?$shopinfo['mobile']:'';?>" placeholder="请输入手机号码">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">对公账户</label>
                        <input id="shop_account"   type="text"  value="<?php echo $shopinfo['shop_account']?$shopinfo['shop_account']:'';?>" placeholder="请输入对公账户">
                    </li>
                    <li class="webkitbox afterFour">
                        <label class="before">开户行</label>
                        <input id="shop_account_bank"  type="text" value="<?php echo $shopinfo['shop_account_bank']?$shopinfo['shop_account_bank']:'';?>" placeholder="请输入对公账户开户行">
                    </li>
                </ul>
            </div>
            <div class="duihuanBot boxSizing">
                <?php if($shopinfo['shop_status']=='2'){?>
                    <span onclick="authentication();">申请修改信息</span>
                <?php }else if($shopinfo['shop_status']=='1'){?>
                    <span>后台审核中…</span>
                <?php }else{?>
                    <span onclick="authentication();">申请认证</span>
                <?php }?>
            </div>
        </div>
        <div class="tanchuDiv">
            <div class="close"></div>
            <div class="modifyTelTitle boxSizing">
                选择区域信息
            </div>
            <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot NoPaddTop">
                <ul class="renzhengMessUl boxSizing" id="selectMess">
                    <li class="webkitbox afterFour">
                        <label>省份</label>
                        <select id="shop_province" onchange="getcity();">
                            <?php foreach($province as $val){?>
                                <option value="<?php echo $val['code'];?>" <?php if($shopinfo['province']==$val['code']) echo 'selected'; ?>><?php echo $val['name']?></option>
                            <?php }?>
                        </select>
                    </li>
                    <li class="webkitbox afterFour">
                        <label>市</label>
                        <select id="shop_city" onchange="getarea()">
                            <?php foreach($city as $val){?>
                                <option value="<?php echo $val['code'];?>" <?php if($shopinfo['city']==$val['code']) echo 'selected'; ?>><?php echo $val['name']?></option>
                            <?php }?>

                        </select>
                    </li>
                    <li class="webkitbox afterFour">
                        <label>地区</label>
                        <select id="shop_area">
                            <?php foreach($area as $val){?>
                                <option value="<?php echo $val['code'];?>" <?php if($shopinfo['area']==$val['code']) echo 'selected'; ?>><?php echo $val['name']?></option>
                            <?php }?>
                        </select>
                    </li>
                    <li class="webkitbox afterFour">
                        <label>详细街道</label>
                        <input type="text" placeholder="请输入详细街道信息" id="moreMess">
                    </li>
                </ul>
            </div>
            <div class="duihuanBot boxSizing">
                <span id="baocun">保存</span>
            </div>
        </div>
    </div>
</section>

<?php $this->beginBlock('script');?>

<script src="/frontend/web/js/my.js"></script>
<script src="/frontend/web/js/lCalendar.js"></script>
<script type="text/javascript" src="/frontend/web/webuploader/webuploader.js"></script>
<script type="text/javascript">
    var calendar = new lCalendar();
    calendar.init({
        'trigger': '#zhuceTime',
        'type': 'date',
//        'minDate':'1900-1-1'
    });

</script>
<script type="text/javascript">
    $(function () {
        var scrollTop = 0;
        $('.selectAreaDiv').on('click', function () {
            show();
        });
        $('.close').on('click', function () {
            hide();
        });
        $('#baocun').on('click', function () {
            var select = $('#selectMess').find('select'),str = '';
            select.each(function () {
                str += $(this).children('option').eq(this.selectedIndex).html();
            });
            str += $('#moreMess').val();
            $('.selectAreaDiv').css('color','#2c2c2c').text(str);
            hide();
        });
        function show(e){
            scrollTop = (document.body || document.documentElement).scrollTop;
            if(e){
                e.preventDefault();
            }
//            $('.PopupLayer').css('display','block');
            $('.TranslateDiv').addClass('keep').css({
                '-webkit-transform' : 'translate3d(-' + window.innerWidth + 'px,0,0)',
            })
//            $('section').css({
//                height : window.innerHeight,
//                'overflow' : 'hidden'
//            });
        }
        function hide(){
            $('.TranslateDiv').css({
                '-webkit-transform' : 'translate3d(0px,0,0)',
            })
//            $('section').removeAttr('style');
//            $(document).scrollTop(scrollTop);
        }
    });

////////////////////////////////////////////////////////////////////////



    function getcity(){
        var code = $("#shop_province").val();
        var url = "<?php echo Url::to(['car/getcity']);?>";
        var html = "";
        $('#shop_city').html('');
        $('#shop_area').html('');
        $.post(url,{code:code},function(json){

            if(json.status == 1){
                $.each(json.data,function(){
                    html += '<option value="'+this.code+'">'+this.name+'</option>';
                });
                $('#shop_city').append(html);
                getarea();
            }else{
                alert(json.msg);
            }
        });
    }

    function getarea(){
        var code = $("#shop_city").val();
        var url = "<?php echo Url::to(['car/getarea']);?>";
        var html = "";
        $('#shop_area').html('');
        $.post(url,{code:code},function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    html += '<option value="'+this.code+'">'+this.name+'</option>';
                });
                $('#shop_area').append(html);
            }else{
                alert(json.msg);
            }
        });
    }
    var is_sub=false;
    function authentication(){
        if(is_sub){
            alert('数据提交中请稍后');
            return false;
        }
        var opt1 = $("#shop_name").val(),
            opt2 = $("#shop_tel").val(),
            opt3 = $("#shop_preson_name").val(),
            opt4 = $("#shop_credit_code").val(),
            opt5 = $("#zhuceTime").val(),
            opt6 = $("#register_address").val(),
            opt7 = $("#shop_account").val(),
            opt8 = $("#shop_account_bank").val(),
            opt9 = $("#shop_av").attr('src'),
            opt10 = $("#shop_address").html(),
            opt11 = $("#shop_province").val(),
            opt12 = $("#shop_city").val(),
            opt13 = $("#shop_area").val(),
            opt14 = $("#mobile").val(),
            status='<?php echo $shopinfo['shop_status']?>';

        if(status == '2'){
            if(! confirm("修改信息要重新审核，你确定提交吗？")){
                return false;
            }
        }
        if(opt1.length==0 ||  opt1.length>50){
            alert('请输入合理公司名称');
            return false;
        }

        if(opt2.length==0 ||  opt2.length>20){
            alert('请输入合理的联系电话');
            return false;
        }

        if(opt3.length==0 ||  opt3.length>20){
            alert('联系人姓名');
            return false;
        }

        if(opt4.length==0 ||  opt4.length>50){
            alert('请输入合理的统一社会信用代码');
            return false;
        }

        if(opt5.length==0 ){
            alert('请选择注册时间');
            return false;
        }

        if(opt6.length==0 ||  opt6.length>150){
            alert('请输入合理的注册地址');
            return false;
        }

        if(opt7.length==0 ||  opt7.length>25){
            alert('请输入合理的对公账号');
            return false;
        }

        if(opt8.length==0 ||  opt8.length>25){
            alert('请输入合理的开户行');
            return false;
        }
        if(opt10.length==0 ||  opt10.length>100){
            alert('请输入选择地址');
            return false;
        }
        if(!/^1[0-9]\d{9}$/.test(opt14)){
            alert('请输入正确的联系人手机号码');
            return false;
        }
        var url = "<?php echo Url::to(['car/authentication']);?>";
        is_sub=true;
        $.post(url,{
            shop_name:opt1,
            shop_tel:opt2,
            shop_preson_name:opt3,
            shop_credit_code:opt4,
            shop_register_time:opt5,
            register_address:opt6,
            shop_account:opt7,
            shop_account_bank:opt8,
            shop_pic:opt9,
            shop_address:opt10,
            province:opt11,
            city:opt12,
            area:opt13,
            mobile:opt14

        },function(json){
            is_sub=false;
            if(json.status == 1){

                window.location.href= "<?php echo Url::to(['car/shop_core']);?>";
            }else{
                alert(json.msg);
            }
        });
    }

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
<?php $this->endBlock('script');?>
