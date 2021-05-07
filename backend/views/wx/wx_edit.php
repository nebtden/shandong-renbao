<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
</style>
<div class="page-header">
    <h4>公众号设置 </h4>
</div>

<div class="ulcontent" id="content">
    <form class="form-horizontal" id="form"  method="post" action="<?php echo Url::to(['wx/wx_edit']) ?>" >
    <div class="div" id="content_1" style="display: block">
        <div class="tabTop clearfix">
            <div class="formConfirm webkitbox">
                <label>公众号名称</label>
                <input type="text" name="wxname" class="spName" placeholder="请输入公众号" value="<?php echo $wx['wxname'];?>">
                <span>*必填</span>
            </div>
            <div class="formConfirm webkitbox">
                <label>微信号</label>
                <input type="text" name="weixin" class="spName" placeholder="请输入微信号" value="<?php echo $wx['weixin'];?>">
                <span>*必填</span>
            </div>
            <div class="formConfirm webkitbox">
                <label>公众号原始ID</label>
                <input type="text" name="ghid" class="spName" placeholder="请输入公众号原始ID" value="<?php echo $wx['ghid'];?>">
                <span>*必填</span>
            </div>
        </div>


        <div class="tabBot clearfix">
            <div class="formConfirm clearfix">
                <label class="imgFloat">头像</label>
                <?php
                if($wx['headpic']){
                        echo '<img width="130" src="'.$wx['headpic'].'" />';
                }
                ?>
                <div id="hadpic"></div>
                <input type="hidden" name="pic" id="pic" value="<?php echo $wx['headpic'];?>"/>
            </div>
            <div class="formConfirm webkitbox">
                <label>APPID</label>
                <input type="text" class="spName"   name="appId" type="text"  placeholder="50" value="<?php echo $wx['appId'];?>"><span>*必填</span>
            </div>
            <div class="formConfirm webkitbox">
                <label>APPSECRET</label>
                <input type="text" class="spName"   name="appSecret" type="text"  placeholder="50" value="<?php echo $wx['appSecret'];?>"><span>*必填</span>
            </div>
        </div>
        <div class="baocunBot webkitbox clearfix">
            <input name="id"  type="hidden" value="<?php echo $wx['id']; ?>">
            <div class="baocunB  am-btn-success"  onclick="document.getElementById('form').submit();">保存</div>
            <div class="fanhuiB" onclick="history.back()">返回</div>
        </div>
    </div>
</form>

<script>
    uploadImg_dan('hadpic','pic',100,100);
    var ue1 = UE.getEditor('parameter');
    var ue2 = UE.getEditor('discrible');
    //--------start
    function handleFiles(obj) {
        window.URL = window.URL || window.webkitURL;
        var fileList = $('<span class="splist"><i></i></span>');
        var files = obj.files,
            img = new Image();
        if(window.URL){
            img.src = window.URL.createObjectURL(files[0]);
            img.onload = function(e) {
                window.URL.revokeObjectURL(this.src);
            }
            fileList.append(img);
            $(obj).parent().after(fileList);
        }
        fileList.find('i').on('click', function () {
            $(this).parents('form')[0].reset();//注意这个顺序
            $(this).parent().remove();
        });
    }

</script>