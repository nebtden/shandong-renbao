<div class="answer-bg">
    <h1 class="hide">胜利大阅兵</h1>
    <div class="answer-con">
        <div class="quiz-container">
            <div class="quiz">
                <p>共六题(<?= $question['id'] ?>/6)</p>
                <h2><?= $question['id'] ?>.<?= $question['question'] ?></h2>
                <span data-value="<?= $answers[0]['id'] ?>"> A: <?= $answers[0]['answer'] ?></span>
                <span data-value="<?= $answers[1]['id'] ?>"> B: <?= $answers[1]['answer'] ?></span>
                <span data-value="<?= $answers[2]['id'] ?>"> C: <?= $answers[2]['answer'] ?></span>
                <span data-value="<?= $answers[3]['id'] ?>"> D: <?= $answers[3]['answer'] ?></span>
            </div>
        </div>
        <?php if ($question['id'] != 6) { ?><a href="javascript:;" id="next">下一题</a>
        <?php } else { ?><a href="javascript:;" id="next">提交</a><?php } ?>

        <div id="results"></div>
    </div>

    <p class="idx-sm anser-foter">已有<?= $total ?>人参与<br />
        临沂人保财险保留在法律范围内对本活动的解释权</p>
</div>

<script src="/frontend/web/shandong-renbao-army/js/index.js"></script>
<script>
    $(function () {
        $('.quiz span').on('click', function() {
            $(this).addClass('selected').siblings().removeClass('selected');
        })
        $('#next').click(function () {
            if($('span.selected').length == 0){
                alert('请选择您的答案');
                return false
            }
            $.post('answer.html',{question_id:<?= $question['id'] ?>,answer:$('span.selected').attr('data-value')},function (data) {
                // console.log();
                if(data.status==0){
                    console.log(data.data);
                    window.location.href = 'last.html';
                }else{
                    window.location.href = 'question.html?id='+data.data.id;
                }
            },'json');
        });
    });


</script>