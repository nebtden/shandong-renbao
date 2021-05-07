<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
</style>
<div class="page-header">
    <h4>途虎数据导入</h4>
</div>

<div class="ulcontent" id="content">
    <form class="form-horizontal" id="form"  method="post" action="" >
        <div class="div" id="content_1" style="display: block">
            <div class="tabBot clearfix">
                <div class="formConfirm clearfix">
                    <label class="imgFloat">文件（.xls）</label>
                    <div id="upfile"></div>
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
<script type="text/javascript">
    var filepath = null,key=0;
    function Leadin(path,key){
        $(".yinying").show();
        $.post('<?php echo Url::to(["order/excutexls"]); ?>',{
            path:path,
            key:key
        },function(json){
            if(json.status == 1){
                $(".yinying").hide();
                alert(json.msg);
            }else{
                $(".yinying").hide();
                Leadin(json.data.path,json.data.key);
            }
        },'json');
    }

    $("#excute").click(function(){
        if(!filepath){
            alert('请先上传文件');
            return false;
        }
        Leadin(filepath,key);
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
            success:function( data ) {""
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
