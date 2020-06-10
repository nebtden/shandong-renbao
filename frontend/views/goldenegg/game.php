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
<div class="img2Box">
    <img src="images/pic2.png" alt="">
</div>
<div class="egg-box">
    <ul>
        <li>
            <div class="egg-up">
                <img src="images/egg.png" class="egg" alt="">
            </div>
            <img src="images/gegg.png" alt="">
        </li>
        <li>
            <div class="egg-up">
                <img src="images/egg.png" class="egg" alt="">
            </div>
            <img src="images/gegg.png" alt="">
        </li>
        <li>
            <div class="egg-up">
                <img src="images/egg.png" class="egg" alt="">
            </div>
            <img src="images/gegg.png" alt="">
        </li>
    </ul>
    <div class="sm-txt">
        参与方式：任意选择一个金蛋，点击“金蛋”即可砸开金蛋参与幸运大奖。
        <br><br>
        如何领奖：中奖后，系统会自动保存您的中奖信息，我们的工作人员会与您取得联系并送上奖品
    </div>
</div>
<div class="prizeTk">
    <div class="prize-center">
        <div class="prize-content">
            <p><span>您获得</span></p>
            <p><span id="prize"></span></p>
        </div>
        <div class="btnBox">
            <a href="<?php echo Url::to(['goldenegg/share']) ?>"><img src="images/hitlq.png"></a>
        </div>
    </div>
</div>

<?php $this->beginBlock('script') ?>
     <script>
         $('.egg-box ul li ').on('click',function () {
             var _this = $(this);
             $(this).find('.egg-up').html('<img src="images/chuiz.png" class="hammer"><img src="images/agg-puo.png" class="egg-puo" alt="">');
             $('.egg-box  ul li').addClass('disabled');
             $.ajax({
                 url:'<?php echo Url::to(['goldenegg/game']) ?>',
                 type:'POST',
                 dataType:'json',
                 data:{id:<?php echo $id ?>},
                 success:function(json){
                     if(json.status == 0){
                         YDUI.dialog.toast(json.msg, 'error', 1000, function () {
                             _this.find('.egg-up').html('<img src="images/egg.png" class="egg" alt="">');
                             $('.egg-box  ul li').removeClass('disabled');
                             window.location.href = json.url
                         });
                     } else{
                         $('#prize').html(json.data);

                         setTimeout(function () {
                             $('.prizeTk').show();
                         },1500)
                     }
                 }
             });
         })
     </script>
<?php $this->endBlock('script') ?>
