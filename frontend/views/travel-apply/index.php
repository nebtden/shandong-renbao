<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 9:32
 */
use yii\helpers\Url;
?>
<?php $this->beginBlock('headStyle')?>
<link rel="stylesheet" href="/frontend/web/travel/css/chengtou-90f4f10114.css">
<link rel="stylesheet" href="/frontend/web/travel/css/index.css">
<link rel="stylesheet" type="text/css" href="/frontend/web/travel/css/public.css">
<?php $this->endBlock('headStyle')?>
<?php $this->beginBlock('hScript')?>
<script src="/frontend/web/travel/lib/js/jquery-2.4.1.js"></script>
<script src="/frontend/web/travel/js/index.js"></script>
<?php $this->endBlock('hScript')?>
<div class="bm-number">
    <div class="con6box">
        <h1>业务员报名信息</h1>
        <div class=" con5">
            <ul class="con5list com5list">
                <li class="con5box">
                    <span>中支:</span>
                    <div class="demo con5select" >
                        <div id="trigger1" data-align="">请选择中支▽</div>
                    </div>
                </li>
                <li class="con5box">
                    <span>机构:</span>
                       <div class="demo con5select">
                        <div id="trigger2" data-align=""></div>
                    </div>
                </li>
                <li class="inputName">
                    <input type="text" class="con7input1 con7input3" id="opt3">
                    <span class="con5gonghao">工号:</span>
                </li>
                <li class="inputName">
                    <input type="text" class="con7input1 con7input3" id="opt4">
                    <span class="con5gonghao">姓名:</span>
                </li>
            </ul>
            <a  href="javascript: void(0);"  class="con5-btn"> 我要报名 </a>
        </div>
    </div>
</div>
<input type="hidden" value="" id="opt1">
<input type="hidden" value="" id="opt2">
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>
<script>

     var weekdayArr1 = '<?php echo $info?>';
     var Arr1 = eval('(' + weekdayArr1 + ')');
            console.log(Arr1);
     var mobileSelect = new MobileSelect({
        trigger: '#trigger1',
        title: '选择中支',
        wheels: [{
            data: Arr1
        }],
        position: [0], //初始化定位 打开时默认选中的哪个  如果不填默认为0
        callback:function(indexArr, data){
            console.log(data); //Returns the selected json data
            console.log(data[0]['value']); //Returns the selected json data
            $('#opt1').val(data[0]['value']);
            $('#opt2').val(data[1]['value']);
            $('#trigger1').html(data[0]['value']);
            $('#trigger2').html(data[1]['value']);
        }
    });



    $(document).ready(function(){
        var is_sub = false;
        $('.con5-btn').on('click', function () {
            var opt1=$('#opt1').val();
            var opt2=$('#opt2').val();
            var opt3=$('#opt3').val();
            var opt4=$('#opt4').val();
            var opt5 = '<?php echo $id;?>';
            if(!opt1){
                alert('请选择中支');
                return false;
            }
            if(!opt2){
                alert('请选择机构');
                return false;
            }

            if(is_sub){
                alert('数据提交中，请稍候');
                return false;
            }
            var url = "<?php echo Url::to(['travel-apply/login']);?>";
            is_sub=true;
            $.post(url,{
                opt1:opt1,
                opt2:opt2,
                opt3:opt3,
                opt4:opt4,
                opt5:opt5

            },function(json){

                is_sub = false;
                console.log(json);
                if(json.status == 1){
                    window.location.href=json.url;
                }else{
                    alert(json.msg);
                }
            });
        })
    });


</script>
<?php $this->endBlock('script'); ?>
