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
    <h4>会员管理 <small>&nbsp;/&nbsp;提现管理</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
        <div class="form-group"><input type="text" id="keywords" name="keywords"  class="form-control"  <?php if($search['keywords']) {?>  value="<?php echo $search['keywords']; ?>" placeholder="<?php echo $search['keywords']; ?>"   <?php } else { ?> placeholder="会员卡、姓名、昵称、手机号码"<?php } ?>  ></div>
        <div class="form-group">
            <select  class="form-control"  name="status" style="margin:0px 15px;height:40px;">
                <option value="-1" >选择审核状态</option>
                <option value="0" >未审核</option>
                <option value="3"  >待确认</option>
                <option value="3" >待支付</option>
                <option value="2"  >已支付</option>
            </select>
        </div>
        <button type="button" class="btn btn-info" id="msousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;height: 600px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
            <!-- <input type="checkbox" id="allCheck"/>	申请金额							状态-->
        </th>
        <th  data-field="nickname">会员姓名</th>
        <th  data-field="account">支付账号</th>
        <th  data-field="amount">申请金额</th>
        <th data-field="tax">扣税金额</th>
        <th data-field="money">实际提现额</th>
        <th data-field="cash_type">提现类型</th>
        <th  data-field="cash_name">提现银行</th>
        <th  data-field="applytime">申请时间</th>
        <th  data-field="paytime">支付时间</th>
        <th  data-field="status">状态</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
        <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
        <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
        <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
        <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['member/edit']);?>",
        listurl='<?php echo Url::to(['member/cash_apply']); ?>',
        durl="<?php echo Url::to(['order/order_del']); ?>";
    $('#download').click(function () {
        var keywords=$('input[name=keywords]').val();
        var card="<?php echo $_REQUEST['card']; ?>";
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
<script src="../js/handle_data_cashapply.js" ></script>


