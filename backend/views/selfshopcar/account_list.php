
<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>账号管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-plus"></i>
        </button>

        <div class="form-group">
            <select id="status" name="status"  placeholder="微信昵称或商户名称"  class="form-control"  >
                <option value="">搜索类型</option>
                <option value="1">商户名称</option>
                <option value="2">账户姓名</option>
                <option value="3">微信昵称</option>
                <option value="4">账号</option>
            </select>
        </div>
        <div class="form-group"><input type="text" id="keywords"   class="form-control" placeholder="搜索内容"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID</th>
        <th data-field="shop_name">商户名称</th>
        <th data-field="payee_name">账户姓名</th>
        <th data-field="nickname">修改人</th>
        <th data-field="payee_account">提现账号</th>
        <th data-field="payee_bank">账号开户行</th>
        <th data-field="admin_mobile">账户手机号</th>
        <th data-field="status">状态</th>
        <th data-field="c_time">添加时间</th>
        <th data-field="u_time">修改时间</th>
        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>

    </tr>
    </thead>
</table>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['selfshopcar/account_edit']);?>",
        listurl='<?php echo Url::to(['selfshopcar/account_list']); ?>',
        durl="";
</script>
<script type="text/javascript" src="../js/handle_car_account.js"></script>

