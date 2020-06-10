<?php 
use yii\helpers\Url;
?>
<section class="MyOrderCont DetailsColor">
    <h1 class="none_">邀请团员</h1>
    <div class="Purchase">
        <span>购买说明</span>
    </div>
    <div class="fahuoTell boxSizing">
        <div class="TellCont">
            产品成件发货，<?php echo $proinfo['pack_num'].$proinfo['unit']?>成件，<?php echo $proinfo['qiding_num'].$proinfo['unit']?>起订，购买数量不是成件数量的倍数或者没有达到起订数，无法正常购买！！！！
        </div>
    </div>
    <div class="acceptAddress boxSizing">
        <div class="addressButton boxSizing">点此填写收货地址</div>
        <div class="HideAddress boxSizing">
            <div class="addressTitle">收货信息</div>
            <div class="addressCont boxSizing">

            </div>
        </div>
    </div>
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>我的订单</span></div>
        <div class="DDBot">
            <dl class="dlDiv linebot boxSizing webkitbox clearfix">
                <dt><img src="<?php echo $proinfo['pic']?>"></dt>
                <dd>
                    <div class="p1"><?php echo $proinfo['proname']?></div>
                    <div class="p3 webkitbox boxSizing">
                    <?php if($proinfo['id']==149 || $proinfo['id']==150){?>
                        <span class="sp"><b>&yen;</b><?php echo $proinfo['bargain_price']?></span>
                        <?php }else{?>
                        <span class="sp"><b>积分</b><?php echo $proinfo['bargain_price']*10?></span>
                         <?php }?>
                        <span class="i"></span>
                        <div class="em webkitbox">
                            <i class="i1" id="jian"></i>
                            <i class="i2" id='proNum' contenteditable="true"><?php echo $proNum?></i>
                            <i class="i3" id="jia"></i>
                        </div>
                    </div>
                </dd>
            </dl>
        </div>
    </div>
<!--    **开票**
<div class="DDBianhao">
        <div class="PlayTop clearfix"><span>发票信息</span></div>
        <div class="fapiaoCont boxSizing clearfix">
            <span class="cur" val=2>@不开票</span>
            <span val=1>@开票</span>
        </div>
    </div>    
    -->
    <div class="allMessage boxSizing">
        <div class="MessDiv boxSizing">
            <dl class="clearfix">
            <?php if($proinfo['id']==149 || $proinfo['id']==150){?>
                <dt>商品金额</dt>
                <dd><i>&yen;</i><?php echo $proinfo['bargain_price']?>元</dd>
            <?php }else{?> 
                <dt>商品积分</dt>
                <dd><i>积分</i><?php echo $proinfo['bargain_price']*10?></dd>  
            <?php }?>         
            </dl>
            <dl class="clearfix">
                <dt>购买数量</dt>
                <dd id='buyNum'><?php echo $proNum.$proinfo['unit']?></dd>
            </dl>
            <dl class="clearfix">
            <?php if($proinfo['id']==149 || $proinfo['id']==150){?>
                <dt>实际付款</dt>
                <dd id='totalmoney'><i>&yen;</i><?php echo $proinfo['bargain_price']*$proNum  ?>元</dd>
                <?php }else{?> 
                <dt>需要积分</dt>
                <dd id='totalmoney'><i>积分</i><?php echo $proinfo['bargain_price']*10*$proNum?></dd>
               <?php }?>   
            </dl>
        </div>
    </div>
    <div class="kaituanBT"><span onclick='tijiao();'>提交付款</span></div>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<!-- 遮罩层 -->
<div class="PopupLayer">
    <div class="SHMessage boxSizing">
        <ul class="SHDiv clearfix">
            <li>
                <span><img src="/static/mobile/images/shr.png">收货人</span>
                <em><input type="text" placeholder="名字" id='name' value='<?php echo $faninfo['realname']?>'></em>
            </li>
            <li>
                <span><img src="/static/mobile/images/sjhm.png">手机号码</span>
                <em><input type="text" placeholder="11位手机号" id='tel' value='<?php echo $faninfo['telphone']?>'></em>
            </li>
            <li>
                <span><img src="/static/mobile/images/xzdq.png">选择地区</span>
                <em><input type="text" readonly id="selectAddr" placeholder="地区信息" value='<?php echo $faninfo['province'].$faninfo['city']?>'></em>
            </li>
            <li>
                <span><img src="/static/mobile/images/xxdz.png">详细地址</span>
                <em><input type="text" placeholder="街道门牌信息" id='addr' value='<?php echo $faninfo['address']?>'></em>
            </li>
            <li>
                <span id='comtitle'><img src="/static/mobile/images/xxdz.png" >公司名称</span>
                <select name="company" id="company" onchange="changcompany();" style="width:150px;height:35px;">
                 <option value="" selected>请选择公司</option>
                <?php foreach($coms as $v){?>
                <option value="<?php echo $v['name']?>" ><?php echo $v['name']?></option>
                <?php }?>
                <option value="other" >其它</option>
                </select>
            </li>
            
        </ul>
        <div class="Tjbotton" >提交</div>
    </div>
    <div class="CancleAddr" id="closeDiv1"><i></i><i></i></div>
</div>

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

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/alert.js"></script>
<script src="/static/mobile/js/MeTool.js"></script>
<script type="text/javascript">
    $(function () {
        var Receipt = function () {
            var element = {
                PopupLayer: $('.PopupLayer'),
                ReadInput : $('#selectAddr'),
                zzLevel   : $('.ReceiptZZ'),
                selectDiv : $('.ReceiptDiv'),
                closeDiv  : $('#closeDiv2'),
                closeDiv1 : $('#closeDiv1'),
                SureBotton: $('.SureAddr'),
                addressButton : $('.addressButton'),
                MyOrderCont : $('.MyOrderCont'),
                Tjbotton : $('.Tjbotton'),
                addressCont : $('.addressCont'),
                HideAddress : $('.HideAddress')
            }

            var getHeight = function () {
                return  window.innerHeight ? window.innerHeight : document.documentElement.clientHeight
            }();

            element.Tjbotton.on('click', function () {

           	 var i=0; 
             var flag=1;
             $('.SHDiv').find('li').each(function(){
                if(!($(this).find('input').val())){
                         switch(i){
                             case 0:
                             	$().tanchu('收货人不能为空！');
                             	flag=2;
                             break;
                             case 1:
                             	$().tanchu('手机号码不能为空！');
                             	flag=2;
                             break;
                             case 2:
                             	$().tanchu('选择地区不能为空！');
                             	flag=2;
                             break;
                             case 3:
                             	$().tanchu('详细地址不能为空！');
                             	flag=2;
                             break;
                            
                         }
                    }
                if(flag==2){return false;}
                      i++;
             });
             if(flag==2){return false;}
             var receiver=$('#name').val();
             if(!(/^[\u4E00-\u9FA5]{1,6}$/.test(receiver))){
             	flag=2;
         	    $().tanchu('请输入中文名称！');
                 }
             if(flag==2){return false;}
             var telphone=$('#tel').val();
             if(!(/^1[34578]\d{9}$/.test(telphone))){
                     flag=2;
             	    $().tanchu('不符合手机号码规范！');
                 }
             if(flag==2){return false;}
             
//              var address=$('#selectAddr').val()+$('#addr').val();
//              if(!(/^[\u4e00-\u9fa5a-zA-Z0-9]+$/.test(address))){
//                  flag=2;
//          	    $().tanchu('不符合地址规范！');
//              }
//              if(flag==2){return false;}

             var companyname=$('#company').val();
             if(companyname==''){
            	 flag=2;
           	    $().tanchu('公司名不能为空！');
                 }
             if(flag==2){return false;}
             if(!(/^[\u4e00-\u9fa5a-zA-Z0-9]+$/.test(companyname))){
                 flag=2;
         	    $().tanchu('不符合公司名称规范！');
             }
             if(flag==2){return false;}
                
                element.PopupLayer.hide();
                element.MyOrderCont.show();
                var li = element.PopupLayer.find('li'), html = '';
                li.each(function (i,v) {
                    if(this.getElementsByTagName('select').length > 0){
                        var select = this.getElementsByTagName('select')[0];
                        var option = select.getElementsByTagName('option');
                        html += select.selectedIndex == 0 ? $(this).children('span').text() + '<br>' : $(this).children('span').text() + ':&nbsp;' + option[select.selectedIndex].text + '<br>';
                    }else{
                        html += $(this).children('span').text() + ':&nbsp;' + $(this).children('em').children('input').val() + '<br>';
                    }
                });
                element.addressCont.html(html);
                element.HideAddress.show();
            });

            element.addressButton.on('touchend', function () {
                element.PopupLayer.show();
                element.MyOrderCont.hide();
            })

            element.ReadInput.on('touchend', function () {
                var getScrollTop = document.body.scrollTop;
                element.zzLevel.css('height',getHeight + getScrollTop).show();
                var height = element.selectDiv.innerHeight();
                element.selectDiv.show();
                element.selectDiv.css('top',(getHeight - height) / 2 + getScrollTop)
                element.MyOrderCont.hide();
            });

            element.zzLevel.add(element.selectDiv).on('touchmove', function (e) {
                e.preventDefault();
            });

            element.closeDiv.on('click', function () {
                element.zzLevel.hide();
                element.selectDiv.hide();
                element.MyOrderCont.show();
            });
            element.closeDiv1.on('click', function () {
                element.PopupLayer.hide();
                element.MyOrderCont.show();
            });

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
            })
        }();
    })

    $(function () {
        var jianNum = $('.i1'),addeNum = $('.i3'),price='<?php echo $proinfo['bargain_price']?>',unit='<?php echo $proinfo['unit']?>';
        var productId='<?php echo $proinfo['id']?>';
        jianNum.on('touchend', function () {
            var ordeNum = $(this).next();
            var textNum = ordeNum.text(),num;
            if(textNum <= 1){
                return false;
            }
            num = Number(textNum);
            num--;
            ordeNum.text(num);
            $('#buyNum').html(num+unit);
            if(productId==149 || productId==150){
            $('#totalmoney').html('<i>&yen;</i>'+(price*num).toFixed(2)+'元');
            }else{
            $('#totalmoney').html('<i>积分</i>'+price*10*num);
                }
        });
        addeNum.on('touchend', function () {
            var ordeNum = $(this).prev();
            var textNum = ordeNum.text(),num;
            num = Number(textNum);
            num++;
            ordeNum.text(num);
            $('#buyNum').html(num+unit);
            if(productId==149 ||productId==150){
                $('#totalmoney').html('<i>&yen;</i>'+(price*num).toFixed(2)+'元');
            }else{
            	$('#totalmoney').html('<i>积分</i>'+price*10*num);
                }
        })
    });

    $(function () {
        $('.fapiaoCont').children('span').click(function(i){
       	    $(this).siblings().removeClass('cur'); 
       	    $(this).addClass('cur');
            })
   })
    
</script>
<script>
     var tag=1;
     function tijiao(){
         var packnum='<?php echo $proinfo['pack_num']?>';
         var qidingnum='<?php echo $proinfo['qiding_num']?>';
         var prounit='<?php echo $proinfo['unit']?>';
    	 var pronum=$('#proNum').html();
    	 
         if($('.HideAddress').css("display")=='none'){
        	 $().tanchu('请完善地址信息！');  return false;
             }
         
         if(parseInt(pronum)<parseInt(qidingnum)){
        	 $().tanchu('商品还差'+(parseInt(qidingnum)-parseInt(pronum))+prounit+'起订！');  return false;
             }

         if(parseInt(pronum)>=parseInt(qidingnum) && parseInt(pronum)%parseInt(packnum)!=0){
        	 $().tanchu('商品还差'+(parseInt(packnum)-(parseInt(pronum)%parseInt(packnum)))+prounit+'整件发货！');  return false;
             }
         var receiver=$('#name').val();
         var telphone=$('#tel').val();
         var area=$('#selectAddr').val();
         var address=$('#addr').val();
         var pronum=$('#proNum').html();
         var companyname=$('#company').val();
   //      var kaipiao=$('.fapiaoCont').children('span').filter(".cur").attr('val');
         var proid=<?php echo $proinfo['id']?>;
         var proprice=<?php echo $proinfo['bargain_price']?>;
         if(tag==1){
         tag=2;
         $.post("<?php echo Url::toRoute('createbuyorder');?>",{receiver:receiver,
                                                                telphone:telphone,
                                                                address:address,
                                                                area:area,
                                                                proid:proid,
                                                                pronum:pronum,
                                                                proprice:proprice,
                                                                companyname:companyname
                                                                },
                          function(data){
                                  
                             //     alert(data);
                                  location.href=data;
                          })
         }
     }

</script>

<script>

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

     addEvent($('#proNum').get(0), 'input', function () {
         if(!(/^\d*$/g).test(this.innerHTML)){
         	this.innerHTML = this.innerHTML.replace(/\D/g, '');
             $(this).tanchu('请输入数字');
             return;
         }
         var writeNum=$('#proNum').html();
         var writeunit='<?php echo $proinfo['unit']?>';
         var writeprice='<?php echo $proinfo['bargain_price']?>';
         var writeproId='<?php echo $proinfo['id']?>';
         $('#buyNum').html(writeNum+writeunit);
         if(writeproId==149 || writeproId==150){
         $('#totalmoney').html('<i>&yen;</i>'+(writeprice*writeNum).toFixed(2)+'元');
         }else{
        	 $('#totalmoney').html('<i>积分</i>'+writeprice*10*writeNum);
             }
     })
     
      function changcompany(){
             if($('#company').val()=='other'){
                $('#company').remove();
                $('#comtitle').after('<em><input type="text" id="company" placeholder="公司名称" value=""></em>');
                 }
         }
</script>

