<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">

<?php $this->endBlock('hStyle')?>

<dl class="common-dl">
    <dt>
        <span>主账号</span>
        <button type="button" id="child-link-btn">生成子账户邀请链接</button>
    </dt>
    <dd>
        <ul>
            <li>
                <i>微信昵称</i>
                <span><?= $masterAccount['nickname']?> </span>
            </li>
            <li>
                <i>手机</i>
                <span><?= $masterAccount['mobile']?></span>
            </li>
        </ul>
    </dd>
</dl>
<div class="chid-account"><span>子账号管理</span></div>
<?php if(!$childAccount):?>
    <p style="padding-left: 20px"><span>暂无子账号</span></p>
<?php else:?>
<?php foreach ($childAccount as $key=>$val):?>
<dl class="common-dl chid-count-dl">

    <dt>
        <i>授权子帐号&#<?php echo 9312+$key?>;</i>
        <i class="delete" data-uid = <?= $val['id'] ?>>删除</i>
    </dt>
    <dd>
        <ul >
            <li>
                <i>微信昵称</i>
                <span><?= $val['nickname'] ?></span>
            </li>
            <li>
                <i>手机</i>
                <span><?= $val['mobile'] ?></span>
            </li>
            <li>
                <i>姓名</i>
                <span><?= $val['realname'] ?></span>
            </li>
        </ul>
    </dd>
</dl>
<?php endforeach;?>
<?php endif;?>

<!--生成邀请链接弹框-->
<div class="commom-popup-outside link-outside" style="display:none">
    <div class="commom-popup">
        <div class="popup-title">生成子账户邀请链接<i class="icon-error"></i></div>
        <div class="popup-content link-content">
            <p>子账户邀请链接有效次数</p>
            <div class="spinner-wrap">
					<span class="m-spinner" id="J_Quantity">
					    <a href="javascript:;" class="J_Del"></a>
					    <input type="text" name="num" class="J_Input" value="1" placeholder=""/>
					    <a href="javascript:;" class="J_Add"></a>
					</span>
                <button type="button" class="link-btn">生成链接</button>
            </div>
            <div class="copy-link" style="display: none;">
                <p>子账户邀请链接</p>
                <input type="text" id="link-site" value="" readonly>
                <div class="commom-submit copy-link-btn">
                    <button type="button" class="btn-block" id="copyBtn">复制链接</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--删除子账号弹窗-->
<div class="commom-popup-outside delete-outside"  style="display:none">
    <div class="commom-popup">
        <div class="popup-title">提&nbsp;示<i class="icon-error"></i></div>
        <div class="popup-content">
            <div class="delete-text">删除子账号后 <br>子账号将无法进行卡券核销<br>请谨慎删除，确定要删除吗？</div>
            <div class="delete-btn-wrapper">
                <span class="cancel">取&nbsp;消</span>
                <i></i>
                <span class="comfirm">确&nbsp;定</span>
            </div>
        </div>
    </div>
</div>

<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script')?>
<script>
    $('#J_Quantity').spinner({
        input: '.J_Input',
        add: '.J_Add',
        minus: '.J_Del',
        unit: function () {
            return 1;
        },
        max: function () {

        },
        callback: function (value, $ele) {
//      	if(value == 1){
//      		$ele.prev().removeClass('changeColor')
//      	}else{
//      		$ele.prev().addClass('changeColor');
//      	}
//          $ele 当前文本框[jQuery对象]
            console.log('值：' + value);
        }
    });

    //  生成子账户邀请链接弹框
    $('#child-link-btn').on('click',function(){
        $('.link-outside').show()
    });

    //  生成链接
    var is_sub = false;
    $('.link-btn').on('click',function(){
        if(is_sub){
            YDUI.dialog.toast('链接生产中，请勿重复点击','error',1500);
            return false;
        }
        var num = $('input[name=num]').val();
        if(num.length == 0 || num>20){
            YDUI.dialog.toast('请输入不大于20的有效次数','error',1500);
            return false;
        }
        is_sub = true;
        $.ajax({
            url:'<?php echo Url::to(['carwashshop/childurl'])?>',
            data:{num:num},
            type:'post',
            dataType:'json',
            timeout:6000,
            success: function(json){
                if(json.status ==1){
                    $('#link-site').val(json.data);
                }else{
                    is_sub = false;
                    YDUI.dialog.toast(json.msg,'error',1500);
                }
            }
        });
        $('.copy-link').show()
    });

    //  复制链接
    $('#copyBtn').on('click',function(){
        copyUrl2()
    });

    function copyUrl2() {
        var Url2 = document.getElementById("link-site");
        Url2.select(); // 选择对象
        document.execCommand("Copy"); // 执行浏览器复制命令
    }

    //	关闭弹窗
    $('.icon-error').on('click',function(){
        $('.commom-popup-outside').hide()
    })

    //	删除子账号
    $('.chid-count-dl').on('click','.delete',function(e){
        var _chid = $(this).parents('.chid-count-dl');
             chid_uid = $(this).attr('data-uid');
        $('.delete-outside').show();
        //	取消按钮
        $('.cancel').on('click',function(){
            $('.delete-outside').hide()
        })

        //	确定按钮
        $('.comfirm').on('click',function(){
            $('.delete-outside').hide();
            YDUI.dialog.loading.open('账号删除中，请稍后');
            $.ajax({
                url:"<?php echo Url::to(['carwashshop/childdel'])?>",
                data:{child:chid_uid},
                type:'post',
                dataType: 'json',
                timeout:6000,
                success: function(json){
                    YDUI.dialog.loading.close();
                    if(json.status == 1){
                        _chid.remove();
                    }else{
                        YDUI.dialog.toast(json.msg, 'error',1000);
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

    })



</script>
<?php $this->endBlock('script') ?>
