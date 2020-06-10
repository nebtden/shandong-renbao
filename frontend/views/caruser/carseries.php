<ul class="car-type-ul drawer-data-ul ">
    <?php if($list): ?>
    <?php foreach ($list as $sname): ?>
    <li data-id="<?= $sname['id'] ?>" data-name="<?= $sname['name'] ?>"><?= $sname['name'] ?></li>
    <?php endforeach; ?>
    <?php else: ?>
        <li>此品牌暂无相关车系</li>
    <?php endif; ?>
</ul>
