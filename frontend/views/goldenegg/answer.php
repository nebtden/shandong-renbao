<?php
use yii\helpers\Url;

?>
<div class="answerBox">
    <p class="tit"><span>趣味问答</span></p>
    <div class="answer-content">
        <div class="answer-content-item">
            <p class="answer-tit"><span>平安e生保（保证续保版）首次投保年龄为（  ）</span></p>
            <ul class="answer-list">
                <li class= 'A'><em>A</em><span>0周岁—50周岁</span></li>
                <li class = 'B'><em>B</em><span>0周岁—55周岁</span></li>
                <li class = 'C'><em>C</em><span>0周岁—60周岁</span></li>
                <li class = 'D'><em>D</em><span>0周岁—65周岁</span></li>
            </ul>
        </div>
        <div class="answer-content-item" style="display: none">
            <p class="answer-tit"><span>平安e生保（保证续保版）每（  ）年为一个保证续保期</span></p>
            <ul class="answer-list">
                <li class="A"><em>A</em><span>5</span></li>
                <li class='B'><em>B</em><span>6</span></li>
                <li class='C'><em>C</em><span>7</span></li>
                <li class='D'><em>D</em><span>8</span></li>
            </ul>
        </div>
        <div class="answer-content-item" style="display: none">
            <p class="answer-tit"><span>本产品约定的医院范围是中华人民共和国境内（港、澳、台地区除外）合法经营的（  ）</span></p>
            <ul class="answer-list">
                <li class="A"><em>A</em><span>二级以上公立医院的普通部</span></li>
                <li class="B"><em class="B">B</em><span>二级以上（含二级）公立医院</span></li>
                <li class="C"><em class="C">C</em><span>二级以上公立医院的普通部、特需部</span></li>
                <li class="D"><em class="D">D</em><span>二级以上（含二级）公立医院的普通部</span></li>
            </ul>
        </div>
    </div>
    <div class="btnBox">
        <a href="javascript:void(0)" class="submit-btn"><img src="images/tj-btn.png" alt=""></a>
    </div>
</div>
<?php $this->beginBlock('script') ?>
<script>
    var  right_result = ['A','B','D'];
    $('.answer-list').on('click', 'li',function () {
        $(this).find('em').addClass('active').parent().siblings().find('em').removeClass('active')
    })
    $('.submit-btn').on('click',function () {
        if($('.answer-content-item:visible').find('.answer-list li  em.active').text() != right_result[$('.answer-content-item:visible').index()]){
            var rightResult = right_result[$('.answer-content-item:visible').index()]+'     ,'+ $('.answer-content-item:visible').find('.'+right_result[$('.answer-content-item:visible').index()]).find('span').text();
            $('body').append("<div class='errMsg'><div>这题的正确答案:<br><br>"+rightResult+"</div></div>");
            setTimeout(function () {
                $('.errMsg').remove()
            },2000);
            return false
        }
        if(  $('.answer-content-item:visible').index() == 2){
            window.location.href = '<?php echo Url::to(['goldenegg/instant','id'=>$id]) ?>';
            return false
        }
        $('.answer-content-item:visible').next().show().siblings().hide();
    })
</script>
<?php $this->endBlock('script') ?>
