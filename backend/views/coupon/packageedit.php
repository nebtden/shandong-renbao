<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 上午 11:38
 */
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        券包编辑 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/packageedit']) ?>" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">卡包号码</label>
            <div class="col-sm-3">
                <input type="text" required name="package_sn" value="<?= $data['package_sn'] ?>"  class="form-control" placeholder="卡包号码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">卡包兑换码</label>
            <div class="col-sm-3">
                <input type="text"  name="package_pwd" value="<?= $data['package_pwd'] ?>" class="form-control" placeholder="卡包兑换码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">过期时间</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" name="use_limit_time" value="<?php echo  !empty($data['use_limit_time'])?date("Y-m-d H:i:s",$data['use_limit_time']):''; ?>" id="use_limit_time"  onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="不填为永久有效">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">卡包批号</label>
            <div class="col-sm-3">
                <input type="text"  name="batch_nb" value="<?= $data['batch_nb'] ?>" class="form-control" placeholder="卡包批号">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-3">
                <select  name="status"  placeholder="状态"  class="form-control">
                    <?php foreach ($pckage_status as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['status']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <span id="coupon_0">

            <?php foreach ($info as $k => $v):?>
                <?php if($k!=0){?>
                    <span>
                <?php }?>
                <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">优惠券的类型</label>
                        <div class="col-sm-3">
                            <select  name="coupon_type<?=$k?>"  placeholder="优惠券的类型"  class="form-control" onchange="uprescue(this)">
                                <?php foreach ($coupon_type as $key => $val):?>
                                    <option value="<?=$key?>" <?php if($v['type']==$key) echo 'selected'; ?>><?=$val?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">优惠券面额</label>
                        <div class="col-sm-3">
                            <input type="number" min="1" max="9999999999"  required name="amount<?=$k?>" value="<?=$v['amount']?>" class="form-control" placeholder="优惠券面额">
                        </div>
                    </div>
                <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">优惠券数量</label>
                        <div class="col-sm-3">
                            <input type="number" min="0" max="36500"  required name="num<?=$k?>" value="<?=$v['num']?>" class="form-control" placeholder="使用限制,0为无限制">
                        </div>
                    </div>

                    <input type="hidden"   value="<?=$v['batch_no']?>"  name="batch_no<?=$k?>" class="form-control" placeholder="批号" >


                <div class="form-group" class="uprescue">
                            <label for="inputPassword3" class="col-sm-2 control-label">券为道路救援时指定类型</label>
                            <div class="col-sm-3">
                                <select  name="scene<?=$k?>"  placeholder="类型"  class="form-control">
                                    <?php foreach ($coupon_faulttype as $key => $val):?>
                                        <option value="<?=$key?>" <?php if($v['scene']==$key) echo 'selected'; ?>><?=$val?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                <?php if($k==0){?>
                    </span><span id="coupon_1">
                <?php }else{?>
                    </span>
                <?php }?>
            <?php endforeach;?>
       </span>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
                <button type="button" class="btn btn-default" onclick="add()">添加优惠券</button>
                <button type="button" class="btn btn-default" onclick="del()">删除优惠券</button>
            </div>
        </div>
        <input type="hidden" id="arrnum" name="arrnum" value="<?=count($info) ?>">
        <input type="hidden" id="id" name="id" value="<?=$data['id'] ?>">
    </form>
</div>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    var num = 0;
    function add() {
        num++;
        var aa = $("#coupon_0").clone(true);
        aa.removeAttr( 'id' );
        var children = aa.children();
        children.each( function () {
            $( this ).find( '.col-sm-3' ).find( '.form-control' ).attr( 'name' ,function ( i , v ) {
                return v + num;
            });
            $( this ).find( 'input' ).val( '' )
        } )
        $("#arrnum").val( Number(num +1));
        $("#coupon_1").append( aa );
    }

    function del() {
        var coupon_1 = $( '#coupon_1' );
        var children = coupon_1.children( 'span' );
        children.last().remove();
    }
    $('form').submit(function(){
        if (! confirm("确定要修改此券包码？")) return false;
    });


</script>
