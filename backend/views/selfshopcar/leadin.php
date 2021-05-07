<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
</style>
<div class="page-header">
    <h4>导入洗车卡 </h4>
</div>

<div class="ulcontent" id="content">
    <form class="form-horizontal" id="form"  method="post" action="<?php echo Url::to(['wx/wx_edit']) ?>" >
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
<script type="text/javascript">
    var filepath = null;
    function Leadin(path){

        $.post('<?php echo Url::to(["car/excutexls"]); ?>',{path:path},function(json){
            if(json.status == 1){
                alert('导入成功');
                window.location.href = '<?php echo Url::to(["car/card_list"])?>';
            }else{
                alert('存在错误');
                console.log(json.error);
            }
        },'json');
    }
    $("#excute").click(function(){
        if(!filepath){
            alert('请先上传文件');
            return false;
        }
        Leadin(filepath);
    });
    $("#upfile").diyUpload(
        {
            url: '<?php echo Url::to(["upload/file"]) ;?>',
            buttonText: '选择excel文件',
            fileVal: 'file',
            formData:{"<?= Yii::$app->request->csrfParam ?>":"<?= Yii::$app->request->csrfToken ?>"},
            accept: {
                title: 'excel',
                extensions: 'xls',
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