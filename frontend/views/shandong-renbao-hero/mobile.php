
<div class="getbag-bg">
    <h1 class="hide">猜英雄 赢大奖</h1>
    <div for="" class="getbag-con1">
        <input type="number" class="vct-number" placeholder="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 请输入手机号码" >
        <input type="text"   class="vct-import" placeholder="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 输入验证码">
    </div>
    <a  href="javascript:popShow('pop_per');" class="scroe-btn submit">
        领取礼包
    </a>

</div>
<!-- 弹窗 -->
<div  class="pop" id="pop_per">
    <div class="pop_cont3">
        <div class="pop-con1">
            <a href="javascript:;">去使用</a>
            <a href="javascript:popShow('pop1');" >邀朋友一起来PK</a>
            <p>好友获得礼品的同时<br/>
                将发放一份随机神秘礼给您</p>
        </div>
        <a href="javascript:;" title="点击关闭" class="btn close gtbcls"> </a>
    </div>
</div>

<div class="pop1" id="pop1">
    <img src="./images/gbga-tc2.jpg" alt="">
</div>


<script>
    $(function () {
        $('.send').click(function () {
            var mobile = $('.vct-number').val();
            console.log(mobile);
            //检测手机号码
            if(!(/^1[3456789]\d{9}$/.test(mobile))){
                alert("手机号码有误，请重填");
                return false;
            }

            $.post('code.html',{mobile:mobile},function (data) {
                // console.log();
                if(data.status==0){
                    alert(data.msg);

                }else{
                    alert('短信发送成功！');
                }
            },'json');
        });

        $(".submit").click(function () {
            var mobile = $('.vct-number').val();
            console.log(mobile);
            var code = $('.code').val();

            if(!(/^1[3456789]\d{9}$/.test(mobile))){
                alert("手机号码有误，请重填");
                return false;
            }

            if(code.length!=6){
                alert("验证码错误,请检查！");
                return false;
            }

            $.post('submit.html',{mobile:mobile,code:code,license_plate:license_plate},function (data) {

                if(data.status==1){
                    window.location.href = 'result.html?id='+data.data.id;
                    popHide();

                }else{
                    alert(data.message);
                }
            },"json");
        });
    });


</script>
