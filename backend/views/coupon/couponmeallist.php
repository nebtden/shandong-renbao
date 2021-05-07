<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 下午 4:54
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
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['coupon/mealgenerate']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group">
            <select id="status" name="status"  placeholder="状态"  class="form-control">
                <option value="">选择套餐券的状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select id="companyid" name="companyid"  placeholder="客户公司"  class="form-control">
                <option value="">客户公司</option>
                <?php foreach ($companys as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group"><input type="text" id="batch_no" name="batch_no"  class="form-control"  placeholder="批号"></div>
        <div class="form-group"><input type="text" id="package_sn" name="package_sn"  class="form-control"  placeholder="搜索套餐券号码"></div>
        <div class="form-group"><input type="number" id="user_id" name="user_id"  class="form-control"  placeholder="用户ID"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/mealgenerate']);?>';">批量生成套餐券</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/mealforbidden']);?>';">批量禁用</button>

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
        <th  data-field="name">套餐券名称</th>
        <th  data-field="package_sn">套餐券号码</th>
        <th  data-field="package_pwd">套餐券兑换码</th>
        <th data-field="meal_id">可选套餐编码</th>
        <th data-field="mealsname">所选套餐</th>
        <th data-field="use_limit_time">过期时间</th>
        <th data-field="use_time">使用时间</th>
        <th data-field="nickname">微信昵称</th>
        <th  data-field="status">状态</th>
        <th  data-field="batch_no">批号</th>
        <th  data-field="companyname">所属公司</th>
        <th  data-field="remarks">备注</th>

<!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_coupon_meal.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/couponmealedit']);?>",
        listurl='<?php echo Url::to(['coupon/couponmeallist']); ?>',
        durl="";

    $("#download").click(function(){
        var opt1 = $("#status").val();
        var opt2 = $("#batch_no").val();
        var opt3 = $("#package_sn").val();
        var opt4 = $("#user_id").val();
        var opt5 = $("#companyid").val();
        var url = '<?php echo Url::to(["coupon/mealdownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            status:opt1,
            batch_no:opt2,
            package_sn:opt3,
            user_id:opt4,
            companyid:opt5
        },function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    content += '<li><a href="'+this.url+'">'+this.name+'</a>';
                });
                content += "</ul>";
                layer.open({
                    type: 1,
                    title: 'Excel导出',
                    area: ['600px', '360px'],
                    shadeClose: true, //点击遮罩关闭
                    content: content
                });
            }else{
                alert(json.msg);
            }
        });
    });
</script>



