<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">

<?php $this->endBlock('hStyle')?>

<div class="info-header">
    <span><img src="<?= $user['headimgurl']?>"></span>
</div>
<dl class="common-dl account-dl child-dl">
    <dd>
        <i>微信昵称</i>
        <span><?= $user['nickname']?></span>
    </dd>
    <dd>
        <i>姓名</i>
        <input type="text" name="realname" placeholder="请输入姓名">
    </dd>
    <dd>
        <i>手机号</i>
        <input type="tel" name="mobile" placeholder="请输入手机号码">
    </dd>
    <dd>
        <i>验证码</i>
        <input type="tel" name="code" placeholder="请输入您收到的验证码">
        <button type="button" class="btn send-code" id="J_GetCode">发送验证码</button>
    </dd>
</dl>
<div class="commom-submit">
    <button type="button" class="btn-block">提交信息</button>
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
    var testMobile = function(m){
        var reg = /^1[0-9]{10}$/;
        return reg.test(m);
    };
    //发送验证码
    $getCode.on('click', function () {
        /* ajax 成功发送验证码后调用【start】 */
        var mobile = $("input[name=mobile]").val();
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
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
    //提交信息
    var is_sub = false;
    $('.btn-block').on('click',function(){
        if(is_sub){
            YDUI.dialog.toast('提交中，请勿重复提交','none',1500);
            return false;
        }
        var mobile = $("input[name=mobile]").val();
        var code = $("input[name=code]").val();
        var realname = $("input[name=realname]").val();
        if(realname.length == 0 || realname.length>25){
            YDUI.dialog.toast('请输入正确的姓名','none',1500);
            return false;
        }
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
        if(code.length == 0 || code.length>6){
            YDUI.dialog.toast('请输入正确的验证码','none',1500);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url:'<?php echo Url::to(['carwashshop/childactive'])?>',
            data:{mobile:mobile,code:code,realname:realname,sid:'<?= $data['sid']?>',uid:'<?= $data['uid']?>'},
            type:'post',
            dataType:'json',
            timeout:6000,
            success:function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast('提交成功', 'success',1000,function () {
                        window.location.href =  '<?php echo Url::to(['carwashshop/index'])?>'
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
    })


</script>
<?php $this->endBlock('script') ?>
