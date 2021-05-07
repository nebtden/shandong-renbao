<?php
use yii\helpers\Url;
?>
<?php echo $this->context->renderPartial('../layouts/header',array('title'=>'后台管理'));?>
<header class="yhtHeader webkitbox clearfix">
    <div class="rightH webkitbox">
        <div class="adminH"><?php echo Yii::$app->session ['UNAME'];?></div>
        <div class="allsecreen">开启全屏</div>
    </div>
    <div class="adminLa">
        <div class="downsj"></div>
        <ul><?php if(Yii::$app->session ['UNAME']!='manages'){?>
            <li><a href="<?php echo Url::to(['admin/resetpwd']); ?>">资料</a></li>
            <li><a href="<?php echo Url::to(['wx/index']);?>">设置</a></li>
            <?php }?>
            <li><a href="<?php echo Url::to(['site/logout']);?>">退出</a></li>
        </ul>
    </div>
</header>
<section class="houtaiCont webkitbox boxSizing">
    <?php echo $this->context->renderPartial('../layouts/menu');?>
    <div class="rightCont boxSizing">
       <?= $content ?>
    </div>
</section>
<?php $this->endBody() ?>
</body>
<script type="text/javascript">

    $('.allsecreen').click(function () {
        if(document.documentElement.webkitRequestFullscreen){
            if(!document.webkitIsFullScreen){
                launchFullScreen(document.documentElement);
                $(this).text('退出全屏');
            }else{
                exitFullScreen();
                $(this).text('开启全屏');
            }
        }else if(document.documentElement.mozRequestFullScreen){
            if(!document.mozFullScreen){
                launchFullScreen(document.documentElement);
                $(this).text('退出全屏');
            }else{
                exitFullScreen();
                $(this).text('开启全屏');
            }
        }
    });
    function launchFullScreen(element) {
        if(element.webkitRequestFullScreen){
            element.webkitRequestFullScreen();
        }else if(element.mozRequestFullScreen){
            element.mozRequestFullScreen();
        }
    }
    function exitFullScreen(){
        if(document.webkitCancelFullScreen){
            document.webkitCancelFullScreen();
        }else if(document.mozCancelFullScreen){
            document.mozCancelFullScreen();
        }
    }

    $('.adminH').click(function (e) {
        e.stopPropagation();
        if($('.adminLa').is(':hidden')){
            $('.adminLa').show()
        }else{
            $('.adminLa').hide()
        }
        $('.adminLa').click(function (e) {
            e.stopPropagation();
        })
    });

    $(document).click(function () {
        $('.adminLa').hide();
    })

    $(function () {
        var li = $('.lfcMid').find('li');
        var rightpos = li.eq(0).find('.rightPos');
        var liTitle = $('.liTitle');
       // rightpos.show().end().find('.liList').show();
        if($('section').innerHeight() + $('header').innerHeight() <= $(window).height()){
            $('.leftCont').height($(window).height() - $('header').innerHeight())
        }else{
            $('.leftCont').height($('section').innerHeight());
        }
        liTitle.click(function () {
            var that = this;
            liTitle.each(function () {
                if(this != that){
                    $(this).next().next().hide(300);
                    $(this).next().hide(300);
                }
            });
            if($(this).next().is(':hidden')){
                $(this).next().slideDown(300);
                $(this).next().next().show(300);
            }else{
                $(this).next().slideUp(300);
            }
        });
    });
    $(function () {
        var posZ = $('.posZ'),containX = $('.containX'),containY = $('.containY'),lfcMid1 = $('.lfcMid1'),lfcTop1 = $('.lfcTop1');
        var width = posZ.parent().width();
        posZ.click(function () {
            if(containY.is(':visible ')){
                containY.hide();
                containX.show();
                $(this).parent().width(containX.width() - 5);
                $(this).css({
                    left : (containX.width() - 5 - $(this).width()) / 2,
                    '-webkit-transform' : 'rotate(-180deg)'
                });
                lfcMid1.find('li').mouseover(function () {
                    $(this).find('.liList1').show();
                    $(this).find('.rightPos1').show()
                }).mouseout(function () {
                    $(this).find('.liList1').hide();
                    $(this).find('.rightPos1').hide()
                });
                lfcTop1.mouseover(function () {
                    $(this).find('.forDivHeng').show();
                }).mouseout(function () {
                    $(this).find('.forDivHeng').hide();
                });
            }else{
                containY.show();
                containX.hide();
                $(this).parent().width(width);
                $(this).css({
                    left : (width - $(this).width()) / 2,
                    '-webkit-transform' : 'rotate(0deg)'
                })
            }
        });
    })
</script>
<!--[if lte IE 9]>
<script type="text/javascript">
    $(function () {
        var width = $('body').width();
        var lefContW = $('.leftCont').outerWidth();
        var posZ = $('.posZ'),containX = $('.containX'),containY = $('.containY');
        $('.rightCont').innerWidth(width - lefContW -1);
        posZ.click(function () {
            var lefContW = $('.leftCont').innerWidth();
            $('.rightCont').css({
                width : width - lefContW - 1
            });
        })
    })
</script>
<![endif]-->
</html>
<?php $this->endPage() ?>