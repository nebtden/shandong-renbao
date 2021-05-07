<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/23 0023
 * Time: 下午 3:01
 */
use yii\helpers\Url;
?>

<div class="page-header am-fl am-cf">
    <h4>账号管理 <small>&nbsp;/&nbsp;编辑账号</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">商户名称</label>
            <div class="col-sm-3">
                <input type="text" name="shop_name" class="form-control" value="<?php echo $data['shop_name'];?>" id="inputEmail3" placeholder="名称" disabled="disabled" >
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">修改人微信昵称</label>
            <div class="col-sm-3">
                <input type="text" name="nickname" class="form-control" value="<?php echo $data['nickname'];?>" id="inputEmail3" placeholder="名称" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">账户姓名</label>
            <div class="col-sm-3">
                <input type="text" name="payee_name" id="payee_name" class="form-control" value="<?php echo $data['payee_name'];?>" id="inputEmail3" placeholder="账户姓名" >
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">提现账号</label>
            <div class="col-sm-3">
                <input type="text" name="payee_account" id="payee_account" class="form-control" value="<?php echo $data['payee_account'];?>" id="inputEmail3" placeholder="提现账号">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">账号开户行</label>
            <div class="col-sm-3">
                <input type="text" name="payee_bank" id="payee_bank" class="form-control" value="<?php echo $data['payee_bank'];?>" id="inputEmail3" placeholder="账号开户行" >
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">账户手机号</label>
            <div class="col-sm-3">
                <input type="text" name="admin_mobile" id="admin_mobile" class="form-control" value="<?php echo $data['admin_mobile'];?>" id="inputEmail3" placeholder="账户手机号" >
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">账号状态</label>
            <div class="col-sm-3">
                <select name="status" id="status"  class="form-control" >
                    <option value="1" <?php if($data['status']==1){ ?> selected = "selected"<?php }?>>正常</option>
                    <option value="2" <?php if($data['status']==2){ ?> selected = "selected"<?php }?>>禁用</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">账号类型</label>
            <div class="col-sm-3">
                <select name="type" id="type"  class="form-control" >
                    <option value="1" <?php if($data['type']==2){ ?> selected = "selected"<?php }?>>银行</option>
                    <option value="2" <?php if($data['type']==1){ ?> selected = "selected"<?php }?>>支付宝</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">添加时间</label>
            <div class="col-sm-3">
                <input type="text"  name="c_time" class="form-control" value="<?php echo $data['c_time'];?>" id="inputEmail3" placeholder="添加时间" disabled="disabled">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="button" class="btn btn-info" onclick="edit_payee(this)">提交修改</button>
            </div>
        </div>
        <input type="hidden"  name="id" id="payee_id"  value="<?php echo $_REQUEST['id']; ?>">
    </form>
</div>

<script>
    //确认打款操作
    function edit_payee(_this){
        if(!confirm('您确认修改该账号信息？')) return false;
        var opt1 = $('#payee_id').val(),
            opt2 = $('#payee_name').val(),
            opt3 = $('#payee_account').val(),
            opt4 = $('#payee_bank').val(),
            opt5 = $('#admin_mobile').val(),
            opt6 = $('#status').val();


        if(opt2.length==0 ||  opt2.length>20){
            alert('请输入合理的账户姓名');
            return false;
        }
        if(opt3.length==0 ||  opt3.length>25){
            alert('请输入正确的提现账号');
            return false;
        }
        if(opt4.length==0 ||  opt4.length>50){
            alert('请输入正确的账号开户行');
            return false;
        }
        if(!/^1[0-9]\d{9}$/.test(opt5)){
            alert('请输入正确的联系人手机号码');
            return false;
        }
        var url = '<?php echo Url::to(['car/account_edit']);?>';
        $(_this).removeAttr('onclick');
        $.post(url,{payee_id:opt1,payee_name:opt2,payee_account:opt3,payee_bank:opt4,admin_mobile:opt5,status:opt6},function(json){

            if(json.status == 1){
                window.location.href= '<?php echo Url::to(['car/account_list']);?>';
            }else{
                alert(json.msg);
            }
            $(_this).attr('onclick','edit_payee(this);');
        });
    };

</script>