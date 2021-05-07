<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>自营门店管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group">
            <select id="shop_province" name="shop_province"  placeholder="省"  class="form-control" onchange="getcity();" >
                <option value="">省</option>
                <?php foreach ($province as $val){?>
                <option value="<?php echo $val['code']?>" ><?php echo $val['name']?> </option>
                <?php }?>
            </select>
        </div>
        <div class="form-group">
            <select id="shop_city" name="shop_city"  placeholder="市"  class="form-control" onchange="getarea()" >
                <option value="">市</option>
            </select>
        </div>

        <div class="form-group">
            <select id="shop_area" name="shop_area"  placeholder="区"  class="form-control" >
                <option value="">县或区</option>
            </select>
        </div>
        <div class="form-group"><input type="text" id="shopname"   class="form-control" placeholder="商户名称"></div>
        <button type="button" class="btn btn-info" id="shopsousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="shop_name">商户名称</th>
        <th data-field="shop_address">商户地址</th>
        <th data-field="shop_tel">联系方式</th>
        <th data-field="shop_preson_name">联系人姓名</th>
        <th data-field="shop_credit_code">信用代码</th>
        <th data-field="shop_register_time">注册时间</th>
        <th data-field="shop_apply_time">申请时间</th>
        <th data-field="shop_status">状态</th>
        <th data-field="adopt_time">通过时间</th>
        <th data-field="nickname">申请人昵称</th>

        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
        <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
        <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
        <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
        <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
    </tr>
    </thead>
</table>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['selfshopcar/shop_edit']);?>",
        listurl='<?php echo Url::to(['selfshopcar/shop_list']); ?>',
        durl="";
</script>
<script src="../js/handle_car_shop.js" ></script>
<script type="text/javascript">
    function getcity(){
        var code = $("#shop_province").val();
        var url = "<?php echo Url::to(['selfshopcar/getcity']);?>";
        var html = "";
        $('#shop_city').html('<option value="">市</option>');
        $('#shop_area').html('<option value="">县或区</option>');
        $.post(url,{code:code},function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    html += '<option value="'+this.code+'">'+this.name+'</option>';
                });
                $('#shop_city').append(html);
            }else{
                alert(json.msg);
            }
        });
    }

    function getarea(){
        var code = $("#shop_city").val();
        var url = "<?php echo Url::to(['selfshopcar/getarea']);?>";
        var html = "";
        $('#shop_area').html('<option value="">县或区</option>');
        $.post(url,{code:code},function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    html += '<option value="'+this.code+'">'+this.name+'</option>';
                });
                $('#shop_area').append(html);
            }else{
                alert(json.msg);
            }
        });
    }


</script>

