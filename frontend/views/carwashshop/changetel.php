<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">

<?php $this->endBlock('hStyle')?>

<div class="info-header">
    <span><img src="<?php echo $washShop['shop_pic']?>"></span>
</div>
<div class="line-wrap">
    <input type="tel" name="shop_tel" placeholder="请输入新的联系电话" />
</div>
<div class="commom-submit">
    <button type="button" class="btn-block">保存</button>
</div>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script>
    var is_sub = false;
    $(".btn-block").on('click',function(){
        var oldTel = "<?php echo $washShop['shop_tel']?>";
            shop_tel = $('input[name=shop_tel]').val();
        if(is_sub){
            YDUI.dialog.toast('数据提交中，请勿重复点击',1000);
            return false;
        }
        if(shop_tel.length == 0 || shop_tel.length > 12){
            YDUI.dialog.toast('请输入正确的电话号码',1000);
            return false;
        }
        if(shop_tel == oldTel){
            YDUI.dialog.toast('输入的号码与当前电话号码重复',1000);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('数据提交中');
        $.ajax({
            url: '<?php echo Url::to(['carwashshop/changetel'])?>',
            data:{shop_tel:shop_tel},
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
