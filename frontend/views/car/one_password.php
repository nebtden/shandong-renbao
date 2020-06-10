<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7 0007
 * Time: 下午 4:31
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd">
    <div class="modifyTelTitle boxSizing">
        修改提现密码
    </div>
    <div class="shopPageCont NoMarginTop modifyPassImg webkitbox">
        <span class="afterFour cur"></span>
        <div class="liuchengImg">
            <em class="img">
                <img src="/frontend/web/images/shenfenhong.png">
            </em>
            <em class="txt cur">
                验证身份
            </em>
        </div>
        <span class="afterFour"></span>
        <div class="liuchengImg">
            <em class="img">
                <img src="/frontend/web/images/mimahui.png">
            </em>
            <em class="txt">
                修改登录密码
            </em>
        </div>
        <span class="afterFour"></span>
        <div class="liuchengImg">
            <em class="img">
                <img src="/frontend/web/images/wanchenghui.png">
            </em>
            <em class="txt">
                完成
            </em>
        </div>
        <span class="afterFour"></span>
    </div>
    <div class="shopPageCont NoPaddLR NoPaddTop NoPaddBot">
        <div class="sureTelMess boxSizing webkitbox afterFour">
            <div>您已验证的手机是：<?php echo substr_replace($payee['mobile'],'****',3,4)?></div>
            <em id="yzm">获取验证码</em>
        </div>
        <div class="inputYZM boxSizing webkitbox">
            <label>请输入您的短信校验码：</label>
            <input type="number" id="yzcode" placeholder="请输入验证码">
        </div>
    </div>
    <div class="YZMtellYou boxSizing">
        若该手机号无法接受验证短信，请拨打<i>400-176-0899</i>申请客服协助处理。
    </div>
    <div class="duihuanBot boxSizing">
        <a href="#" id="baocun">下一步</a>
    </div>
</section>
<?php $this->beginBlock('script');?>
<script type="text/javascript">
    $(function () {

        var testMobile = function(m){
            var reg = /^1[0-9]{10}$/;
            return reg.test(m);
        }
        var getYzcode = function(){
            var mobile = <?php echo $payee['mobile']?>;
            if(!testMobile(mobile)){
                alert('请输入正确的手机号码！');
                return false;
            }
            $.post('<?php echo Url::to(["car/smscode"])?>',{mobile:mobile},function(json){
                if(json.status != 1){
                    alert('验证码获取失败，请重新获取');
                }
            });
            return true;
        }
        $('#yzm').click(function () {
            if($(this).data('yzm') == undefined){
                if(!getYzcode()){
                    return false;
                }
                var _this = this;
                var num   = 59;
                $(this).css('background','#bfbfbf').html('<i>60</i>' + 's后重新获取').data('yzm',true);
                var setTimer = setInterval(function () {
                    $(_this).find('i').text(num--);
                    if(num < 0){
                        num = 59;
                        $(_this).removeAttr('style').removeData('yzm').html('获取验证码');
                        clearInterval(setTimer);
                    }
                },1000);
            }else{
                return false;
            }
        });
        var is_sub=false;
        $('#baocun').on('click', function () {
            if(is_sub){
                alert('数据提交中请稍后');
                return false;
            }
            var opt1 = $("#yzcode").val();
            var opt2 = '<?php echo $payee['mobile']?>';
            if(opt1=='' || opt1.length != 6  ){
                alert('请输入正确的验证码');
                return false;
            }
            is_sub=true;
            $.post('<?php echo Url::to(["car/one_password"]);?>',{yzcode:opt1,mobile:opt2},function(json){
                is_sub=false;
                if(json.status == 1){

                    window.location.href= "<?php echo Url::to(['car/two_password']);?>";
                }else{
                    alert(json.msg);
                }
            });
        });
    });
</script>
<?php $this->endBlock('script');?>
<?php $this->beginBlock('footer');?>

<?php $this->endBlock('footer');?>
