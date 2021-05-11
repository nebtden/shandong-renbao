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
    <h4>VIP管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline" id="upfh">
        <div class="form-group"><input type="text" id="uname" name="uname"  class="form-control" placeholder="请输入真实姓名或手机号"></div>
        <input type="hidden" name="sec" id="sec" value="0">
        <div class="form-group">
            <select id="status" name="status"  placeholder="状态"  class="form-control">
                <option value="">状态 </option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="s_time" id="s_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索录入开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="e_time" id="e_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索录入结束时间">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info"  onclick="window.location.href='<?php echo Url::to(['member/editvip']);?>';">
            <i class="glyphicon glyphicon-plus"></i> 添加
        </button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th class="table-check" data-field="id">编号ID
        </th>
        <th  data-field="uid">会员ID</th>
        <th  data-field="realname">真实姓名</th>
        <th data-field="mobile">手机号</th>
        <th data-field="email">邮箱</th>
        <th data-field="company">公司</th>
        <th data-field="duties">职务</th>
        <th data-field="is_vip">是否是VIP</th>
        <th data-field="status">状态</th>
        <th data-field="vip_time">认证时间</th>
        <th data-field="c_time">创建时间</th>
        <th data-field="u_time">最后修改时间</th>
        <th data-field="is_web">用户来源</th>
        <th data-field="remarks">备注</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script type="text/javascript">
    var type='id',
        stext,
        order='desc',
        ids='',
        imgcon=1,
        more,
        eurl="<?php echo Url::to(['member/editvip']);?>",
        listurl='<?php echo Url::to(['member/vip']); ?>',
        durl="";
</script>
<script src="../js/handle_data.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>


