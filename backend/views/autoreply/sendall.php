<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>微信群发信息  <small>&nbsp;/&nbsp;群发信息页</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group">
            <select name="rid"  id="rid"  class="form-control">
                <option value="" >-----请选择群发消息主题-----</option>
                <?php foreach($data as $v) {?>
                    <option value="<?php echo  $v['id'] ;?>" ><?php echo  $v['title'] ;?></option>
                <?php }?>
            </select>
        </div>

        <button type="button" class="btn btn-info" id="rsousuo"><span class="glyphicon glyphicon-send"></span> 群发消息</button>
    </form>
</div>

<script>
    $(function(){
        $('#rsousuo').on('click',function(){
            var rid=$('#rid').val();
            $.post("<?php echo Url::to(['/autoreply/sendall',array('token'=>Yii::$app->session['token'])]); ?>",{rid:rid},function(s)
            {

                var res=eval('('+s+')');

                if(res.errcode==0)
                {
                    alert('发送成功');
                }
                else if(res.errcode==40007)
                {
                    alert('本地图片丢失，发送失败');
                }
                else if(res.errcode==-1)
                {
                    alert('系统繁忙，发送失败');
                }
                else
                {
                    alert('返回错误代码：'+res.errcode+'发送失败');
                }
            });
        });
    });
</script>