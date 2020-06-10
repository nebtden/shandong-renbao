<?php 
use yii\helpers\Url;
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微支付</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $pay['jsApiParameters']; ?>,
			function(res){
//  				WeixinJSBridge.log(res.err_msg);
//  				alert(res.err_code+res.err_desc+res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok" ){
					  var thd = '<?php echo $third;?>';
					  if(thd=='yes'){
						  location.href='http://mswxgj.minshenglife.com/mslife_wx_web/html/blessingYear17/pay_transfer.html?openId=<?php echo $order['third_openId'];?>&orderId=<?php echo $order['orderId'];?>';
						  //location.href='http://mswgj.minshenglife.com/mslife_wx_web/html/blessingYear17/pay_transfer.html?openId=<?php echo $order['third_openId'];?>&orderId=<?php echo $order['orderId'];?>';
					  }else if(thd=='yet'){
						  location.href='http://mswgj.minshenglife.com/mslife_wx_web/html/blessingYear17/pay_transfer.html?openId=<?php echo $order['third_openId'];?>&orderId=<?php echo $order['orderId'];?>';
					  }else{
						  location.href='<?php echo Url::toRoute('/mall/myorder');?>';
					  }
				 }else{
					 	alert("抱歉，微信支付失败!");
					 	history.back(1);
				 }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
</head>
<body>
<script type="text/javascript">
 window.onload = callpay();
</script>
</body>
</html>