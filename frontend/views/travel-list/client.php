<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 9:32
 */
use yii\helpers\Url;
?>
<?php $this->beginBlock('headStyle')?>
    <script type="text/javascript" src="/frontend/web/travel/lib/js/swiper-4.3.3.min.js"></script>
    <link rel="stylesheet" href="/frontend/web/travel/lib/css/animate.css">
    <link rel="stylesheet" href="/frontend/web/travel/css/chengtou-90f4f10114.css">
    <link rel="stylesheet" href="/frontend/web/travel/css/index.css">
<?php $this->endBlock('headStyle')?>

<div class="swiper-container fullpage swiper-container-initialized swiper-container-vertical" id="swiper_index">
    <div class="swiper-wrapper" style="transform: translate3d(0px, 0px, 0px);">



      <!-- 首页1start -->
      <div class="swiper-slide swiper-slide-active" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; background-position: 50% 100%; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <div class="wrapper">
            <div class="ele animated2 zoomIn ts2"
              style="background-image: url('/frontend/web/travel/img/chengtou/slide-6_6.png?v=1'); background-size: 100%; background-position: 50% 0%; display: block;">
            </div>
            <img src="/frontend/web/travel/img/chengtou/slide-6_4.png?v=1" class="ele animated2 zoomIn ts4" style="display: inline;">
          </div>
        </div>
      </div>

      <!-- 首页1end -->
      <!-- 第二页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container">
          <div class="wrapper">
            <div class="ele animated2 zoomIn ts1"
              style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_3.png?v=1'); background-size: 100%; background-position: 50% 0%; display: block;">
            </div>
          </div>
        </div>
      </div>
      <!-- 第二页end -->
      <!-- 第三页 -->
      <div class="swiper-slide swiper-slide-next" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <img src="/frontend/web/travel/img/chengtou/slide-2_4.png?v=1" class="ele animated2 zoomIn" style="display: inline;"></div>
      </div>
      <!-- 第三页end -->
      <!-- 第四页 -->
      <?php if($id==1){  ?>
      <!-- 古滇王国高铁第一页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-2_1.png?v=1" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-2_1.2.png?v=1" class="ele animated2 zoomIn ts3">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>
                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=1"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 古滇王国第二页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-2_2.png?v=1" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-2_1.2.png?v=1" class="ele animated2 zoomIn ts3">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=1"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 古滇王国第三页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-2_3.png?v=1" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-2_1.2.png?v=1" class="ele animated2 zoomIn ts3">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=1"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 古滇王国第四页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-2_3.4.png?v=1" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-2_1.2.png?v=1" class="ele animated2 zoomIn ts3">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=1"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 古滇王国第五页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-2_3.5.png?v=1" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-2_1.2.png?v=1" class="ele animated2 zoomIn ts3">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>

                  <a href="/frontend/web/travel-information/success.html?id=1"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <?php } ?>
        <?php if($id==2){  ?>
            <!-- 古滇王国高铁第一页 -->
            <div class="swiper-slide" style="height: 966px;">
                <div class="ele animated2 fallDown ts1"
                     style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
                </div>
                <div class="container" style="transform: scale(0.848858, 0.848858);">
                    <ul>
                        <li>
                            <img src="/frontend/web/travel/img/chengtou/slide-2_1.png?v=1" class="ele animated2 zoomIn ts2">
                            <img src="/frontend/web/travel/img/chengtou/slide-2_1.1.png?v=1" class="ele animated2 zoomIn ts3">
                            <div class="ele con-4"
                                 style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                                <div>

                                    <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                                    <a href="/frontend/web/travel-information/success.html?id=2"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                                </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 古滇王国第二页 -->
            <div class="swiper-slide" style="height: 966px;">
                <div class="ele animated2 fallDown ts1"
                     style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
                </div>
                <div class="container" style="transform: scale(0.848858, 0.848858);">
                    <ul>
                        <li>
                            <img src="/frontend/web/travel/img/chengtou/slide-2_2.png?v=1" class="ele animated2 zoomIn ts2">
                            <img src="/frontend/web/travel/img/chengtou/slide-2_1.1.png?v=1" class="ele animated2 zoomIn ts3">
                            <div class="ele con-4"
                                 style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                                <div>

                                    <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                                    <a href="/frontend/web/travel-information/success.html?id=2"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                                </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 古滇王国第三页 -->
            <div class="swiper-slide" style="height: 966px;">
                <div class="ele animated2 fallDown ts1"
                     style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
                </div>
                <div class="container" style="transform: scale(0.848858, 0.848858);">
                    <ul>
                        <li>
                            <img src="/frontend/web/travel/img/chengtou/slide-2_3.png?v=1" class="ele animated2 zoomIn ts2">
                            <img src="/frontend/web/travel/img/chengtou/slide-2_1.1.png?v=1" class="ele animated2 zoomIn ts3">
                            <div class="ele con-4"
                                 style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                                <div>

                                    <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                                    <a href="/frontend/web/travel-information/success.html?id=2"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                                </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 古滇王国第四页 -->
            <div class="swiper-slide" style="height: 966px;">
                <div class="ele animated2 fallDown ts1"
                     style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
                </div>
                <div class="container" style="transform: scale(0.848858, 0.848858);">
                    <ul>
                        <li>
                            <img src="/frontend/web/travel/img/chengtou/slide-2_3.4.png?v=1" class="ele animated2 zoomIn ts2">
                            <img src="/frontend/web/travel/img/chengtou/slide-2_1.1.png?v=1" class="ele animated2 zoomIn ts3">
                            <div class="ele con-4"
                                 style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                                <div>

                                    <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                                    <a href="/frontend/web/travel-information/success.html?id=2"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                                </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 古滇王国第五页 -->
            <div class="swiper-slide" style="height: 966px;">
                <div class="ele animated2 fallDown ts1"
                     style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
                </div>
                <div class="container" style="transform: scale(0.848858, 0.848858);">
                    <ul>
                        <li>
                            <img src="/frontend/web/travel/img/chengtou/slide-2_3.5.png?v=1" class="ele animated2 zoomIn ts2">
                            <img src="/frontend/web/travel/img/chengtou/slide-2_1.1.png?v=1" class="ele animated2 zoomIn ts3">
                            <div class="ele con-4"
                                 style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                                <div>

                                    <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>1]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>

                                    <a href="/frontend/web/travel-information/success.html?id=2"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                                </div>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
        <?php if($id==3){  ?>
      <!-- 太保路线第一页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-tb-1.png" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-tbtxt.png" class="ele animated2 zoomIn ts3">
              <div class="ele  con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>2]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=3"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 太保路线第二页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-tb-2.png" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-tbtxt.png" class="ele animated2 zoomIn ts3">
              <div class="ele  con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>2]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>

                  <a href="/frontend/web/travel-information/success.html?id=3"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 太保路线第三页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-tb-3.png" class="ele animated2 zoomIn ts3">
              <img src="/frontend/web/travel/img/chengtou/slide-tbtxt.png" class="ele animated2 zoomIn ts2">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>2]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=3"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 太保路线第四页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-tb-4.png" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-tbtxt.png" class="ele animated2 zoomIn ts3">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>2]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=3"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
        <?php }  ?>
        <?php if($id==4){  ?>
      <!-- 浙江财富第一页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-zj-1.png" class="ele animated2 zoomIn ts3">
              <img src="/frontend/web/travel/img/chengtou/slide-slide-zjtxt.png" class="ele animated2 zoomIn ts2">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>3]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=4"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 浙江财富第二页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-zj-2.png" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-slide-zjtxt.png" class="ele animated2 zoomIn ts3">
              <div class="ele  con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>3]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=4"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 浙江财富第三页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-zj-3.png" class="ele animated2 zoomIn ts3">
              <img src="/frontend/web/travel/img/chengtou/slide-slide-zjtxt.png" class="ele animated2 zoomIn ts2">
              <div class="ele  con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>3]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=4"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 浙江财富第四页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-zj-4.png" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-slide-zjtxt.png" class="ele animated2 zoomIn ts3">
              <div class="ele  con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>3]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>
                  <a href="/frontend/web/travel-information/success.html?id=4"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- 浙江财富第五页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-zj-5.png" class="ele animated2 zoomIn ts3">
              <img src="/frontend/web/travel/img/chengtou/slide-slide-zjtxt.png" class="ele animated2 zoomIn ts2">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>3]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>

                  <a href="/frontend/web/travel-information/success.html?id=4"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- 浙江财富第六页 -->
      <div class="swiper-slide" style="height: 966px;">
        <div class="ele animated2 fallDown ts1"
          style="background-image: url('/frontend/web/travel/img/chengtou/slide-5_1.jpg?v=1'); background-size: cover; display: block;">
        </div>
        <div class="container" style="transform: scale(0.848858, 0.848858);">
          <ul>
            <li>
              <img src="/frontend/web/travel/img/chengtou/slide-zj-6.png" class="ele animated2 zoomIn ts2">
              <img src="/frontend/web/travel/img/chengtou/slide-slide-zjtxt.png" class="ele animated2 zoomIn ts3">
              <div class="ele con-4"
                style="background-image: url('/frontend/web/travel/img/chengtou/slide-2_1.3.png?v=1'); background-size: cover; display: block;">
                <div>

                  <a href="<?php echo Url::to(['travel-apply/index' , 'id'=>3]);?>"><img src="/frontend/web/travel/img/chengtou/con4-btn.png" alt="" class="con4btn"></a>

                  <a href="/frontend/web/travel-information/success.html?id=4"> <img src="/frontend/web/travel/img/chengtou/con4-btn1.png" alt="" class="con4btn1"></a>
                </div>
            </li>
          </ul>
        </div>

      </div>
        <?php }   ?>
      <!-- 第四页end -->
    </div>
  </div>
  <div class="up">
    <div class="ups"></div>
  </div>

<?php $this->beginBlock('script')?>
    <script src="/frontend/web/travel/js/donghua.js"></script>
<?php $this->endBlock('script')?>