<?php foreach ($list as $item):?>
<li data-lng="<?=$item['lng']?>" data-lat="<?=$item['lat']?>">
    <h3><?=$item['name']?></h3>
    <span><?=$item['address']?></span>
</li>
<?php endforeach;?>