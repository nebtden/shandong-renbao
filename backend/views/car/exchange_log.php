<?php
use yii\helpers\Url;
?>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default" >
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
        <div class="form-group">
            <select  name="status" id="status"  placeholder="状态"  class="form-control">
                <option value="">选择审核状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="form-group"><input type="text" id="company"   class="form-control" placeholder="发卡公司名称"></div>
        <div class="form-group"><input type="text" id="nickname"   class="form-control" placeholder="客户微信昵称"></div>
        <div class="form-group"><input type="text" id="personname"   class="form-control" placeholder="业务员姓名"></div>
        <div class="form-group"><input type="text" id="batch_no"   class="form-control" placeholder="卡批号"></div>
        <div class="form-group"><input type="text" id="shop_name"   class="form-control" placeholder="回购商户"></div>

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
        <th data-field="exchange_card_num">回收卡卡号</th>
        <th data-field="exchange_card_amount">回收卡的面值</th>
        <th data-field="nickname">回收者微信昵称</th>
        <th data-field="exchange_name">商户名称</th>
        <th data-field="carno">车牌号</th>
        <th data-field="exchange_tel">被回收者手机号码</th>
        <th data-field="exchange_time">回收时间</th>
        <th data-field="company">发卡公司</th>
        <th data-field="personname">业务员姓名</th>
        <th data-field="batch_no">卡批号</th>
        <th data-field="status">状态</th>
        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>

    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script type="text/javascript" src="../js/handle_exchange_log.js"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['car/exchange_edit']);?>",
        listurl='<?php echo Url::to(['car/exchange_log']); ?>',
        durl="<?php echo Url::to(['news/news_del']); ?>";

    $("#download").click(function(){
        var opt1=$('#company').val();
        var opt2=$('#nickname').val();
        var opt3=$('#personname').val();
        var opt4=$('#batch_no').val();
        var opt5=$('#shop_name').val();
        var opt6=$('#status').val();
        var url = '<?php echo Url::to(["car/download_e"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            company:opt1,
            nickname:opt2,
            personname:opt3,
            batch_no:opt4,
            shop_name:opt5,
            status:opt6
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

