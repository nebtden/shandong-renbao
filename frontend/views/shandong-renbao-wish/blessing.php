
<div class="bless" >
    <!-- Swiper -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="/frontend/web/shandong-renbao-wish/images/1.jpg" /></div>
            <div class="swiper-slide"><img src="/frontend/web/shandong-renbao-wish/images/2.jpg" /></div>
            <div class="swiper-slide"><img src="/frontend/web/shandong-renbao-wish/images/3.jpg" /></div>
            <div class="swiper-slide"><img src="/frontend/web/shandong-renbao-wish/images/4.jpg" /></div>
            <div class="swiper-slide"><img src="/frontend/web/shandong-renbao-wish/images/5.jpg" /></div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>

    <p >送家人父母长辈</p>
    <a href="letter.html?id=1" class="idx-yk ble-yk">用这张祝福海报</a>

</div>
<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: {
            el: '.swiper-pagination',
            dynamicBullets: true,
        },

    });
    swiper.on('slideChange', function () {
        console.log('slide changed');
        var id = swiper.activeIndex;
        console.log(id);
        id = id+1;
        $('.ble-yk').attr('href','letter.html?id='+id)
    });


</script>
