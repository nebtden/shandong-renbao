<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/21 0021
 * Time: 下午 2:53
 */

use yii\helpers\Url;
use yii\helpers\Html;
use Faker\date;
?>
<div class="page-header am-fl am-cf">
    <h4>免兑换手机管理 <small>&nbsp;/&nbsp;批号修改</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/batchnoedit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">（旧）券包批号</label>
            <div class="col-sm-3">
                <input type="text" required placeholder="（旧）券包批号" name="oldbatchno"   value="" class="form-control"  />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">（新）券包批号</label>
            <div class="col-sm-3">
                <input type="text" required placeholder="（新）券包批号" name="newbatchno"   value="" class="form-control"  />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">数量</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="100" required placeholder="数量" name="num"   value="" class="form-control"  />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>
<script>

    $('form').submit(function(){
        if (! confirm("确定要修改批号？")) return false;
    });

</script>
