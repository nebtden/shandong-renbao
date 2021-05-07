<?php
use yii\helpers\Url;
?>
<style>
    .layui-layer-content{
        padding-left:23px;
    }
    .layui-layer-rim{ font-size: 14px;}
</style>
<div class="page-header am-fl am-cf">
    <h4>会员管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
        <div class="form-group"><input type="text" id="keywords" name="keywords"  class="form-control"  <?php if($search['keywords']) {?>  value="<?php echo $search['keywords']; ?>" placeholder="<?php echo $search['keywords']; ?>"   <?php } else { ?> placeholder="会员卡、姓名、昵称、手机号码"<?php } ?>  ></div>
        <div class="form-group">
            <select name="status" id="status"  class="form-control" >
                <option value="-1" >请选择</option>
                <option value="0" >未关注</option>
                <option value="1" >已关注</option>
                <option value="2">取消关注</option>
            </select>
        </div>

        <button type="button" class="btn btn-info" id="msousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
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
        <th  data-field="nickname">会员名称</th>
        <th data-field="telphone">手机</th>
        <th data-field="province">省份</th>
        <th  data-field="city">城市</th>
        <th  data-field="status">会员关注状态</th>
        <th  data-field="source">会员关注来源</th>
        <th  data-field="subscribe_time">关注时间</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
        <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
        <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
        <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
        <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
    </tr>
    </thead>
</table>
<input type="hidden" id="card" value="">
<script src="../js/layer/layer.js"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['member/edit']);?>",
        listurl='<?php echo Url::to(['member/list']); ?>',
        durl="<?php echo Url::to(['order/order_del']); ?>";
    $('#download').click(function () {
        var keywords=$('input[name=keywords]').val();
        var card=$('#card').val();
        var status=$("select[name=status]").val();
        $.post('<?php echo Url::to(['member/download']);?>',{keywords:keywords,status:status,card:card},function(s){
            layer.open({
                type: 1,
                title: '<font style="font-weight:bold;font-size:14px;">导出数据</font>',
                skin: 'layui-layer-rim', //加上边框
                area: ['420px', '240px'], //宽高
                content: s
            });
        });
    });
</script>
<script src="../js/handle_data_member.js" ></script>


