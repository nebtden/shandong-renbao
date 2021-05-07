<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13 0013
 * Time: 下午 4:00
 */
use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
</style>
<div class="page-header">
    <h4>导入手机号 </h4>
</div>

<div class="ulcontent" id="content">
    <form class="form-horizontal" id="form"  method="post" action="" >
        <div class="div" id="content_1" style="display: block">
            <div class="tabBot clearfix">
                <div class="formConfirm clearfix">
                    <label class="imgFloat">文件（.xls）</label>
                    <div id="upfile"></div>
                </div>
                <div class="formConfirm clearfix" >
                    <label for="inputEmail3" class="imgFloat">券包批号</label>
                    <div class="col-sm-3">
                        <input type="text"  required name="batch_no" id="batch_no" value="" class="form-control" placeholder="券包批号">
                    </div>
                </div>
            </div>
            <div class="baocunBot webkitbox clearfix">
                <div class="baocunB  am-btn-success" id="excute">执行</div>
                <div class="fanhuiB" onclick="history.back()">返回</div>
            </div>
        </div>
    </form>
</div>
<div class="yinying" style="
position: fixed;width: 100%; height: 100%; left: 0;top: 0;background: #333;opacity: 0.6;z-index: 1000; display:none;"><span ></span>
</div>
<script src="../js/layer/layer.js"></script>
<script type="text/javascript">
    var filepath = null;
    function Leadin(path,batch_no){
        $(".yinying").show();
        $.post('<?php echo Url::to(["coupon/excutexlsmobile"]); ?>',{
            path:path,
            batch_no:batch_no

        },function(json){
            if(json.status == 1){

                Polling(json.data.xxzkey,json.data.xxzmaxnum);
            }else{
                $(".yinying").hide();
                alert(json.msg);
                console.log(json.msg);
            }
        },'json');
    }

    var num = 0;
    function Polling(xxzkey,xxzmaxnum){
        var content = "<ul style='padding:10px 20px;'>";
        $.post('<?php echo Url::to(["coupon/batchmobile"]); ?>',{
            key:xxzkey,
            num:num,
            xxzmaxnum:xxzmaxnum
        },function(json){

            if(json.status == 1){
                $(".yinying").hide();
                alert('导入成功');
                window.location.href=json.url;
            }else if(json.status == 2){
                num++;
                Polling(json.data.xxzkey,json.data.xxzmaxnum);
            }else if(json.status == 3){
                $(".yinying").hide();
                alert('部分导入成功，有'+json.number+'个异常手机号');
                content += '<li><a href="'+json.url+'">异常手机号</a>';
                content += "</ul>";
                layer.open({
                    type: 1,
                    title: 'Excel导出',
                    area: ['600px', '360px'],
                    shadeClose: true, //点击遮罩关闭
                    content: content
                });
            } else{
                $(".yinying").hide();
                alert(json.error);
                console.log(json.error);
            }
        },'json');
    }


    $("#excute").click(function(){
        var batch_no=$('#batch_no').val(),company_id=$('#company_id').val();

        if(batch_no == ''){
            alert('请输入需绑定的批号');
            return false;
        }

        if(!filepath){
            alert('请先上传文件');
            return false;
        }
        Leadin(filepath,batch_no,company_id);
    });
    $("#upfile").diyUpload(
        {
            url: '<?php echo Url::to(["upload/file"]) ;?>',
            buttonText: '选择excel文件',
            fileVal: 'file',
            formData:{"<?= Yii::$app->request->csrfParam ?>":"<?= Yii::$app->request->csrfToken ?>"},
            accept: {
                title: 'excel',
                extensions: 'xls,xlsx',
                mimeTypes: 'application/vnd.ms-excel'
            },
            success:function( data ) {
                alert('文件上传成功，点击执行进行导入');
                filepath = data.path;
            },
            error:function( err ) {
                alert('文件上传失败');
                console.log( err );
            }
        }
    );
</script>

