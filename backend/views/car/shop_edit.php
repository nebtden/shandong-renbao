<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>

<div class="page-header am-fl am-cf">
    <h4>商户管理 <small>&nbsp;/&nbsp;商户详情</small></h4>
</div>

<form class="form-horizontal"   method="post" action="<?php echo Url::to(['car/shop_edit']) ?>" >
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户编号</label>
        <div class="col-sm-3">
            <input type="text" name="shop_id" class="form-control" value="<?php echo $data['id'];?>"  placeholder="名称" disabled="disabled" >
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户门头照</label>
        <div class="col-sm-3" style="float: left;">
            <?php
            $style = '';
            if ($data['shop_pic']) {
                echo '<div id="curpiclist" style="height: 140px;">';
                echo '<ul   style="background:url(' . $data['shop_pic'] . ');background-size:cover;background-position:center;background-repeat:no-repeat; width:150px; height:130px; float:left; margin-right:10px;"><img src="../images/imgclose1.png" style="float:right;padding:5px;"  ></ul>';
                $style = 'style="margin-top:10px;"';
                echo '</div>';
            }
            ?>
            <div>
                <div id="hadpic" <?php echo $style; ?>></div>
                <input type="hidden" name="shop_pic" id="shop_pic" value="<?php echo $data['shop_pic']; ?>"/>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户名称</label>
        <div class="col-sm-3">
            <input type="text" name="shop_name" class="form-control" value="<?php echo $data['shop_name'];?>"  placeholder="名称" >
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户联系电话</label>
        <div class="col-sm-3">
            <input type="text" name="shop_tel" class="form-control" value="<?php echo $data['shop_tel'];?>"  placeholder="名称" >
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户地址</label>
        <div class="col-sm-3">
            <select id="shop_province" name="province"  placeholder="省"  class="form-control" onchange="getcity();"  >
                <?php foreach ($province as $val){?>
                    <option value="<?php echo $val['code']?>" <?php if($address[0]['code'] == $val['code']){?> selected = "selected" <?php }?>><?php echo $val['name']?></option>
                <?php }?>
            </select>
            <select id="shop_city" name="city"  placeholder="市"  class="form-control"  onchange="getarea()">
                <option value="<?php echo $address[1]['code']?>"><?php echo $address[1]['name']?></option>
            </select>
            <select id="shop_area" name="area"  placeholder="县或区"  class="form-control" onchange="getaddress()" >
                <option value="<?php echo $address[2]['code']?>"><?php echo $address[2]['name']?></option>
            </select>
            <input type="text" id="shop_address" name="shop_address" class="form-control" value="<?php echo $data['shop_address'];?>"  placeholder="商户地址">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户信用代码</label>
        <div class="col-sm-3">
            <input type="text" name="shop_credit_code" class="form-control" value="<?php echo $data['shop_credit_code'];?>"  placeholder="名称" >
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户注册时间</label>
        <div class="col-sm-3">
            <input type="text" class=" form-control" name="shop_register_time" id="shop_register_time" value="<?php echo $data['shop_register_time'];?>" readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户注册地址</label>
        <div class="col-sm-3">
            <input type="text" name="register_address" class="form-control" value="<?php echo $data['register_address'];?>">
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户对公账号</label>
        <div class="col-sm-3">
            <input type="text"  name="shop_account" class="form-control" value="<?php echo $data['shop_account'];?>"  placeholder="提成系数">
        </div>
    </div>


    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户对公账号开户行</label>
        <div class="col-sm-3">
            <input type="text" name="shop_account_bank" class="form-control" value="<?php echo $data['shop_account_bank'];?>"  placeholder="提成系数">
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">提交申请时间</label>
        <div class="col-sm-3">
            <input type="text"  name="shop_apply_time" class="form-control" value="<?php echo $data['shop_apply_time'];?>"  placeholder="提交申请时间" disabled="disabled">
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">通过时间</label>
        <div class="col-sm-3">
            <input type="text"  name="adopt_time" class="form-control" value="<?php echo $data['adopt_time'];?>"  placeholder="提交申请时间" disabled="disabled">
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户状态</label>
        <div class="col-sm-3">
            <select id="shop_status" name="shop_status"  placeholder="商户状态"  class="form-control" >
                <option value="0" <?php if($data['shop_status']==0){ ?> selected = "selected"<?php }?>>冻结</option>
                <option value="1" <?php if($data['shop_status']==1){ ?> selected = "selected"<?php }?>>已申请</option>
                <option value="2" <?php if($data['shop_status']==2){ ?> selected = "selected"<?php }?>>申请通过</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">可提现金额</label>
        <div class="col-sm-3">
            <input type="text"  name="amount" class="form-control" value="<?php echo $data['amount'];?>"  placeholder="可提现金额" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">已提现金额</label>
        <div class="col-sm-3">
            <input type="text"  name="already_amount" class="form-control" value="<?php echo $data['already_amount'];?>"  placeholder="已提现金额" disabled="disabled">
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">总收入</label>
        <div class="col-sm-3">
            <input type="text"  name="gross_income" class="form-control" value="<?php echo $data['gross_income'];?>"  placeholder="总收入" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">申请人微信昵称</label>
        <div class="col-sm-3">
            <input type="text"  name="nickname" class="form-control" value="<?php echo $data['nickname'];?>"  placeholder="申请人微信昵称" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">商户联系人名称</label>
        <div class="col-sm-3">
            <input type="text"  name="shop_preson_name" class="form-control" value="<?php echo $data['shop_preson_name'];?>"  placeholder="商户联系人名称">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">联系人手机号码</label>
        <div class="col-sm-3">
            <input type="text"  name="mobile" class="form-control" value="<?php echo $data['mobile'];?>"  placeholder="联系人手机号码">
        </div>
    </div>

    <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>">
    <div class="form-group" style="margin:0 auto;">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
<!--            --><?php //if($data['shop_status']=='已申请'){?>
<!--                <button type="button" class="btn btn-info" id="adopt" >通过</button>-->
<!--            --><?php //}?>
            <button type="submit" class="btn btn-info">保存</button>
        </div>
    </div>
</form>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script>
 ////////////////////////
 uploadImg('hadpic','shop_pic',100,100);
//////////////////////////////////////////////////////////////
function getcity(){
    var code = $("#shop_province").val();
    var url = "<?php echo Url::to(['car/getcity']);?>";
    var html = "";
    $('#shop_city').html('');
    $.post(url,{code:code},function(json){
        if(json.status == 1){
            $.each(json.data,function(){
                html += '<option value="'+this.code+'">'+this.name+'</option>';
            });
            $('#shop_city').append(html);
            getarea();
        }else{
            alert(json.msg);
        }
    });
}

function getarea(){
    var code = $("#shop_city").val();
    var url = "<?php echo Url::to(['car/getarea']);?>";
    var html = "";
    var str='';
    $('#shop_area').html('');
    $.post(url,{code:code},function(json){
        if(json.status == 1){
            $.each(json.data,function(){
                html += '<option value="'+this.code+'">'+this.name+'</option>';
            });
            $('#shop_area').append(html);
            str=$('#shop_province').find("option:selected").text()+$('#shop_city').find("option:selected").text()+$('#shop_area').find("option:selected").text();
            $('#shop_address').val(str);
        }else{
            alert(json.msg);
        }
    });
}
function  getaddress() {
    var str='';
    str=$('#shop_province').find("option:selected").text()+$('#shop_city').find("option:selected").text()+$('#shop_area').find("option:selected").text();
    $('#shop_address').val(str);
}

</script>
