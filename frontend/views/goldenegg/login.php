<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle'); ?>
<style>
    input::-webkit-input-placeholder{
        color:#fff;
    }
    input::-moz-placeholder{
        color:#fff;
    }
    input:-moz-placeholder{
        color:#fff;
    }
    input:-ms-input-placeholder{
        color:#fff;
    }
</style>
<?php $this->endBlock('hStyle'); ?>
<div class="loginBox">
    <div class="fromBox">
        <p class="tit">请先填写信息参与活动</p>
        <div class="iptBox">
            <input type="text" id="jobNum"  placeholder="请输入工号">
        </div>
        <div class="iptBox">
            <input type="text" id="name" placeholder="请输入姓名">
        </div>
        <div class="btnBox">
            <a href="#" onclick="login()"><img src="images/qr-btn.png" alt=""></a>
        </div>
    </div>
    <div class="txt">
        温馨提示：您填写的个人信息是获取参与抽奖资格的重要依据，请注意填写准确的信息资料，幸运好礼等你拿！
    </div>
</div>
<?php $this->beginBlock('script') ?>

<script>
    var dialog = YDUI.dialog;

    function login(){
        var jobNum = $('#jobNum').val();
        var name = $('#name').val();
        if(!jobNum){
            dialog.toast('请输入工号', 'error', 1000, function () {
                $("#jobNum").get(0).focus();
            });
        } else if(!name){
            dialog.toast('请输入姓名', 'error', 1000, function () {
                $("#name").get(0).focus();
            });
        } else {
            dialog.loading.open('确认中');
            $.ajax({
                url:'<?php echo Url::to(['goldenegg/login'])?>',
                dataType: 'json',
                type: 'POST',
                timeout:6000,
                data: {jobNum:jobNum,name:name},
                success:function (json){
                    dialog.loading.close();
                    if(json.status == 0){
                        dialog.toast('工号或者姓名不正确，请重新输入', 'error', 1000, function () {
                            $("#jobNum").get(0).focus();
                        });
                    } else if(json.status == 3){
                        dialog.toast('活动尚未开始', 'error', 1000, function () {
                            $("#jobNum").get(0).focus();
                        });
                    }
                    else {
                        window.location.href = json.url;
                    }
                },
                complete: function(XMLHttpRequest,status){
                    if(status == 'timeout'){
                        YDUI.dialog.loading.close();
                        YDUI.dialog.toast('请求超时', 'error',1000, function(){

                        });
                    }
                },
                error: function(XMLHttpRequest) {
                    YDUI.dialog.loading.close();
                    YDUI.dialog.notify('服务器错误' + XMLHttpRequest.status, 3000, function(){
                        console.log('服务器错误');
                    });
                }
            })
        }
    }
</script>
<?php $this->endBlock('script') ?>
