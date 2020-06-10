<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">

<?php $this->endBlock('hStyle')?>

<div class="info-header">
    <span><img src="<?php echo $washShop['shop_pic']?>"></span>
</div>
<div class="first">
    <dl class="common-dl modify-dl account-dl">
    <dd>
        <i>原手机号</i>
        <span><?php echo $washShop['mobile']?></span>
    </dd>
    <dd>
        <i>验证码</i>
        <input type="tel" name="code" placeholder="请输入您收到的验证码">
        <button type="button" class="btn send-code" id="J_GetCode">发送验证码</button>
    </dd>
    </dl>
    <div class="commom-submit">
        <a href="javascript:" onclick="checkCode()" class="btn-block">下一步</a>
    </div>
</div>
<div class="second" style="display: none">
    <dl class="common-dl modify-dl account-dl">
        <dd>
            <i>新手机号</i>
            <input type="tel" class="mobile" name="mobile" placeholder="请输入您需要绑定的新手机号">
        </dd>
        <dd>
            <i>验证码</i>
            <input type="tel" name="code" placeholder="请输入您收到的验证码">
            <button type="button" class="btn send-code" id="J_GetCode1">发送验证码</button>
        </dd>
    </dl>
    <div class="commom-submit">
        <button type="button" onclick="changeMobile()" class="btn-block">保存修改</button>
    </div>
</div>

<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script>
    var is_code = false;
    /**
     * 发送验证码
     * 定义参数
     */
    var $getCode = $('#J_GetCode');
    var $getCode1 = $('#J_GetCode1');

    $getCode.sendCode({
        disClass: 'btn-disabled ',
        secs: 59,
        run: false,
        runStr: '重新发送{%s}',
        resetStr: '重新获取'
    });
    $getCode1.sendCode({
        disClass: 'btn-disabled ',
        secs: 59,
        run: false,
        runStr: '重新发送{%s}',
        resetStr: '重新获取'
    });

    var testMobile = function(m){
        var reg = /^1[0-9]{10}$/;
        return reg.test(m);
    };

    //发送验证码
    $getCode.on('click', function () {
        /* ajax 成功发送验证码后调用【start】 */
        var mobile = '<?php echo $washShop['mobile']?>';
        codePost(mobile);
    });
    $getCode1.on('click', function () {
        /* ajax 成功发送验证码后调用【start】 */
        var mobile = $(".mobile").val();
        console.log(mobile);

        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
        codePost(mobile,'code1');
    });
    function codePost(mobile,getCode){
        YDUI.dialog.loading.open('发送中');
        $.post("<?php echo Url::to(['carwashshop/smscode']);?>",{mobile:mobile},function(json){
            YDUI.dialog.loading.close();
            if(json.status == 1){
                if(!getCode){
                    $getCode.sendCode('start');
                } else {
                    $getCode1.sendCode('start');
                }
                YDUI.dialog.toast('已发送', 'none',1500);
            }else {
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    }


    //验证手机验证码
    function checkCode(){
        if(is_code){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        var code = $("input[name=code]").val();
        if(code.length == 0 || code.length>6){
            YDUI.dialog.toast('请填写正确的验证码',1000);
            return false;
        }
        is_code = true;
        YDUI.dialog.loading.open('手机号码验证中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/checkmobile'])?>',
            data:{mobile:<?php echo $washShop['mobile']?>,code:code},
            type:'post',
            dataType: 'json',
            timeout: 6000,
            success:function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast('手机验证成功','success',function(){
                        $('.first').empty();
                        $('.second').css('display','block');
                    });
                } else {
                    YDUI.dialog.toast(json.msg,'error',1000);
                    is_code = false;
                }
            },
            complete: function(XMLHttpRequest,status){
                if(status == 'timeout'){
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('请求超时', 'error',1000);
                    is_code = false;
                }
            },
            error: function(XMLHttpRequest) {
                YDUI.dialog.loading.close();
                YDUI.dialog.toast('错误代码' + XMLHttpRequest.status, 1500);
                is_code = false;
            }
        })

    }
    var is_sub = false;
    //修改手机号码
    function changeMobile(){
        var oldTel = "<?php echo $washShop['mobile']?>";
            shop_tel = $('.mobile').val();
            code = $("input[name=code]").val();
        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        if(code.length == 0 || code.length>6){
            YDUI.dialog.toast('请输入正确的验证码',1000);
            return false;
        }
        if(shop_tel.length == 0 || shop_tel.length > 11){
            YDUI.dialog.toast('请输入正确的电话号码',1000);
            return false;
        }
        if(shop_tel == oldTel){
            YDUI.dialog.toast('输入的号码与当前电话号码重复',1000);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/changemobile'])?>',
            data:{mobile:shop_tel,code:code},
            type:'post',
            dataType:'json',
            timeout:6000,
            success:function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast('修改成功', 'success',1000,function () {
                        window.location.href =  '<?php echo Url::to(['carwashshop/shopdetail'])?>'
                    });
                }else {
                    YDUI.dialog.toast(json.msg,'error',1000);
                    is_sub = false;
                }
            },
            complete: function(XMLHttpRequest,status){
                if(status == 'timeout'){
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('请求超时', 'error',1000);
                    is_sub = false;
                }
            },
            error: function(XMLHttpRequest) {
                YDUI.dialog.loading.close();
                YDUI.dialog.toast('错误代码' + XMLHttpRequest.status, 1500);
                is_sub = false;
            }
        })
    }

</script>
<?php $this->endBlock('script') ?>
