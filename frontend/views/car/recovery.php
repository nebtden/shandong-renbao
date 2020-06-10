<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7 0007
 * Time: 下午 4:24
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd">
    <div class="modifyTelTitle boxSizing">
        请输入验证信息兑换产品/服务
    </div>
    <div class="shopPageCont NoPaddLR NoPaddTop NoPaddBot NoMarginTop">
        <ul class="renzhengMessUl boxSizing">
            <li class="webkitbox afterFour">
                <label>卡号</label>
                <input type="text" id="code_num" placeholder="请输入您的卡号">
            </li>
            <li class="webkitbox afterFour">
                <label>密码</label>
                <input type="password" id="code_password" placeholder="请输入您的卡密">
            </li>
        </ul>
    </div>
    <div class="duihuanBot boxSizing">
        <a href="#" onclick="recovery()">下一步</a>
    </div>
</section>

<?php $this->beginBlock('script');?>
<script type="text/javascript">
    var is_sub=false;
    function recovery(){
        if(is_sub){
            alert('数据提交中请稍后');
            return false;
        }
        var code = $("#code_num").val();
        var password = $("#code_password").val();
        var url = "<?php echo Url::to(['car/recovery']);?>";
        is_sub=true;
        $.post(url,{code:code,password:password},function(json){
            is_sub=false;
            if(json.status == 1){

                window.location.href= "<?php echo Url::to(['car/recovery_2']);?>";
            }else if(json.status == 101){
                alert(json.msg);
                window.location.href= "<?php echo Url::to(['car/authentication']);?>";
            }else{
                alert(json.msg);
            }
        });
    }


</script>
<?php $this->endBlock('script');?>