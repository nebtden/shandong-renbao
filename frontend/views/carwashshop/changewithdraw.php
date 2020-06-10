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
    <ul class="modify-lc">
        <li class="actived">
            <div class="dot"></div>
            <p>验证身份</p>
        </li>
        <li>
            <div class="dot"></div>
                <p>修改提现密码</p>
        </li>
    </ul>
    <p class="tel-p">输入发送到此号码的验证码：<span><?php echo $washShop['mobile']?></span></p>
    <dl class="common-dl psd-dl">
        <dd>
            <i>验证码</i>
            <input type="tel" name="code" placeholder="请输入您收到的验证码">
            <button type="button" class="btn send-code" id="J_GetCode">发送验证码</button>
        </dd>
    </dl>
    <p class="service-p">若该手机号无法接受验证短信,请拨打<a href="<?php echo \Yii::$app->params['yunche_hotline'] ?>"><?php echo \Yii::$app->params['yunche_hotline'] ?></a>申请客服协助处理</p>
    <div class="commom-submit">
        <a href="javascript:;" onclick="checkCode()" class="btn-block">下一步</a>
    </div>
</div>
<div class="second" style="display: none">
    <dl class="common-dl modify-dl">
        <dd>
            <i>设置/修改提现密码</i>
            <input type="text" name="w_password" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" placeholder="请输入新密码">
        </dd>
        <dd>
            <i>再次输入提现密码</i>
            <input type="text" name="s_password" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" placeholder="再次输入新密码">
        </dd>
    </dl>
    <div class="commom-submit">
        <a href="javascript:;" onclick="changeWithdraw()" class="btn-block">提交</a>
    </div>

</div>


<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script>

    /**
     * 发送验证码
     * 定义参数
     */
    var $getCode = $('#J_GetCode');
    $getCode.sendCode({
        disClass: 'btn-disabled ',
        secs: 59,
        run: false,
        runStr: '重新发送{%s}',
        resetStr: '重新获取'
    });

    //发送验证码
    $getCode.on('click', function () {
        /* ajax 成功发送验证码后调用【start】 */
        var mobile = "<?php echo $washShop['mobile']?>";
        YDUI.dialog.loading.open('发送中');
        $.post("<?php echo Url::to(['carwashshop/smscode']);?>",{mobile:mobile},function(json){
            YDUI.dialog.loading.close();
            if(json.status == 1){
                $getCode.sendCode('start');
                YDUI.dialog.toast('已发送', 'none',1500);
            }else {
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });

    //验证手机验证码
    var is_code = false;
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
                    YDUI.dialog.toast('手机验证成功','success',1000,function(){
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
    function changeWithdraw(){
        var w_password = $("input[name=w_password]").val();
            s_password = $("input[name=s_password]").val();
        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        console.log(w_password+'//'+s_password);
        if(w_password.length == 0 ){
            YDUI.dialog.toast('请输入新密码',1000);
            return false;
        }
        if(w_password.length<6 || w_password.length>16){
            YDUI.dialog.toast('密码只能是6-16的数字或字母',1000);
            return false;
        }
        if(s_password.length == 0 ){
            YDUI.dialog.toast('请输入确认密码',1000);
            return false;
        }

        if(w_password != s_password){
            YDUI.dialog.toast('两次输入的密码不一样',1000);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/changewithdraw'])?>',
            data:{w_password:w_password,s_password:s_password},
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
