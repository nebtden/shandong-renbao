<?php
use yii\helpers\Url;
?>
<div class="return-address">
    <ul class="return-address-ul">
        <?php foreach ($useraddrs as $v): ?>
        <li>
            <div class="left">
                <i class="icon-cloudCar2-radio <?php if ($v['isdefault']==1) echo 'icon-cloudCar2-radioactive'; ?>" data-id="<?= $v['id'] ?>"></i>
            </div>
            <div class="middle">
                <span><i><?= $v['name'] ?></i><em><?= $v['mobile'] ?></em></span>
                <span><?= $v['province'] ?><?= $v['city'] ?><?= $v['region'] ?><?= $v['street'] ?></span>
            </div>
            <div class="right">
                <a href="<?php echo Url::to(['handleuseraddr','id' => $v['id']])?>" class="btn a-modify">修改</a>
                <i></i>
                <a href="javascript:;" class="btn a-delete" data-id="<?= $v['id'] ?>">删除</a>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
    <div class="add-address-wrapper">
        <a href="<?php echo Url::to(['handleuseraddr','id' => 0])?>" >
            <img src="/frontend/web/cloudcarv2/images/add.png" >
            <span>添加地址</span>
        </a>
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    window.addEventListener('pageshow', function(e) {
        // 通过persisted属性判断是否存在 BF Cache
        if (e.persisted) {
            location.reload();
        }
    });
    //按钮切换
    $('.return-address-ul>li>.left').on('click',function(e){
        e.stopPropagation();
        $('.return-address-ul>li>.left>i').removeClass('icon-cloudCar2-radioactive');
        $(this).find('i').addClass('icon-cloudCar2-radioactive');
        var id = $(this).find('i').data('id');
        $.post("<?php echo Url::to(['handleuseraddr'])?>", {
            type: 'setdefault',
            id: id,
        }, function (json) {
            if (json.status == 1) {
                window.history.go(-1);
            }
        }, 'json');
    });
    //删除
    $('.return-address-ul>li>.right>.a-delete').on('click',function(e){
        e.stopPropagation();
        var id = $(this).data('id');
        var index = $(this).parents('li').index();
        YDUI.dialog.confirm('提示','确认删除这条地址？',function(e){
            YDUI.dialog.loading.open('提交中...');
            $.post("<?php echo Url::to(['handleuseraddr'])?>", {
                type: 'del',
                id: id,
            }, function (json) {
                YDUI.dialog.loading.close();
                if (json.status == 1) {
                    YDUI.dialog.toast(json.msg, 'none', 1000, function () {
                        $('.return-address-ul li:eq('+index+')').remove();
                    });
                } else {
                    YDUI.dialog.alert(json.msg);
                }
            }, 'json');
        });

    });
</script>
<?php $this->endBlock('script'); ?>
