<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/12 0012
 * Time: 上午 10:01
 */
use yii\helpers\Url;
?>
<?php $this->beginBlock('headStyle')?>
<link rel="stylesheet" href="/frontend/web/travel/css/chengtou-90f4f10114.css">
<link rel="stylesheet" href="/frontend/web/travel/css/index.css">
<?php $this->endBlock('headStyle')?>
<div class="bm-number con-box">
    <div class="con6box">
        <h1>出游人日期选择</h1>

        <ul class="con7 xuanze con7date">
            <li>可选择出游日期:</li>
            <li>
                <form action="">
                    <?php foreach ($info as $val){?>
                        <label><input type="radio" id="" name="radio" value="<?php echo $val['id'] ?>" /><?php echo $val['date'] ?></label> <br />
                    <?php }?>
                </form>
            </li>
        </ul>
        <div class="submitbtn">
            <a href="javascript: void(0);" onclick="subdate()">下一步</a>
        </div>
        <img src="./img/chengtou/logo.png" alt="" class="con7-logo">
    </div>
</div>
<script>
    var is_subm = false;
    function  subdate() {
        var date_id=$('input:radio[name="radio"]:checked').val();
        var list_id='<?php echo $list_id?>';
        if(is_subm){
            alert('数据提交中，请稍候');
            return false;
        }
        var url = "<?php echo Url::to(['travel-apply/changedate']);?>";
        is_subm=true;
        $.post(url,{
            date_id:date_id,
            list_id:list_id
        },function(json){
            console.log(111);
            is_subm = false;

            if(json.status == 1){
               console.log(111);
                window.location.href=json.url;
            }else{
                alert(json.msg)
            }
        });
    }
</script>