<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 上午 11:09
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd">
    <header class="huishouTitle boxSizing">
        查询您所在的省市的服务网点
    </header>
    <article class="huishouCont">
        <ul class="TopUl webkitbox boxSizing afterFour">
            <li id="shop_province" value="" >省</li>
            <li id="shop_city" value="" >市</li>
            <li id="shop_area" value="" >区或县</li>
        </ul>
        <ul class="BotDl boxSizing" id="shop_list">

            <?php foreach ($shoplist as $val){?>
                <li class="afterFour">
                    <a href="#">
                        <dl class="webkitbox">
                            <dt>
                                <?php if(!empty($val['shop_pic'])){?>
                                    <img src="<?php echo $val['shop_pic']?>">
                                <?php }else{?>
                                    <img src="/frontend/web/images/qiche.jpg">
                                <?php }?>
                            </dt>
                            <dd>
                                <p class="p1"><?php echo $val['shop_name']?></p>
                                <p class="p2">地址：<?php echo $val['shop_address']?></p>
                                <p class="p3">电话：<?php echo $val['mobile']?></p>
                            </dd>
                        </dl>
                    </a>
                    <div class="huishouButton webkitbox">
                        <a href="<?php echo Url::to(['car/address' , 'address'=>$val['shop_address'], 'shop_name'=>$val['shop_name']]);?>" class="afterFour">查看位置</a>
                    </div>
                </li>
            <?php }?>
        </ul>
    </article>
</section>

<div class="MusicHideList">

    <div class="Title afterFour">请选择区域</div>
    <ul class="Content" id="address">
        <li class="webkitbox">

        </li>
    </ul>
</div>
<div class="fenxiang" id="fenxiang"></div>

<?php $this->beginBlock('script');?>
<script type="text/javascript">
    var List = function () {
        var fenxiang = document.getElementById('fenxiang'),height = window.innerHeight,section = document.querySelector('section'),MusicHideList = document.querySelector('.MusicHideList'),index = 0;
        $('.TopUl li').on('click', function (e) {
            var v_id = $(e.target).attr('id');
            if(v_id=='shop_province'){
                getprovince();
            }else if(v_id=='shop_city'){
                var province=$('#shop_province').val();
                if(province==0 || province==''){
                    alert('请先选择省');return false;
                }
                getcity(province);
            }else if(v_id=='shop_area'){
                var city=$('#shop_city').val();
                if(city==0 || city==''){
                    alert('请先选择市');return false;
                }
                getarea(city);
            }else{
                return false;
            }
            index = $(this).index();
            MusicHideList.setAttribute('data',index);
            section.style.cssText = 'overflow:hidden; height:' + height + 'px;-webkit-box-sizing:border-box;';
            fenxiang.style.cssText = 'height:' + height + 'px;width:100%';
            MusicHideList.classList.add('h_keep1');
            MusicHideList.style.cssText = '-webkit-transform:translate3d(0px,-' + window.innerHeight * 0.7 + 'px,0px)'
        });
        addEvent(fenxiang, 'touchstart', function () {
            close();
        });
        function close(){
            section.removeAttribute('style');
            var setTime = setTimeout(function () {
                fenxiang.removeAttribute('style');
                clearTimeout(setTime);
            },300);
            MusicHideList.style.cssText = '-webkit-transform:translate3d(0px,' + window.innerHeight * 0.5 + 'px,0px)';
        }

        $('.MusicHideList').on('click','.Content span',function(){
            $(this).parent().addClass('cur').siblings().removeClass('cur');
            $(this).append('<i></i>').parent().siblings().children('span').children('i').remove();
            $(this).parents('li').siblings().children('div').removeClass('cur').children('span').children('i').remove();
            newshop();
        })

         function newshop() {
            var span = '';
            var val='';
            $('.MusicHideList .Content div').each(function () {
                if($(this).hasClass('cur')){
                    span = $(this).children('span').text();
                    val = $(this).children('.adr_code').val();
                    return false;
                }
            });
            if(!(span == '')){

                $('.TopUl li').eq($('.MusicHideList').attr('data')).html(span);
                $('.TopUl li').eq($('.MusicHideList').attr('data')).val(val);
                getshoplist(val);
            }
            close();
        }
    }()


    function getshoplist(code){
        var html = "";
        var url = "<?php echo Url::to(['car/shop_list']);?>";
        $("#shop_list").html('');
        $.post(url,{code:code},function(json){
            if(json.status == 1){

                $.each(json.data,function(key, val){
                    html+='<li class="afterFour">';
                    html+='<a href="#">';
                    html+='<dl class="webkitbox">';
                    html+='<dt>';
                    if(val['shop_pic']!=''){
                        html+='<img src="'+val['shop_pic']+'">';
                    }else{
                        html+='<img src="/frontend/web/images/qiche.jpg">';
                    }
                    html+='</dt>';
                    html+='<dd>';
                    html+='<p class="p1">'+val['shop_name']+'</p>';
                    html+='<p class="p2">地址：'+val['shop_address']+'</p>';
                    html+='<p class="p3">电话：'+val['mobile']+'</p>';
                    html+='</dd>';
                    html+='</dl>';
                    html+='</a>';
                    html+='<div class="huishouButton webkitbox">';
                    html+='<a href="/frontend/web/car/address.html?address='+val['shop_address']+'&shop_name='+val['shop_name']+'" class="afterFour">查看位置</a>';
                    html+='</div>';
                    html+='</li>';


                });

                $("#shop_list").html(html);
            }else{
                alert(json.msg);
            }
        });
    }

    function getprovince(){
        var url = "<?php echo Url::to(['car/getprovince']);?>";
        var province=$('#shop_province').val();
        var html = "";
        $('#shop_city').val('');
        $('#shop_city').html('市');
        $('#shop_area').val('');
        $('#shop_area').html('区或县');
        $("#address").html('');
        province = province.toString();
        if(province.length == 1){
            province='00'+province;
        }else if(province.length == 2){
            province='0'+province;
        }

        $.post(url,{},function(json){
            if(json.status == 1){
                html+='<li class="webkitbox">';
                $.each(json.data,function(key, val){
                    if(val.code == province){
                        html+='<div class="cur">';
                        html+='<span>'+val.name+'<i></i></span>';
                    }else{
                        html+='<div>';
                        html+='<span>'+val.name+'</span>';
                    }
                    html+='<input class="adr_code" type="hidden" value="'+val.code+'">';
                    html+='</div>';
                    if((key+1)%3 == 0  && key > 0){
                        html+='</li><li class="webkitbox">';
                    }
                });
                html+='</li>';
                $("#address").html(html);
            }else{
                alert(json.msg);
            }
        });
    }

    function getcity(code){
        var url = "<?php echo Url::to(['car/getcity']);?>";
        var html = "";
        var city_code = $('#shop_city').val();

        $('#shop_area').val('');
        $('#shop_area').html('区或县');
        $("#address").html('');

        if(code.length == 0){
            alert('请先选择省级单位');
            return false;
        }
        code = code.toString();
        if(code.length == 1){
            code='00'+code;
        }else if(code.length == 2){
            code='0'+code;
        }else{
            return false;
        }

        city_code = city_code.toString();
        if(city_code.length == 4){
            city_code='00'+city_code;
        }else if(city_code.length == 5){
            city_code='0'+city_code;
        }

        $.post(url,{code:code},function(json){
            if(json.status == 1){
                html+='<li class="webkitbox">';
                $.each(json.data,function(key, val){
                    if(val.code == city_code){
                        html+='<div class="cur">';
                        html+='<span>'+val.name+'<i></i></span>';
                    }else{
                        html+='<div>';
                        html+='<span>'+val.name+'</span>';
                    }
                    html+='<input class="adr_code" type="hidden" value="'+val.code+'">';
                    html+='</div>';
                    if((key+1)%3 == 0  && key > 0){
                        html+='</li><li class="webkitbox">';
                    }
                });
                html+='</li>';
                $("#address").html(html);
            }else{
                alert(json.msg);
            }
        });
    }

    function getarea(code){
        var url = "<?php echo Url::to(['car/getarea']);?>";
        var html = "";
        var area_code = $('#shop_area').val();

        if(code.length == 0){
            alert('请先选择市级单位');
            return false;
        }
        code = code.toString();
        if(code.length == 4){
            code='00'+code;
        }else if(code.length == 5){
            code='0'+code;
        }else{
            return false;
        }
        area_code = area_code.toString();
        if(area_code.length == 7){
            area_code='00'+area_code;
        }else if(area_code.length == 8){
            area_code='0'+area_code;
        }

        $("#address").html('');
        $.post(url,{code:code},function(json){
            if(json.status == 1){
                html+='<li class="webkitbox">';
                $.each(json.data,function(key, val){
                    if(val.code == area_code){
                        html+='<div class="cur">';
                        html+='<span>'+val.name+'<i></i></span>';
                    }else{
                        html+='<div>';
                        html+='<span>'+val.name+'</span>';
                    }
                    html+='<input class="adr_code" type="hidden" value="'+val.code+'">';
                    html+='</div>';
                    if((key+1)%3 == 0  && key > 0){
                        html+='</li><li class="webkitbox">';
                    }
                });
                html+='</li>';
                $("#address").html(html);
            }else{
                alert(json.msg);
            }
        });
    }

</script>
<?php $this->endBlock('script');?>