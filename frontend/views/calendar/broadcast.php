<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/24 0024
 * Time: 下午 1:30
 */

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
</div>

<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>

<script src="./js/jquery.min.js"></script>
<script src="./js/fixScreen.js"></script>
<script>
    $(document).ready(function(){
        // 这里是播放初始化
        $('#video-play-btn').on('click', function () {
            $('#video-play-btn').hide();
            var myVid = document.getElementById("video1"); //获取video 元素
            myVid.play();
        })
    });
</script>
<?php $this->endBlock('script'); ?>

