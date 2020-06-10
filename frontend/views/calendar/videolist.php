<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/24 0024
 * Time: 下午 1:37
 */

use yii\helpers\Url;
?>
<div id="app">
    <div class="banner-box">
        <img src="<?php echo $info['background_pic'];?>" alt="">
    </div>
    <div class="content <?php echo $info['class'];?>">
        <ul class="video-list">
            <?php foreach ($list as $key =>$val){?>
                <li>
                    <div class="video-box">
                        <a href="<?php echo Url::to(['calendar/broadcast' , 'id'=>$val['id']]);?>">
                            <img src="<?php echo $val['pic']?$val['pic']:'./images/pre-view.png';?>" class="pre-view-img" alt="">
                            <div class="video-play-mark">
                                <span class="video-play-btn"></span>
                            </div>
                        </a>
                    </div>
                    <p class="desc fz-30"><?php echo $val['title'];?></p>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>




