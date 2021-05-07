<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17 0017
 * Time: 上午 10:17
 */

use yii\helpers\Url;
use Faker\date;
?>
    <div class="page-header am-fl am-cf">
        <h4>免兑换手机管理  <small>&nbsp;/&nbsp;手机号编辑</small></h4>
    </div>
    <div class="container-fluid" style="padding-top: 15px;height:800px;">
        <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/mobileedit']) ?>" >
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">编号</label>
                <div class="col-sm-3">
                    <span><?php   echo  $data['id'];  ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">券包批号</label>
                <div class="col-sm-3">
                    <span><?php   echo  $data['coupon_batch_no'];  ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">添加时间</label>
                <div class="col-sm-3">
                    <span><?php   echo  $data['c_time'];  ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">修改时间</label>
                <div class="col-sm-3">
                    <span><?php   echo  $data['u_time'];  ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">手机批号</label>
                <div class="col-sm-3">
                    <span><?php   echo  $data['batch_no'];  ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">客户公司名称</label>
                <div class="col-sm-3">
                    <span><?php   echo  $data['company_id'];  ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">手机号</label>
                <div class="col-sm-3">
                    <input type="text" placeholder="手机号" name="mobile"   value="<?php   echo $data['mobile']; ?>"    class="form-control"  />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
                </div>
            </div>
            <?php  if($data['id']) {?>
                <input type="hidden"  name="id"  value="<?php echo $data['id']; ?>"><?php }?>
        </form>
    </div>
    <script>

        $('form').submit(function(){
            var error=0;
            $('input[type="text"]').each(function(){
                if($(this).val().length==0) {
                    $(this).parent().parent().addClass('has-error');
                    error = 1;
                }
                else{
                    $(this).parent().parent().removeClass('has-error');
                }
            });
            if(error>0) return false;
            if (! confirm("确定要修改此手机号码？")) return false;

        });

    </script>
