<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
    .spName{margin-left:40px;}
    .webkitboxwidth{
        display:block;
        overflow: hidden;
    }
    .webkitboxwidth span{
        color: #333;
        font-size: 20px;
        font-weight: bolde;
    }
    .webkitboxwidth label{
        display: inline-block;
        float: left;
        height:21px;
        text-align:left;
        margin-top:10px;

    }
</style>
<div class="ulcontent" id="content">
    <form class="form-horizontal" id="form"  method="post" action="<?php echo Url::to(['authority/user_edit']) ?>" >
    <div class="div" id="content_1" style="display: block">
        <div class="tabTop clearfix">
           <div class="formConfirm webkitbox" style="margin-bottom: 30px;">
                <label>用户</label>
                <input type="text" name="proname" class="spName"  placeholder="请输入账号" value="<?php echo $user['username'];?>">
                <span>*必填</span>
            </div>
            <div class="formConfirm webkitbox webkitboxwidth " >
                <label>用户组:</label>
                <?php foreach($groups as $v){?>
                <?php if(in_array($v['id'],$grpArr)){?>
                <label><input name="group_id[]" type="checkbox" checked="checked" value="<?php echo $v['id']?>" /><?php echo $v['group_name']?> </label>
                <?php }else{ ?>
                <label><input name="group_id[]" type="checkbox" value="<?php echo $v['id']?>" /><?php echo $v['group_name']?> </label>
                <?php }}?>
                
            </div>

        </div>


        <div class="baocunBot webkitbox clearfix">
            <input name="id"  type="hidden" value="<?php echo $user['id']; ?>">
            <div class="baocunB  am-btn-success"  onclick="document.getElementById('form').submit();">保存</div>
            <div class="fanhuiB" onclick="history.back()">返回</div>
        </div>
    </div>
</form>
