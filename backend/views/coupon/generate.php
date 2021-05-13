
<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        优惠券批量生成 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">生成数量</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="100000" id="generate_num" name="generate_num" class="form-control" placeholder="请输入要生成的数量">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">优惠券的类型</label>
            <div class="col-sm-3">
                <select id="coupon_type" name="coupon_type"  placeholder="优惠券的类型"  class="form-control" onchange="changescene(this)">
                    <?php foreach ($coupon_type as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group upcompany">
            <label for="inputPassword3" class="col-sm-2 control-label">油卡供应商</label>
            <div class="col-sm-3">
                <select id="oil_company" name="oil_company"  placeholder="油卡供应商"  class="form-control" onchange="changecompany(this)">
                    <?php foreach ($oil_company as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- 年检券供应商 -->
        <div class="form-group ins_xxz">
            <label for="inputPassword3" class="col-sm-2 control-label">年检供应商</label>
            <div class="col-sm-3">
                <select id="ins_company" name="ins_company"  placeholder="类型"  class="form-control">
                    <?php foreach ($ins_company as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <!-- 洗车券供应商 -->
        <span class="wash_xxz">
            <div class="form-group wash_xxz">
                <label for="inputPassword3" class="col-sm-2 control-label">洗车券供应商</label>
                <div class="col-sm-3">
                    <select id="wash_company" name="wash_company"  placeholder="类型"  class="form-control">
                        <?php foreach ($wash_company as $key => $val):?>
                            <option value="<?=$key?>"><?=$val?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <!-- 是否支持每月必用一次 -->
            <div class="form-group wash_xxz">
                <label for="inputPassword3" class="col-sm-2 control-label">是否支持每月必用一次</label>
                <div class="col-sm-3">
                    <select id="is_mensal" name="is_mensal"  placeholder="是否支持每月必用一次"  class="form-control">
                        <option value="1">是</option>
                        <option value="0">否</option>
                    </select>
                </div>
            </div>
        </span>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">优惠券名称</label>
            <div class="col-sm-3">
                <input type="text"  required id="coupon_name"  name="name" class="form-control" placeholder="优惠券名称">
            </div>
        </div>
        <div class="form-group xxz_amount">
            <label for="inputEmail3" class="col-sm-2 control-label">优惠券面额（可使用次数，公里数,-1不限次数）</label>
            <div class="col-sm-3">
                <input type="number" min="-1" max="9999999999" id="amount" name="amount" class="form-control" placeholder="优惠券可使用次数">
            </div>
        </div>
        <div class="form-group xxz_bindid" >
            <label for="inputPassword3" class="col-sm-2 control-label">滴滴代驾权益类型</label>
            <div class="col-sm-3">
                <select id="bindid" name="bindid"  placeholder="滴滴代驾权益类型"  class="form-control">
                    <?php foreach ($driving_coupon_type as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <div class="form-group aiqiyi_bindid" >
            <label for="inputPassword3" class="col-sm-2 control-label">爱奇艺类型</label>
            <div class="col-sm-3">
                <select id="bindid2" name="bindid2"  placeholder="滴滴代驾权益类型"  class="form-control">
                    <?php foreach ($aiqiyi_coupon_types as $key => $val):?>
                        <option value="<?=$val['product_id']?>"><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">激活后多少天有效</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="36500"  required id="expire_days" name="expire_days" class="form-control" placeholder="激活后多少天有效">
            </div>
        </div>

        <div class="form-group uprescue">
            <label for="inputPassword3" class="col-sm-2 control-label">救援类型（券为道路救援时可用）</label>
            <div class="col-sm-3">
                <select id="scene" name="scene"  placeholder="类型"  class="form-control">
                    <?php foreach ($coupon_faulttype as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group oil_xxz">
            <label for="inputPassword3" class="col-sm-2 control-label">油券类型（券为油卡充值券时可用）</label>
            <div class="col-sm-3">
                <select  id="oil_type" name="oil_type"  placeholder="类型"  class="form-control">
                    <?php foreach ($oil_type as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="button" class="btn btn-default" onclick="Leadin()">批量生成</button>
            </div>
        </div>
    </form>
</div>

<div class="yinying" style="
position: fixed;width: 100%; height: 100%; left: 0;top: 0;background: #333;opacity: 0.6;z-index: 1000; display:none;"><span ></span>
</div>
<script type="text/javascript">

    function Leadin(){

        var generate_num = $("#generate_num").val(),
            coupon_type = $("#coupon_type").val(),
            oil_company = $("#oil_company").val(),
            wash_company = $("#wash_company").val(),
            coupon_name = $("#coupon_name").val(),
            amount = $("#amount").val(),
            expire_days = $("#expire_days").val(),
            scene = $("#scene").val(),
            oil_type = $("#oil_type").val(),
            bindid=$("#bindid").val(),
            is_mensal=$("#is_mensal").val(),
            ins_company=$("#ins_company").val()
            ;
        if(coupon_type==11){
            bindid=$("#bindid2").val();
        }
        if(generate_num == '' ){
            alert('生成数量不能为空');
            return false;
        }

        if(generate_num>100000){
            alert('生成数量不能超过10万');
            return false;
        }
        if(coupon_name == '' || coupon_name == null ){
            alert('优惠券名称不能为空');
            return false;
        }
        if(coupon_type != 5 && coupon_type != 1 && coupon_type != 11){
            if(isNaN(amount) || amount == '' || amount == null ){
                alert('优惠券面额不是一个数字');
                return false;
            }
        }


        if(isNaN(expire_days) || expire_days == '' || expire_days == null ){
            alert('激活后多少天有效不是一个数字');
            return false;
        }
        $(".yinying").show();
        $.post('<?php echo Url::to(["coupon/generate"]); ?>',{
            generate_num:generate_num,
            coupon_type:coupon_type,
            oil_company:oil_company,
            wash_company:wash_company,
            name:coupon_name,
            amount:amount,
            expire_days:expire_days,
            scene:scene,
            oil_type:oil_type,
            bindid:bindid,
            is_mensal:is_mensal,
            ins_company:ins_company
        },function(json){
            if(json.status == 1){
                console.log(json.data);
                Polling(json.data);
            }else{
                alert(json.msg);
                $(".yinying").hide();
                console.log(json.msg);
            }
        },'json');
    }
    var num = 0;
    function Polling(data){
        $.post('<?php echo Url::to(["coupon/batchcoupon"]); ?>',{
            key:data.xxzkey,
            num:num,
            xxzmaxnum:data.xxzmaxnum,
        },function(json){

            if(json.status == 1){
                $(".yinying").hide();
                alert('批量生成成功');
                window.location.href=json.url;
                return false;
            }else if(json.status == 2){
                num++;
                Polling(json.data);
            }else{
                $(".yinying").hide();
                alert(json.error);
                console.log(json.error);
            }
        },'json');
    }




    $('.uprescue').hide();
    $('.oil_xxz').hide();
    $('.upcompany').hide();
    $('.wash_xxz').hide();
    $('.xxz_amount').hide();
    $('.ins_xxz').hide();
    $('.aiqiyi_bindid').hide();
     function changescene(_this) {
         var typeval=$(_this).val();
         if(typeval=='1'){
             $('.uprescue').hide();
             $('.oil_xxz').hide();
             $('.upcompany').hide();
             $('.xxz_amount').hide();
             $('.wash_xxz').hide();
             $('.xxz_bindid').show();
             $('.ins_xxz').hide();
             $('.aiqiyi_bindid').hide();
         }else if(typeval=='2'){
             $('.uprescue').show();
             $('.oil_xxz').hide();
             $('.upcompany').hide();
             $('.xxz_amount').show();
             $('.wash_xxz').hide();
             $('.xxz_bindid').hide();
             $('.ins_xxz').hide();
             $('.aiqiyi_bindid').hide();
         }else if(typeval=='5'){
             $('.uprescue').hide();
             $('.oil_xxz').show();
             $('.upcompany').show();
             $('.xxz_amount').hide();
             $('.wash_xxz').hide();
             $('.xxz_bindid').hide();
             $('.ins_xxz').hide();
             $('.aiqiyi_bindid').hide();
         }else if(typeval=='4'){
             $('.upcompany').hide();
             $('.oil_xxz').hide();
             $('.uprescue').hide();
             $('.xxz_amount').show();
             $('.wash_xxz').show();
             $('.xxz_bindid').hide();
             $('.ins_xxz').hide();
             $('.aiqiyi_bindid').hide();
         }else if(typeval=='7'){
             $('.upcompany').hide();
             $('.oil_xxz').hide();
             $('.uprescue').hide();
             $('.xxz_amount').show();
             $('.wash_xxz').hide();
             $('.xxz_bindid').hide();
             $('.ins_xxz').show();
             $('.aiqiyi_bindid').hide();
         }else if(typeval=='10'){
             $('.upcompany').hide();
             $('.oil_xxz').hide();
             $('.uprescue').hide();
             $('.xxz_amount').show();
             $('.wash_xxz').hide();
             $('.xxz_bindid').hide();
             $('.aiqiyi_bindid').hide();
         }else if(typeval=='11'){
             $('.upcompany').hide();
             $('.oil_xxz').hide();
             $('.ins_xxz').hide();
             $('.uprescue').hide();
             $('.xxz_amount').show();
             $('.wash_xxz').hide();
             $('.xxz_bindid').hide();
             $('.aiqiyi_bindid').show();
         }else{

         }

     }
</script>
