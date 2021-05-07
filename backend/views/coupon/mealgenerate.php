<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 下午 5:04
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        套餐券批量生成 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/mealgenerate']) ?>" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">生成数量</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="5000" required name="generate_num" class="form-control" placeholder="请输入要生成的数量">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">套餐券名称</label>
            <div class="col-sm-3">
                <input type="text"  required name="name" class="form-control" placeholder="套餐券名称">
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">套餐券估值</label>
            <div class="col-sm-3">
                <input type="text"  required name="amount" class="form-control" placeholder="套餐券估值必须是4位如0100">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">过期时间</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" name="use_limit_time" id="use_limit_time"  onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="不填为永久有效">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">所属省份</label>
            <div class="col-sm-3">
                <select  name="province"  placeholder="所属省份"  class="form-control">
                    <?php foreach ($provinces as $key => $val):?>
                        <option value="<?=$val['code']?>"><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">公司名称</label>
            <div class="col-sm-3">
                <select  name="company"  placeholder="公司名称"  class="form-control">
                    <?php foreach ($companys as $key => $val):?>
                        <option value="<?=$val['id']?>"><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">用户可选备注对应的套餐</label>
            <div class="col-sm-3" >
                <select  name="remarks"  placeholder="用户可选套餐"  class="form-control">
                    <?php foreach ($remarksall as $key => $val):?>
                        <option value="<?=$val?>"><?=$val?></option>
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
        <input type="hidden" id="arrnum" name="arrnum" value="1">
    </form>
</div>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">

</script>
