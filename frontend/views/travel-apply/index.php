<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 9:32
 */
use yii\helpers\Url;
?>
<div class="bm-number">
    <div class="con6box">
        <h1>业务员报名信息</h1>
        <div class=" con5">
            <ul class="con5list com5list">
                <li class="con5box">
                    <span>工号:</span>
                    <select class="con5select" id="opt1" onchange="getMechanism()">
                        <option>请选择</option>
                        <?php foreach ($organ as $key=>$val){?>  
                            <option value="<?php echo $val['id']?>" ><?php echo $val['name']?></option>  
                        <?php } ?>
                    </select>
                </li>
                <li class="con5box">
                    <span>机构:</span>
                    <select class="con5select" id="opt2">
                            <option >请选择</option>
                    </select>
                </li>
                <li class="inputName">
                    <input type="text" id="opt3" class="con7input1 con7input3">
                    <span class="con5gonghao">工号:</span>
                </li>
                <li class="inputName">
                    <input type="text" id="opt4" class="con7input1 con7input3">
                    <span class="con5gonghao">姓名:</span>
                </li>
            </ul>
            <a href = "javascript: void(0);" class="con5-btn"> 我要报名 </a>
        </div>
    </div>
</div>
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock('footer'); ?>
<?php $this->beginBlock('script'); ?>
<script>
    $(document).ready(function(){
        var is_sub = false;
        $('.con5-btn').on('click', function () {
            var opt1=$('#opt1').val();
            var opt2=$('#opt2').val();
            var opt3=$('#opt3').val();
            var opt4=$('#opt4').val();
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
                opt4:opt4
            },function(json){

                is_sub = false;
                console.log(json);
                if(json.status == 1){
                    window.location.href=json.url;
                }else{
                    alert(json.message);
                }
            });
        })
    });
    var is_subm = false;
    function  getMechanism() {
        var opt1=$('#opt1').val();
        var html='';
        if(is_subm){
            alert('数据提交中，请稍候');
            return false;
        }
        var url = "<?php echo Url::to(['travel-apply/mechanism']);?>";
        $("#opt2").html('');
        is_subm=true;
        $.post(url,{
            opt1:opt1
        },function(json){
            is_subm = false;
            if(json.status == 1){
                $.each(json.data,function(key, val){
                    html+='<option value='+val.id+'>'+val.name+'</option> '
                });
                $("#opt2").html(html);
            }
        });
    }
</script>
<?php $this->endBlock('script'); ?>
