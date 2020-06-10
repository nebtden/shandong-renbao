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
    <dl class="uploadFace webkitbox boxSizing afterFour">

        <dd>
            <div id="filePicker">点击上传门店头像 ></div>
        </dd>
    </dl>
</div>
<div class="commom-submit">
    <button type="button" class="btn-block">保存</button>
</div>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script src="/frontend/web/js/my.js"></script>
<script src="/frontend/web/cloudcarv2/js/my.js"></script>
<script type="text/javascript" src="/frontend/web/webuploader/webuploader.js"></script>
<script>
    var is_sub = false;
    $(".btn-block").on('click',function(){
        var shop_pic = $("#shop_av").attr('src');

        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        if(shop_pic.length == 0 || shop_pic.length > 255){
            YDUI.dialog.toast('请上传头像',1000);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/changepic'])?>',
            data:{shop_pic:shop_pic},
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
