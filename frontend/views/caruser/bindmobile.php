<?php

use yii\helpers\Url;

?>

<div class="my-header">
    <span><img src="<?= $user['headimgurl'] ?>" onerror="this.src='/frontend/web/cloudcar/images/dhlogo.png'"></span>
</div>
<div class="commom-input-list">
    <ul class="commom-input-ul">
        <li>
            <i class="icon-cloudCar2-shoujihaoma"></i>
            <input type="tel" pattern="[0-9]*" name="mobile" placeholder="请输入您的手机号码">
        </li>
        <li>
            <i class="icon-cloudCar2-yanzhengma-copy"></i>
<!--            <input type="tel" placeholder="请输入您的手机验证码">-->
            <input type="text" name="code" placeholder="请输入验证码">
            <button type="button" class="btn send-code " id="J_GetCode">发送验证码</button>
        </li>
    </ul>
</div>
<div class="send-comfirm commom-submit fillMember-submit ">
    <a class="btn-block btn-primary" href="javascript:;">确&nbsp;&nbsp;定</a>
</div>

<?php $this->beginBlock('script'); ?>
<script>
    var testMobile = function(m){
        var reg = /^1[0-9]{10}$/;
        return reg.test(m);
    };
    var $getCode = $('#J_GetCode');
    /* 定义参数 */
    $getCode.sendCode({
        disClass: 'btn-disabled ',
        secs: 59,
        run: false,
        runStr: '重新发送{%s}',
        resetStr: '重新获取'
    });
    $getCode.on('touchstart', function () {
        var mobile = $("input[name=mobile]").val();
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
        /* ajax 成功发送验证码后调用【start】 */
        YDUI.dialog.loading.open('发送中');
        $.post("<?php echo Url::to(['sendsms']);?>",{mobile:mobile},function(json){
            YDUI.dialog.loading.close();
            if(json.status == 1){
                $getCode.sendCode('start');
                YDUI.dialog.toast('验证码已发送', 'none', 1500);
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });
    //提交确认
    var isSubmit = false;
    $('.send-comfirm').on('touchstart',function(){
        console.log(1)
        if(isSubmit) return false;
        var mobile = $("input[name=mobile]").val();
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
        var code = $("input[name=code]").val();
        if(!code.length){
            YDUI.dialog.toast('请输入验证码','none',1500);
            return false;
        }
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['bindmobile'])?>",{mobile:mobile,code:code},function(json){
            isSubmit = false;
            YDUI.dialog.loading.close();
            if(json.status == 1){
                YDUI.dialog.toast('绑定成功', 'none', 1500,function(){
                    window.location.href = json.url;
                });
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });
</script>
<?php $this->endBlock('script'); ?>
