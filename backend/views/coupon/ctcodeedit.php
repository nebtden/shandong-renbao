<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/10 0010
 * Time: 下午 3:16
 */
use yii\helpers\Url;
use yii\helpers\Html;
use Faker\date;
?>
<div class="page-header am-fl am-cf">
    <h4>诚泰客户信息管理 <small>&nbsp;/&nbsp;客户信息编辑</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/ctcodeedit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">客户姓名</label>
            <div class="col-sm-3">
                <input type="text" required placeholder="客户姓名" name="customer_name"   value="<?php  echo $info['customer_name']; ?>" class="form-control"  />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">客户身份证号</label>
            <div class="col-sm-3">
                <input type="text" placeholder="客户身份证号" name="customer_code"   value="<?php  echo $info['customer_code']; ?>" class="form-control"  />
            </div>
        </div>
        <?php  if($info['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $info['id']; ?>">
        <?php }?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn am-btn-success">保存</button>
            </div>
        </div>
    </form>
</div>

