<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="page-header am-fl am-cf">
    <h4>
        券包批量生成<small>&nbsp;&nbsp;/ 批量生成</small>
    </h4>
</div>
<table class="table table-bordered "  style="margin-top:10px;">
    <thead>
    <tr>
        <th>类型编号</th>
        <th>优惠券类型</th>
        <th>可使用次数或公里数</th>
        <th>优惠券数量</th>
        <th>救援类型</th>
        <th>对应优惠券批号</th>

    </tr>
    </thead>
    <tbody id="coupon_table">
    <?php foreach ($aplist as $list):?>
        <tr>
            <td><?=$list['id']?></td>
            <td><?=$list['prize_name']?></td>
            <td><?=$list['prize_num']?></td>
            <td><?=$list['probability']?></td>
            <td><?=$list['used_num']?></td>

        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/packagegenerate']) ?>" onsubmit="return check_sub();" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">生成数量</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="100000" required id="generate_num" name="generate_num" class="form-control" placeholder="请输入要生成的数量">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">过期时间</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" readonly="readonly" id="use_limit_time" name="use_limit_time"  onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="过期时间为必填参数">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">所属省份</label>
            <div class="col-sm-3">
                <select id="province" name="province"  placeholder="所属省份"  class="form-control">
                    <?php foreach ($provinces as $key => $val):?>
                        <option value="<?=$val['code']?>"><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">公司名称</label>
            <div class="col-sm-3">
                <select  name="company" id="companyid"  placeholder="公司名称"  class="form-control">
                    <?php foreach ($companys as $key => $val):?>
                        <option value="<?=$val['id']?>"><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">是否免兑换</label>
            <div class="col-sm-3">
                <select id="is_redeem"  name="is_redeem"  placeholder=""  class="form-control">
                    <option value="0">否</option>
                    <option value="1">是</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">套餐券估值</label>
            <div class="col-sm-3">
                <input type="text"  required id="gu_amount" name="gu_amount" class="form-control" placeholder="套餐券估值必须是4位如0100">
            </div>
        </div>
        <hr style="border:1px dotted #036; margin-bottom: 20px" />
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
        <div class="form-group check_xxz">
            <label for="inputEmail3" class="col-sm-2 control-label" id="amount_html">优惠公里数</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="9999999999"   name="amount" class="form-control" placeholder="优惠券可使用次数">
            </div>
        </div>
        <div class="form-group oil_type">
            <label for="inputPassword3" class="col-sm-2 control-label" >油券面额</label>
            <div class="col-sm-3">
                <select id="oil_type" name="oil_type"  placeholder="类型"  class="form-control">
                    <?php foreach ($oil_type as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">优惠券数量</label>
            <div class="col-sm-3">
                <input type="number" min="-1" max="36500"   name="num" class="form-control" placeholder="优惠券数量">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">对应优惠券批号</label>
            <div class="col-sm-3">
                <input type="text"    name="batch_no" class="form-control" placeholder="对应优惠券批号">
            </div>
        </div>
        <div class="form-group uprescue">
            <label for="inputPassword3" class="col-sm-2 control-label" >救援类型（券为道路救援时可用）</label>
            <div class="col-sm-3">
                <select id="scene" name="scene"  placeholder="类型"  class="form-control">
                    <?php foreach ($coupon_faulttype as $key => $val):?>
                        <option value="<?=$key?>"><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>



        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="button" class="btn btn-info" onclick="Leadin()">批量生成</button>
                <button type="button" class="btn btn-info" id="ajax-post" data-op="add" data-rescue="" data-id="0">添加优惠券</button>

            </div>
        </div>

        <div class="form-group coupon_all" >

        </div>
        <input type="hidden" id="fornum" name="fornum" value="">';
    </form>
</div>
<div class="yinying" style="
position: fixed;width: 100%; height: 100%; left: 0;top: 0;background: #333;opacity: 0.6;z-index: 1000; display:none;"><span ></span>
</div>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script>
    $(function(){

        $("#ajax-post").click(function(){
            var op = $(this).attr('data-op');
            var id = $(this).attr('data-id');
            var rescue = $(this).attr('data-rescue');
            var data = {};
            if(op == 'edit'){
                data['id'] = id;
            }
            //alert($('.coupon_all input').length);return;
            var html='',
                coupon_type = $("#coupon_type").val(),
                coupon_type_text =$("#coupon_type").find("option:selected").text(),
                amount = $("input[name=amount]").val(),
                num = $("input[name=num]").val(),
                generate_num = $("input[name=generate_num]").val(),
                batch_no = $("input[name=batch_no]").val(),
                companyid = $("#companyid").val(),
                scene = $("#scene").val(),
                scene_text =$("#scene").find("option:selected").text();

            if(coupon_type === '5'){
                amount = $("#oil_type").val();
            }
            if(!amount || isNaN(amount) ){
                alert('请输入正确的面额或使用次数');
                return false;
            }

            if(coupon_type === '2' && Number(amount) > 366){
                alert('道路救援券使用次数不能大于366');
                return false;
            }

            if(coupon_type === '4' && Number(amount) > 12){
                alert('洗车券使用次数不能大于12');
                return false;
            }

            if(!num || isNaN(num) ){
                alert('请输入正确的优惠券数量');
                return false;
            }

            if(batch_no=='' ||  batch_no==null){
                alert('请填写优惠券批号');
                return false;
            }

            if(!generate_num || isNaN(generate_num) || generate_num < 0){
                alert('请输入券包生成数量');
                return false;
            }

            $.post("<?php echo Url::to(['checkbatchno'])?>", {
                batch_no: batch_no,
                company:companyid,
                coupon_type:coupon_type,
                coupon_num:num,
                generate_num:generate_num,
                scene:scene,
                amount:amount
            }, function (json) {
                if (json.status === 1) {

                    var scene_text_0='--'
                    if(coupon_type === '2')  scene_text_0 = scene_text;
                    if(op == 'edit'){
                        html+='<td class="info_coupon_type">'+coupon_type+'</td>';
                        html+='<td class="info_coupon_type_text">'+coupon_type_text+'</td>';
                        html+='<td class="info_amount">'+amount+'</td>';
                        html+='<td class="info_num">'+num+'</td>';
                        html+='<td class="info_scene" data-scene="'+scene+'">'+scene_text_0+'</td>';
                        html+='<td class="info_batch_no">'+batch_no+'</td>';

                    }else{
                        if(coupon_type=='2'){
                            html+='<tr id="rescue_type_'+scene+'">';
                        }else{
                            html+='<tr id="coupon_type_'+coupon_type+'">';
                        }
                        html+='<td class="info_coupon_type">'+coupon_type+'</td>';
                        html+='<td class="info_coupon_type_text">'+coupon_type_text+'</td>';
                        html+='<td class="info_amount">'+amount+'</td>';
                        html+='<td class="info_num">'+num+'</td>';
                        html+='<td class="info_scene" data-scene="'+scene+'">'+scene_text_0+'</td>';
                        html+='<td class="info_batch_no">'+batch_no+'</td>';
                        html+='</tr>';
                        $('#coupon_table').append(html);
                    }
                    var str=amount+','+coupon_type+','+num+','+batch_no;

                    if(coupon_type === '2') {
                        str = amount+','+coupon_type+','+num+','+batch_no+','+scene;
                    }

                    var i=$('.coupon_all input').length,html_xxz='',fornum=0;
                    html_xxz='<input type="hidden" class="coupon_info" name="coupon_info" value="'+str+'">';
                    $('.coupon_all').append(html_xxz);
                    $("#fornum").val($('.coupon_all input').length);

                    $("select[name=coupon_type]").val('1');
                    $("input[name=amount]").val('');
                    $("input[name=num]").val('');
                    $("input[name=batch_no]").val('');
                    $("select[name=scene]").val('0');
                    $('#ajax-post').attr("data-id",'');
                    $('#ajax-post').attr("data-op",'add');
                    $('#ajax-post').attr("data-rescue",'');
                    $('#ajax-post').text("添加优惠券");
                    changescene('#coupon_type');

                } else {
                    alert(json.msg);
                    return false;
                }
            }, 'json');
        });
    });


    function Leadin(){

        var generate_num = $("#generate_num").val(),
            use_limit_time = $("#use_limit_time").val(),
            province = $("#province").val(),
            companyid = $("#companyid").val(),
            is_redeem = $("#is_redeem").val(),
            gu_amount = $("#gu_amount").val(),
            couponinfo =  new Array;

            $(".coupon_info").each(function(){
                couponinfo.push($(this).val());
            });
            if(use_limit_time==null || use_limit_time=='' ){
                alert('过期时间为必填参数');
                return false;
            }

            if(couponinfo==null || couponinfo=='' ){
                alert('你没有填写任何优惠券信息');
                return false;
            }
            if(generate_num == '' ){
                alert('生成数量不能为空');
                return false;
            }
            if(generate_num>100000){
                alert('生成数量不能超过10万');
                return false;
            }
        $(".yinying").show();
        $.post('<?php echo Url::to(["coupon/packagegenerate"]); ?>',{
            generate_num:generate_num,
            use_limit_time:use_limit_time,
            province:province,
            company:companyid,
            is_redeem:is_redeem,
            gu_amount:gu_amount,
            couponinfo:couponinfo

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
        $.post('<?php echo Url::to(["coupon/packageleadingtwo"]); ?>',{
            xxzkey:data.xxzkey,
            num:num,
            xxzmaxnum:data.xxzmaxnum,
            maxnum:data.maxnum,
            companyid:data.companyid,
            batch_no:data.batch_no,
            use_limit_time:data.use_limit_time
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
    $('.oil_type').hide();
    function changescene(_this) {
        var xxz_type=$(_this).val();
        var str='';
        $('.check_xxz').show();
        if(xxz_type == '2'  ){
            $('#amount_html').html('可使用次数');
            $('.uprescue').show();
            $('.oil_type').hide();
        }else if(xxz_type == '1') {
            $('#amount_html').html('优惠公里数');
            $('.uprescue').hide();
            $('.oil_type').hide();
        }else if(xxz_type == '4' || xxz_type == '6' || xxz_type == '7'|| xxz_type == '8') {
            str='面额';
            $('#amount_html').html(str);
            $('.uprescue').hide();
            $('.oil_type').hide();
        }else if(xxz_type == '5') {
            $('.oil_type').show();
            $('.uprescue').hide();
            $('.check_xxz').hide();

        }
    }

</script>
