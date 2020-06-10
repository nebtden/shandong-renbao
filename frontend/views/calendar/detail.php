<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5 0005
 * Time: 下午 4:39
 */
use yii\helpers\Url;
?>
<?php $this->beginBlock('headStyle')?>

    <link rel="stylesheet" href="/frontend/web/calendar/css/reset.css" />
    <link rel="stylesheet" href="/frontend/web/calendar/css/font-set.css">
    <link rel="stylesheet" href="/frontend/web/calendar/css/detail.css">
<?php $this->endBlock('headStyle')?>
<div id="app">
    <div class="com-header">
        <a class="back-btn" onclick="history.back()"></a>
        <span class="fz-36">视频观看</span>
        <a></a>
    </div>
    <div class="video-box">
        <div class="video-box">
            <video src="<?php echo $info['path']?>" id="video1" controls="controls" width="690px" height="400px" x5-playsinline="" playsinline="" webkit-playsinline=""></video>
            <div class="video-play-mark" id="video-play-btn">
                <img src="<?php echo $info['pic']?$info['pic']:'./images/pre-view.png';?>" class="pre-view-img" alt="">
                <span class="video-play-btn"></span>
            </div>
        </div>
    </div>
    <p class="question fz-32"><?php echo $info['title']?></p>
    <?php if($info['show_desc'] == 1){?>
        <p class="tro fz-28">简介：</p>
        <p class="answer fz-28"><?php echo $info['introduction']?></p>
    <?php }?>
    <!-- 弹框-输入 -->
    <div class="pop-mark com-show" id="pop-input-box">
        <div class="pop-content-box">
            <input placeholder="请输入观看码" id="viewsn" type="text" class="fz-30 input-code" id="pop-input-code">
            <!-- 错误提示 -->
            <p class="error-text" id="pop-error-text"></p>
            <div class="fz-36 com-pop-confirm-box">
                <span id="pop-input-cancel-btn">取消</span>
                <span id="pop-input-confirm-btn" onclick="">确定</span>
            </div>
        </div>
    </div>
    <!-- 弹框-二次确认 -->
    <div class="pop-mark" id="pop-confirm-box">
        <div class="pop-content-box">
            <p class="fz-38 remaining-text-1">
                剩余观看次数<b id="pop-remaining-text">14</b>次
            </p>
            <p class="remaining-text-2">确定观看后，将会减少1次观看次数</p>
            <p></p>
            <div class="fz-36 com-pop-confirm-box">
                <span id="pop-confirm-cancel-btn">取消</span>
                <span id="pop-confirm-confirm-btn">确定</span>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="viewcode" value="">
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>
<script>
    $(document).ready(function(){
        // 这里是播放初始化
        $('#video-play-btn').on('click', function () {
            $('#video-play-btn').hide();
            var myVid = document.getElementById("video1"); //获取video 元素
            myVid.play();
        })

        // 这里是弹框时间
        var $popInputBox = $('#pop-input-box'),
            $popInputCode = $('#pop-input-code'),
            $popErrorText = $('#pop-error-text'),
            $popInputCancelBtn = $('#pop-input-cancel-btn'),
            $popInputConfirmBtn = $('#pop-input-confirm-btn');
        var $popConfirmBox = $('#pop-confirm-box'),
            $popRemainingText = $('#pop-remaining-text'),
            $popConfirmCancelBtn = $('#pop-confirm-cancel-btn'),
            $popConfirmConfirmBtn = $('#pop-confirm-confirm-btn');
        // 输入观看码弹框
        // 取消
        $popInputCancelBtn.on('click', function () {
            $url = "<?php echo $backurl;?>";
            window.location.replace($url);

        })
        // 确定
        var is_sub=false;
        $popInputConfirmBtn.on('click', function () {
            // error:不能输入空
            if ($popInputCode.val() === '') {
                $popErrorText.show().text('不能输入空');
                return false;
            }

            // 判断观看码ajax请求
            var opt1=$('#viewsn').val(),opt2="<?php echo $info['company_id'];?>";
            if(is_sub){
                $popErrorText.show().text('信息提交中，请稍后');
                return false;
            }
            var url = "<?php echo Url::to(['calendar/checkviewsn']);?>";
            is_sub=true;
            $.post(url,{viewsn:opt1,company_id:opt2},function(json){
                is_sub=false;
                if(json.status == 1){
                    $popInputBox.removeClass('com-show');
                    $popConfirmBox.addClass('com-show');
                    $popRemainingText.text(json.data.num);
                    $('#viewcode').val(json.data.code);
                }else{
                    $popErrorText.show().text(json.msg);
                }
            });

//            if (true) {
//                $popInputBox.removeClass('com-show');
//                $popConfirmBox.addClass('com-show');
//                $popRemainingText.text(99);
//            } else {
//                // 观看码错误，请重新输入/观看次数已用完
//                $popErrorText.show().text('观看码错误，请重新输入/观看次数已用完');
//            }
        })
        // 二次确认弹框
        // 取消
        $popConfirmCancelBtn.on('click', function () {
            $popInputBox.addClass('com-show');
            $popConfirmBox.removeClass('com-show');
        })
        // 确定
        $popConfirmConfirmBtn.on('click', function () {
            var opt1=$('#viewcode').val();
            if(is_sub){
                return false;
            }
            var url = "<?php echo Url::to(['calendar/viewvideo']);?>";
            is_sub=true;
            $.post(url,{viewsn:opt1},function(json){
                is_sub=false;
                if(json.status == 1){
                    $popConfirmBox.removeClass('com-show');
                }else{
                    $popInputBox.addClass('com-show');
                    $popConfirmBox.removeClass('com-show');
                    $popErrorText.show().text(json.msg);
                }
            });
        })
    });


</script>
<?php $this->endBlock('script'); ?>

