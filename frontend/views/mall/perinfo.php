<?php 
use yii\helpers\Url;
?>

<section class="MyOrderCont overFlow DetailsColor" style="padding: 10px">
    <div class="SHMessage SHPadbot20 boxSizing">
        <ul class="PerMessDiv clearfix">
            <li>
                <span><img src="/static/mobile/images/shr.png">姓名</span>
                <em><input type="text" placeholder="名字" id='name' value='<?php echo $cachinfo['name']?>'></em>
            </li>
            <li>
                <span><img src="/static/mobile/images/shr.png">性别</span>
                <em>
                    <select id='sex'>
                        <?php if($cachinfo['sex']==1){?>
                        <option val='1' selected='selected'>男</option>
                        <option val='2'>女</option>
                        <?php }else{?>
                        <option val='1' >男</option>
                        <option val='2' selected='selected'>女</option>
                        <?php }?>
                    </select>
                </em>
            </li>
			 <li>
                <span><img src="/static/mobile/images/yzbm.png">生日</span>
                <em><input type="text" id="lastYue" readonly="readonly" name="appDate" placeholder="2016年11月17日" value='<?php echo $cachinfo['birth']?>'></em>
            </li>
            <li>
                <span><img src="/static/mobile/images/sjhm.png">手机号码</span>
                <em><input id='tel' type="number" placeholder="11位手机号" value='<?php echo $cachinfo['phone']?>'></em>
                <i id='check'>验证</i>
            </li>
            <li>
                <span><img src="/static/mobile/images/sjhm.png">验证码</span>
                <em><input type="text" placeholder="请输入验证码" id='code'></em>
            </li>
            <li>
                <span><img src="/static/mobile/images/xzdq.png">选择地区</span>
                <em><input type="text" readonly id="selectAddr" placeholder="地区信息" value='<?php echo $cachinfo['area']?>'></em>
            </li>
            <li>
                <span><img src="/static/mobile/images/xxdz.png">详细地址</span>
                <em><input type="text" placeholder="街道门牌信息" id='detail' value='<?php echo $cachinfo['detail']?>'></em>
            </li>
           
        </ul>
        <div class="Tjbotton">确认</div>
    </div>
</section>

<div class="ReceiptDiv">
    <div class="ReceiptCont">
        <span>选择省:</span>
        <select name="sheng" id="sheng" onchange="changeshen();">
            <option value="" selected>请选择所在省份</option>
	        <?php foreach($areas as $v) { ?>
	        <option value="<?php echo $v['code']; ?>"  <?php if(strpos($v['name'],$myadd['province'])!==false) echo 'selected'; ?>><?php echo $v['name']; ?></option>
	        <?php } ?>
        </select>
        <span>选择市:</span>
        <select name="city" id="city">
             <?php foreach($city as  $v) {  ?>
	        <option value="<?php echo $v['code']; ?>"  <?php if(strpos($v['name'],$myadd['city'])!==false) echo 'selected'; ?>  ><?php  echo $v['name'];?></option>
	        <?php }?>
        </select>
    </div>
    <div class="SureAddr">确定</div>
    <div class="CancleAddr" id="closeDiv2"><i></i><i></i></div>
</div>
<div class="ReceiptZZ"></div>

<script type="text/javascript" src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/mobiscroll_002.js" type="text/javascript"></script>
<script src="/static/mobile/js/mobiscroll.js" type="text/javascript"></script>
<script src="/static/mobile/js/alert.js"></script>
<script type="text/javascript">
    $(function () {
        var element = {
            PopupLayer: $('.PerMessDiv'),
            ReadInput : $('#selectAddr'),
            zzLevel   : $('.ReceiptZZ'),
            closeDiv  : $('#closeDiv2'),
            MyOrderCont : $('.MyOrderCont'),
            selectDiv : $('.ReceiptDiv'),
            SureBotton: $('.SureAddr'),
        }
        var getHeight = function () {
            return  window.innerHeight ? window.innerHeight : document.documentElement.clientHeight
        }();

        element.SureBotton.on('click', function () {
            var sheng = element.selectDiv.find('select').filter(function (i,v) {
                        return $(this).attr('name') == 'sheng';
                    }),

                    city  = element.selectDiv.find('select').filter(function () {
                        return $(this).attr('name') == 'city';
                    });

            var index1 = sheng.get(0).selectedIndex,
                    index2 = city.get(0).selectedIndex,
                    option1 = sheng.find('option'),
                    option2 = city.find('option'),
                    shengValue = option1[index1].innerHTML,
                    cityValue = option2[index2].innerHTML;

            element.ReadInput.val(shengValue+cityValue)
            element.zzLevel.hide();
            element.selectDiv.hide();
            element.MyOrderCont.show();
        })

        element.closeDiv.on('click', function () {
            element.zzLevel.hide();
            element.selectDiv.hide();
            element.MyOrderCont.show();
        });

        element.ReadInput.on('click', function () {
            var getScrollTop = document.body.scrollTop;
            element.zzLevel.css('height',getHeight + getScrollTop).show();
            var height = element.selectDiv.innerHeight();
            element.selectDiv.show();
            element.selectDiv.css('top',(getHeight - height) / 2 + getScrollTop)
            element.MyOrderCont.hide();
        });

    })
</script>

<script>
        $('#check').click(function(){
                var telphone=$('#tel').val();
                if(!(/^1[34578]\d{9}$/.test(telphone))){
                        flag=2;
                	    $().tanchu('不符合手机号码规范！');
                	    return;
                    }else{
                   	 if($(this).data('yzm') == undefined){
                         var _this = this;
                         var num   = 59;
                         $(this).css('color','#ffffff').html(60 + 's').data('yzm',true);
                         var setTimer = setInterval(function () {
                             $(_this).text(num-- + 's');
                             if(num < 0){
                                 num = 59;
                                 $(_this).removeAttr('style').removeData('yzm').html('验证');
                                 clearInterval(setTimer);
                             }
                         },1000)
                     }else{
                         return false;
                     }
                    	$.post("<?php echo Url::toRoute('checktel');?>",{tel:telphone},function(s){
                   		 if(s==0) {
                    			$.get('<?php echo Url::toRoute('smsg');?>',{tel:telphone},function(){
                                    $().tanchu('验证码已发送');
                                });
                       		 }
                        	})
                        }
            });

        function changeshen(){
            var sheng=$('#sheng').val();
            $.post("<?php echo Url::toRoute('changsheng');?>",{sheng:sheng},function(data){
         	   var cities=jQuery.parseJSON(data);
         	   var str='';
         	   for(i in cities){
         		   str+='<option value="'+cities[i].code+'">'+cities[i].name+'</option>';
             	   }
         	   $('#city').html(str);
                })
          } 

        $('.Tjbotton').click(function(){
        	var i=0; 
            var flag=1;
           
           
            var name=$('#name').val();
            if(name==""){
            	flag=2;
         	    $().tanchu('姓名不能为空！');
                }
            if(flag==2){return false;}
            if(!(/^[\u4E00-\u9FA5]{1,6}$/.test(name))){
             	flag=2;
         	    $().tanchu('请输入中文名称！');
                 }
             if(flag==2){return false;}
             var telphone=$('#tel').val();
             if(telphone==""){
            	 flag=2;
           	     $().tanchu('手机号码不能为空！');
                 }
             if(flag==2){return false;}
             if(!(/^1[34578]\d{9}$/.test(telphone))){
                     flag=2;
             	    $().tanchu('不符合手机号码规范！');
                 }
             if(flag==2){return false;}
             var telcode=$('#code').val();
             if(telcode==""){
            	 flag=2;
           	     $().tanchu('验证码不能为空！');
                 }
             if(flag==2){return false;}
             var address=$('#selectAddr').val();
             if(address==""){
            	 flag=2;
           	     $().tanchu('地区不能为空！');
                 }
             if(flag==2){return false;}
             var detail=$('#detail').val();
             if(detail==""){
            	 flag=2;
           	     $().tanchu('详细地址不能为空！');
                 }
             if(flag==2){return false;}
             
             var sheng=$('#sheng').find("option:selected").text();
             var city=$('#city').find("option:selected").text();
             var code=$('#code').val();
             var sex=$('#sex').val();
             var birth=$('#lastYue').val();
          //   alert(birth); return false;
             $.post("<?php echo Url::toRoute('regedit');?>",{name:name,
                                                             telphone:telphone,
                                                             detail:detail,
                                                             sheng:sheng,
                                                             city:city,
                                                             code:code,
                                                             sex:sex,
                                                             birth:birth
                                                             },function(data){
                                                                
                                                            if(data='bupipei'){
                                                            	$().tanchu('验证码错误！');
                                                                }
                                                            if(data='pipei'){
                                                            	$().tanchu('注册成功，欢迎加入鼎翰易购！');
                                                            	
                                                           	 setTimeout(function () { 
                                                            		location.href='<?php echo Url::toRoute('catlist');?>';
                                                     	      }, 2000);
                                                                }
                 })
            });
</script>
<script>
			$(function () {
			    var currYear = (new Date()).getFullYear();
			    var opt={};
			    opt.date = {preset : 'date'};
			    opt.datetime = {preset : 'datetime'};
			    opt.time = {preset : 'time'};
			    opt.default = {
			        theme: 'android-ics light', //皮肤样式
			        display: 'modal', //显示方式
			        mode: 'scroller', //日期选择模式
			        dateFormat: 'yyyy年mm月dd日',
			        lang: 'zh',
			        showNow: true,
			        nowText: "今天",
			        startYear: currYear - 50, //开始年份
			        endYear: currYear  //结束年份
			    };
			    if($("#lastYue").mobiscroll){
			        $("#lastYue").mobiscroll($.extend(opt['date'], opt['default']));
			    }
			});

</script>
<script>
history.pushState({},'',location.href);
window.onpopstate = function(){
	var cachename=$('#name').val();
	var cachetelphone=$('#tel').val();
	var cachearea=$('#selectAddr').val();
    var code=$('#code').val();
    var cachesex=$('#sex').val();
    if(cachesex=='男'){
    	cachesex=1
        }else{
        cachesex=2
            }
    var cachebirth=$('#lastYue').val();
    var cachedetail=$('#detail').val();
    $.post("<?php echo Url::toRoute('cachedata');?>",{cachename:cachename,
											    	  cachetelphone:cachetelphone,
											    	  cachearea:cachearea,
											    	  cachesex:cachesex,
											    	  cachebirth:cachebirth,
											    	  cachedetail:cachedetail,
       												 },function(data){
           
                                                     })
}
       
</script>
