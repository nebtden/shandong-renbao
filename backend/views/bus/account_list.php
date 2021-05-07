
<?php
use yii\helpers\Url;
?>
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
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="shop_name">商户名称</th>

        <th data-field="amount">金额</th>
        <th data-field="c_time">添加时间</th>

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
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['car/account_edit']);?>",
        listurl='<?php echo Url::to(['car/account_list']); ?>',
        durl="<?php echo Url::to(['news/news_del']); ?>";
</script>
<script type="text/javascript" src="../js/handle_car_account.js"></script>

