<?php 
use yii\helpers\Url;
?>

<header class="shopHeader">
    <div class="swiper-container TGContainer">
        <div class="swiper-wrapper">
        <?php foreach($proInfo['pic'] as $v){?>
            <div class="swiper-slide"><a href="#"><img src="<?php echo $v?>"></a></div>
        <?php }?>    
        </div>
        <div class="swiper-pagination"></div>
    </div>
</header>


<section class="shopDetails DetailsColor boxSizing">
    <h1 class="none_">物品详情</h1>
    <div class="ItemDesc boxSizing">
        <span><?php echo $proInfo['desc']?></span>
    </div>
    <dl class="PriceOrder boxSizing clearfix">
    <?php if($proInfo['id']==149 || $proInfo['id']==150){?>
        <dt>&yen;<i><?php echo $proInfo['bargain_price']?></i>/<?php echo $proInfo['unit']?></dt>
        <?php }else{?>
        <dt>积分<i><?php echo $proInfo['bargain_price']*10?></i>/<?php echo $proInfo['unit']?></dt>
        <?php }?>
        <dd>邀请团员</dd>
    </dl>
    <dl class="OrderNum LaunchTuan boxSizing clearfix">
        <dt>团长:<?php echo $orInfo['receiver']?></dt>
        <dd><?php echo $num?>人参团/整件成团</dd>
    </dl>
    <div class="zuTuanPlay">
        <div class="PlayTop clearfix"><span>组团玩法</span><a href="#">规则详情</a></div>
        <div class="PlayBot boxSizing webkitbox">
            <dl>
                <dt>开团</dt>
                <dd>选择商品<br>并参团/开团</dd>
            </dl>
            <div class="chaIcon"></div>
            <dl>
                <dt><img src="/static/mobile/images/yaoqinghaoyou.png"></dt>
                <dd>邀请好友<br>参加</dd>
            </dl>
            <div class="chaIcon"></div>
            <dl>
                <dt><img src="/static/mobile/images/couqirenshu.png"></dt>
                <dd>凑齐三人<br>成团购买</dd>
            </dl>
        </div>
    </div>
    <div class="zuTuanList boxSizing">
        <div class="PlayTop clearfix"><span>团员列表</span></div>
        <ul class="TuanList boxSizing">
        <?php foreach($receivers as $v){?>
        <?php if($v['openid']==$myopenid){?>
            <li>
                <span><img src="<?php echo $v['imgurl']?>"></span>
                <em>团员：<?php echo $v['receiver']?></em>
                <i contenteditable="true" id='changenum' tag='member' hao="<?php echo $v['id']?>" style="color: #555;box-shadow: 0 0 1px 1px rgba(0,0,0,.6);"><?php echo $v['pro_num']?></i>
            </li>
            <?php }?>
        <?php }?>    
        
        
         <li>
                <span><img src="<?php echo $orInfo['imgurl']?>"></span>
                <em>团长：<?php echo $orInfo['receiver']?></em>
                <?php if($orInfo['openid']==$myopenid){?>
                <i contenteditable="true" id='changenum' tag='order' hao="<?php echo $orInfo['id']?>" style="color: #555;box-shadow: 0 0 1px 1px rgba(0,0,0,.6);"><?php echo $orInfo['pro_num']?></i>
                <?php }else{?>
                <i><?php echo $orInfo['pro_num']?></i>
                <?php }?>
         </li>
        <?php foreach($receivers as $v){?>
        <?php if($v['openid']!=$myopenid){?>
            <li>
                <span><img src="<?php echo $v['imgurl']?>"></span>
                <em>团员：<?php echo $v['receiver']?></em>
                <i><?php echo $v['pro_num']?></i>
            </li>
            <?php }?>
        <?php }?>    
        </ul>
        <div class="Tsubmit boxsizing webkitbox"><span>总共<i id='totalnum'><?php echo $orInfo['num_total']?></i><?php echo $proInfo['unit']?></span>
        <?php if(Yii::$app->session['openid']==$orInfo['openid']){?>
        <?php if($orInfo['num_total']>=$proInfo['qiding_num'] && $orInfo['num_total']%$proInfo['pack_num']==0){?>
        <a id='paybutton' href="<?php echo Url::to(['grouppay','orderid'=> $orInfo ['id']]);?>" style="background: #fe9402;color: #ffffff;">团购成功，去付款</a>
        <?php }else{?>
        <a id='paybutton' href="#">团购成功，去付款</a>
        <?php }?>
        <?php }?>
        </div>
    </div>

    <div class="Trules">
        <div class="PlayTop clearfix"><span>规则详情</span></div>
        <div class="RuleCont boxSizing">
            <div class="RuleTell boxSizing">
                <h2>第一： 拼团说明</h2>
                <p>1.请先在商城选择需要购买的产品</p>
                <p>2.选中产品后发起团购，团长与团员输入自己的需球数量点击确认购买。</p>
                <p>3.达到团购发货的整件数量后团购成功，团长确认支付订单即可。</p>
                <p>4.订单成功后，产品发货。</p>
                <p>5.拼团不成功是指未达到整件发货数量，订单将不能支付。</p>
                <h2>第二：如何拼团</h2>
                <p>点击“开团”并参团确认需求数量，然后把你开的团的链接发给朋友或者分享到朋友圈，朋友继续参团，达到团购人数即可成团。不达到团购人数的订单将在团购结束后自动关闭，请知晓！</p>
            </div>
            <div class="TellSend"><img src="/static/mobile/images/tell.jpg"></div>
        </div>
    </div>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/swiper.min.js"></script>
<script src="/static/mobile/js/alert.js"></script>
<script src="/static/mobile/js/MeTool.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
$(function(){
	var s_title = '<?php echo $proInfo['proname']?>';
	var s_imgUrl = '<?php echo 'http://' . $_SERVER ['HTTP_HOST'].$proInfo['pic'][1]?>';
	var s_desc = '<?php echo $proInfo['desc']?>';
	var s_link = '<?php echo 'http://' . $_SERVER ['HTTP_HOST'].Url::toRoute(['offered','orderid'=>$orInfo['id']]);?>';
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


<script>
$(function() {
        function weixin(){
            var clientWidth = $(document).innerWidth();
            var clientHight = $(document).innerHeight();
            var fenxiang = $('<div class="fenxiang"><div id="wxyindao"></div></div>');
            fenxiang.appendTo('body');
            var wxyindao = $('#wxyindao');
            var wxWidth = wxyindao.width();
            var wxHight = wxyindao.height();
            fenxiang.innerWidth(clientWidth);
            fenxiang.innerHeight(clientHight);
            wxyindao[0].style.left = (document.documentElement.clientWidth - wxWidth)/2 + 'px';
            wxyindao[0].style.top = '0px';
            fenxiang.click(function(e){
                $(this).remove();
            });
        };
        $('.PriceOrder dd').click(function () {
            weixin();
        });
    });
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
</script>
<script>
 //    var mytag=1;
    $('#changenum').on('input',{value : null}, function (e) {
        if($(this).text().length == 6){
            e.data.value = $(this).text();
        }
   	 if(!(/^\d*$/g).test(this.innerHTML)){
	    	this.innerHTML = this.innerHTML.replace(/\D/g, '');
	        $(this).tanchu('请输入数字');
	        return;
	    }
        if($(this).text().length > 6){
            $(this).text(e.data.value);
            $(this).tanchu('最多只能输入6位');
            return;
        }
        
        var tag=$(this).attr('tag');
        var hao=$(this).attr('hao');
        var orderid='<?php echo $orInfo['id']?>';
        var proNum=$(this).html();
        var price='<?php echo $proInfo['bargain_price']?>';
        var qiding='<?php echo $proInfo['qiding_num']?>';
        var pack='<?php echo $proInfo['pack_num']?>';
        var myopenid='<?php echo $myopenid?>';
        var orderopenid='<?php echo $orInfo['openid']?>';
        var changetag=1;
        $.post("<?php echo Url::toRoute('changenum');?>",{tag:tag,hao:hao,orderid:orderid,proNum:proNum,price:price},function(data){
                   var res= JSON.parse(data); 
        	       $('#totalnum').html(res.num_total);

        	//       alert(myopenid+','+orderopenid);   return false;
                  if(myopenid==orderopenid){
        	       if(parseInt(res.num_total)>=parseInt(qiding) && (res.num_total)%pack==0 ){
                            $('#paybutton').remove();
                            $('.Tsubmit').append('<a id="paybutton" href="<?php echo Url::to(["grouppay","orderid"=> $orInfo ["id"]]);?>" style="background: #fe9402;color: #ffffff;">团购成功，去付款</a>');
            	       }else{
            	    	    $('#paybutton').remove();
            	    	    $('.Tsubmit').append('<a id="paybutton" href="#">团购成功，去付款</a>');
                	       }
                  }
           })
        
        var writeNum=$('#proNum').html();
        var writeunit='<?php echo $proinfo['unit']?>';
        var writeprice='<?php echo $proinfo['bargain_price']*10?>';
        $('#buyNum').html(writeNum+writeunit);
        $('#totalmoney').html('<i>积分</i>'+writeprice*writeNum);  
    });

         
          var orid='<?php echo $orInfo['id']?>';
          $(function() {
        	  setTimeout(function(){
            	  $.post("<?php echo Url::toRoute('orderchange');?>",{orderid:orid},function(res){
              		 
          	  })           
                  },9500); 
             
        	  setInterval(function(){
            	  var orId='<?php echo $orInfo['id']?>';
            	  $.post("<?php echo Url::toRoute('checkchange');?>",{orId:orId},function(data){
              	      if(data==2){
                  	      
               	    	 location.reload();
                  	      
                  	      }
                 })              
                  }, 10000);  
              })          
</script>