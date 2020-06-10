<?php foreach ($list as $bigtype): ?>
    <dl class="carType-dl">
        <dt data-id="<?= $bigtype['id'] ?>"><?= $bigtype['name'] ?></dt>
        <?php foreach ($bigtype['list'] as $bname): ?>
            <dd data-id="<?= $bname['id'] ?>" data-name="<?= $bname['name'] ?>"
                data-logo="<?= $bname['logo'] ?>"><?= $bname['fullname'] ?></dd>
        <?php endforeach; ?>
    </dl>
<?php endforeach; ?>