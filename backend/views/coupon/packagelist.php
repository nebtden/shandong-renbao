<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 上午 11:38
 */

use yii\helpers\Url;
?>
<style>
    .layui-layer-content{
        padding-left:23px;
    }
    .layui-layer-rim{ font-size: 14px;}

    .bigautocomplete-layout{
        display: none;;
        background-color: #ffffff;
        border: 1px solid #BCBCBC;
        position: fixed;
        z-index: 100;
        height: 300px;
        max-height: 220px;
        overflow-x:hidden;
        overflow-y:auto;
        box-shadow: 0 2px 5px 2px rgba(0,0,0,.1);
    }
    .bigautocomplete-layout table{
        border-collapse: collapse;
        border-spacing: 0;
        background: none repeat scroll 0 0 ;
        width: 100%;
        cursor: default;
    }

    .bigautocomplete-layout table tr{
        background: none repeat scroll 0 0;
    }

    .bigautocomplete-layout .ct{
        background: none repeat scroll 0 0 #D2DEE8 !important;
    }
    .bigautocomplete-layout div{
        word-wrap:break-word;
        word-break:break-all;
        padding:1px 5px;
    }

</style>
<div class="page-header am-fl am-cf">
    <h4>券包管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group">
            <select id="_status" name="_status"  placeholder="状态"  class="form-control">
                <option value="">选择券包状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="form-group">
            <input type="text" id="companyid" name="companyid"  class="form-control"  placeholder="客户公司">
        </div>

        <div class="form-group"><input type="text" id="batch_nb" name="batch_nb"  class="form-control"  placeholder="券包批号"></div>
        <div class="form-group"><input type="text" id="package_sn" name="package_sn"  class="form-control"  placeholder="搜索券包号码"></div>
        <div class="form-group"><input type="text" id="package_pwd" name="package_pwd"  class="form-control"  placeholder="搜索券包兑换码"></div>
        <div class="form-group"><input type="number" id="user_id" name="user_id"  class="form-control"  placeholder="用户ID"></div>
        <div class="form-group"><input type="number" min="10000000000" max="19999999999" id="mobile" name="mobile"  class="form-control"  placeholder="激活手机"></div>
        <div class="form-group"><input type="text" id="c_batch_no" name="c_batch_no"  class="form-control"  placeholder="优惠券批号"></div>
        <div class="form-group">
            <input type="text" class=" form-control" name="s_time" id="s_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索券包激活开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="e_time" id="e_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索券包激活结束时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索券包生成开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索券包生成结束时间">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/apply-package']);?>';">提交申请</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/forbidden']);?>';">批量禁用</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/updeadline']);?>';">批量修改过期时间</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/packbatchnomerge']);?>';">券包批号合并</button>

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
        <th  data-field="package_sn">卡包号码</th>
        <th  data-field="package_pwd">卡包兑换码</th>
        <th data-field="info">券信息（面值，数量，券批号）</th>
        <th data-field="use_limit_time">过期时间</th>
        <th data-field="use_time">激活时间</th>
        <th  data-field="c_time">生成时间</th>
        <th data-field="nickname">微信昵称</th>
        <th  data-field="status">状态</th>
        <th  data-field="batch_nb">批号</th>
        <th  data-field="companyid">客户公司</th>
        <th  data-field="mobile">激活手机</th>
        <th  data-field="source">用户来源</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/complete.js" ></script>
<script src="../js/handle_data_package.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/packageedit']);?>",
        listurl='<?php echo Url::to(['coupon/packagelist']); ?>',
        durl="<?php echo Url::to(['coupon/coupon_del']); ?>";
    $("#companyid").bigAutocomplete({
        width: 604,
        url: "<?php echo Url::to(['coupon/get-new-company']); ?>",
        before: function () {
        },
        callback: function (data) {
            $("#companyid").attr('data-id',data.id);

        }
    });
    $("#download").click(function(){
        var opt1 = $("#_status").val();
        var opt2 = $("#batch_nb").val();
        var opt3 = $("#package_sn").val();
        var opt4 = $("#user_id").val();
        var opt5 = $("#companyid").attr('data-id');
        var opt6 = $("#mobile").val();
        var opt7 = $("#start_time").val();
        var opt8 = $("#end_time").val();
        var opt9 = $("#s_time").val();
        var opt10 = $("#e_time").val();
        var opt11 = $("#c_batch_no").val();
        var opt12 = $("#package_pwd").val();
        if(opt11){
            if(opt11.length != 8 && opt11.length != 10){
                alert('不存在此优惠券批号');
                return false;
            }
        }
        var url = '<?php echo Url::to(["coupon/packagedownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            status:opt1,
            batch_nb:opt2,
            package_sn:opt3,
            user_id:opt4,
            companyid:opt5,
            mobile:opt6,
            start_time:opt7,
            end_time:opt8,
            s_time:opt9,
            e_time:opt10,
            c_batch_no:opt11,
            package_pwd:opt12

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



