<?php
use yii\helpers\Url;
?>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default" >
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group"><input type="text" id="car_card"   class="form-control" placeholder="卡号"></div>
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
        <div class="form-group">
            <select  name="status" id="status"  placeholder="状态"  class="form-control">
                <option value="">选择卡的状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group"><input type="text" id="company"   class="form-control" placeholder="发卡公司"></div>
        <div class="form-group"><input type="text" id="personname"   class="form-control" placeholder="业务员名称"></div>
        <div class="form-group"><input type="text" id="batch_no"   class="form-control" placeholder="批号"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <a href="<?php echo Url::to(['car/leadin']) ;?>" class="btn btn-info">excel导入</a>
        <a href="<?php echo Url::to(['car/generate']);?>" class="btn btn-info">批量生成</a>
        <button type="button" class="btn btn-info" onclick="downloadExcel()">下载导入模板</button>
        <a href="<?php echo Url::to(['car/disable_card']);?>" class="btn btn-info">批量禁用/解禁</a>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="card_num">卡号</th>
        <th data-field="card_password">卡密码</th>
        <th data-field="card_amount">卡的面值</th>
        <th data-field="card_region">卡可用区域</th>
        <th data-field="card_status">卡的状态</th>
        <th data-field="t_sum">回收系数</th>
        <th data-field="company">公司名陈</th>
        <th data-field="personname">业务员姓名</th>
        <th data-field="personcode">业务员编号</th>
        <th data-field="expire_time">过期时间</th>
        <th data-field="batch_no">批号</th>
        <th data-field="is_bisable">是否禁用</th>
        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script type="text/javascript">
    function downloadExcel(){
        window.location.href = "<?php echo Url::to(['car/dexcel']) ?>";
    }
</script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['car/card_edit']);?>",
        listurl='<?php echo Url::to(['car/card_list']); ?>',
        durl="<?php echo Url::to(['news/news_del']); ?>";
</script>
<script src="../js/handle_card_list.js" ></script>
<script type="text/javascript">
    function getcity(){
        var code = $("#shop_province").val();
        var url = "<?php echo Url::to(['car/getcity']);?>";
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
        var url = "<?php echo Url::to(['car/getarea']);?>";
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