<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">
<style>

    .uploadFace {
        max-width: 100%;
        max-height: 100%;
    }
    .uploadFace #car-pic{
        max-width: 100%;
        max-height: 126px;
    }

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
</style>
<?php $this->endBlock('hStyle')?>

<div class="info-header">
    <span><img src="<?= $washShop['shop_pic']?>"></span>
</div>
<dl class="common-dl upfile-dl">
    <dd>
        <i>服务码</i>
        <input class="code-input" type="text" name="consumerCode" placeholder="请输入您的服务码或使用扫码功能">
        <em class="scan-code" onclick="scanCode()"></em>
    </dd>
    <dd class="upfile-wrap">
        <div><img src="/frontend/web/cloudcarv2/images/eg-pic.jpg"></div>
        <div>
            <div class="uploadFace webkitbox boxSizing">
                <div id="filePicker">
                    <img id="car-pic" src="/frontend/web/cloudcarv2/images/upfile.png" >
                </div>
            </div>
        </div>




    </dd>
</dl>
<div class="commom-submit">
    <button type="button" class="btn-block sure-btn cancel-btn " disabled>确认核销</button>
</div>
<!--    服务码有误弹框-->
<div class="commom-popup-outside service-code-outside" style="display:none">
    <div class="commom-popup">
        <div class="popup-title service-code-title"><i class="icon-error"></i></div>
        <div class="popup-content service-code-content">
            <div class="service-code-text"><p class="result-msg">对不起，您的服务码有误</p><p>请重新输入！</p></div>
            <p>如果在使用过程中有疑难问题</p>
            <p>请拨打客服热线：<a href="tel:<?php echo \Yii::$app->params['yunche_hotline'] ?>"><?php echo \Yii::$app->params['yunche_hotline'] ?></a></p>
            <div class="commom-submit">
                <button type="button" class="btn-block reset-btn">重新输入</button>
            </div>
        </div>
    </div>
</div>
<!-- 服务码核销成功弹窗-->
<div class="commom-popup-outside hexiao-outside" style="display:none">
    <div class="commom-popup">
        <div class="popup-title service-code-title"><i class="icon-error"></i></div>
        <div class="popup-content service-code-content">
            <div class="service-code-text">服务卡核销成功<br>请进入商户中心查看收入明细</div>
            <p>未授权的用户a如果在使用过程中有疑难问题</p>
            <p>请拨打客服热线：<a href="tel:<?php echo \Yii::$app->params['yunche_hotline'] ?>"><?php echo \Yii::$app->params['yunche_hotline'] ?></a></p>
            <div class="commom-submit">
                <a href="<?php echo Url::to(['carwashshop/index'])?>" class="btn-block store-btn">商户中心</a>
            </div>
        </div>
    </div>
</div>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script') ?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="/frontend/web/js/my.js"></script>
<script src="/frontend/web/js/lCalendar.js"></script>
<script type="text/javascript" src="/frontend/web/webuploader/webuploader.js"></script>


<script>


    //微信扫一扫接口
    function scanCode(){
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
                'scanQRCode'
            ]
        });
        wx.ready(function () {
            wx.scanQRCode({
                needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr;// 当needResult 为 1 时，扫码返回的结果
                    $("input[name=consumerCode]").val(result);
                }
            });
        });
    }


    //	关闭弹窗
    $('.icon-error,.store-btn,.reset-btn').on('click',function(){
        $('.commom-popup-outside').hide()
    });

    //确认核销点击弹窗
    var is_sub = false;
    $('.sure-btn').on('click',function(){
        if(is_sub){
            YDUI.dialog.toast('核销中，请勿重复点击',1500);
            return false;
        }
        var consumerCode = $('input[name=consumerCode]').val();
            car_pic = $('#car-pic').attr('src');
            if(consumerCode.length == 0){
                YDUI.dialog.toast('请输入服务码',1500);
                return false;
            }
            if( car_pic.length == 0){
                YDUI.dialog.toast('请上传车牌照片',1500);
                return false;
            }
            is_sub = true;
            YDUI.dialog.loading.open('服务码核销中');
            $.ajax({
                url:"<?php echo Url::to(['carwashshop/verification'])?>",
                data:{consumerCode:consumerCode,car_pic:car_pic},
                type:'post',
                dataTypes:'json',
                timeout:6000,
                success: function(json){
                    YDUI.dialog.loading.close();
                    if(json.status == 1){
                        $('.hexiao-outside').show()
                    }else{
                        is_sub = false;
                        $('.result-msg').text(json.msg);
                        $('.service-code-outside').show();
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
    });

    //	重新输入
    $('.reset-btn').on('click',function(){
//		$('.service-code-outside').hide()
        $('.code-input').focus()
    })

</script>
<script>

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
                $('#car-pic').replaceWith('<span>不能预览</span>');
                return;
            }
            $('#car-pic').attr( 'src', src );

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
        $('#car-pic').attr( 'src', img_url );
        $('.btn-block').removeClass('cancel-btn').attr('disabled',false);
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



<?php $this->endBlock('script')?>
