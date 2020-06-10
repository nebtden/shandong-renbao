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
            <?php if($type == 1):?>
                <p>修改支付宝账户</p>
            <?php else:?>
                <p>修改账户信息</p>
            <?php endif;?>
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
    <p class="service-p">若该手机号无法接受验证短信,请拨打<a href="tel:<?php echo \Yii::$app->params['yunche_hotline'] ?>"><?php echo \Yii::$app->params['yunche_hotline'] ?></a>申请客服协助处理</p>
    <div class="commom-submit">
        <a href="javascript:;" onclick="checkCode()" class="btn-block">下一步</a>
    </div>
</div>
<div class="second" style="display: none">
    <dl class="common-dl modify-dl account-dl">
        <?php if($type == 1):?>
            <dd>
                <i>支付宝账号</i>
                <input type="text" name="payee_account" value="<?php echo $account['payee_account']?>" placeholder="请输入支付宝账号">
            </dd>
            <dd>
                <i>真实姓名</i>
                <input type="text" name="payee_name" value="<?php echo $account['payee_name']?>" placeholder="请输入支付宝实名认证姓名">
                <input type="hidden" name="payee_bank" value="支付宝">
            </dd>
        <?php else:?>
            <dd>
                <i>账户名称</i>
                <input type="text" name="payee_name" value="<?php echo $account['payee_name']?>" placeholder="请输入开户人名称">
            </dd>
            <dd>
                <i>银行账号</i>
                <input type="text" name="payee_account" value="<?php echo $account['payee_account']?>" placeholder="请输入银行账号">
            </dd>
            <dd>
                <i>开户行</i>
                <input type="text" name="payee_bank" value="<?php echo $account['payee_bank']?>" placeholder="请输入开户行名称">
            </dd>
        <?php endif;?>

    </dl>
    <p class="service-p">请确保您的收款账户信息的准确性，因填写错误造成的损失后果自负</p>
    <div class="commom-submit">
        <button type="button" class="btn-block" onclick="changeAccount()">保存</button>
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
    function changeAccount(){
        var payee_name = $("input[name=payee_name]").val();
            payee_account = $("input[name=payee_account]").val();
            payee_bank = $("input[name=payee_bank]").val();
            payee_type = '<?php echo $type?>';
        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        if(payee_name.length == 0 || payee_name.length>20){
            YDUI.dialog.toast('请输入正确的名称',1000);
            return false;
        }
        if(payee_account.length == 0 || payee_account.length > 25){
            YDUI.dialog.toast('请输入正确的账号',1000);
            return false;
        }
        if(payee_bank.length == 0 || payee_bank.length > 25){
            YDUI.dialog.toast('请输入正确的开户行',1000);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/changeaccount'])?>',
            data:{payee_name:payee_name,payee_account:payee_account,payee_bank:payee_bank,type:payee_type},
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
