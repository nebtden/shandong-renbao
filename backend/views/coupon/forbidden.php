<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/20 0020
 * Time: 下午 2:30
 */
use yii\helpers\Url;
?>

<style>

</style>
<div class="page-header am-fl am-cf">
    <h4>
        <h4>券包管理 <small>&nbsp;/&nbsp;批量禁用</small></h4>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/forbidden']) ?>" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">批号</label>
            <div class="col-sm-3">
                <input type="text" required name="batch_no" class="form-control" placeholder="请输入批号" value="">
            </div>
        </div>
        <div class="form-group uprescue">
            <label for="inputPassword3" class="col-sm-2 control-label" >状态</label>
            <div class="col-sm-3">
                <select id="status" name="status"  placeholder="状态"  class="form-control">
                    <?php foreach ($status as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <input type="hidden" name="type" value="0">
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

    $("form").submit(function(){
        var $status=$('#status').val();
        if($status=='1'){
            return true;
        }
        if(confirm("确定要禁用此批券吗？"))
        {
            return true;
        }else{
            return false;
        }
    });
</script>
