<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 上午 8:41
 */
use yii\helpers\Url;
?>
<div class="input-title">请输入客户手机验证</div>
<div class="bind-phone-wrapper">
    <ul class="bind-phone-ul">
        <li>
            <span>手机号码</span>
            <input   type="tel" name="mobile" pattern="[0-9]*" placeholder="请输入手机号码">
        </li>
        <li>
            <span>验证码</span>
            <input  type="text" name="code" placeholder="请输入验证码" >
            <button type="button" class="btn btn-danger " id="J_GetCode">获取验证码</button>
        </li>
        <li class="plateNumber-input">
            <span>车牌号码</span>
            <span class="select-province" data-ydui-actionsheet="{target:'#actionSheet-province',closeElement:'#cancel'}">
                    <em>湘</em><i class="iconfont icon-car-zelvxuanzefeiyongdaosanjiaoxingfandui"></i>
                </span>
            <span class="select-Letter" data-ydui-actionsheet="{target:'#actionSheet-letter',closeElement:'#cancel'}">
                    <em>A</em><i class="iconfont icon-car-zelvxuanzefeiyongdaosanjiaoxingfandui"></i>
                </span>
            <input type="text" id="carcode" placeholder="请输入车牌后5位">
        </li>
    </ul>
</div>
<div class="send-comfirm">
    <button type="button" class="btn-block btn-danger">下一步</button>
</div>
<!-- 上拉菜单 省份选择 -->
<div class="m-actionsheet" id="actionSheet-province">
    <ul class="same-actionSheet-ul province-ul">
        <li>京</li>
        <li>沪</li>
        <li>浙</li>
        <li>苏</li>
        <li>粤</li>
        <li>鲁</li>
        <li>晋</li>
        <li>冀</li>
        <li>豫</li>
        <li>川</li>
        <li>渝</li>
        <li>辽</li>
        <li>吉</li>
        <li>黑</li>
        <li>皖</li>
        <li>鄂</li>
        <li class="active">湘</li>
        <li>赣</li>
        <li>闽</li>
        <li>陕</li>
        <li>甘</li>
        <li>宁</li>
        <li>蒙</li>
        <li>津</li>
        <li>贵</li>
        <li>云</li>
        <li>桂</li>
        <li>琼</li>
        <li>青</li>
        <li>新</li>
        <li>藏</li>
    </ul>
</div>
<!-- 上拉菜单 字母选择 -->
<div class="m-actionsheet" id="actionSheet-letter">
    <ul class="same-actionSheet-ul letter-ul">
        <li class="active">A</li>
        <li>B</li>
        <li>C</li>
        <li>D</li>
        <li>E</li>
        <li>F</li>
        <li>G</li>
        <li>H</li>
        <li>I</li>
        <li>J</li>
        <li>k</li>
        <li>L</li>
        <li>M</li>
        <li>N</li>
        <li>O</li>
        <li>P</li>
        <li>Q</li>
        <li>R</li>
        <li>S</li>
        <li>T</li>
        <li>U</li>
        <li>V</li>
        <li>W</li>
        <li>X</li>
        <li>Y</li>
        <li>Z</li>
    </ul>
</div>
<?php $this->beginBlock('script');?>
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
        runStr: '重新获取{%s}',
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
        $.post("<?php echo Url::to(['sendcode']);?>",{mobile:mobile},function(json){
            YDUI.dialog.loading.close();
            if(json.status == 1){
                $getCode.sendCode('start');
                YDUI.dialog.toast('验证码已发送', 'none', 1500);
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });
    //上拉省份简称选择
    $('.province-ul>li').on('touchstart',function(){
        $('.province-ul>li').removeClass('active');
        $(this).addClass('active');
        $('.select-province>em').text($(this).text());
    });
    $('#actionSheet-province').on('click', function () {
        $(this).actionSheet('close');
    });
    //上拉字母选择
    $('.letter-ul>li').on('touchstart',function(){
        $('.letter-ul>li').removeClass('active');
        $(this).addClass('active');
        $('.select-Letter>em').text($(this).text());
    });
    $('#actionSheet-letter').on('click', function () {
        $(this).actionSheet('close');
    });
    //提交确认
    var isSubmit = false;
    $('.send-comfirm').on('touchstart',function(){
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
        var carcode = '',strcode=$('#carcode').val(), carprovince=$('.select-province em').html(),carletter=$('.select-Letter em').html();
        var reg = /^[0-9a-zA-Z]+$/;
        if(!reg.test(strcode)){
            YDUI.dialog.toast('你输入的车牌后5位不是数字或者字母','none',1500);
            return false;
        }
        if(strcode.length>6){
            YDUI.dialog.toast('您输入车牌号过长','none',1500);
            return false;
        }
        if(!carprovince.length){
            YDUI.dialog.toast('请选择车牌所属省简称','none',1500);
            return false;
        }
        if(!carletter.length){
            YDUI.dialog.toast('请选择车牌首字母','none',1500);
            return false;
        }
        carcode=String(carprovince)+String(carletter)+String(strcode);

        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['car/recovery_2'])?>",{mobile:mobile,code:code,carcode:carcode},function(json){
            isSubmit = false
            YDUI.dialog.loading.close();
            if(json.status == 1){
                YDUI.dialog.toast(json.msg, 'none', 1000,function(){
                    window.location.href = json.url;
                });

            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });




</script>

<?php $this->endBlock('script');?>
