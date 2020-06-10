<?php 
use yii\helpers\Url;
?>
 
    <?php if($status=='group'){?>
    <?php if($ordergroup2){?>
         <div style="margin-top: 10px;margin-left:10px;color:red;">团长订单</div>
    <?php foreach($ordergroup2 as $v){?>
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>订单号：<?php echo $v['order_code']?></span><a  href="<?php echo Url::toRoute(['orderdetail','orderid'=>$v['id']]);?>">订单详情</a></div>
        <div class="DDBot">
            <dl class="dlDiv boxSizing webkitbox">
                <dt><img src="<?php echo $proArr2[$v['pro_id']]['pic']?>"></dt>
                <dd>
                    <p class="p1"><?php echo $proArr2[$v['pro_id']]['proname']?></p>
                    <p class="p2 webkitbox boxSizing">
                    <?php if($v['pro_id']==149 || $v['pro_id']==150){?>
                        <span><b>&yen;</b><?php echo $proArr2[$v['pro_id']]['bargain_price']?></span>
                        <?php }else{?>
                        <span><b>积分</b><?php echo $proArr2[$v['pro_id']]['bargain_price']*10?></span>
                        <?php }?>
                        <i></i>
                        <em><?php echo $v['num_total']?></em>
                    </p>
                </dd>
            </dl>
        </div>
    </div> 
    <?php if($v['order_status']==0){?>
    <div style="background-color: #FFF;text-align:center;padding-Bottom:10px;"><a  onclick="cancel('<?php echo $v ['order_code']?>');" style="color:#aaa;">取消订单</a></div>
    <div class="acceptButton">
    <?php if($v['num_total']>=$proArr2[$v['pro_id']]['qiding_num'] && $v['num_total']%$proArr2[$v['pro_id']]['pack_num']==0){?>
    <span><a href="<?php echo Url::toRoute(['groupbuy','orderid'=>$v['id']]);?>" style="color:#fff;">组团成功，去付款</a></span>
    <?php }?>
    <?php if($v['num_total']<$proArr2[$v['pro_id']]['qiding_num'] ){?>
    <span><a href="<?php echo Url::toRoute(['groupbuy','orderid'=>$v['id']]);?>" style="color:#fff;">还差<?php echo ($proArr2[$v['pro_id']]['qiding_num']-$v['num_total']).$proArr2[$v['pro_id']]['unit'];?>组团成功</a></span>
    <?php }?>
    <?php if($v['num_total']>=$proArr2[$v['pro_id']]['qiding_num'] && $v['num_total']%$proArr2[$v['pro_id']]['pack_num']!=0){?>
    <span><a href="<?php echo Url::toRoute(['groupbuy','orderid'=>$v['id']]);?>" style="color:#fff;">还差<?php echo $proArr2[$v['pro_id']]['pack_num']-$v['num_total']%$proArr2[$v['pro_id']]['pack_num'].$proArr2[$v['pro_id']]['unit'];?>凑齐整件</a></span>
     <?php }?>
    </div>
    <?php }?>
    <?php if($v['order_status']==2){?>
    <div class="WLMess">
        <div class="PlayTop clearfix"><span>物流信息</span></div>
        <ul class="WLCont boxSizing">
            <li><span>配送公司:</span><em><?php echo $companyArr[$v['ship_name']]?></em></li>
            <li><span>配送单号:</span><em><?php echo $v['ship_code']?></em></li>
        </ul>
    </div>
    <div class="acceptButton">
        <span><a onclick="confirmorder(<?php echo $v ['id']?>);" style="color:#fff;">确认收货</a></span>
    </div>
     <?php }?>
     <?php if($v['order_status']==3){?>
          <div style="background-color: #FFF;text-align:center;padding-Bottom:10px;">订单已完成</div>
     <?php }?>
    <?php }?>
    <?php }?>
    <?php if($ordergroup1){?>
    <div style="margin-top: 10px;margin-left:10px;color:red;">团员订单</div>
    <?php foreach($ordergroup1 as $v){?>
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>订单号：<?php echo $v['order_code']?></span><a  href="<?php echo Url::toRoute(['orderdetail','orderid'=>$v['id']]);?>">订单详情</a></div>
        <div class="DDBot">
            <dl class="dlDiv boxSizing webkitbox">
                <dt><img src="<?php echo $proArr1[$v['pro_id']]['pic']?>"></dt>
                <dd>
                    <p class="p1"><?php echo $proArr1[$v['pro_id']]['proname']?></p>
                    <p class="p2 webkitbox boxSizing">
                    <?php if($v['pro_id']==149 || $v['pro_id']==150){?>
                        <span><b>&yen;</b><?php echo $proArr1[$v['pro_id']]['bargain_price']?></span>
                        <?php }else{?>
                        <span><b>积分</b><?php echo $proArr1[$v['pro_id']]['bargain_price']*10?></span>
                        <?php }?>
                        <i></i>
                        <em><?php echo $v['num_total']?></em>
                    </p>
                </dd>
            </dl>
        </div>
    </div>
      <?php if($v['order_status']==0){?>
     <div class="acceptButton">
     <?php if($v['num_total']>=$proArr1[$v['pro_id']]['qiding_num'] && $v['num_total']%$proArr1[$v['pro_id']]['pack_num']==0){?>
    <span><a href="<?php echo Url::toRoute(['groupbuy','orderid'=>$v['id']]);?>" style="color:#fff;">组团成功</a></span>
    <?php }?>
    <?php if($v['num_total']<$proArr1[$v['pro_id']]['qiding_num'] ){?>
    <span><a href="<?php echo Url::toRoute(['groupbuy','orderid'=>$v['id']]);?>" style="color:#fff;">还差<?php echo ($proArr1[$v['pro_id']]['qiding_num']-$v['num_total']).$proArr1[$v['pro_id']]['unit'];?>组团成功</a></span>
    <?php }?>
    <?php if($v['num_total']>=$proArr1[$v['pro_id']]['qiding_num'] && $v['num_total']%$proArr1[$v['pro_id']]['pack_num']!=0){?>
    <span><a href="<?php echo Url::toRoute(['groupbuy','orderid'=>$v['id']]);?>" style="color:#fff;">还差<?php echo $proArr1[$v['pro_id']]['pack_num']-$v['num_total']%$proArr1[$v['pro_id']]['pack_num'].$proArr1[$v['pro_id']]['unit'];?>凑齐整件</a></span>
     <?php }?>
    </div>
    <?php }?>
    <?php if($v['order_status']==3){?>
     <div class="WLMess">
        <div class="PlayTop clearfix"><span>物流信息</span></div>
        <ul class="WLCont boxSizing">
             <li><span>配送公司:</span><em><?php echo $companyArr[$v['ship_name']]?></em></li>
             <li><span>配送单号:</span><em><?php echo $v['ship_code']?></em></li>
        </ul>
    </div>
    <?php if($groupstatus[$v['order_code']][0]==1){?>
     <div class="acceptButton">
        <span><a onclick="confirmmemberorder(<?php echo $groupstatus[$v['order_code']][1]?>);" style="color:#fff;">确认收货</a></span>
    </div>
    <?php }else{?>
    <div class="acceptButton">
       <div style="background-color: #FFF;text-align:center;padding-Bottom:10px;"><a href="#">已收货</a></div>
       </div>
    <?php }?>
    <?php }?>
    <?php }?>
    <?php }?>
    
    
    
    <?php }else{?>
    <?php foreach($orders as $v){?>
    <div class="DDBianhao">
        <div class="PlayTop clearfix"><span>订单号：<?php echo $v['order_code']?></span><a  href="<?php echo Url::toRoute(['orderdetail','orderid'=>$v['id']]);?>">订单详情</a></div>
        <div class="DDBot">
           
            <?php foreach($orderproarr[$v['id']] as $value){?>
            <dl class="dlDiv boxSizing webkitbox">
                <dt><img src="<?php echo $proarr[$value['pro_id']]['pic']?>"></dt>
                <dd>
                    <p class="p1"><?php echo $proarr[$value['pro_id']]['proname']?></p>
                    <p class="p2 webkitbox boxSizing">
                        <?php if($value['pro_id']==149 ||$value['pro_id']==150){?>
                        <span><b>&yen;</b><?php echo $proarr[$value['pro_id']]['bargain_price']?></span>
                        <?php }else{?>
                        <span><b>积分</b><?php echo $proarr[$value['pro_id']]['bargain_price']*10?></span>
                        <?php }?>
                        <i></i>
                        <em><?php echo $value['pro_num']?></em>
                    </p>
                </dd>
            </dl>
            <?php }?>
        </div>
    </div> 
    <?php if($v['order_status']==0){?>
    <div style="background-color: #FFF;text-align:center;padding-Bottom:10px;"><a  onclick="cancelpersonal('<?php echo $v ['order_code']?>');" style="color:#aaa;">取消订单</a></div>
     <?php }?>
    <div class="acceptButton">
    <?php if($v['order_status']==0){?>
        <span><a href='<?php echo Url::toRoute(['/payment/jsapi','product_id'=> $v ['order_code']]);?>' style="color:#fff;">去支付</a></span>
        <?php }?>
    <?php if($v['order_status']==2){?>
        <span><a onclick="confirmorder(<?php echo $v ['id']?>);" style="color:#fff;">确认收货</a></span>
        <?php }?>    
    </div>
    <?php }?>
    <?php }?>
    <?php if(!$orders && $status!='group'){
           switch($status){
                 case 0:           	
                    echo '<div style="text-align:center;color:red;margin-top:20px;">没有待支付订单</div>';
                 break;
                 case 1:           	
                    echo '<div style="text-align:center;color:red;;margin-top:20px;">没有待发货订单</div>';
                 break;
                 case 2:
                 	echo '<div style="text-align:center;color:red;;margin-top:20px;">没有待收货订单</div>';
                 break;
                 case 3:
                 	echo '<div style="text-align:center;color:red;;margin-top:20px;">没有已完成订单</div>';
                 break;
           }    
     }?>
     <?php if(!$ordergroup2 && !$ordergroup1 && $status=='group'){
                 echo '<div style="text-align:center;color:red;;margin-top:20px;">没有团购订单</div>';
           }
           ?>