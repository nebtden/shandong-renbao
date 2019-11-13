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
        <h1><?php echo $list_info['title2'] ?></h1>
        <div class="con5 con6">
            <p class="p1">名额仅剩：<b><?php echo $sum?></b></p>
            <p class="p2">锁定中名额：<b><?php echo $locked?></b></p>
            <ul class="con5list bm-list">
                <li class="inputName">
                    <input type="number"  class="con7input1 con6input" id="inputName" min="2" max="10000">
                    <span>填写您报名人数:</span>
                </li>
            </ul>
            <p class="p3">(业务员本人也将占一个人数)</p>
            <span class="p4">
              注意：名额剩余数在每次页面加载或刷新时更新，<br/>
              可能在当时显示有数量，但实际已经被抢光的情况；<br/>
              如果存在锁定中名额，代表您还有机会。
          </span>
            <a href="javascript: void(0);" class="con5-btn" onclick="subdate()"> 下一步 </a>
        </div>
    </div>
</div>
<script>
    var is_subm = false;
    function  subdate() {
        var num=$('#inputName').val();
        var date_id='<?php echo $date_id?>';
        var list_id='<?php echo $list_id?>';
        if(!num){
            alert('请填写报名人数');
            return false;
        }
        if(num < 2){
            alert('报名人数不得少于两人');
            return false;
        }
        if(is_subm){
            alert('数据提交中，请稍候');
            return false;
        }
        var url = "<?php echo Url::to(['travel-apply/add']);?>";
        is_subm=true;
        $.post(url,{
            num:num,
            date_id:date_id,
            list_id:list_id
        },function(json){
            is_subm = false;
            if(json.status == 1){
                window.location.href=json.url;
            }else{
                alert(json.msg);
            }
        });
    }
</script>


