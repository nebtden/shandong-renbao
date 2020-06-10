<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">

<?php $this->endBlock('hStyle')?>

<dl class="common-dl">
    <dt>
        <span>提现</span>
        <i>可提现金额：<em>￥<?php echo number_format($washShop['amount'],2,".",",")?>元</em></i>
    </dt>
    <dd>
        <i>提现金额</i>
        <input type="text" class="amount-num" name="amount" placeholder="每笔提现金额需大于￥50.00才可提现">
    </dd>
    <dd>
        <i>提现密码</i>
        <input type="text" name="w_password" placeholder="请输入提现密码">
    </dd>
    <dd>
        <i>提现手机号</i>
        <span><?php echo $washShop['mobile']?></span>
    </dd>
    <dd>
        <i>验证码</i>
        <input type="tel" name="code" placeholder="请输入您收到的验证码">
        <button type="button" class="btn send-code" id="J_GetCode">发送验证码</button>
    </dd>
</dl>
<p class="tx-tips">在收到您的提现申请后系统会自动为您办理，由于银行及支付宝的时效不同，请以具体到账时间为准</p>
<dl class="common-dl">
    <dt>
        <div class="account-item" >
            <i class="icon-cloudCar2-radio <?php echo $bank?'icon-cloudCar2-radioactive':''?>" data-type="2" data-info="<?php echo $bank?:''?>"></i>
            <span >银行账户信息</span>
        </div>
        <a href="<?php echo Url::to(['carwashshop/changeaccount','type'=>2])?>" class="edit-btn">编辑/修改</a>
    </dt>
    <dd>
        <ul class="radio-ul">
            <li>
                <i>账户名称</i>
                <span><?php echo $bank['payee_name']?></span>
            </li>
            <li>
                <i>账号</i>
                <span><?php echo $bank['payee_account']?></span>
            </li>
            <li>
                <i>开户行</i>
                <span><?php echo $bank['payee_bank']?></span>
            </li>
        </ul>
    </dd>
</dl>
<dl class="common-dl">
    <dt>
        <div class="account-item">
            <i class="icon-cloudCar2-radio <?php if(!$bank && $aipay) echo 'icon-cloudCar2-radioactive'?>" data-type="1" data-info="<?php echo $bank?:''?>"></i>
            <span >支付宝账户信息</span>
        </div>
        <a href="<?php echo Url::to(['carwashshop/changeaccount','type'=>1])?>" class="edit-btn">编辑/修改</a>
    </dt>
    <dd>
        <ul class="radio-ul">
            <li>
                <i>支付宝账号名</i>
                <span><?php echo $aipay['payee_account']?></span>
            </li>
            <li>
                <i>真实姓名</i>
                <span><?php echo $aipay['payee_name']?></span>
            </li>
        </ul>
    </dd>
</dl>
<div class="commom-submit">
    <?php if($washShop['amount']<50):?>
        <button type="button" class="btn-block cancel-btn" disabled >申请提现</button>
    <?php else:?>
        <button type="button" class="btn-block " onclick="withdraw()">申请提现</button>
    <?php endif;?>
</div>


<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script>

    //选择提现账户
    $('.account-item').on('click',function(){
        var account_info = $(this).children('.icon-cloudCar2-radio').attr('data-info');
        if(account_info.length == 0){
            YDUI.dialog.toast('请编辑完整的账号信息后点击',1500);
            return false;
        }
        $('.account-item > i').removeClass('icon-cloudCar2-radioactive');
        $(this).children('.icon-cloudCar2-radio').addClass('icon-cloudCar2-radioactive');
    });

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


    var is_sub = false;
    //申请提现
    function withdraw(){
        var amount = $("input[name=amount]").val();
            w_password = $("input[name=w_password]").val();
            code = $("input[name=code]").val();
            mobile = '<?php echo $washShop['mobile']?>';
            account = $('.account-item > .icon-cloudCar2-radioactive').attr('data-type');

        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        if(amount.length == 0){
            YDUI.dialog.toast('请输入提现金额',1000);
            $('.amount-num').focus();
            return false;
        }
        if(amount <50 || amount> <?php echo $washShop['amount']?>){
            YDUI.dialog.toast('提现金额小于50或者大于可提现金额，请重新输入',1000);
            $('.amount-num').focus();
            return false;
        }
        if(w_password.length == 0 ){
            YDUI.dialog.toast('请输入提现密码',1000);
            return false;
        }
        if(w_password.length<6 || w_password.length>16){
            YDUI.dialog.toast('密码只能是6-16的数字或字母',1000);
            return false;
        }
        if(code.length == 0 || code.length>6){
            YDUI.dialog.toast('请输入正确的验证码',1000);
            return false;
        }

        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/withdraw'])?>',
            data:{amount:amount,w_password:w_password,code:code,account:account,mobile: mobile},
            type:'post',
            dataType:'json',
            timeout:6000,
            success:function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast('提交成功', 'success',1000,function () {
                        window.location.href =  '<?php echo Url::to(['carwashshop/withdrawals'])?>'
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
