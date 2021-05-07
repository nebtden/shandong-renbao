
<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="page-header am-fl am-cf">
    <h4>
        汽车卡批量生成 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['car/generate']) ?>" >
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">生成数量</label>
            <div class="col-sm-3">
                <input type="number" required  name="generate_num" class="form-control" placeholder="请输入要生成的数量">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡的面值</label>
            <div class="col-sm-3">
                <input type="text"  required name="card_amount" class="form-control" placeholder="卡的面值">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">卡的区域</label>
            <div class="col-sm-3">
                <select id="shop_province" name="province"  placeholder="省"  class="form-control" onchange="getcity();"  >
                    <?php foreach ($province as $val){?>
                        <option value="<?php echo $val['code']?>" <?php if($address[0]['code'] == $val['code']){?> selected = "selected" <?php }?>><?php echo $val['name']?></option>
                    <?php }?>
                </select>
                <select id="shop_city" name="city"  placeholder="市"  class="form-control"  onchange="getarea()">
                    <option value="<?php echo $address[1]['code']?>"><?php echo $address[1]['name']?></option>
                </select>
                <select id="shop_area" name="area"  placeholder="县或区"  class="form-control" onchange="getaddress()" >
                    <option value="<?php echo $address[2]['code']?>"><?php echo $address[2]['name']?></option>
                </select>
                <input type="hidden" id="card_region" name="card_region" class="form-control" value="<?php echo $data['shop_address'];?>"  placeholder="商户地址">
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">提成系数</label>
            <div class="col-sm-3">
                <input type="text" required  name="t_sum" class="form-control"  id="inputEmail3" placeholder="提成系数">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">有效期至</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" name="expire_time" id="expire_time" value="" readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">公司名称</label>
            <div class="col-sm-3">
                <input type="text"  required name="company" class="form-control"  id="inputEmail3" placeholder="公司名称">
            </div>
        </div>


        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">业务员姓名</label>
            <div class="col-sm-3">
                <input type="text"  required name="personname" class="form-control"  id="inputEmail3" placeholder="业务员姓名">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">业务员编号</label>
            <div class="col-sm-3">
                <input type="text"  required name="personcode" class="form-control"  id="inputEmail3" placeholder="业务员编号">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script>
    $('form').submit(function(){
        var error=0;
        $('input[type="text"]').each(function(){
            if($(this).val().length==0) {
                $(this).parent().parent().addClass('has-error');
                error = 1;
            }
            else{
                $(this).parent().parent().removeClass('has-error');
            }
        });
        if(error>0) return false;
        else  return true;
    });
    getcity();
    function getcity(){
        var code = $("#shop_province").val();
        var url = "<?php echo Url::to(['car/getcity']);?>";
        var html = "";
        $('#shop_city').html('');
        $.post(url,{code:code},function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    html += '<option value="'+this.code+'">'+this.name+'</option>';
                });
                $('#shop_city').append(html);
                getarea();
            }else{
                alert(json.msg);
            }
        });
    }

    function getarea(){
        var code = $("#shop_city").val();
        var url = "<?php echo Url::to(['car/getarea']);?>";
        var html = "";
        var str='';
        $('#shop_area').html('');
        $.post(url,{code:code},function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    html += '<option value="'+this.code+'">'+this.name+'</option>';
                });
                $('#shop_area').append(html);
                getaddress();
            }else{
                alert(json.msg);
            }
        });
    }
    function  getaddress() {
        var str='';
        str=$('#shop_province').find("option:selected").text()+$('#shop_city').find("option:selected").text()+$('#shop_area').find("option:selected").text();
        $('#card_region').val(str);
    }

</script>
