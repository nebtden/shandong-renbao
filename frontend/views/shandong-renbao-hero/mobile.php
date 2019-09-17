
<div class="getbag-bg">
    <h1 class="hide">猜英雄 赢大奖</h1>
    <div for="" class="getbag-con1">
        <input type="number" class="vct-number" placeholder=" 请输入手机号码" >
        <input type="text"   class="vct-import" placeholder=" 输入验证码">
        <a href="javascript:;" class="vct-btn"></a>
    </div>
    <a  href="javascript:;" class="scroe-btn submit" >
        开始抽奖
    </a>

</div>
<!-- 弹窗 -->
<div  class="pop" id="pop_per">
    <div class="pop_cont3">
        <div class="pop-con1">
            <b class="lootey">
                恭喜您<br/>
                本次抽奖获得
            </b>
            <span class="ai-barcode"></span>
            <h2></h2>
            <a href="rewards.html" class="pop-btn use" >去使用</a>
            <a href="javascript:popShow('pop1');"  class="pop-btn pop-btn1">邀朋友一起来PK</a>

        </div>
        <a href="javascript:popHide();" title="点击关闭" class="btn close gtbcls"> </a>
    </div>
</div>

<div class="pop1" id="pop1">
    <div class="pop1-zz">

    </div>
    <img src="/frontend/web/shandong-renbao-hero/images/jt.png" alt="箭头">
    <p>邀请好友一起来抽奖</p>
</div>

<script src="/frontend/web/shandong-renbao-hero/js/show.js"></script>
<script>
    $(function () {
        $('.vct-btn').click(function () {
            var mobile = $('.vct-number').val();
            console.log(mobile);
            //检测手机号码
            if(!(/^1[3456789]\d{9}$/.test(mobile))){
                alert("手机号码有误，请重填");
                return false;
            }

            $.post('code.html',{mobile:mobile},function (data) {
                // console.log();
                if(data.status==1){
                    alert('短信发送成功！');
                } else if(data.status==-1){
                    alert(data.msg);
                    $('h2').text(data.data.rewards);
                    popShow('pop_per');

                }else{
                    alert(data.msg);
                }
            },'json');
        });

        $(".submit").click(function () {
            var mobile = $('.vct-number').val();
            console.log(mobile);
            var code = $('.vct-import').val();

            if(!(/^1[3456789]\d{9}$/.test(mobile))){
                alert("手机号码有误，请重填");
                return false;
            }

            if(code.length!=6){
                alert("验证码错误,请检查！");
                return false;
            }

            $.post('submit.html',{mobile:mobile,code:code},function (data) {

                if(data.status==1){
                    // window.location.href = 'result.html?id='+data.data.id;
                    if(data.data.rewards_id==0){
                        $('.lootey').html('非常遗憾<br>您未中奖');
                        $('h2').html('下次活动28号<br>不见不散');
                        $('.use').hide();
                    }else{
                        $('h2').text(data.data.rewards);
                    }
                    popShow('pop_per');

                }else if(data.status==-1){
                    alert(data.message);
                    if(data.data.rewards_id==0){
                        $('.lootey').html('非常遗憾<br>您未中奖');
                        $('h2').html('下次活动28号 <br> 不见不散');
                        $('.use').hide();
                    }else{
                        $('h2').text(data.data.rewards);

                    }
                    popShow('pop_per');
                }else{
                    alert(data.message);
                }
            },"json");
        });
    });


</script>
