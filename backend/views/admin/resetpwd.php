<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        资料管理 <small>&nbsp;&nbsp;/密码修改</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal" data-am-validator >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">旧密码</label>
            <div class="col-sm-3">
                <input type="password" name="oldpwd"  id="oldpwd" class="form-control" value="<?php  ?>"   placeholder="旧密码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">新密码</label>
            <div class="col-sm-3">
                <input type="password"  id="newpwd"  name="newpwd"  class="form-control"   value=""    placeholder="新密码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">确认密码</label>
            <div class="col-sm-3">
                <input type="password"  id="resurepwd"   name="resurepwd"  class="form-control"   value=""  placeholder="确认密码">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="button" id="submit" class="btn btn-default">保存</button>
            </div>
        </div>
        <?php  if($_REQUEST['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>"><?php }?>
        <input name="act"  type="hidden" value="<?php if($_REQUEST['id']) {echo 'edit';}else {echo 'add';}?>">
    </form>
</div>
<script>
    $(function(){
        $('#submit').click(function(){
            var  pwd1=$('#oldpwd').val();
            var  pwd2=$('#newpwd').val();
            var  pwd3=$('#resurepwd').val();
            if(pwd1.length==0){
                $('#oldpwd').parent().parent().addClass('has-error');
                return ;
            }
            else
            {
                $('#oldpwd').parent().parent().removeClass('has-error');
            }
            if(pwd2.length==0){
                $('#newpwd').parent().parent().addClass('has-error');
                return ;
            }
            else
            {
                $('#newpwd').parent().parent().removeClass('has-error');
            }
            if(pwd3.length==0){
                $('#resurepwd').parent().parent().addClass('has-error');
                return ;
            }
            else
            {
                $('#resurepwd').parent().parent().removeClass('has-error');
            }
            if(pwd2!=pwd3){
                alert('两次密码输入不一致');
                return;
            }
            $.ajax(
                {
                    type:"POST",
                    url:'<?php echo Url::to(['admin/resetpwd']); ?>',
                    data:{resurepwd:pwd3,oldpwd:pwd1},
                    dataType:'html',
                    success:function(data){
                      alert(data);
                      history.go(0);
                    }
                }
            );



        });

    });
</script>