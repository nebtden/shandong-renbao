<?php 
use yii\helpers\Url;
?>

<header class="shopHeader">
    <div class="swiper-container TGContainer">
        <div class="swiper-wrapper">
        <?php foreach($proinfo['pic'] as $v){?>
            <div class="swiper-slide"><a href="#"><img src="<?php echo $v?>"></a></div>
        <?php }?>    
        </div>
        <div class="swiper-pagination"></div>
    </div>
</header>


<section class="shopDetails DetailsColor boxSizing">
    <h1 class="none_">物品详情</h1>
   
    <dl class="PriceOrder boxSizing clearfix">
    <?php if($proinfo['id']==149 ||$proinfo['id']==150){?>
        <dt>&yen;<i><?php echo $proinfo['bargain_price']?></i>/<?php echo $proinfo['unit']?>
        <?php if($proinfo['pack_num']){?>
        (<?php echo $proinfo['pack_num'].$proinfo['unit']?>成件)
        <?php }?></dt>
    <?php }else{?> 
        <dt>积分<i><?php echo $proinfo['bargain_price']*10?></i>/<?php echo $proinfo['unit']?>
        <?php if($proinfo['pack_num']){?>
        (<?php echo $proinfo['pack_num'].$proinfo['unit']?>成件)
        <?php }?></dt>
    <?php }?>
    <?php if($proinfo['id']==149 ||$proinfo['id']==150){?>   
        <dd style="background-color: #fff;color:#000;"><?php echo $proinfo['proname'];if($proinfo['pack_num']){
        	
        	echo "(".$proinfo['qiding_num'].$proinfo['unit']."起订)";
        }?>
        
        </dd>
        <?php }else{?>
             <dd><a onclick="addPros();">加入购物车</a></dd>
        <?php }?>
    </dl>
    <dl class="OrderNum boxSizing clearfix">
        <dt>数量</dt>
        <dd>
            <span class="jianNum"></span>
            <span class="ordeNum" contenteditable="true">1</span>
            <span class="addeNum"></span>
        </dd>
    </dl>
    <div class="detailShow">
        <div class="detailTop clearfix"><span>参数</span></div>
        <div class="detailBot boxSizing">
            <?php echo $proinfo['parameter']?>
        </div>
    </div>
    <div class="detailShow">
        <div class="detailTop clearfix"><span>图文详情</span></div>
        <div class="detailBot boxSizing">
            <?php echo $proinfo['content']?>
        </div>
    </div>
</section>

<footer class="tuanPay webkitbox boxSizing">
    <div class="Pzhuye">
        <a href="<?php echo Url::toRoute('catlist');?>">
            <span></span>
            <em>主页</em>
        </a>
    </div>
    <div class="Ptuan"><a href="<?php echo Url::toRoute(['groupstart','pid'=>$proinfo['id']]);?>">发起团购</a></div>
    
    <div class="Pgou"><a onclick="personalbuy();">立即购买</a></div>
    <?php if($proinfo['id']!=149 && $proinfo['id']!=150 ){?>
    <div class="Pgouwuche">
        <a href="<?php echo Url::toRoute('shopcart');?>">
            <span></span>
            <em>购物车</em>
        </a>
    </div>
    <?php }?>
</footer>

<script src="/static/mobile/js/MeTool.js"></script>
<script src="/static/mobile/js/swiper.min.js"></script>
<script src="/static/mobile/js/alert.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
$(function(){
	var s_title = '<?php echo $proinfo['proname']?>';
	var s_imgUrl = '<?php echo 'http://' . $_SERVER ['HTTP_HOST'].$proinfo['pic'][1]?>';
	var s_desc = '<?php echo $proinfo['desc']?>';
	var s_link = '<?php echo 'http://' . $_SERVER ['HTTP_HOST'].Url::toRoute(['prodetail','pid'=>$proinfo['id']]);?>';
 	wx.config({
 	    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	    appId: '<?php echo $Sign_info['appId']; ?>', // 必填，公众号的唯一标识
	    timestamp: <?php echo $Sign_info['timestamp'] ?> , // 必填，生成签名的时间戳
	    nonceStr: '<?php echo $Sign_info['noncestr'] ?>', // 必填，生成签名的随机串
	    signature: '<?php echo $Sign_info['signature'] ?>',// 必填，签名，见附录1
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
<script type="text/javascript">
    addEvent(document, 'DOMContentLoaded', function () {
        var jianNum = document.querySelector('.jianNum'),
            ordeNum = document.querySelector('.ordeNum'),
            addeNum = document.querySelector('.addeNum');

        addEvent(jianNum, 'touchend', function () {
            var textNum = ordeNum.innerText,num;
            if(textNum <= 1){
                return false;
            }
            num = Number(textNum);
            num--;
            ordeNum.innerText = num;
        });

        addEvent(addeNum, 'touchend', function () {
            var textNum = ordeNum.innerText,num;
            num = Number(textNum);
            num++;
            ordeNum.innerText = num;
        });
    });
</script>
<script>
         function personalbuy(){
                  var proNum=$('.ordeNum').html();
                  var url = '<?php echo Url::to(['personalbuy','pid' => $proinfo['id'] ]);?>'+'&proNum='+proNum
                  	 location.href=url;
             }

         function addPros(){
        	      var proNum=$('.ordeNum').html();
                  var url = '<?php echo Url::to(['shopcart','proId' => $proinfo['id'] ]);?>'+'&proNum='+proNum
                  location.href=url;
             }
</script>
<script>
    var swiper = new Swiper('.swiper-container.TGContainer', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
//        spaceBetween: 30,
        centeredSlides: true,
        autoplay: 2500,
        autoplayDisableOnInteraction: false
    });
    
    addEvent($('.ordeNum').get(0), 'input', function () {
        if(!(/^\d*$/g).test(this.innerHTML)){
        	this.innerHTML = this.innerHTML.replace(/\D/g, '');
            $(this).tanchu('请输入数字');
            return;
        }
    })
</script>

