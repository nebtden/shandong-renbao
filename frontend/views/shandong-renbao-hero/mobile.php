
<div class="bg-vcation">
    <input type="number"  class="vct-number mobile" placeholder="请输入手机号码">
    <div class="vct-hqbtn">
        <input type="text" placeholder="输入验证码" class="vct-import code" >
        <div class="vct-btn send">
            获取
        </div>
    </div>
    <a href="#" class="submit">
        <img src="/frontend/web/shandong-renbao/images/pg-btn1.png" alt="评估" class="vction-btn">
    </a>
    <p class="vct-p"> 已有<b><?=$total ?></b>人参与<br/>最终解释权归临沂人保财险所有</p>
</div>


<script>
    $(function () {
        $('.send').click(function () {
            var mobile = $('.mobile').val();
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
            var mobile = $('.mobile').val();
            var code = $('.code').val();
            var license_plate = "<?= $license_plate; ?>";
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
                }else{
                    alert(data.message);
                }
            },"json");
        });
    });


</script>
