
<?php 
use yii\helpers\Url;
?>




<section class="MyOrderCont DetailsColor showHide">
    <h1 class="none_">方案下载</h1>
    <div class="Purchase">
        <span>方案下载</span>
    </div>
    <?php foreach($news as $v){?>
    <dl class="dingzhiDl webkitbox boxSizing">
        <dt><img src="<?php echo $v['pic']?>"></dt>
        <dd fid=<?php echo $v['id']?>>
            <p><?php echo $v['title']?></p>
            <p>项目说明：<?php echo $v['short_desc']?></p>
            <p class="clearfix"><a class="ke-insertfile a1" href="<?php echo $v['source']?>" target="_blank">下载</a><a class="a2" href="<?php echo Url::toRoute(['fangandetail','fid'=> $v['id']])?>">More</a></p>
        </dd>
    </dl>
    <?php }?>
    
    
</section>

<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/Inputsearch.js"></script>
