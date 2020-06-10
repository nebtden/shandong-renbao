<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/etc.css">
<link rel="stylesheet" href="/frontend/web/sdetc/iconfonts/iconfont.css">
<style>
    .tab-panel-inner .show-hide{
        display: none;
    }
    .info-date{
        color: #666666;
        margin: .3rem .25rem;
    }


</style>

<?php $this->endBlock('hStyle')?>
<div class="m-tab" data-ydui-tab>
    <ul class="etc-tab-nav">
        <li class="etc-tab-item"><a href="javascript:void(0);">ETC账单</a></li>

    </ul>
    <div class="tab-panel tab-panel-outer">
        <!-- 内嵌选项卡1 -->
        <div class="tab-panel-item tab-active">
            <ul class="etc-tab-nav-inner tab-nav-inner1">
                <li><a class="active" href="javascript:;"  data-inner-nav="0">全部</a></li>
                <?php if(is_array($userCar)):?>
                    <?php foreach($userCar as $key=>$val):?>
                        <li><a href="javascript:;" data-inner-nav="<?=$val['id']?>"><?= $val['card_province'].$val['card_char'].$val['card_no']?></a></li>
                <?php endforeach;endif;?>

            </ul>
            <div class="tab-panel-inner tab-panel-inner1">
                <?php if(!is_array($list)):?>
                    <div class="etc-item active" data-inner-nav="0">
                        <div class="no-data-tip">暂无数据噢</div>
                    </div>
                <?php else:?>
                <?php foreach($list as $index=> $bill):?>
                    <div class="etc-item <?php echo $index==0?'':'show-hide'?>" data-inner-nav="<?=$index?>" data-plate-no="<?php echo $userCar[$index]['card_province'].$userCar[$index]['card_char'].$userCar[$index]['card_no']?:'0'?>">
                         <?php foreach($bill as $k=>$v):?>
                            <div class="info-date"><?= date('Y年m月',strtotime($k.'01'))?></div>
                            <ul>
                                <?php foreach($v as $val):?>
                                <li>
                                    <a href="<?php echo Url::to(['sdetc/etcbill','id'=>$val['id']])?>">
                                        <div class="info-item">
                                            <p><i class="icon-cloudCar2-yonghu"></i><?= $val['name']?></p>
                                            <p>车牌号：<?=$val['plate_no']?></p>
                                            <p><?=date('Y-m-d H:i',strtotime($val['out_time']))?></p>
                                        </div>
                                        <div class="money-item"> &yen;<i><?=number_format($val['bill_money'],2,".",",")?></i></div>
                                    </a>
                                </li>
                                <?php endforeach;?>

                            </ul>
                         <?php endforeach;?>
                    </div>
                <?php endforeach;endif;?>
            </div>
        </div>
    </div>
</div>
<?php $this->beginBlock('script')?>
<script>
    //内嵌选项卡1点击
    $('.tab-nav-inner1>li>a').on('click',function(e){
        e.stopPropagation();
        $('.tab-nav-inner1>li>a').removeClass('active');
        $(this).addClass('active');
        var id = $(this).data('inner-nav');
        setShow(id)
    });

    function setShow(id){
        $('.tab-panel-inner > .etc-item').each(function(v,k){
            var myId = $(this).data('inner-nav');
            if(myId == id){
                $(this).show();
            }else{
                $(this).hide();
            }
        })
    }


        var page = 2;

        var loadMore = function (callback) {
            var plate = $('.tab-panel-inner > .etc-item:visible').data('inner-nav');
            var plateNo = $('.tab-panel-inner > .etc-item:visible').data('plate-no');
            console.log(plateNo);
            $.ajax({
                url: '<?php echo Url::to(['sdetc/bill'])?>',
                dataType: 'json',
                data: {
                    page: page,
                    plateNo: plateNo,
                },
                success: function (ret) {
                    typeof callback == 'function' && callback(ret);
                }
            });
        };

        $('#J_List').infiniteScroll({
            binder: '#J_List',
            pageSize: pageSize,
            initLoad: true,
            loadingHtml: '<img src="http://static.ydcss.com/uploads/ydui/loading/loading10.svg"/>',
            loadListFn: function () {
                var def = $.Deferred();

                loadMore(function (listArr) {

                    var html = '';
                    $.each(listArr.data,function(key,val){
                    html = '<div class="etc-item" data-inner-nav="'+plate+'" data-plate-no="'+plateNo+'">'+
                            <?php foreach($bill as $k=>$v):?>
                            <div class="info-date"><?= date('Y年m月',strtotime($k.'01'))?></div>
                            <ul>
                            <?php foreach($v as $val):?>
                            <li>
                            <a href="<?php echo Url::to(['sdetc/etcbill','id'=>$val['id']])?>">
                            <div class="info-item">
                            <p><i class="icon-cloudCar2-yonghu"></i><?= $val['name']?></p>
                        <p>车牌号：<?=$val['plate_no']?></p>
                        <p><?=date('Y-m-d H:i',strtotime($val['out_time']))?></p>
                        </div>
                        <div class="money-item"> &yen;<i><?=number_format($val['bill_money'],2,".",",")?></i></div>
                        </a>
                        </li>
                        <?php endforeach;?>

                        </ul>
                        <?php endforeach;?>
                        </div>

                    });

                    def.resolve(listArr);

                    ++page;
                });

                return def.promise();
            }
        });

</script>
<?php $this->endBlock('script')?>
