<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/23 0023
 * Time: 下午 5:01
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>模板管理 <small>&nbsp;/&nbsp;编辑或添加模板</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:auto;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/templateedit'])?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">说明内容</label>
            <div class="col-sm-3">
                <textarea id="content" name="content" style=" width: 393px;height: 209px;" placeholder="商品参数" ><?php echo $data['content']?$data['content']:'';?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">优惠券的类型</label>
            <div class="col-sm-3">
                <select  name="coupon_type<?=$k?>"  placeholder="优惠券的类型"  class="form-control" onchange="getcompany(this)">
                    <?php foreach ($coupon_type as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['coupon_type']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">供应商</label>
            <div class="col-sm-3">
                <select id="company"  name="company"  placeholder="供应商"  class="form-control">
                    <?php foreach ($company as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['company']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <?php  if($data['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $data['id']; ?>">
        <?php }?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>

    </form>
</div>
<script>
    function getcompany(_this){
        var company = $(_this).val();
        var url = "<?php echo Url::to(['coupon/getcompany']);?>";
        var html = "";
        $('#company').html('');
        $.post(url,{company:company},function(json){
            if(json.status == 1){
                $.each(json.data,function(key,val){
                    html += '<option value="'+key+'">'+val+'</option>';
                });
                $('#company').append(html);
            }else{
                alert(json.msg);
            }
        });
    }

</script>