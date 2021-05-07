<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 上午 11:38
 */
use yii\helpers\Url;
?>

<style>

</style>
<div class="page-header am-fl am-cf">
    <h4>
        套餐添加或修改 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/mealedit']) ?>" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">套餐名称</label>
            <div class="col-sm-3">
                <input type="text" required name="mealname" class="form-control" placeholder="请输入套餐名称" value="<?php echo $data['name']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">套餐券图片</label>
            <div class="col-sm-3">
                <div id="hadpic" ></div>
                <input type="hidden" name="meal_pic" id="meal_pic" value="<?php echo $data['meal_pic']?>"/>
                <?php
                if($data['meal_pic']){
                    echo '<img width="390" src="'.$data['meal_pic'].'" />';
                }
                ?>
                <p class="help-block"> </p>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-3">
                <input type="text" required name="remarks" class="form-control" placeholder="请输入备注" value="<?php echo $data['remarks']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">说明内容</label>
            <div class="col-sm-3">
                <textarea id="desc" name="desc" style=" width: 393px;height: 209px;" placeholder="说明内容" ><?php echo $data['desc']?$data['desc']:'';?></textarea>
            </div>
        </div>

        <?php if(empty($data['id'])){?>
            <span id="coupon_0">
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">优惠券的类型</label>
                    <div class="col-sm-3">
                        <select  name="coupon_type"  placeholder="优惠券的类型"  class="form-control">
                            <?php foreach ($coupon_type as $key => $val):?>
                                <option value="<?=$key?>"><?=$val?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">优惠券面额（可使用次数，公里数，油券面额等）</label>
                    <div class="col-sm-3">
                        <input type="number" min="-1" max="9999999999"  required name="amount" class="form-control" placeholder="-1不限次数">
                    </div>
                </div>
                  <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">优惠券数量</label>
                            <div class="col-sm-3">
                                <input type="number" min="0" max="36500"  required name="num" value="" class="form-control" placeholder="优惠券数量">
                            </div>
                     </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">优惠券批号</label>
                    <div class="col-sm-3">
                        <input type="text"  name="couponbatchno" class="form-control" placeholder="优惠券批号" value="">
                    </div>
                </div>

                 <div class="form-group" class="uprescue">
                    <label for="inputPassword3" class="col-sm-2 control-label">救援类型（券为道路救援时可用）</label>
                    <div class="col-sm-3">
                        <select  name="scene"  placeholder="类型"  class="form-control">
                            <?php foreach ($coupon_faulttype as $key => $val):?>
                                <option value="<?=$key?>"><?=$val?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
            </span>
            <span id="coupon_1">
            </span>
        <?php }else{?>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">是否禁用</label>
                <div class="col-sm-3">
                    <select  name="status"  placeholder="是否禁用"  class="form-control">
                        <?php foreach ($status as $key => $val):?>
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
                                <input type="number" min="0" max="36500"  required name="num<?=$k?>" value="<?=$v['num']?>" class="form-control" placeholder="优惠券数量">
                            </div>
                     </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">优惠券批号</label>
                            <div class="col-sm-3">
                                <input type="text"  name="couponbatchno<?=$k?>" class="form-control" placeholder="优惠券批号" value="<?=$v['batch_no']?>">
                            </div>
                        </div>
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
        <?php }?>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
                <button type="button" class="btn btn-default" onclick="add()">添加优惠券</button>

            </div>
        </div>
        <input type="hidden" id="arrnum" name="arrnum" value="1">
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

    $(function(){
        uploadImg('hadpic','meal_pic');
    });
    var id='<?php echo $data['id']?>';
    if(id != '' && id != false && id != null){
        $('form').submit(function(){
            var msg = "修改套餐可能改变优惠券与套餐的关联，请慎重操作！你是否确定修改";
            if (confirm(msg)==true){
                return true;
            }else{
                return false;
            }
        });
    }

</script>
