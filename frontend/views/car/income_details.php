<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 上午 11:06
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd">
    <div class="shopPageCont NoMarginTop">
        <dl class="shouzhiTop boxSizing webkitbox">
            <dt>
            <p>总收入：<i><img src="/frontend/web/images/income.png" ><?php echo $shopinfo['gross_income']?></i></p>
            <p>已提现：<i><img src="/frontend/web/images/income.png" ><?php echo $shopinfo['already_amount']?></i></p>
            </dt>
            <dd>
                <a href="<?php echo Url::to(['car/apply_withdrawal']);?>">申请提现</a>
            </dd>
        </dl>
    </div>
    <div class="shopPageCont NoPaddLR NoPaddTop NoPaddBot">
        <ul class="tixianJLCont tixianMore" id="ongoing">
            <?php foreach ($log as $val){?>
                <li class="boxSizing afterFour">
                    <div class="left">
                        <p class="p1">回收记录（卡号：<?php echo $val['exchange_card_num']?>）</p>
                        <p class="p2"><?php echo date("Y-m-d H:i:s",$val['exchange_time']);?></p>
                    </div>
                    <div class="right">
                        +<?php echo $val['exchange_card_amount']*(1+$val['t_amount'])?>
                    </div>
                </li>
            <?php }?>

        </ul>
    </div>
</section>

<?php $this->beginBlock('script');?>
<script type="text/javascript">

    var pagenum=2;
    $(document).ready(function () { //本人习惯这样写了
        $(window).scroll(function () {
            //$(window).scrollTop()这个方法是当前滚动条滚动的距离
            //$(window).height()获取当前窗体的高度
            //$(document).height()获取当前文档的高度
            //var bot = 50; //bot是底部距离的高度
            if (($(window).scrollTop()) >= ($(document).height() - $(window).height())) {
                //当底部基本距离+滚动的高度〉=文档的高度-窗体的高度时；
                //我们需要去异步加载数据了
                pagelist();
            }
        });
    });

    function pagelist(){
        var html='';
        $.post('ajaxincomepage.html',{pagenum:pagenum},function(s){
            if(s.msg==0){
                alert('没有提现记录');return false;
            }else {
                $.each(s.data,function(key,val){
                    html+='<li class="boxSizing afterFour">';
                    html+='<div class="left">';
                    html+='<p class="p1">回收记录（卡号：'+val['exchange_card_num']+'）</p>';
                    html+='<p class="p2">'+val['exchange_time']+'</p>';
                    html+='</div>';
                    html+='<div class="right">';
                    html+='+'+val['exchange_card_amount']*(1+val['t_amount']);
                    html+='</div>';
                    html+='</li>';
                });
                $("#ongoing").append(html);
                pagenum++;
            }

        });
    };
</script>
<?php $this->endBlock('script');?>