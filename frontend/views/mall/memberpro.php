<?php 
use yii\helpers\Url;
?>
<section class="MyOrderCont DetailsColor">
     <div class="DDBianhao">
        <div class="tijiaoTell boxSizing">
            <div class="TellCont">
                产品成件发货，<?php echo $proInfo['pack_num'].$proInfo['unit']?>成件，<?php echo $proInfo['qiding_num'].$proInfo['unit']?>起订，购买数量不是成件数量的倍数或者没有达到起订数，无法正常购买！！！！
            </div>
        </div>
    </div>

    <div class="singleSP">
        <div class="DDBot paddBot0">
            <dl class="dlDiv linebot boxSizing webkitbox clearfix">
                <dt><img src="<?php echo $proInfo['pic']?>"></dt>
                <dd>
                    <div class="p1"><?php echo $proInfo['proname']?></div>
                    <div class="p3 webkitbox boxSizing">
                    <?php if($proInfo['id']==149 ||$proInfo['id']==150){?>
                        <span class="sp" style="font-size:16px;"><b>&yen;</b><?php echo $proInfo['bargain_price']?></span>
                        <?php }else{?>
                        <span class="sp" style="font-size:16px;"><b>积分</b><?php echo $proInfo['bargain_price']*10?></span>
                        <?php }?>
                        <span class="i"></span>
                        <div class="em webkitbox">
                            <i class="i1"></i>
                            <i class="i2" id="proNum" contenteditable="true">3</i>
                            <i class="i3"></i>
                        </div>
                    </div>
                </dd>
            </dl>
        </div>
    </div>
    <?php if($tpl==1){?>
    <div style="margin-left:40px;margin-top:10px;height:30px;">
    <span>姓名：</span>
    <span>
    <?php if($faninfo['realname']){?>
    <input type="text" style="height: 25px;border: none;font-size: 1rem;line-height: 25px;" id='name' value='<?php echo $faninfo['realname']?>'/>
    <?php }else{?>
    <input type="text" style="height: 25px;border: none;font-size: 1rem;line-height: 25px;" id='name' />
    <?php }?>
    </span>
    </div>
     <div style="margin-left:40px;margin-top:10px;height:30px;">
    <span>手机：</span>
    <span>
    <?php if($faninfo['telphone']){?>
    <input type="text" style="height: 25px;border: none;font-size: 1rem;line-height: 25px;" id='tel' value='<?php echo $faninfo['telphone']?>'/>
    <?php }else{?>
    <input type="text" style="height: 25px;border: none;font-size: 1rem;line-height: 25px;" id='tel' />
    <?php }?>
    </span>
    </div>
    <?php }?>
    
    <?php if($tpl==2){?>
    <div style="margin-left:40px;margin-top:10px;height:30px;">
    <span>姓名：</span>
    <span>
    <?php echo $receiver['receiver']?>
    </span>
    </div>
     <div style="margin-left:40px;margin-top:10px;height:30px;">
    <span>手机：</span>
    <span>
    <?php echo $receiver['telphone']?>
    </span>
    </div>
   
    <?php }?>
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>收货人信息</span></div>
        <div class="tijiaoTell boxSizing">
            <div class="TellCont">
          
        <?php echo $orInfo['receiver'].'&nbsp;&nbsp;'.$orInfo['telphone'].'&nbsp;&nbsp;'.$orInfo['address']?>        
            </div>
        </div>
    </div>
    <div class="kaituanBT"><span onclick="tijiao();">提交订单</span></div>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/alert.js"></script>
<script src="/static/mobile/js/MeTool.js"></script>
<script type="text/javascript">
   

    $(function () {
        var jianNum = $('.i1'),addeNum = $('.i3');
        jianNum.on('touchend', function () {
            var ordeNum = $(this).next();
            var textNum = ordeNum.text(),num;
            if(textNum <= 1){
                return false;
            }
            num = Number(textNum);
            num--;
            ordeNum.text(num);
        });
        addeNum.on('touchend', function () {
            var ordeNum = $(this).prev();
            var textNum = ordeNum.text(),num;
            num = Number(textNum);
            num++;
            ordeNum.text(num);
        })
    });
    var tj=1;
    function tijiao(){
             var flag=1;
             var proNum=$('#proNum').html();
             var name=$('#name').val();
             var telphone=$('#tel').val();
             var tpl='<?php echo $tpl ?>';
             if(tpl==1){
	             if(name==''){
	                 $().tanchu('姓名不能为空！');
	                 return false;
	             }
	
	             if(telphone==''){
	                 $().tanchu('手机不能为空！');
	                 return false;
	             }
	             
	             if(!(/^[\u4E00-\u9FA5]{1,6}$/.test(name))){
	         	     $().tanchu('请输入中文名称！');
	          	     return false;
	             }
	             
	             if(!(/^1[34578]\d{9}$/.test(telphone))){
	             	   $().tanchu('不符合手机号码规范！');
	              	   return false;
	             }
             }
        
             
            if(tj==1){
             tj=2;
             $.post("<?php echo Url::toRoute('ajaxmemberpro');?>",
                     {
                      proNum:proNum,
                      proPrice:<?php echo $proInfo['bargain_price']?>,
                      orderId:<?php echo $orInfo['id']?>,
                      orderCode:'<?php echo $orInfo['order_code']?>',
                      name:name,
                      telphone:telphone
                      },
                      function(data){
                       //    console.log(data); 
            	           location.href=data;
             })
            }
        }

    addEvent($('#proNum').get(0), 'input', function () {
        if(!(/^\d*$/g).test(this.innerHTML)){
        	this.innerHTML = this.innerHTML.replace(/\D/g, '');
            $(this).tanchu('请输入数字');
            return;
        }
    })
</script>
