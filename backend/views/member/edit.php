<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
    <div class="page-header am-fl am-cf">
        <h4>
            会员管理 <small>&nbsp;&nbsp;/会员编辑</small>
        </h4>
    </div>
    <div class="container-fluid" style="padding-top: 15px;height:800px;">
        <form class="form-horizontal"   method="post" action="<?php echo Url::to(['member/edit']) ?>" >
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">真实姓名</label>
                <div class="col-sm-3">
                    <input type="text"   class="form-control"  name="realname"  value="<?php   echo  $row['realname'];  ?>" >
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">联系方式</label>
                <div class="col-sm-3">
                    <input type="text"   id="inputError1" for="inputError1"  class="form-control"     placeholder="联系方式" name="telphone"   value="<?php  if($act) echo $row['telphone']; ?>" >
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">性别</label>
                <div class="col-sm-3">
                    <input type="radio" placeholder="女" name="sex" <?php if($row['sex']==1) {?> checked   <?php }?> value="1"  />
                    女&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" placeholder="男" name="sex" <?php if($row['sex']==2) {?>  checked <?php }?> value="2" />男
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">邮箱</label>
                <div class="col-sm-3">
                    <input type="text" placeholder="邮箱" name="email"   value="<?php   echo $row['email']; ?>"    class="form-control"  />
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">地址</label>
                <div class="col-sm-3">
                    <input type="text" placeholder="地址" name="address"   value="<?php  echo $row['address']; ?>" class="form-control"  />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
                </div>
            </div>
            <?php  if($_REQUEST['id']) {?>
                <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>"><?php }?>
        </form>
    </div>
    <script>

        $('form').submit(function(){
            var error=0;
            $('input[type="text"]').each(function(){
                if($(this).val().length==0) {
                    $(this).parent().parent().addClass('has-error');
                    error = 1;
                }
                else{
                    $(this).parent().parent().removeClass('has-error');
                }
            });
            if(error>0) return false;
            else  return true;

        });

    </script>
<?php

?>