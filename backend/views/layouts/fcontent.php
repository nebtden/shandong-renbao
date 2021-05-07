<?php echo $this->context->renderPartial('../layouts/header',array('title'=>'后台管理'));?>
<section class="houtaiCont webkitbox boxSizing">
    <div class="rightCont boxSizing">
        <?= $content ?>
    </div>
</section>
<?php $this->endBody() ?>
</body>
<?php $this->endPage() ?>