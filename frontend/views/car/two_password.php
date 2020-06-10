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
        <span class="afterFour cur"></span>
        <div class="liuchengImg">
            <em class="img">
                <img src="/frontend/web/images/mimahong.png">
            </em>
            <em class="txt cur">
                修改提现密码
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
        <ul class="renzhengMessUl boxSizing">
            <li class="webkitbox afterFour">
                <label>设置提现密码</label>
                <input type="password" id="one_password" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" placeholder="请输入新密码">
            </li>
            <li class="webkitbox afterFour">
                <label>重新输入一次</label>
                <input type="password" id="two_password" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" placeholder="请保持两次输入一致">
            </li>
        </ul>
    </div>
    <div class="duihuanBot boxSizing">
        <a href="#" id="checkpswd" onclick="checkpswd()">提交</a>
    </div>
</section>
<?php $this->beginBlock('script');?>
<script>
    var is_sub=false;
    function  checkpswd() {
        var opt1=$('#one_password').val();
        var opt2=$('#two_password').val();
        if(is_sub){
            alert('数据提交中请稍后');
            return false;
        }
        if(opt1 == ''){
            alert('请设置提现密码');
            return false;
        }
        if(opt2 == ''){
            alert('请重新输入密码');
            return false;
        }

        if(opt1.length < 6 || opt1.length > 16){
            alert('密码只能是6-16的数字或字母');
            return false;
        }
        if(opt2 != opt1){
            alert('两次输入的密码不一样');
            return false;
        }
        var url = "<?php echo Url::to(['car/two_password']);?>";
        $('#checkpswd').removeAttr('onclick');
        is_sub=true;
        $.post(url,{one_password:opt1,two_password:opt2},function(json){
            is_sub=false;
            if(json.status == 1){
                window.location.href= "<?php echo Url::to(['car/three_password']);?>";
            }else{
                alert(json.msg);
            }
            $('#withdrawal').attr('onclick','checkpswd();');
        });
    }


</script>
<?php $this->endBlock('script');?>
<?php $this->beginBlock('footer');?>

<?php $this->endBlock('footer');?>
