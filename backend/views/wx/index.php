<?php
use yii\helpers\Url;
?>
<div class="page-header">
    <h4>公众号设置 </h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
      <?php if(!$wxusr){?>
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
     <?php }?>   
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
     <?php if($wxusr){?>   
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
     <?php }?>   
       <!-- <div class="form-group"><input type="text" id="uname"   class="form-control" placeholder="用户名"></div>
        <div class="form-group"> <select id="sec"   placeholder="用户ID"  class="form-control" >
                <option value="">请选择</option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>-->
    </form>
</div>
<table class="table table-bordered"  >
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th data-field="headpic">头像</th>
        <th data-field="wxname">公众号名称</th>
        <th data-field="weixin">微信号</th>
        <th data-field="token">Token</th>
        <th data-field="url">Url</th>
        <th data-field="status">状态</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
        <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
        <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
        <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
        <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
    </tr>
    </thead>
</table>

<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon= 1,more,eurl="<?php echo Url::to(['wx/wx_edit']);?>",
        listurl='<?php echo Url::to(['wx/index']); ?>',
        durl="<?php echo Url::to(['wx/wx_del']); ?>",height = $(window).height()-120;
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
           window.location.href='<?php echo Url::to(['wx/wx_edit']); ?>';
        });

    });
</script>
<script src="../js/handle_data.js"></script>