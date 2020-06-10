<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">
<style>
    .webuploader-container {
        position: relative;
    }
    .webuploader-element-invisible {
        position: absolute !important;
        clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
        clip: rect(1px,1px,1px,1px);
    }
    .webuploader-pick {
        position: relative;
        display: inline-block;
        cursor: pointer;
        color: #fff;
        text-align: center;
        border-radius: 3px;
        overflow: hidden;
    }
    .webuploader-pick-hover {
        background: #00a2d4;
    }

    .webuploader-pick-disable {
        opacity: 0.6;
        pointer-events:none;
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
        <div class="upfile-box">
            <div id="uploader-demo">
                <!--用来存放item-->
                <div id="car_pic" class="uploader-list"></div>
                <div id="filePicker"><img src="/frontend/web/cloudcarv2/images/upfile.png" ></div>
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
                needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr;// 当needResult 为 1 时，扫码返回的结果
                    $("input[name=consumerCode]").val(result);
                }
            });
        });
    }

    //微信上传图片接口
    function uploadimages(){
        var _this = this;
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
                'scanQRCode',
                'chooseImage',
            ]
        });
        wx.ready(function () {
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    _this.uploadImg();

                }
            });
        });
    }

    function uploadImg(fid,tid,w,h){
        $('#'+fid).diyUpload({
            url:'/backend/web/server/fileupload.php?w='+w+'&h='+h,
            success:function( data ) {
                console.info( data );
                $('input[name="'+tid+'"]').val('/static/upfile/'+data.result);
                $('input[name="'+tid+'"]').next('img').remove();
                $('#'+fid).hide();

            },
            error:function( err ) {
                console.info( err );
            }
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
            car_pic = $('#car_pic').attr('src');
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


</script>



<?php $this->endBlock('script')?>
