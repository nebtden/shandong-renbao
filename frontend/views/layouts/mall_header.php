<?php 
use yii\helpers\Url;
?>

<header class="shopHeader">
     <div class="swiper-container TGContainer">
        <div class="swiper-wrapper">
          <?php foreach($adinfo as $v){?>
            <div class="swiper-slide">
            <?php if($v['url']){?>
            <a href=<?php echo $v['url']?>><img src="/static/upfile/<?php echo $v['picurl']?>"></a>
            <?php }else{?>
            <a href='#'><img src="/static/upfile/<?php echo $v['picurl']?>"></a>
            <?php }?>
            </div>
            <?php }?>
        </div>
        <div class="swiper-pagination"></div>
        </div>
        <div class="shopCitySearch boxSizing webkitbox">
        <div class="searchLeft boxSizing">
            <input type="text" id="shopID" placeholder="搜索商品">
        </div>
        <ul class="on_changes boxSizing">
        </ul>
        <div class="searchRigh" onclick="search(this);">搜索</div>
    </div>
</header>
<div class="shopZZ">
    <!-- 热门搜索 -->
    <div class="hotShop boxSizing">
        <div class="tit">热门搜索</div>
        <ul class="clearfix">
            
            <?php 
            $key="tpykey";
            $rs = Yii::$app->cache->get($key);
  //          Yii::$app->cache->delete($key);
            if($rs){
            arsort($rs);
            $outrs= array_slice($rs, 0, 6);
            foreach($outrs as $k=>$v){
                echo '<li onclick="search(this,\''.$k.'\');"><a>'.$k.'</a></li>';   
            }
            }
            ?>
        </ul>
    </div>
</div>

<script>

$(function(){

	 var shopID = $('#shopID'), showHide = $('.showHide'), shopZZ = $('.shopZZ'), searchRigh = $('.searchRigh');
     var scrollTop = 0;
     shopID.on('focus', function () {
         if(shopZZ.is(':visible')){
             return;
         }
         scrollTop = $(window).scrollTop();
         shopZZ.show();
         searchRigh.text('取消').show();
         showHide.hide();
     }).on('input', function () {
         if((/^\s*$/g).test($(this).val())){
             searchRigh.text('取消');
         }else{
             searchRigh.text('搜索');
         }
     });
     searchRigh.on('click', function () {
         if($(this).text() == '取消'){
             shopZZ.hide();
             showHide.show();
             shopZZ.get(0).blur();
             searchRigh.hide();
             $(window).scrollTop(scrollTop);
         }
     });
     $("#shopID").changeTips({
         divTip:".on_changes"
     });
	
})

            
function search(obj,proname){
	if($(obj).text() == '取消') return false;
	if(!proname){
    	proname=$('#shopID').val();
	}
	if(proname){
    	$.post("<?php echo Url::toRoute('search');?>",{proname:proname},function(data){
	        if(data=='no'){
	            $('#nopro').remove();
	    	    $('.shopZZ').append('<div id="nopro" style="width:100%;z-index:9999;color:red;text-align:center;font-size:18px;margin-top:20px;">不好意思，没有你要搜索的产品！</div>');
	        }else{
        	//	location.href='/frontend/web/mobile/shop/prodetail.html?id='+data+'&search='+proname;
	        	var res=jQuery.parseJSON(data);
	        	$('.shopZZ').css('display', 'none'); 
	        	var str=' <dl class="ListTop clearfix color1">';
	                str+='<dt>搜索结果</dt>';
	                str+='<dd><a href="#">查看更多</a></dd>';
	                str+='</dl>';
	                str+='<div class="ulDiv webkitbox boxSizing">';
	                for(var i in res){
	                	    str+='<div class="dlDiv">';
		 	                str+='<a href="#">';
		 	                str+='<dl>';
		 	                str+='<dt><img src="'+res[i].pic+'"></dt>';
		 	                str+='<dd>';
		 	                str+='<p>'+res[i].proname+'</p>';
		 	                str+='<p>积分：<i>'+res[i].bargain_price*10+'</i></p>';
		 	                str+='</dd>';
		 	                str+='</dl>';
		 	                str+='</a>';
		 	                str+='</div>';    
			        	}
	                str+='</div>';
	        	
	        	$('.showHide').html(str);
	        	$('.showHide').css('display', 'block'); 
	        	
            }
        });
	}
}
</script>




