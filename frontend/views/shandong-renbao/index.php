<?php

use yii\helpers\Url;

?>
<div class="bg-index">
    本次活动已结束，谢谢您的关注

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
