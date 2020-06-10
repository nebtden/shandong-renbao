<?php foreach ($list as $item):?>
<li data-lng="<?=$item['lng']?>" data-lat="<?=$item['lat']?>">
    <i class="icon-cloudCar2-weizhi"></i>
    <div class="right">
        <span><?=$item['name']?></span>
        <i><?=$item['address']?></i>
    </div>
</li>
<?php endforeach;?>