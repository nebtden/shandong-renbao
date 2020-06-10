<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/20 0020
 * Time: 下午 3:09
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>营销员管理 <small>&nbsp;/&nbsp;营销员信息编辑</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['travel/salesman-edit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">营销员姓名</label>
            <div class="col-sm-3">
                <input type="text" required name="name" id="name" value="<?php  echo $info['name']; ?>" class="form-control"  placeholder="营销员姓名" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">营销员代码</label>
            <div class="col-sm-3">
                <input type="text" required name="code" id="code" value="<?php  echo $info['code']; ?>" class="form-control"  placeholder="营销员代码" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">中支公司</label>
            <div class="col-sm-3">
                <select  name="company_pid" id="company_pid"  placeholder="中支公司"  class="form-control" onchange="changejigouedit()">
                    <?php foreach ($companylist as $key =>$val){?>
                        <option value="<?php echo $key?>" <?php if($info['company_pid']==$key) echo 'selected'; ?>><?php echo $val?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">机构</label>
            <div class="col-sm-3">
                <select  name="company_id" id="company_id"  placeholder="机构"  class="form-control">
                    <?php foreach ($jigoulist as $key =>$val){?>
                        <option value="<?php echo $key?>" <?php if($info['organ_id']==$key) echo 'selected'; ?>><?php echo $val?></option>
                    <?php }?>
                </select>
            </div>
        </div>

        <?php  if($info['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $info['id']; ?>">
        <?php }?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script>
    function changejigouedit(){
        var company_pid = $("#company_pid").val();
        var url = "<?php echo Url::to(['getjigou']);?>";
        var html = "";
        $('#company_id').html('');
        $.post(url,{orgain:company_pid},function(json){
            if(json.status == 1){
                $.each(json.data,function(key,val){
                    html += '<option value="'+key+'">'+val+'</option>';
                });
                $('#company_id').html(html);
                getuserlist();
            }else{
                alert(json.msg);
            }
        });
    }



</script>

