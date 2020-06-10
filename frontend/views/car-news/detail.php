<?php

use yii\helpers\Url;

?>
<div class="cloud-car-said">
    <div class="title"><?= $detail['title'] ?></div>
    <div class="subtitle"><?= $detail['short_desc'] ?></div>
    <div class="content">
         <?= $detail['content']  ?>
    </div>
</div>
<style>
    .content img {
        max-width: 100%;
    }
</style>
<?php $this->beginBlock('script'); ?>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    $(function(){
        var s_title = '<?php echo $detail['title']?>';
        var s_desc = '<?php echo $detail['short_desc']?>';
        var s_imgUrl = '<?php echo 'http://' . $_SERVER ['HTTP_HOST'].$detail['img'] ?>';
        var s_link = '<?php echo 'http://' . $_SERVER ['HTTP_HOST'].Url::toRoute(['detail','id'=>$detail['id']]);?>';
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: '<?php echo $alxg_sign['appId']; ?>', // 必填，公众号的唯一标识
            timestamp: <?php echo $alxg_sign['timestamp'] ?> , // 必填，生成签名的时间戳
            nonceStr: '<?php echo $alxg_sign['noncestr'] ?>', // 必填，生成签名的随机串
            signature: '<?php echo $alxg_sign['signature'] ?>',// 必填，签名，见附录1
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage'
            ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
        wx.ready(function () {
            wx.onMenuShareTimeline({
                title: s_title, // 分享标题
                imgUrl: s_imgUrl, // 分享图标
                link: s_link,
                success: function () {
                },
                cancel: function () {
                }
            });
            wx.onMenuShareAppMessage({
                title: s_title, // 分享标题
                desc: s_desc, // 分享描述
                link: s_link,
                imgUrl: s_imgUrl, // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                },
                cancel: function () {
                }
            });
        });
    });
</script>
<?php $this->endBlock('script'); ?>
<!-- <div class="liulan-record-wrapper">
    <span class="left">浏览：<?/*= $detail['browse_number'] */?></span>
    <span class="right"><i class="icon-cloudCar2-zan"></i><em><?/*= $detail['point_number'] */?></em></span>
</div>-->

