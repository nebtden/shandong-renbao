<?php
use yii\helpers\Url;
?>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group"><input type="text" id="payee"   class="form-control" placeholder="提现人姓名"></div>
        <div class="form-group"><input type="text" id="account"   class="form-control" placeholder="提现账号"></div>
        <div class="form-group"><input type="text" id="shop_name"   class="form-control" placeholder="提现商户名称"></div>
        <div class="form-group">
            <select  name="status" id="status"  placeholder="状态"  class="form-control">
                <option value="">选择打款状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="withdrawals_no">提现单号</th>
        <th data-field="payee">提现人姓名</th>
        <th data-field="amount">提现金额</th>
        <th data-field="account">提现账号</th>
        <th data-field="account_bank">账号开户行</th>
        <th data-field="shop_name">商户名称</th>

        <th data-field="status">状态</th>
        <th data-field="playmoney_time">打款时间</th>

        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
        <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
        <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
        <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
        <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script type="text/javascript" src="../js/handle_withdrawals_log.js"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['car/withdrawals_edit']);?>",
        listurl='<?php echo Url::to(['car/withdrawals_log']); ?>',
        durl="<?php echo Url::to(['news/news_del']); ?>";



    $("#download").click(function(){
        var opt1=$('#payee').val();
        var opt2=$('#account').val();
        var opt3=$('#shop_name').val();
        var opt4=$('#status').val();
        var url = '<?php echo Url::to(["car/download_w"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            payee:opt1,
            account:opt2,
            shop_name:opt3,
            status:opt4
    },function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    content += '<li><a href="'+this.url+'">'+this.name+'</a>';
                });
                content += "</ul>";
                layer.open({
                    type: 1,
                    title: 'Excel导出',
                    area: ['600px', '360px'],
                    shadeClose: true, //点击遮罩关闭
                    content: content
                });
            }else{
                alert(json.msg);
            }
        });
    });
</script>

