<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/19
 * Time: 11:08
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
            <label for="inputPassword3" class="col-sm-2 control-label">审核人</label>
            <div class="col-sm-3">
                <span><?php   echo  $data['ad_uid'];  ?></span>
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
            <label for="inputEmail3" class="col-sm-2 control-label">联络单截图</label>
            <div class="col-sm-3">
                <div id="hadpic" ></div>
                <input type="hidden" name="liaison_pic" id="liaison_pic" value="<?php echo $data['liaison_pic']?>"/>
                <?php
                if($data['liaison_pic']){
                    echo '<img width="390" src="'.$data['liaison_pic'].'" />';
                }
                ?>
                <p class="help-block"> </p>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">申请描述</label>
            <div class="col-sm-3">
                <textarea id="apply_desc" minlength="10" style="height:150px"  class="form-control"  placeholder="必要时可填写" name="apply_desc"><?php   echo $data['apply_desc']; ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <?php if($data['status'] == 2){?>
                    <button type="button" class="btn btn-info" onclick="Leadin()" >生成券码</button>
                <?php }elseif($data['status'] == 3 || $data['status'] == 1 ){?>
                    <button type="button" class="btn btn-info" onclick="submitInfo()">提交修改</button>
                <?php }?>
            </div>
        </div>

            <input type="hidden" id="post_id"  name="id"  value="<?php echo $data['id']; ?>">
    </form>
</div>
<script>
    uploadImg('hadpic','liaison_pic',100,100);
    function submitInfo(){
        var liaison_pic = $("#liaison_pic").val(),
            id = '<?php echo $data['id']; ?>',
            apply_desc = $("#apply_desc").val();
        $.post('<?php echo Url::to(["coupon/apply-ajax"]); ?>',{
            liaison_pic:liaison_pic,
            apply_desc:apply_desc,
            id:id,
            is_apply:1
        },function(json){
            if(json.status == 1){
                window.location.href=json.url;
                return false;
            }else
                alert(json.msg);
            console.log(json.json);
        },'json');
    }

    function Leadin(){
        var id = '<?php echo $data['id']; ?>';
        $(".yinying").show();
        $.post('<?php echo Url::to(["coupon/packagecreate"]); ?>',{
            id:id
        },function(json){
            if(json.status == 1){
                console.log(json.data);
                Polling(json.data);
            }else{
                alert(json.msg);
                $(".yinying").hide();
                console.log(json.msg);
            }
        },'json');
    }
    var num = 0;
    function Polling(data){
        $.post('<?php echo Url::to(["coupon/packageleadingtwo"]); ?>',{
            xxzkey:data.xxzkey,
            num:num,
            xxzmaxnum:data.xxzmaxnum,
            maxnum:data.maxnum,
            companyid:data.companyid,
            batch_no:data.batch_no,
            use_limit_time:data.use_limit_time,
            examine_id:data.examine_id,
        },function(json){

            if(json.status == 1){
                $(".yinying").hide();
                alert('批量生成成功');
                window.location.href=json.url;
                return false;
            }else if(json.status == 2){
                num++;
                Polling(json.data);
            }else{
                $(".yinying").hide();
                alert(json.error);
                console.log(json.error);
            }
        },'json');
    }
</script>

