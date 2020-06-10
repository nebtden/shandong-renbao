<ul class="car-type-ul car-series-ul">
    <?php if($list): ?>
    <?php foreach ($list as $mname): ?>
    <li data-id="<?= $mname['id'] ?>" data-name="<?= $mname['name'] ?>"><?= $mname['name'] ?></li>
    <?php endforeach; ?>
    <?php else: ?>
        <li>此品牌暂无相关车型</li>
    <?php endif; ?>
</ul>
