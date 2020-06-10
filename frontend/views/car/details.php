<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7 0007
 * Time: 下午 2:52
 */
use yii\helpers\Url;
?>

<section class="contentFull bgColor overFlow secPadd">
    <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot">
        <div class="renzhengTopTitle afterFour">
            基础信息
        </div>
        <form id="upimg">
        <dl class="uploadFace webkitbox boxSizing afterFour">
            <dt id="img_list">
                <?php if ($shopinfo['shop_pic']){?>
                    <img id="shop_av" src="<?php echo $shopinfo['shop_pic'];?>">
                <?php }else{?>
                    <img id="shop_av" src="/frontend/web/images/qiche.jpg">
                <?php }?>
            </dt>
            <dd>
                <div id="filePicker">更换LOGO</div>


            </dd>
        </dl>
        </form>
        <ul class="renzhengMessUl boxSizing">
            <li class="webkitbox afterFour">
                <label>公司名称</label>
                <em><?php  echo $shopinfo['shop_name'];?></em>
            </li>
            <li class="webkitbox afterFour">
                <label>注册地址</label>
                <em><?php  echo $shopinfo['register_address'];?></em>
            </li>
            <li class="webkitbox afterFour">
                <label>地址信息</label>
                <em><?php  echo $shopinfo['shop_address'];?></em>
            </li>
            <li class="webkitbox afterFour">
                <label>统一社会信用代码</label>
                <em><?php  echo $shopinfo['shop_credit_code'];?></em>
            </li>
            <li class="webkitbox afterFour">
                <label>注册时间</label>
                <em><?php  echo $shopinfo['shop_register_time'];?></em>
            </li>
            <li class="webkitbox afterFour">
                <label>联系电话</label>
                <em><?php  echo $shopinfo['shop_tel'];?></em>
            </li>
            <li class="webkitbox afterFour">
                <label>手机号码</label>
                <em><?php  echo $shopinfo['mobile'];?></em>
            </li>
        </ul>
    </div>
    <div class="shopPageCont NoPaddLR NoPaddBot NoPaddTop">
        <ul class="renzhengMessUl boxSizing refereceMessJC">
            <li class="afterFour">
                <a href="#">
                    <label>收款账户</label>
                    <em><?php  echo $shopinfo['shop_account'];?> <br><?php  echo $shopinfo['shop_account_bank'];?></em>
                </a>
            </li>
            <?php if (empty($_SESSION['wx_user_auth']['pid'])){?>
            <li class="afterFour">
                <a href="<?php echo Url::to(['car/one_password']);?>">
                    <label>账户安全</label>
                    <em>修改提现密码</em>
                </a>
            </li>
            <?php }?>
        </ul>
    </div>
</section>
<?php $this->beginBlock('script');?>
    <!--引入JS-->
<script type="text/javascript" src="/frontend/web/webuploader/webuploader.js"></script>
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

        var url = "<?php echo Url::to(['car/insertimg']);?>";
        var img_url = '/static/upfile/'+response.result ;
        $.post(url,{img_url:img_url},function(json){
                console.log(json);
        });
    });
//    uploader.on( 'uploadSuccess', function( file ,response ) {
//        path.push(response);
//        console.log(path);
//        $( '#'+file.id ).addClass('upload-state-done');
//
//    });

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


    function aa (e) {
        $('#filePicker').html(file.id);
    }

</script>
<?php $this->endBlock('script');?>