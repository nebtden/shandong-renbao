<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
</style>
<div class="page-header">
    <h4>导入优惠券 </h4>
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
                    <label for="inputEmail3" class="imgFloat">券名</label>
                    <div class="col-sm-3">
                        <input type="text"  required name="coupon_name" id="coupon_name" value="" class="form-control" placeholder="请输入券名">
                    </div>
                </div>
                <div class="formConfirm clearfix" >
                    <label for="inputEmail3" class="imgFloat"> 优惠券的类型</label>
                    <div class="col-sm-3">
                        <select id="coupon_type" name="coupon_type"  placeholder="优惠券的类型"  class="form-control">
                            <?php foreach ($coupon_type as $key => $val):?>
                                <option value="<?=$key?>"><?=$val?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="formConfirm clearfix" >
                    <label for="inputEmail3" class="imgFloat"> 券面值</label>
                    <div class="col-sm-3">
                        <select id="coupon_amount" name="coupon_amount"  placeholder="E代驾券面值"  class="form-control">
                            <?php foreach ($coupon_amount as $key => $val):?>
                                <option value="<?=$key?>"><?=$val?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="formConfirm clearfix" >
                    <label for="inputEmail3" class="imgFloat">激活多少天后失效</label>
                    <div class="col-sm-3">
                        <input type="number" min="0" max="730" required name="expire_days" id="expire_days" value="" class="form-control" placeholder="默认365天">
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
<script type="text/javascript">
    var filepath = null;
    function Leadin(path,coupon_type,coupon_name,coupon_amount,expire_days){
        $(".yinying").show();
        $.post('<?php echo Url::to(["coupon/excutexls"]); ?>',{
            path:path,
            coupon_type:coupon_type,
            coupon_name:coupon_name,
            coupon_amount:coupon_amount,
            expire_days:expire_days
        },function(json){
            if(json.status == 1){

                Polling(json.data.xxzkey,json.data.xxzmaxnum);
            }else{
                $(".yinying").hide();
                alert(json.error);
                console.log(json.error);
            }
        },'json');
    }

    var num = 0;
    function Polling(xxzkey,xxzmaxnum){
        $.post('<?php echo Url::to(["coupon/batchpolling"]); ?>',{
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
            }else{
                $(".yinying").hide();
                alert(json.error);
                console.log(json.error);
            }
        },'json');
    }


    $("#excute").click(function(){
        var coupon_type=$('#coupon_type').val(),
            coupon_amount=$('#coupon_amount').val(),
            expire_days=$('#expire_days').val(),
            coupon_name=$('#coupon_name').val();

        if(coupon_type != '1' && coupon_type != '8'){
            alert('暂只支持e代驾或代步优惠券导入');
            return false;
        }
        if(coupon_name == '' || coupon_name == null || coupon_name == false ){
            alert('请输入券名');
            return false;
        }
        if(!expire_days){
            expire_days=365;
        }
        if(!filepath){
            alert('请先上传文件');
            return false;
        }
        Leadin(filepath,coupon_type,coupon_name,coupon_amount,expire_days);
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