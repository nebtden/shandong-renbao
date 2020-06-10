
<section class="MyOrderCont overFlow DetailsColor">
    <div class="FriendTitle huozMarg boxSizing webkitbox">
        <p></p><div class="text">订单详情</div><p></p>
    </div>
    <div class="messUl">
        <ul class="ulDlayer clearfix">
            <li>
                <span>订单状态</span>
                <?php if($order['pay_status']==1){?>
                <em>已付款</em>
                <?php }?>
                <?php if($order['pay_status']==0){?>
                <em>未付款</em>
                <?php }?>
            </li>
            <li>
                <span>发货状态</span>
                <?php if($order['ship_status']==1){?>
                <em>已发货</em>
                <?php }?>
                 <?php if($order['ship_status']==0){?>
                <em>未发货</em>
                <?php }?>
            </li>
            <li>
                <span>订单号</span>
                <em><?php echo $order['order_code']?></em>
            </li>
            <li>
                <span>下单时间</span>
                <em><?php  echo date('Y-m-d H:i:s',$order['create_time'])?></em>
            </li>
        </ul>
    </div>
    <div class="FriendTitle huozMarg boxSizing webkitbox">
        <p></p><div class="text">收货信息</div><p></p>
    </div>
    <div class="messUl">
        <ul class="ulDlayer clearfix">
            <li>
                <span>收货地址</span>
                <em><?php echo $order['address']?></em>
            </li>
            <li>
                <span>收货人</span>
                <em><?php echo $order['receiver']?></em>
            </li>
            <li>
                <span>联系电话</span>
                <em><?php echo $order['telphone']?></em>
            </li>
        </ul>
    </div>
    <?php if($order['order_status']==2 || $order['order_status']==3){?>
    <div class="FriendTitle huozMarg boxSizing webkitbox">
        <p></p><div class="text">物流信息</div><p></p>
    </div>
    <div class="messUl">
        <ul class="ulDlayer clearfix">
            <li>
                <span>配送公司</span>
                <em><?php echo $companyinfo['name']?></em>
            </li>
            <li>
                <span>配送单号</span>
                <em><?php echo $order['ship_code']?></em>
            </li>
            <li>
                <span>物流电话</span>
                <em><?php echo $companyinfo['telephone']?></em>
            </li>
        </ul>
    </div>
    <?php if($order['type']==2){?>
     <div class="FriendTitle huozMarg boxSizing webkitbox">
        <p></p><div class="text">团员收货</div><p></p>
    </div>
    <div class="messUl">
     <ul class="ulDlayer clearfix">
     <li>
     <span>收货人</span>
     <em>数量</em>
     <em>收货状态</em>
     </li>
        <?php foreach($receivers as $v){?>
            <li>
                <span><?php echo $v['receiver']?></span>
                <em><?php echo $v['pro_num']?></em>
                <em><?php if($v['status']==1){
                	echo '未收货';
                }else{
                	echo '已收货';
                }?></em>
            </li>
            <?php }?>
        </ul>
        </div>
        <?php }?>
    <?php }?>
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>

