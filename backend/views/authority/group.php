<?php
use yii\helpers\Url;
?>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
       <!-- <div class="form-group"><input type="text" id="uname"   class="form-control" placeholder="用户名"></div>
        <div class="form-group"> <select id="sec"   placeholder="用户ID"  class="form-control" >
                <option value="">请选择</option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>-->
    </form>
</div>
<table class="table table-bordered"  style=" width: 700px;height:300;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
         <th class="table-check" data-field="id">编号ID
           <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="group_name">用户组名称</th>
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

    var type='id',stext,order='desc',ids='',imgcon= 1,more,eurl="<?php echo Url::to(['authority/group_edit']);?>",
        listurl='<?php echo Url::to(['authority/group']); ?>',
        durl="";
    <?php if($_REQUEST['id']) echo 'var imgcon;'; else echo ' var imgcon=1;';?>
    $(function(){
        $('#saveupt').click(function(){
            var op1=$('#name').val();
            var op2=$('#sort').val();
            var id=$('#id').val();
            $.post(upturl,{op1:op1,op2:op2,id:id},function(s){ console.log(s);
                $('.table').bootstrapTable('refresh');
            });

        });

        $('.btn-default').eq(0).click(function(){
           window.location.href='<?php echo Url::to(['authority/group_edit']); ?>';
        });

    });
</script>
<script src="../js/handle_data.js"></script>