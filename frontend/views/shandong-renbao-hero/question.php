<?php

use yii\helpers\Url;

?>
<div class="hero-bg">
    <h1 class="hide">猜英雄 得大奖</h1>
    <div class="hero-con" style="display: block">
        <div class="hero-con1">
            <input type="hidden" class="question" value="<?= $question['id'] ?>">

            <img src="/frontend/web/shandong-renbao-hero/images/hero-<?= $question['id'] ?>.jpg" alt="">
            <ul class="hero-xz">
                <?php foreach ($answers as $answer){ ?>
                    <li class="answer" data-id="<?= $answer['id'] ?>" ><?= $answer['answer'] ?></li>
                <?php } ?>

            </ul>

        </div>
        <ul class="hero-con2">
            <li <?php if($question['id']==1){ ?>class="current"<?php }?>>  </li>
            <li <?php if($question['id']==2){ ?>class="current"<?php }?>></li>
            <li <?php if($question['id']==3){ ?>class="current"<?php }?>></li>
            <li <?php if($question['id']==4){ ?>class="current"<?php }?>></li>
        </ul>
    </div>



    <a href="javascript:;"  class="hero-btn submit"> 下一步 </a>
</div>

<script>
    $(function () {
        var answer = 0;
        $('.answer').click(function () {
            answer = $(this).data('id');
            $('.answer').removeClass('hero-current');
            $(this).addClass('hero-current');
        });


        $('.submit').click(function () {
            var question_id = $('.question').val();
            if(answer==0){
                alert('请选择英雄');
                return false
            }

            $.post('answer.html',{question_id:question_id,answer:answer},function (data) {
                // console.log();
                if(data.status==0){
                    console.log(data.data);
                    window.location.href = 'last.html?scores='+data.data.scores;
                }else{
                    window.location.href = 'question.html?id='+data.data.id;
                }
            },'json');
        });
    });


</script>

