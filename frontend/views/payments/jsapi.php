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
                var type = '<?php echo $third;?>'
                if (type == 'pay') {
                    location.href = '<?php echo "http://www.yunche168.com/frontend/web/pay/index.html?id=12";?>';
                }else if(type=='product'){
                    location.href='<?php echo "http://buyybn.yunche168.com/frontend/web/mobile/product/order.html";?>';
                } else if(type=='shenzhen_guoshou'){
                    location.href='<?php echo "http://buyybn.yunche168.com/frontend/web/mobile/shenzhen-guoshou/zhanglili.html?type=shenzhen_guoshou&pro_id=$product_id";?>';
                }else {
                    location.href = '<?php echo Url::toRoute('/mall/myorder');?>';
                }
//  				WeixinJSBridge.log(res.err_msg);
 				alert(res.err_code+res.err_desc+res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok" ){
                     location.href = '<?php echo "http://www.yunche168.com/frontend/web/pay/index.html?id=12";?>';
					 location.href='<?php echo Url::toRoute('/mall/myorder');?>';
				 }else{
					 alert("抱歉，微信支付失败!");

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