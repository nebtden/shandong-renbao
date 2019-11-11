<div class="getbag-bg">
    <h1 class="hide">猜英雄 赢大奖</h1>
    <div class="getbag-con1">
        <input type="number" class="vct-number" placeholder="请输入手机号码" >
        <input type="text"   class="vct-import" placeholder="输入验证码">
        <a href="javascript:;" class="vct-btn"></a>
    </div>
    <a  href="javascript:;" data-url="" class="scroe-btn">
        查看礼包
    </a>
    <p class="getbag-p1">奖品将发送至所填手机号</p>

</div>
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


<script src="/frontend/web/shandong-renbao-army/js/index.js"></script>
<script>
    $(function () {
        // $('.scroe-btn').click(function () {
        //     if($(this).hasClass('disabled')){
        //         alert('请填写手机号码');
        //     }else{
        //         window.location.href = $(this).data('url');
        //     }
        // });


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
                    window.location.href = 'prize.html?id='+data.data.id;

                }else{
                    alert(data.msg);
                }
            },'json');
        });

        $(".scroe-btn").click(function () {
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
                    window.location.href = 'prize.html?id='+data.data.id;

                }else if(data.status==-1){
                    alert(data.msg);
                    window.location.href = 'prize.html?id='+data.data.id;
                }else{
                    alert(data.message);
                }
            },"json");
        });
    });


</script>