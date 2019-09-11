<?php

use yii\helpers\Url;

?>
<div class="">

    <a href="question.html?id=1" class="check">
         开始测试
    </a>

    <p class="index-footer">已有<?= $total ?>人参与<br/>
        最终解释权归临沂人保财险所有</p>
</div>
<script>
    $(function () {
        $('.check').click(function () {
            var license_plate = $('.license_plate').val();
            var re =  /^[0-9a-zA-Z]*$/g;  //判断字符串是否为数字和字母组合     //判断正整数 /^[1-9]+[0-9]*]*$/

            if(!re.test(license_plate) || license_plate.length!=5){
                alert('车牌格式不正确！');
            }else{
                window.location.href = 'mobile.html?license_plate='+license_plate;
            }
        });
    });


</script>
