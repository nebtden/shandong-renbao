<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/24
 * Time: 10:37
 */

use yii\helpers\Url;
use Faker\date;
?>

<div class="page-header am-fl am-cf">
    <h4>申请管理  <small>&nbsp;/&nbsp;申请详情</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">编号</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['id'];  ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">申请时间</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['c_time'];  ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">申请人</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['uid'];  ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">审核时间</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['u_time'];  ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">卡券信息</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['info'];  ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">数量</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['number'];  ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">客户公司名称</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['companyid'];  ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">申请描述</label>
            <div class="col-sm-3">
                <span> <?php   echo $data['apply_desc']; ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">联络单截图</label>
            <div class="col-sm-3" style="float: left;">
                <div>
                    <?php
                    if($data['liaison_pic']){
                        echo '<img width="800" src="'.$data['liaison_pic'].'" />';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">审核说明</label>
            <div class="col-sm-3">
                <textarea id="examine_desc" minlength="10" style="height:150px"  class="form-control"  placeholder="若不通过请描述原因" name="examine_desc"><?php   echo $data['examine_desc']; ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <?php if($data['status'] == 1){?>
                    <button type="button" class="btn btn-info" onclick="submitInfo(2)">审核通过</button>
                    <button type="button" class="btn btn-default" onclick="submitInfo(3)">审核不通过</button>
                <?php }?>
            </div>
        </div>
    </form>
</div>
<script>
    function submitInfo(status){
        var id ='<?php echo $data['id']; ?>',
            examine_desc = $("#examine_desc").val();
        $.post('<?php echo Url::to(["coupon/apply-ajax"]); ?>',{
            examine_desc:examine_desc,
            status:status,
            id:id,
            is_apply:2
        },function(json){
            if(json.status == 1){
                window.location.href=json.url;
                return false;
            }else
                alert(json.msg);
            console.log(json.json);
        },'json');
    }
</script>

