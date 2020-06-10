<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle'); ?>
<style>
    img{
        display: inline;
    }
</style>
<?php $this->endBlock('hStyle'); ?>
<div class="imgBox">
    <div>
        <img src="images/im.png" alt="">
        <img src="<?= $user->image ?>" alt="">
    </div>
    <img src="images/sharepic.png" alt="">
</div>
<a href="javascript:void(0)" class="lj-btn"><img src="images/share-btn.png"  onclick="sharePage()" style="width: 5.12rem" alt=""></a>
<?php $this->beginBlock('script') ?>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script>
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
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareQZone',
                'onMenuShareWeibo'
            ]
        });
        wx.ready(function () {
            wx.onMenuShareTimeline({
                title: '<?= $shareInfo['pengyouquan'] ?>', // 分享标题
                link: '<?= $shareInfo['link'] ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '<?= $shareInfo['imgUrl'] ?>', // 分享图标
                success: function () {
                    // 用户点击了分享后执行的回调函数
                }
            });
            wx.onMenuShareAppMessage({
                title: '<?= $shareInfo['title'] ?>', // 分享标题
                desc: '<?= $shareInfo['desc'] ?>', // 分享描述
                link: '<?= $shareInfo['link'] ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '<?= $shareInfo['imgUrl'] ?>', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {

                }
            });
            wx.onMenuShareQQ({
                title: '<?= $shareInfo['title'] ?>', // 分享标题
                desc: '<?= $shareInfo['desc'] ?>', // 分享描述
                link: '<?= $shareInfo['link'] ?>', // 分享链接
                imgUrl: '<?= $shareInfo['imgUrl'] ?>', // 分享图标
                success: function () {

                },
                cancel: function () {

                }
            });
            wx.onMenuShareQZone({
                title: '<?= $shareInfo['title'] ?>', // 分享标题
                desc: '<?= $shareInfo['desc'] ?>', // 分享描述
                link: '<?= $shareInfo['link'] ?>', // 分享链接
                imgUrl: '<?= $shareInfo['imgUrl'] ?>', // 分享图标
                success: function () {

                },
                cancel: function () {

                }
            });
            wx.onMenuShareWeibo({
                title: '<?= $shareInfo['title'] ?>', // 分享标题
                desc: '<?= $shareInfo['desc'] ?>', // 分享描述
                link: '<?= $shareInfo['link'] ?>', // 分享链接
                imgUrl: '<?= $shareInfo['imgUrl'] ?>', // 分享图标
                success: function () {

                },
                cancel: function () {

                }
            });

        });
    </script>

<?php $this->endBlock('script') ?>