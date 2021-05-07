<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/21 0021
 * Time: 上午 10:10
 */

use yii\helpers\Url;

?>

<style>

</style>
<div class="page-header am-fl am-cf">
    <h4>
        <h4>券包管理
            <small>&nbsp;/&nbsp;批量修改过期时间</small>
        </h4>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal" method="post" action="<?php echo Url::to(['coupon/updeadline']) ?>">
        <div class="form-group uprescue">
            <label for="inputPassword3" class="col-sm-2 control-label">依据条件</label>
            <div class="col-sm-3">
                <select id="condition_type" name="condition_type" placeholder="依据条件" class="form-control" onchange="changes_condition(this)">
                    <option value="0">批号</option>
                    <option value="1">号码段</option>
                </select>
            </div>
        </div>

<!-- 以批号为条件-->
        <span id="xxz_batchno">
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">批号</label>
                <div class="col-sm-3">
                    <input type="text" id="batch_nb"  name="batch_nb" class="form-control" placeholder="请输入批号" value="">
                </div>
            </div>
        </span>
<!-- 以号码段为条件-->
        <span id="xxz_number">
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">开始号码</label>
                <div class="col-sm-3">
                    <input type="number"  id="start_no" name="start_no" class="form-control" placeholder="请输入开始号码" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">结束号码</label>
                <div class="col-sm-3">
                    <input type="number"  id="end_no" name="end_no" class="form-control" placeholder="请输入结束号码" value="">
                </div>
            </div>
        </span>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">过期时间</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" readonly="readonly" id="use_limit_time" name="use_limit_time"
                       onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="不能为空">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-info">保存</button>
            </div>
        </div>
    </form>
</div>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#xxz_batchno').show();
    $('#xxz_number').hide();
    function changes_condition(_this) {
        if($(_this).val()=='1'){
            $('#xxz_number').show();
            $('#xxz_batchno').hide();
        }else {
            $('#xxz_number').hide();
            $('#xxz_batchno').show();
        }
    }
    $("form").submit(function () {
        var condition_type = $('#condition_type').val(),
            batch_nb = $('#batch_nb').val(),
            start_no = $('#start_no').val(),
            use_limit_time = $('#use_limit_time').val(),
            end_no = $('#end_no').val();


        if (condition_type == '0') {

            if(batch_nb=='' || batch_nb==null || batch_nb.length > 20){
                alert('批号不能为空或批号过长');
                return false;
            }
        }else {

            if(start_no=='' || start_no==null || start_no.length > 15 || start_no.length < 14){
                alert('开始号码不能为空、过长或过短');
                return false;
            }
            if(end_no=='' || end_no==null || end_no.length > 15 || end_no.length < 14){
                alert('结束号码不能为空或过长、过长或过短');
                return false;
            }
            if(start_no > end_no){
                alert('开始号码不能大于结束号码');
                return false;
            }
        }

        if(use_limit_time=='' || use_limit_time==null ){
            alert('过期时间不能为空');
            return false;
        }

        if (confirm("确定要修改此批券包的过期时间吗？")) {
            return true;
        } else {
            return false;
        }
    });



</script>
