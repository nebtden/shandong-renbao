<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/13 0013
 * Time: 下午 3:08
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>旅游报名管理 <small>&nbsp;/&nbsp;旅游报名编辑</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['travel/enroll-edit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">游客姓名</label>
            <div class="col-sm-3">
                <input type="text" required name="name" id="name" value="<?php  echo $info['name']; ?>" class="form-control"  placeholder="游客姓名" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">游客身份证号</label>
            <div class="col-sm-3">
                <input type="text" required name="code" id="code" value="<?php  echo $info['code']; ?>" class="form-control"  placeholder="游客身份证号" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">游客手机</label>
            <div class="col-sm-3">
                <input type="text"  required name="mobile" id="mobile" value="<?php  echo $info['mobile']; ?>" class="form-control"  placeholder="游客手机" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-3">
                <textarea  name="remark" id="remark" style="width: 100%;height: 200px;" placeholder="简介"><?php echo $info['remark'];?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">性别</label>
            <div class="col-sm-3">
                <select  name="sex"  placeholder="性别"  class="form-control">
                    <option value="男" <?php if($info['sex']=='男') echo 'selected'; ?>>男</option>
                    <option value="女" <?php if($info['sex']=='女') echo 'selected'; ?>>女</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">旅游路线</label>
            <div class="col-sm-3">
                <select  name="travel_list_id" id="travel_list_id" placeholder="旅游路线"  class="form-control" onchange="getdatelist()">
                    <?php foreach ($luxianlist as $key =>$val){?>
                        <option value="<?php echo $key?>" <?php if($info['travel_list_id']==$key) echo 'selected'; ?>><?php echo $val?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">出行日期</label>
            <div class="col-sm-3">
                <select  name="travel_date_id" id="travel_date_id"  placeholder="出行日期"  class="form-control">
                    <?php foreach ($datelist as $key =>$val){?>
                        <option value="<?php echo $key?>" <?php if($info['travel_list_id']==$key) echo 'selected'; ?>><?php echo $val?></option>
                    <?php }?>
                </select>
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
                <select  name="company_id" id="company_id"  placeholder="出行日期"  class="form-control" onchange="getuserlist()">
                    <?php foreach ($jigoulist as $key =>$val){?>
                        <option value="<?php echo $key?>" <?php if($info['company_id']==$key) echo 'selected'; ?>><?php echo $val?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">营销员</label>
            <div class="col-sm-3">
                <select  name="travel_user_id" id="travel_user_id"  placeholder="营销员"  class="form-control">
                    <?php foreach ($userlist as $key =>$val){?>
                        <option value="<?php echo $val['id']?>" <?php if($info['travel_user_id']==$val['id']) echo 'selected'; ?>><?php echo $val['name']?></option>
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
    function getuserlist(){
        var company_id = $("#company_id").val();
        var url = "<?php echo Url::to(['getuser']);?>";
        var html = "";
        $('#travel_user_id').html('');
        $.post(url,{company_id:company_id},function(json){
            if(json.status == 1){
                $.each(json.data,function(key,val){
                    html += '<option value="'+val.id+'">'+val.name+'</option>';
                });
                $('#travel_user_id').html(html);
            }else{
                alert(json.msg);
            }
        });
    }
    function getdatelist(){
        var travel_list_id = $("#travel_list_id").val();
        var url = "<?php echo Url::to(['getdate']);?>";
        var html = "";
        $('#travel_date_id').html('');
        $.post(url,{travel_list_id:travel_list_id},function(json){
            if(json.status == 1){
                console.log(json.data);
                $.each(json.data,function(key,val){
                    html += '<option value="'+key+'">'+val+'</option>';
                });
                $('#travel_date_id').html(html);
            }else{
                alert(json.msg);
            }
        });
    }


</script>

