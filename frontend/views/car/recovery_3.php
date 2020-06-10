<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 上午 8:41
 */
use yii\helpers\Url;
?>

<div class="input-title">请拍照服务卡与车牌合一的照片</div>
<div class="photoGraph-wrapper">
    <div class="photo-wrapper">
        <div class="img-wraper  insertImg">
            <img src="/frontend/web/images/photo1.jpg" >
        </div>
        <div class="img-wraper upload-wrapper addImg" >
            <input type="hidden" name="car_picurl" id="car_picurl" value="">
            <span><i class="iconfont icon-car-paizhao"></i></span>
            <span>上传图片</span>
        </div>
    </div>
    <div class="photoGraph-tip">请保证车牌号，卡号、卡密清晰，卡号、卡密、车牌号不清晰可能 导致无法兑换。如有疑问请致电：<a href="tel:400-176-0899"></a>400-176-0899</div>
</div>
<div class="send-comfirm">
    <button type="button" class="btn-block btn-btn-danger">确定</button>
</div>
<?php $this->beginBlock('script');?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo $alxg_sign['appId']; ?>', // 必填，公众号的唯一标识
        timestamp: <?php echo $alxg_sign['timestamp'] ?> , // 必填，生成签名的时间戳
        nonceStr: '<?php echo $alxg_sign['noncestr'] ?>', // 必填，生成签名的随机串
        signature: '<?php echo $alxg_sign['signature'] ?>',// 必填，签名，见附录1
        jsApiList: [
            'chooseImage',
            'previewImage',
            'uploadImage'
        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    $(".addImg").click(function(){
        var box = $(this).parents('.photo-wrapper');
        var upload = function(box,id){
            wx.uploadImage({
                localId: id,
                isShowProgressTips: 1,
                success: function(res){
                    box.find('input').val(res.serverId);
                }
            });
        };
        wx.chooseImage({
            count: 1,
            sizeType: ['compressed'],
            sourceType: ['camera'],
            success:function(res){
                $('.insertImg').html('<img src="'+res.localIds[0]+'">');
                upload(box,res.localIds[0]);
            }
        });
    });
</script>
<script>
    //删除图片
    $('.img-wraper>i.icon-error').on('click',function(e){
        e.stopPropagation();
        $(this).closest('.img-wraper').remove();
        if($('.photo-wrapper>.img-wraper').length>=3){
            $('.upload-wrapper').hide();
        }else{
            $('.upload-wrapper').show();
        }
    })
    //提交确认
    var isSubmit = false;
    $('.send-comfirm').on('touchstart',function(){
        var picurl=$('#car_picurl').val();
        if(!picurl.length){
            YDUI.dialog.toast('上传车牌正面照','none',1500);
            return false;
        }
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['recovery_3'])?>",{picurl:picurl},function(json){
            isSubmit = false
            YDUI.dialog.loading.close();
            if(json.status == 1){
                YDUI.dialog.toast(json.msg, 'none', 1000,function(){
                    window.location.href = json.url;
                });
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });

</script>
<?php $this->endBlock('script');?>
