<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">
<style>
    .line-wrap>input {
        width: 48%;
        height: .93rem;
        border: none;
        outline: none;
        font-size: .28rem;
        color: #535353;
        padding: 0 .29rem;
        text-align: center;
    }
</style>

<?php $this->endBlock('hStyle')?>

<div class="info-header">
    <span><img src="<?php echo $washShop['shop_pic']?>"></span>
</div>
<div class="line-wrap">
    <input type="text" name="start_time" placeholder="请输入开始营业时间" /><span>-</span>
    <input type="text" name="end_time" placeholder="请输入结束营业时间" />
</div>
<div class="commom-submit">
    <button type="button" class="btn-block">保存</button>
</div>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script>
    var is_sub = false;
    $(".btn-block").on('click',function(){
        var oldStart = "<?php echo $washShop['start_time']?>";
        var oldEnd = "<?php echo $washShop['end_time']?>";
            newStart = $('input[name=start_time]').val();
            newEnd = $('input[name=end_time]').val();
        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        if(newStart.length == 0 || !/^(0[1-9]|1[0-9]):[0-5][0-9]$/.test(newStart)){
            YDUI.dialog.toast('请输入正确的开始营业时间',1000);
            return false;
        }
        if(newEnd.length == 0 || !/^(0[1-9]|1[0-9]|2[0-3]):[0-5][0-9]$/.test(newEnd)){
            YDUI.dialog.toast('请输入正确的结束营业时间',1000);
            return false;
        }
        if(newStart==oldStart && newEnd==oldEnd){
            YDUI.dialog.toast('输入的数据与当前营业时间重复',1000);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/changeservicetime'])?>',
            data:{start_time:newStart,end_time:newEnd},
            type:'post',
            dataType:'json',
            timeout:6000,
            success:function(json){
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast('修改成功', 'success',1000,function () {
                        window.location.href =  '<?php echo Url::to(['carwashshop/shopdetail'])?>'
                    });
                }else {
                    YDUI.dialog.toast(json.msg,'error',1000);
                    is_sub = false;
                }
            },
            complete: function(XMLHttpRequest,status){
                if(status == 'timeout'){
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('请求超时', 'error',1000);
                    is_sub = false;
                }
            },
            error: function(XMLHttpRequest) {
                YDUI.dialog.loading.close();
                YDUI.dialog.toast('错误代码' + XMLHttpRequest.status, 1500);
                is_sub = false;
            }
        })
    })
</script>
<?php $this->endBlock('script') ?>
