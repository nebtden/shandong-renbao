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
        <th data-field="out_biz_no">流水号</th>
        <th data-field="account_id">账户账户id</th>
        <th data-field="account_type_text">账户类型</th>
        <th data-field="payee_account">账号</th>
        <th data-field="payee_real_name">收款人姓名</th>
        <th data-field="amount">转账金额</th>
        <th data-field="status_text">状态</th>
        <th data-field="err_msg">状态描述</th>
        <th data-field="payer_show_name">转账人</th>
        <th data-field="remark">备注</th>
        <th data-field="c_time_text">时间</th>
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
        eurl = "",
        listurl = '<?php echo Url::to(['finance/transferlog']); ?>',
        durl = "",
        height = $(window).height() - 120;
</script>
<script src="../js/my_handle_data.js"></script>


