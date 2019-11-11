<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 9:32
 */

?>

<div class="bm-number con-box ">
    <div class="con6box conimportant ">
        <h1>已提交出游人信息</h1>
        <?php foreach ($list as $data ){  ?>
            <div class="con7  " style="height: 6.1rem">
                <li class="inputName">
                    <input type="text" class="con7input1" value="<?= $data['name'] ?> " disabled>
                    <span>出游人姓名:  </span>
                </li>
                <li class="inputName">
                    <input type="text" class="con7input1" value="<?= $data['sex'] ?>" disabled>
                    <span>出游人性别:  </span>
                </li>
                <li class="inputName">
                    <input type="text" class="con7input1" value="<?= $data['code'] ?>" disabled>
                    <span>身份证号码:  </span>
                </li>
                <li class="inputName">
                    <input type="text" class="con7input1 con7input2" value="<?= $data['mobile'] ?>" disabled>
                    <span>联系电话: </span>
                </li>
                <li class="inputName">
                    <input type="text" class="con7input1 con7input2" value="<?= $data['travel_date'] ?>" disabled>
                    <span>出游日期: </span>
                </li>

                <texarea class="con7input1 con7-text" style="padding-left:0rem;"><?= $data['remark'] ?> </texarea>
            </div>
        <?php } ?>

    </div>
</div>
