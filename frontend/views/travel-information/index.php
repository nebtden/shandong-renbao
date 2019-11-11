<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 9:32
 */

?>
<?php $this->beginBlock('headStyle')?>
    <script type="text/javascript" src="/frontend/web/travel/lib/js/swiper-4.3.3.min.js"></script>
    <link rel="stylesheet" href="/frontend/web/travel/lib/css/animate.css">
    <link rel="stylesheet" href="/frontend/web/travel/css/chengtou-90f4f10114.css">
    <link rel="stylesheet" href="/frontend/web/travel/css/index.css">
<?php $this->endBlock('headStyle')?>
    <div class="bm-number con-box">
        <div class="con6box">
            <h1>出游人信息填写</h1>
            <ul class="con7">
                <li class="inputName">
                    <input type="text"  class="con7input1" id="inputName">
                    <span>出游人姓名:</span>
                </li>
                <li class="inputName">
                    <input type="text"  class="con7input1" id="inputName">
                    <span>出游人性别:</span>
                </li>
                <li class="inputName">
                    <input type="text"  class="con7input1" id="inputName">
                    <span>身份证号码:</span>
                </li>
                <li class="inputName">
                    <input type="text"  class="con7input1 con7input2" id="inputName">
                    <span>联系电话:</span>
                </li>
                <ul class="xuanze">
                    <li>可选择出游日期:</li>
                    <li>
                        <form action="">
                            <label><input type="radio" id="rad1" name="radio" />2019-11-10</label> <br/>
                            <label><input type="radio" id="rad2" name="radio" />2019-11-10</label>
                        </form>
                    </li>

                </ul>

                <input type="texarea" value="备注:" class="con7-text" class="con7input1">
                <!-- 删除出游人 -->
                <span class="con7delete">

        </span>
        </div>
        <!--添加出游人  -->
        <span class="con7add">

      </span>
    </div>
    <div class="con7footer">
        <p>
            请在<b>59:59:60</b>内填写完并提交本页面信息否则名额
            将会释放，在填写完成并提交之前关闭了此页面，
            也不会为您锁定名额，如有需要需重新抢订名额

        </p>
    </div>
    <div class="submitbtn">
        <a href="submit.html"><img src="/frontend/web/travel/img/chengtou/submit.png" alt=""></a>
    </div>
    </div>

    <script>
        var textField = document.getElementByTagName("input"), //获取表单域
            startText = textField.value; //获取开头字符串
        textField.onkeyup = function () {
            //如果不是以startText开头的，就把文本框内的值设为startText
            (textField.value.indexOf(startText) === 0) || (textField.value = startText);
        }
    </script>

<?php $this->beginBlock('script')?>
    <script src="/frontend/web/travel/js/donghua.js"></script>
<?php $this->endBlock('script')?>