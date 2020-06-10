<?php

use Yii;
use yii\helpers\Url;

?>
<div class="set-out-top">
    <i class="iconfont icon-car-sousuo1"></i>
    <input type="text" class="alxg-sousuo1" placeholder="请输入目的地">
</div>
<div class="link-addr">
    <ul class="link-addr-ul">
        <li>
            <?php if ($list[0]): ?>
                <a href="javascript:;" data-addr="a" data-sort="1" data-lng="<?=$list[0]['lng']?>" data-lat="<?=$list[0]['lat']?>">
                    <i><?=$list[0]['title']?></i>
                    <span><?=$list[0]['name']?></span>
                </a>
            <?php else: ?>
                <a href="javascript:;" data-addr="b" data-sort="1" data-lng="" data-lat="">
                    <i>添加</i>
                    <span>设置常用地址1</span>
                </a>
            <?php endif; ?>
        </li>
        <li>
            <?php if ($list[1]): ?>
                <a href="javascript:;" data-addr="a" data-sort="2" data-lng="<?=$list[1]['lng']?>" data-lat="<?=$list[1]['lat']?>">
                    <i><?=$list[1]['title']?></i>
                    <span><?=$list[1]['name']?></span>
                </a>
            <?php else: ?>
                <a href="javascript:;" data-addr="b" data-sort="2" data-lng="" data-lat="">
                    <i>添加</i>
                    <span>设置常用地址2</span>
                </a>
            <?php endif; ?>
        </li>
    </ul>
</div>
<!-- 搜索结果 -->
<div class="search-reasult">
    <ul class="search-reasult-ul end-reasult-ul">
    </ul>
</div>
<!-- 覆盖层 常用地址 -->
<div class="mask-fixed mask-commomAddr">
    <div class="bind-phone-wrapper">
        <ul class="bind-phone-ul">
            <li>
                <span>地址命名</span>
                <input type="text" maxlength="5" name="title" placeholder="请给地址命名，最多5个字，如“家”“公司”">
            </li>
            <li>
                <span>定位</span>
                <input class="location" type="text" name="lbs" readonly placeholder="请输入地址，精准定位">
                <input type="hidden" name="address" value="">
                <input type="hidden" name="lng" value="">
                <input type="hidden" name="lat" value="">
                <input type="hidden" name="sort" value="">
            </li>
        </ul>
    </div>
    <div class="send-comfirm">
        <button type="button" class="btn-block btn-primary">确认</button>
    </div>
</div>
<!--覆盖层 精准定位 -->
<div class="mask-fixed mask-position" style="overflow-y: auto;">
    <div class="set-out-top">
        <i class="iconfont icon-car-sousuo1"></i>
        <input type="text" class="alxg-sousuo2" placeholder="请输入目的地">
    </div>
    <!-- 搜索结果 -->
    <div class="search-reasult">
        <ul class="search-reasult-ul position-reasult-ul">

        </ul>
    </div>
</div>
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>
<script>
    var region = '<?=$region?>';
    //搜索
    var searchPlace = {
        getVer: 0,
        keyVer: 0,
        preVal: '',//上一次查的值
        bind: function (obj, res) {//res结果显示区域
            var _this = this;
            obj.on("keyup", {res: res}, function (e) {
                var val = obj.val();
                if (!val.length) {
                    res.html('');
                } else if (val != _this.preVal) {
                    _this.preVal = val;
                    _this.keyVer++;
                    _this.search(val, region, e.data.res);
                }
            });
        },
        search: function (q, r, box) {
            var _this = this;
            var lastKeyVer = _this.keyVer;
            var func = function (q, r) {
                $.post("<?php echo Url::to(['search'])?>", {q: q, r: r}, function (json) {
                    if (json.status === 1) {
                        box.html(json.data);
                    } else {
                        box.html('');
                    }
                }, 'json');
            };
            setTimeout(function (q, r, v) {
                if (v == _this.keyVer) {
                    func(q, r);
                }
            }, 350, q, r, lastKeyVer);
        }
    };
    searchPlace.bind($(".alxg-sousuo1"), $(".end-reasult-ul"));
    searchPlace.bind($(".alxg-sousuo2"), $(".position-reasult-ul"));
</script>
<script>
    var render = function(){
        $(".alxg-sousuo2").val('');
        $(".position-reasult-ul").html('');
        $("input[name=title]").val('');
        $("input[name=lbs]").val('');
    };
    //选择 目的地(搜索)
    $('.link-addr-ul>li>a').on('click', function (e) {
        e.preventDefault();
        var addr = $(this).data('addr');
        if (addr == 'a') {
            var addr = $(this).find('span').text();
            $('#iframe', parent.document).removeClass('show');
            $('#iframe', parent.document).attr('src', '');
            $('input.drive-end', parent.document).val(addr);
            $('input[name=end_lng]', parent.document).val($(this).data('lng'));
            $('input[name=end_lat]', parent.document).val($(this).data('lat'));
            parent.fireDestination();
        } else {
            $("input[name=sort]").val($(this).data('sort'));
            $('.mask-fixed').hide();
            $('.mask-commomAddr').show();
        }
    });
    //链接精准定位
    $('.bind-phone-ul>li>input.location').on('click', function (e) {
        e.preventDefault();
        $('.mask-fixed').hide();
        $('.mask-position').show();
    });
    //点击目的地搜索结果获取出发地或者目的地
    $('.end-reasult-ul').on('click', 'li', function () {
        var addr = $(this).find('h3').text();
        $('#iframe', parent.document).removeClass('show');
        $('#iframe', parent.document).attr('src', '');
        $('input[name=end_lng]', parent.document).val($(this).data('lng'));
        $('input[name=end_lat]', parent.document).val($(this).data('lat'));
        $('input.drive-end', parent.document).val(addr);
        parent.fireDestination();
    });
    //点击精准搜索结果
    $('.position-reasult-ul').on('click', 'li', function () {
        var name = $(this).find('h3').text();
        var address = $(this).find('span').text();
        var lng = $(this).data('lng');
        var lat = $(this).data('lat');
        $('.mask-commomAddr .bind-phone-ul>li>input.location').val(name);
        $("input[name=address]").val(address);
        $("input[name=lng]").val(lng);
        $("input[name=lat]").val(lat);
        $('.mask-fixed').hide();
        $('.mask-commomAddr').show();
    });
    //常用地址确认提交
    var isSubmit = false;
    $('.mask-commomAddr .send-comfirm>button').on('click', function () {
        if (isSubmit) return false;
        var title, name, lng, lat, address, sort;
        title = $("input[name=title]").val();
        if (!title.length) {
            YDUI.dialog.toast('请输入地址命名', 'none', 1000);
            return false;
        }
        name = $("input[name=lbs]").val();
        if (!name.length) {
            YDUI.dialog.toast('请选择定位', 'none', 1000);
            return false;
        }
        address = $("input[name=address]").val();
        lng = $("input[name=lng]").val();
        lat = $("input[name=lat]").val();
        sort = $("input[name=sort]").val();
        isSubmit = true;
        var data = {title: title, name: name, address: address, lng: lng, lat: lat, sort: sort};
        $.post("<?php echo Url::to(['editloc']);?>", data, function (json) {
            isSubmit = false;
            if (json.status === 1) {
                var box = $(".link-addr-ul>li>a[data-sort='" + sort + "']");
                box.data('addr', 'a');
                box.find('i').text(title);
                box.find('span').text(name);
                box.data('lng', lng);
                box.data('lat', lat);
                render();
                $('.mask-fixed').hide();
            } else {
                YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>

