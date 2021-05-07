<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/14 0014
 * Time: 上午 11:50
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>兑换卡管理 <small>&nbsp;/&nbsp;批量修改兑换卡状态</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['car/disable_card']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">批号</label>
            <div class="col-sm-3">
                <input type="text" name="batch_no" class="form-control" value="" id="inputEmail3" placeholder="批号"  >
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡的状态</label>
            <div class="col-sm-3">
                <select  name="status"  placeholder="状态"  class="form-control">
                    <?php foreach ($status as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
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