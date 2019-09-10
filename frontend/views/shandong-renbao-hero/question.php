<?php

use yii\helpers\Url;

?>
<div class="bg-index">

        <?= $question['question'] ?>
    <input type="hidden" class="question" value="<?= $question['id'] ?>">

        <br>
        <?php foreach ($answers as $answer){ ?>
            <input type="radio" id="answer" name="answer" value="<?= $answer['id'] ?>" ><?= $answer['answer'] ?>
            <br>
        <?php } ?>


        <button type="submit"  class="submit" >提交</button>

</div>
<script>
    $(function () {
        $('.submit').click(function () {
            var question_id = $('.question').val();
            var answer= $('input[name=answer]:checked').val();

            $.post('answer.html',{question_id:question_id,answer:answer},function (data) {
                // console.log();
                if(data.status==0){
                    alert(data.msg);
                }else{
                    window.location.href = 'question.html?id='+data.data.id;
                }
            },'json');
        });
    });


</script>

