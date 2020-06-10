<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\4\30 0030
 * Time: 13:48
 */
use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
    <style>
        .select-province,.select-letter{
            display: inline-flex;
            align-items: center;
        }
        .car-no{
            display: flex;
            flex-wrap: nowrap;
        }
        .car-no em {
            background-color: #009b62;
            color: #fff;
            height: .45rem;
            line-height: .45rem;
            padding: 0 .08rem;
            border-radius: .06rem;
            margin: 0 .09rem;
        }
        .car-no>span>i{
            width: 0;
            height: 0;
            border-top: 6px solid #797986;
            border-right: 6px solid transparent;
            border-left: 6px solid transparent;
            margin-left: -3px;
        }
        .same-actionSheet-ul{
            display: flex;
            flex-wrap: wrap;
            padding: .24rem .12rem 0rem .12rem;
        }
        .same-actionSheet-ul>li{
            height:.8rem;
            line-height:.8rem;
            border-radius: .1rem;
            border:1px solid #d9d9d9;
            padding: 0 .1rem;
            font-size:.4rem;
            margin-bottom:.3rem;
            margin-left: .24rem;
            min-width:.55rem;
            color: #464b55;
        }
        .same-actionSheet-ul>li.active{
            border:none;
            background: #009b62;
            color: #fff;
        }
    </style>
<?php $this->endBlock('jStyle')?>
<div class="submit-wrapper">
    <ul>
        <li>
            <i>车牌号</i>
            <div class="right car-no">
                <span class="select-province" data-ydui-actionsheet="{target:'#actionSheet-province',closeElement:'#cancel'}">
                    <em><?= $cardNo['card_province']?:'京'?></em>
                    <i></i>
                </span>
                <input type="hidden" name="card_province" value="<?= $cardNo['card_province']?:'京'?>">
                <span class="select-letter" data-ydui-actionsheet="{target:'#actionSheet-letter',closeElement:'#cancel'}">
                    <em style="margin-left: 3px;"><?= $cardNo['card_char']?:'A'?></em>
                    <i></i>
                </span>
                <input type="hidden" name="card_char" value="<?= $cardNo['card_char']?:'A'?>">
                <input type="text" maxlength="5" size="15" name="card_no" style="flex: 1;"  placeholder="请输入车牌号5位" value="<?= $cardNo['card_no']?:''?>">
            </div>
        </li>
        <li>
            <i>兑换码</i>
            <div class="right">
                <input type="text" style="width: 100%" name="pwd"  placeholder="" />
            </div>
        </li>
    </ul>
    <div class="commom-submit">
        <button class="btn-block btn-primary" >确认激活</button>
    </div>
    <div class="bottom-wrap">
        <div class="logo-img">
            <img src="/frontend/web/h5/img/logo.png">
        </div>
        <p>
            本卡使用服务解释权归提供服务的云车驾到平台所有<br />
            24小时客服热线：<a href="tel:<?= Yii::$app->params['yunche_hotline']?>"><?= Yii::$app->params['yunche_hotline']?></a>
        </p>
    </div>
</div>
    <!-- 上拉菜单 省份选择 -->
    <div class="m-actionsheet" id="actionSheet-province">
        <ul class="same-actionSheet-ul province-ul">
            <?php foreach ($province as $k => $p): ?>
                <li class="<?php if($p['desc']=='京'){ echo 'active';} ?>" data-id="<?= $p['id'] ?>"><?= $p['desc'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- 上拉菜单 字母选择 -->
    <div class="m-actionsheet" id="actionSheet-letter">
        <ul class="same-actionSheet-ul letter-ul">
            <?php foreach($letter as $key):?>
               <li class="<?php if($key['desc'] == 'A'){ echo 'acitve';}?>" ><?= $key['desc']?></li>
            <?php endforeach;?>
        </ul>
    </div>
<?php $this->beginBlock('script')?>
<script>
    //	android键盘弹起时背景图片压缩
    let Height = $('body').height();
    $(window).resize(function() {
        $('body').height(Height);
    });
    //	ios键盘收起后页面未下滑
    $('input').on('blur',function(){
        window.scroll(0,0);
    });

    //上拉省份简称选择
    $('.province-ul>li').on('click', function () {
        $('.province-ul>li').removeClass('active');
        $(this).addClass('active');
        $('input[name="card_province"]').val($(this).text());
        $('.select-province>em').text($(this).text());
        var id = $(this).data('id');
        $(".letter-ul").html('');
        $.post("<?php echo Url::to(['caruser/getcitydesc'])?>", {id: id}, function (json) {
            if (json.status === 1) {
                $(".letter-ul").html(json.data);
                $('.select-letter>em').text('A');
            }
        }, 'json');
    });
    $('#actionSheet-province').on('click', function () {
        $(this).actionSheet('close');
    });

    //上拉字母选择
    $(document).on('touchstart','.letter-ul>li',function () {
        $('.letter-ul>li').removeClass('active');
        $(this).addClass('active');
        $('input[name="card_char"]').val($(this).text());
        $('.select-letter>em').text($(this).text());
    });

    $('#actionSheet-letter').on('click', function () {
        $(this).actionSheet('close');
    });

    //按钮切换
    $('.select-radio>span').on('click', function () {
        $(this).parent('.select-radio').find('i').removeClass('icon-cloudCar2-radioactive');
        $(this).find('i').addClass('icon-cloudCar2-radioactive');
        $(this).parent('.select-radio').find('input').val($(this).find('i').data('val'));
    });
    <?php if(empty($user)):?>
    YDUI.dialog.toast('请登录后再兑换', 'error', 2500, function () {
        window.location.href = '<?= Url::to(['h5service/guoshouwash'])?>';
    });
    <?php endif;?>
    var is_sub = false;
    $('.btn-primary').on('click',function(){
        if(is_sub){
            YDUI.dialog.toast('提交中，请勿重复点击','none',1500);
            return false;
        }
        var pwd = $("input[name=pwd]").val();
        var card_province = $('input[name="card_province"]').val(); //车牌号
        var card_char = $('input[name="card_char"]').val(); //车牌号
        var card_no = $('input[name="card_no"]').val(); //车牌号
        if(card_no.length==0 || card_no.length>5){
            YDUI.dialog.toast('请输入车牌号码','none',1500);
            return false;
        }
        if(pwd.length==0 ){
            YDUI.dialog.toast('请输入兑换码','none',1500);
            return false;
        }
        if(!/^[a-zA-Z\d]{8}$/.test(pwd)){
            YDUI.dialog.toast('请输入正确的兑换码','none',1500);
            return false;
        }
        is_sub = true;
        YDUI.dialog.loading.open('兑换码激活中，请稍后');
        $.post('<?php echo Url::to(['h5service/accoupon'])?>', {
            pwd: pwd,
            card_province:card_province,
            card_char:card_char,
            card_no:card_no,
        }, function (json) {
            YDUI.dialog.loading.close();
            if (json.status == 1) {
                YDUI.dialog.toast('激活成功','success',1000,function(){
                    window.location.href = "<?php echo Url::to(['h5service/guoshouwash']);?>";
                })
            } else {
                is_sub = false;
                YDUI.dialog.toast(json.msg, 1000);
            }

        }, 'json')
    })

</script>
<?php $this->endBlock('script')?>