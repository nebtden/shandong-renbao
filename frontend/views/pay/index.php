<?php
use yii\helpers\Url;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php echo $pro_info['name']?></title>
    <link rel="stylesheet" href="/static/css/shopcommon.css" type="text/css">
    <link rel="stylesheet" href="/static/css/css.css" type="text/css">
    <link rel="stylesheet" href="/static/css/lCalendar.css" type="text/css">
    <link rel="stylesheet prefetch" href="/static/photoswipe/css/photoswipe.css">
    <link rel="stylesheet prefetch" href="/static/swiper.min.css">
    <link rel="stylesheet prefetch" href="/static/swiper.min.js">
    <link rel="stylesheet prefetch" href="/static/photoswipe/css/default-skin/default-skin.css">
    <style>
        .my-gallery img{
            display: block;
            width: 100%;
        }

        .swiper-container {
            width: 100%;
            height: 100%;
        }
        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;

            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }
        .dd2{
            width: 100%;
        }

    </style>

    <script src="/static//js/jquery-1.10.1.js"></script>
    <script src="/static//js/swiper.min.js"></script>
    <script src="/static//js/MeTool.js"></script>
    <script src="/static//js/complete.js"></script>
    <script src="/static//js/lCalendar.js"></script>
</head>

<body>
<section class="contentFull bgColor overFlow padbot40">
    <div class="shopPageContent">
        <div class="CarCont DDshopList boxSizing">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php foreach ($images as $image){ ?>
                        <div class="swiper-slide"> <img src="<?=$image?>" width="100%"></div>
                    <?php } ?>
                    <div class="swiper-pagination"></div>

                </div>
            </div>

            <dl class="dlDiv afterFour boxSizing webkitbox" pro_id='<?php echo $pro_info['id']?>'>

                <dd class="dd2">
                    <div class="p1"><?php echo $pro_info['name']?></div>

                    <div class="p3 boxSizing clearfix">
                        <span class="sp"><b>&yen;</b><?php echo $pro_info['price']?></span>
                        <div class="em webkitbox afterFour">
                            <i class="i1 minus"></i>
                            <input class="i2" value="5">
                            <!--                            <i class="i2">5</i>-->
                            <i class="i3 add"></i>
                            个
                        </div>

                    </div>
                </dd>
            </dl>
        </div>

        <div class="DDLiDiv afterFour P_No boxSizing">
            <p class="webkitbox">
                <span>商品总金额</span>
                <em>
                    <i class="i3">&yen;<i id="spMoney"><?php echo $pro_info['price']*5?></i></i>
                </em>
            </p>
        </div>
        <div class="company">
            <span>请填写以下必填信息</span>

            <br>
            <br>
            <div class=" select">
                <select name="company_id " id="company_id">
                    <option value="0" >请选择机构</option>
                    <?php foreach($companies as $k=>$company){?>
                        <option  value="<?= $company['id'] ?>"><?= $company['name'] ?></option>
                    <?php }?>
                </select>
            </div>
            <br>
            <br>
            <!--            <div class="phone " >
                            <input class="input" type="text" name="address" id="address" placeholder="请输入您的区域" value="">
                        </div>
                        <br>-->
            <div class="name " >
                <input class="input" type="text" name="name" id="name" placeholder="请输入您的姓名" value="">
            </div>
            <br>
            <!--            <div class="phone " >
                            <input class="input" type="text" name="phone" id="phone" placeholder="请输入您的手机号码" value="">
                        </div>
                        <br>-->
            <div class="phone " >
                <input class="input" type="text" name="code" id="code" placeholder="请输入您的代码" value="">
            </div>
            <br>

            <br>

        </div>
    </div>
</section>

<footer class="DDSendDiv boxSizing clearfix">
    <span>需付款：<i>&yen;</i><b id="allMoney"><?php echo $pro_info['price']*5?></b></span>
    <button type="button" id="tijiao">支付</button>
</footer>
<input type="hidden" id="product_id"    value="<?php echo $pro_info['id']?>">
<input type="hidden" id="product_number" value="5">
<input type="hidden" id="product_price" value="<?php echo $pro_info['price']?>">
<script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<link rel="stylesheet" href="/static/css/pay.css">

<style>

</style>

<script type="text/javascript">
    console.debug();
    /*    var swiper = new Swiper('.swiper-container', {
            pagination: {
                el: '.swiper-pagination',
                dynamicBullets: true,
            },
        });*/

    function changeNum(number){
        $('#product_number').val(number);
        var price = $('#product_price').val();
        $('.i2').val(number);
        console.log(parseFloat(price));
        var total = number*parseFloat(price);
        $('#allMoney').text(total);
        $('#spMoney').text(total);
    }

    $(function () {
        $('.add').click(function () {
            var number = $('#product_number').val();
            number = parseInt(number)+1;
            changeNum(number);
        });

        // $('.i2').change(function () {
        //     var number = $(this).val();
        //     number = parseInt(number);
        //     changeNum(number);
        // });

        $('.i2').on('input propertychange',function () {
            var number = $(this).val();
            number = parseInt(number);
            changeNum(number);
        });



        $('.minus').click(function () {
            var number = $('#product_number').val();
            number = parseInt(number)-1;
            if(number==0){
                alert('最小需要一个商品');
                return false;
            }
            changeNum(number);
        });
    });
</script>
<script>
    var clicked = false;
    $('#tijiao').bind('click',function(){
        var product_id =   $('#product_id').val();

        var company_id =   $('select[name="company_id"]').val();
        var company_id =   $('#company_id').val();
        var number   =  $('#product_number').val();
        var name   =  $('#name').val();
        var phone   =  $('#phone').val();
        var code   =  $('#code').val();
        var address   =  $('#address').val();


        if(clicked){
            alert('您提交速度过快，请耐心等待');
            return false;
        }
        if(number<5){
            alert('数量必须大于等于5个！');
            return false;
        }
        // alert(company_id);
        if(company_id=="0"){

            alert('机构必须选择！');
            return false;
        }

        if(!name){
            alert('您必须输入自己的姓名');
            return false;
        }
        /*        if(!phone){
                    alert('您必须输入手机号');
                    return false;
                }*/
        if(code.length!=11){
            alert('代码长度为11位！');
            return false;
        }
        $.post('<?php echo Url::to(['save']); ?>',{
            product_id:product_id,number:number,company_id:company_id,
            name:name,code:code
        },function(res){
            if(res.status==1){
                location.href=res.data.url;
            }else{
                alert(res.message);
            }

        },'json')
    })
</script>
</body>
</html>
