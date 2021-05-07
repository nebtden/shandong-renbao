<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 上午 11:38
 */

use yii\helpers\Url;
?>
<style>
    .layui-layer-content{
        padding-left:23px;
    }
    .layui-layer-rim{ font-size: 14px;}
</style>
<div class="page-header am-fl am-cf">
    <h4>券包管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group"><input type="number" min="10000000000" max="19999999999" id="mobile" name="mobile"  class="form-control"  placeholder="激活手机"></div>
        <div class="form-group"><input type="text" id="package_sn" name="package_sn"  class="form-control"  placeholder="搜索券包号码"></div>
        <div class="form-group"><input type="text" id="package_pwd" name="package_pwd"  class="form-control"  placeholder="搜索券包兑换码"></div>
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
        <th data-field="uid">用户ID</th>
        <th  data-field="package_sn">卡包号码</th>
        <th  data-field="package_pwd">卡包兑换码</th>
        <th data-field="info">券信息（面值，数量，券批号）</th>
        <th data-field="use_limit_time">过期时间</th>
        <th data-field="use_time">使用时间</th>
        <th data-field="nickname">微信昵称</th>
        <th  data-field="status">状态</th>
        <th  data-field="batch_nb">批号</th>
        <th  data-field="companyid">客户公司</th>
        <th  data-field="mobile">激活手机</th>
        <th  data-field="source">用户来源</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_data_package.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['service/packagelist']); ?>',
        durl="";
</script>



