<?php

use yii\helpers\Url;
use frontend\controllers\CaruserController;

?>
<style>
    body{
        background:  #F2F2F4;
    }
    .tab-nav-inner li a{
        border-radius: 20px;
        padding: .08rem 0;
    }
    .tab-nav-inner li{
        flex: 0 0 18%;
        margin-top: .15rem;
    }
    .tab-nav-inner{
        margin-top: 0.15rem;
        margin-bottom: 0.15rem;
    }
    .use-instructions-ol>li {
        font-size: .24rem;
        color: #959595;
        list-style: none;
        margin-top: .04rem;
    }

    .tab-panel-item .card-list-ul li{padding: 0}
</style>
<div class="m-tab" data-ydui-tab>
    <ul class="tab-nav">
<!--        0未激活，1已激活，2已使用，3已过期，4卡券异常-->
        <li class="tab-nav-item tab-active" data-id="-1">
            <a href="javascript:;">全部</a>
        </li>
        <li class="tab-nav-item" data-id="1">
            <a href="javascript:;">可用</a>
        </li>
        <li class="tab-nav-item" data-id="2">
            <a href="javascript:;">已用</a>
        </li>
        <li class="tab-nav-item" data-id="3">
            <a href="javascript:;">失效</a>
        </li>
    </ul>
    <ul class="tab-nav-inner" style="background: #F2F2F4">
        <li><a class="active" href="javascript:;"  data-inner-nav="-1">全部</a></li>
        <li><a href="javascript:;" data-inner-nav="4">洗车</a></li>
        <li><a href="javascript:;" data-inner-nav="1">代驾</a></li>
        <li><a href="javascript:;" data-inner-nav="2">救援</a></li>
        <li><a href="javascript:;" data-inner-nav="5">加油</a></li>
        <li><a href="javascript:;" data-inner-nav="7">年检</a></li>
        <li><a href="javascript:;" data-inner-nav="10">杀菌</a></li>

        <li><a href="javascript:;" data-inner-nav="11">爱奇艺</a></li>
        <li><a href="javascript:;" data-inner-nav="3">其他</a></li>
    </ul>
    <div class="tab-panel tab-panel-outer" style="
    padding: 0 .24rem;
    background: #F2F2F4;">
        <!-- 内嵌选项卡1 -->
        <div class="tab-panel-item tab-active">

            <div class="tab-panel-inner tab-panel-inner1">
                <div class="tab-panel-inner-item active" data-inner-nav="0" >
                    <!--                    <div class="no-data-tip">暂无数据噢</div>-->
                    <ul class="card-list-ul">
                        <?php foreach ($list as $val): ?>
                            <li data-type="<?= $val['coupon_type'] ?>"  data-status="<?= $val['status'] ?>"
                                class="<?= CaruserController::$couponTypeCss[$val['coupon_type']]?> <?= (($val['status']==2 && $val['show_button'] != 'show')||$val['status']==3)?'invalid':''?>">
                                <div class="title ">
                                    <i><?= $val['name'] ?></i>
                                    <?php switch($val['coupon_type']):case 1:?>
                                            <span><?= intval($val['amount']) ?>公里</span>
                                            <?php break;?>
                                        <?php case 2:?>
                                            <span>包年</span>
                                            <?php break;?>
                                        <?php case 4 :?>
                                        <?php case 10 :?>
                                            <span>剩余<?= intval($val['show_coupon_left']) ?>次</span>
                                            <?php break;?>
                                        <?php case INSPECTION:?>
                                        <?php case SEGWAY:?>
                                        <?php case 9:?>
                                            <span><?= intval($val['amount']) ?>次</span>
                                            <?php break;?>

                                       <?php case 11:?>
                                        <span><?= intval($val['amount']) ?>次</span>
                                        <?php break;?>
                                        <?php default:?>
                                            <span>￥<?= $val['amount'] ?></span>
                                            <?php break;?>
                                    <?php endswitch;?>
                                </div>
                                <div class="content">
                                    <div class="up">
                                        <div class="left">
                                            <?php if($val['coupon_type']==4 || $val['coupon_type']==10 ): ?>
                                                <span>券码: <?= $val['coupon_sn'] ?></span>
                                            <?php  else: ?>
                                                <span>服务码: <?= $val['coupon_sn'] ?></span>
                                            <?php endif; ?>
                                            <span>有效期至：<?= $val['show_coupon_endtime'] ?></span>

                                            <?php if ($val['status'] ==2):?>
                                                <span>使用日期：<?= $val['show_coupon_usetime'] ?></span>
                                            <?php endif;?>
                                            <?php  if($val['coupon_type']==4 || $val['coupon_type']==10): ?>
                                            <span><?= $val['show_coupon_desc'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="right">
                                            <?php if($val['status']<2 && $val['show_coupon_url']): ?>
                                                <?php  if($val['coupon_type']==1 && $val['company']==1 ):  ?>
                                                   <a data-url="<?= $val['show_coupon_url'] ?>" href="javascript:;" class="btn didi">选择</a>
                                                <?php elseif(($val['coupon_type']==4 || $val['coupon_type']==10) && $val['w_status']==2 && $val['is_mensal']==1):?>
                                                    <a class="btn " href="javascript:;">本月已使用</a>
                                                <?php else: ?>
                                                    <a class="btn "  data-url="<?= $val['show_coupon_url'] ?>" href="<?= $val['show_coupon_url'] ?>">使用</a>
                                                <?php endif;?>

                                            <?php elseif ($val['status']==2):?>
                                                <?php if($val['show_button'] == 'show'){?>
                                                    <a data-show="<?= $val['show_button']?>" data-url="<?= $val['show_coupon_url'] ?>" href="javascript:;" class="btn didi">选择</a>
                                                <?php }else{?>
                                                    <a class="btn" href="javascript:;">已使用</a>
                                                <?php }?>
                                            <?php elseif ($val['status']==3):?>
                                                <?=      $val['status']  ?>
                                                <a class="btn " href="javascript:;">已失效</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="down">
                                        <div class="card-explain">
                                            <em class="icon-cloudCar2-qiaquanshuoming"></em>
                                            <span>卡券说明</span>
                                            <i class="icon-cloudCar2-jiantou_down"></i>
                                        </div>
                                        <div class="explain-content">
                                            <ul class="use-instructions-ol">
                                                <?php if(!$use_text[$val['coupon_type']][$val['company']]){
                                                    $use_text[$val['coupon_type']][$val['company']] = $use_text[$val['coupon_type']][0];
                                                }?>
                                                <?php foreach ($use_text[$val['coupon_type']][$val['company']] as $txt): ?>
                                                    <li><?= $txt ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php  endforeach; ?>
                    </ul>
                </div>

                <div class="tab-panel-inner-item " data-inner-nav="6" >
                    <div class="no-data-tip">暂无数据噢</div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    //服务暂停提示1611676800
    <?php  if(time() > 1611676800 && time() < 1614384000){?>
        YDUI.dialog.alert('因春节期间，各地服务人员返乡或疫情限制出行，2021-01-27至2021-02-26服务可能提供不了，敬请见谅，预计在 2021-02-27 08:00:00 恢复，感谢支持');
    <?php }?>
    //内嵌选项卡1点击
    $('.tab-nav .tab-nav-item').on('click',function(e){
        //设置状态为0，即全部
        status = $(this).data('id')
        $('.tab-nav .tab-nav-item').removeClass('tab-active');
        $(this).addClass('tab-active');
        $('.tab-nav-inner>li>a').removeClass('active');
        $('.tab-nav-inner>li>a:eq(0)').addClass('active');
        setStatus(status,-1)
    });

    //滴滴代驾点击
    $('.didi').on('click',function(){
        var url = $(this).data('url');
        var show = $(this).data('show');
        if(show == 'show'){
            window.location.href = url;
            return;
        }
        $.get(url,{},function (json) {
            if(json.status == 1){
                window.location.href = json.url;
            }else{
                YDUI.dialog.alert(json.msg);
            }
        })
    });

    //洗车券点击
    $('.wash-click').on('click',function(){
        var url = $(this).data('url');
        $.get('<?php echo Url::to(['carwash/checklimit'])?>',{},function(json){
            if(json.status == 1){
                window.location.href = json.url?json.url:url;
            }else{
                YDUI.dialog.toast(json.msg,1500);
            }
        })
    });

    $('.tab-nav-inner>li>a').on('click',function(e){
        //设置状态为
        status = $('.tab-nav .tab-nav-item.tab-active').data('id')
        $('.tab-nav-inner>li>a').removeClass('active');
        $(this).addClass('active');

        type =  $(this).data('inner-nav');
        setStatus(status,type);
    });

    function setStatus(status,selected_type){
        $('.card-list-ul>li').each(function(v,i){
            mystatus = $(this).data('status');
            mytype = $(this).data('type');
            $(this).hide();
            if(status==-1){
                if(selected_type==-1){
                    $(this).show()
                }
                if(mytype==selected_type){
                    $(this).show()
                }
            }else{
                if(mystatus==status && selected_type==-1){
                    $(this).show()
                }
                if(mystatus==status && mytype==selected_type){
                    $(this).show()
                }
            }
        });
    }


    //展开收起说明
    $('.card-explain').on('click',function(e){
        e.stopPropagation();
        var isShow = $(this).attr('data-show');
        if(isShow=='true'){
            $(this).attr('data-show','fasle')
            $(this).next('.explain-content').hide(10);
            $(this).find('i').removeClass('icon-zjlt-jiantou_up').addClass('icon-zjlt-jiantou_down');
        }else{
            $(this).attr('data-show','true')
            $(this).next('.explain-content').show(10);
            $(this).find('i').removeClass('icon-zjlt-jiantou_down').addClass('icon-zjlt-jiantou_up');
        }
    });
</script>
<?php $this->endBlock('script'); ?>
