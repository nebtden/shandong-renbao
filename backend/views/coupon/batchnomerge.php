<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7 0007
 * Time: 下午 1:50
 */

use yii\helpers\Url;
?>

<style>

</style>
<div class="page-header am-fl am-cf">
    <h4>
        券批号合并 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/batchnomerge']) ?>" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">主批号</label>
            <div class="col-sm-3">
                <input type="text" required name="oldbatch_no" class="form-control" placeholder="请输入合并之后的批号" value="">
            </div>
        </div>
        <span id="newbatch">
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">副批号1</label>
                <div class="col-sm-3">
                    <input type="text" required name="newbatch_no[]" class="form-control" placeholder="请输入需要合并的批号" value="">
                </div>
            </div>
        </span>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-info">保存</button>
                <button type="button" class="btn btn-default" onclick="add()">添加副批号</button>
            </div>
        </div>
    </form>
</div>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    function add() {
        var newlength=$("#newbatch input").length ;
        if(newlength==10){
            alert('最多添加10个');
            return false;
        }
        var html='';
            html+='<div class="form-group">';
            html+='<label for="inputPassword3" class="col-sm-2 control-label">副批号'+(newlength+1)+'</label>';
            html+='<div class="col-sm-3">';
            html+='<input type="text" required name="newbatch_no[]" class="form-control" placeholder="请输入需要合并的批号" value="">';
            html+='</div>';
            html+='</div>';


        $("#newbatch").append( html );
    }

</script>
