<?php

use yii\helpers\Url;

?>
<div class="page-header am-fl am-cf">
    <h4>
        财务
        <small>&nbsp;&nbsp;/ 用户账户列表</small>
    </h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline" id="upfh">
        <div class="form-group"><input type="text" id="uname" name="uname" class="form-control"
                                       placeholder="请输入真实姓名或手机号"></div>
        <input type="hidden" name="sec" id="sec" value="0">
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索
        </button>
        <button type="button" class="btn btn-info"
                onclick="window.location.href='<?php echo Url::to(['finance/editaccount']); ?>';">
            <i class="glyphicon glyphicon-plus"></i> 添加
        </button>
    </form>
</div>
<table class="table table-bordered table-condensed table-responsive" style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="account_type_text">账户类型</th>
        <th data-field="payee_account">账号</th>
        <th data-field="realname">开户名</th>
        <th data-field="status">状态（1正常，0禁用）</th>
        <th data-field="u_time_text">最后修改时间</th>
        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter"
            data-events="actionEvents">操作
        </th>
        <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
        <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
        <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
        <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
    </tr>
    </thead>
</table>
<script type="text/javascript">
    var type = 'id',
        stext,
        order = 'desc',
        ids = '',
        imgcon = 1,
        more,
        eurl = "<?php echo Url::to(['finance/editaccount']);?>",
        listurl = '<?php echo Url::to(['finance/index']); ?>',
        durl = "",
        height = $(window).height() - 120;
    var hrefArr = [
        {href: '<?php echo Url::to(['finance/transfer']);?>', field: 'id', title: '转账'}
    ];
</script>
<script src="../js/my_handle_data.js"></script>


