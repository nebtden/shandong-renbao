<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<style>
    .commom-popup>.title {
        position: relative;
        display: block;
        width: 100%;
        color: #fff;
        font-size: .36rem;
        text-align: center;
        padding: .24rem 0;
        background-color: #00be62;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .card-popup-ul>li {
         border-bottom:1px solid #CCCCCC;
     }
    .card-popup-ul>li>.title {
        display: flex;
        align-items: center;
        font-size: .36rem;
        color: #00be62;
        font-weight: 700;
        padding-top: .3rem;
    }
    .card-popup-ul>li>.down>a {
        height: auto;
        line-height: inherit;
        color: #fff;
        font-size: .26rem;
        border-radius: 20px;
        padding: .05rem .26rem;
        background-color: #00be62;
    }
</style>
<?php $this->endBlock('hStyle')?>
<!-- 洗车券弹窗 -->
<?php if (empty($washCoupon )): ?>
    <div class="commom-popup-outside  small-popup-outside" style="display:none;" >
        <div class="commom-popup">
            <div class="title">
                您暂时还没有此类型的优惠券哦
                <i class="icon-error"></i>
            </div>
            <div class="content">
                <div class="up">

                </div>
                <div class="commom-submit need-submit">
                    <a class="btn-block btn-primary small-popup-btn" href="<?php echo Url::to(['h5service/accoupon'])?>" >去激活</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="commom-popup-outside big-popup-outside" style="display: none">
        <div class="commom-popup">
            <div class="title">请先选择洗车券<i class="icon-error"></i></div>
            <div class="content">
                <ul class="card-popup-ul">
                    <?php foreach($washCoupon as $val): ?>
                        <?php if($val['status'] < 3):?>
                            <li>
                                <div class="title"><span><?= $val['name'] ?></span>
                                    <i>剩余<?= $val['show_coupon_left'] ?>次</i>
                                </div>
                                <?php if(!empty($val['servicecode'])): ?>
                                    <div class="middle">服务码：<?= $val['servicecode'] ?></div>
                                <?php else: ?>
                                    <div class="middle">&nbsp;</div>
                                <?php endif; ?>

                                <div class="down">
                                    <time>有效期至：<?= $val['show_coupon_endtime'] ?></time>

                                    <?php if($val['w_status']==2 && $val['is_mensal']==1): ?>
                                        <a class="btn " href="javascript:;">本月已使用</a>
                                    <?php else: ?>
                                    <a href="<?= $val['show_coupon_url'] ?>" class="btn">选择</a></div>
                            <?php endif;?>
                            </li>
                        <?php endif;?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="imgWrap">
    <img src="/frontend/web/h5/img/washing-car-bg.jpg" />
    <div class="btn-item">
        <?php if(empty($user)):?>
            <a href="javascript:;" onclick="login()" class="active-btn">立即激活</a>
            <a href="javascript:;" onclick="login()" class="wash-btn">一键洗车</a>
        <?php else:?>
            <a href="<?= Url::to(['h5service/accoupon'])?>" class="active-btn">立即激活</a>
            <a href="javascript:;" class="wash-btn wash-service">一键洗车</a>
        <?php endif;?>
    </div>
</div>
<?php if(empty($user)):?>
    <!--	登录弹框-->
    <div class="login-wrap" style="display: none;">
        <div class="login-content">
            <div class="commom-input-list">
                <ul class="commom-input-ul">
                    <li>
                        <i>手机号</i>
                        <input type="tel" name="mobile" pattern="[0-9]*" >
                    </li>
                    <li>
                        <i>验证码</i>
                        <input type="text" name="code">
                        <button type="button" class="btn send-code" id="J_GetCode">获取验证码</button>
                    </li>
                </ul>
            </div>
            <div class="commom-submit">
                <button class="btn-block btn-primary identify-btn login">立即验证</button>
            </div>
        </div>
    </div>
<?php endif;?>
<?php $this->beginBlock('script')?>
<script>
    //ios input框失焦,按钮错位
    $("input,select").blur(function(){
        setTimeout(function() {
            var scrollHeight = document.documentElement.scrollTop || document.body.scrollTop || 0;
            window.scrollTo(0, Math.max(scrollHeight - 1, 0));
        }, 100);
    });

    function login() {
        $('.login-wrap').show();
    }
    //洗车服务 弹窗显示
    $('a.wash-service').on('click',function(){
        // runCommomLayer();
        <?php if(count($washCoupon)==1): ?>
        window.location.href="<?php echo $washCoupon[0]['show_coupon_url']?>";
        <?php else: ?>
        $('.commom-popup-outside').show();
        <?php endif; ?>
    });
    //关闭大弹窗
    $('body').on('click','.commom-popup>.title>i',function(e){
        $('.big-popup-outside').hide();
    });
    //关闭小弹窗
    $('.small-popup-outside>.commom-popup>.title>i').on('click',function(e){
        $('.small-popup-outside').hide();
    });
</script>
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
        $.post("<?php echo Url::to(['webcarlogin/smscode']);?>",{mobile:mobile},function(json){
            YDUI.dialog.loading.close();
            if(json.status == 1){
                $getCode.sendCode('start');
                YDUI.dialog.toast('验证码已发送', 'none', 1500);
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });

    //登录确认
    var isSubmit = false;
    $('.commom-submit>.login').on('touchstart',function(){
    // $(document).on('click','.commom-submit>.login',function(){
        //防止重复提交
        if(isSubmit){
            return false;
        }
        var mobile = $("input[name=mobile]").val();
        var code = $("input[name=code]").val();
        //验证手机号码
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
        if(!code){
            YDUI.dialog.toast('请输入验证码','none',1500);
            return false;
        }
        $('.login-wrap').hide();
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post(
            '<?php echo Url::to(['webcarlogin/login'])?>',
            {mobile:mobile,code:code},
            function(json){
                isSubmit = false;
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast(json.msg, 'none', 1000,function(){
                        window.location.href = '<?php echo Url::to(['h5service/accoupon'])?>';
                    });
                }else {
                    YDUI.dialog.toast(json.msg,'none',1500);
                }
            }
            ,'json')
    });

</script>
<?php $this->endBlock('script')?>
