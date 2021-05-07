<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
    <div class="page-header am-fl am-cf">
        <h4>
            支付信息编辑 <small>&nbsp;&nbsp;/表单信息</small>
        </h4>
    </div>
    <div class="container-fluid" style="padding-top: 15px;height:800px;">
        <form class="form-horizontal"   method="post" action="<?php echo Url::to(['payment/edit']) ?>" >
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">支付方式</label>
                <div class="col-sm-3">
                     <select name="pay_type" class="form-control">
                     <option value="1" <?php if($data['pay_type']==1) echo 'selected';  ?>>微支付</option>
                     <option value="2" <?php if($data['pay_type']==2) echo 'selected'; ?>>支付宝</option>
                     </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">支付账号</label>
                <div class="col-sm-3">
                    <input type="text" name="account" class="form-control" value="<?php echo $data['account'];?>" id="inputEmail3" placeholder="名称">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">payment_key</label>
                <div class="col-sm-3">
                    <input type="text"  name="payment_key" id="inputError1" for="inputError1"  class="form-control"   value="<?php echo $data['payment_key'];?>"     placeholder="payment_key">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">pass_key</label>
                <div class="col-sm-3">
                    <input type="text"  name="pass_key" id="inputError1" for="inputError1"  class="form-control"   value="<?php echo $data['pass_key'];?>"     placeholder="pass_key">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
                </div>
            </div>
            <?php  if($_REQUEST['id']) {?>
                <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>"><?php }?>
        </form>
    </div>
    <script>
        var paytype= $('select[name="pay_type"]').val();
        block(paytype);
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

        $('select[name="pay_type"]').change(function(){
            var ptype=$(this).val();
            block(ptype);
        });

        function  block(type){
            if(type==1){
                $('.control-label').eq(2).html('PartnerKey');
                $('.control-label').eq(3).html('PaySignKey');
                $('input[name="payment_key"]').attr('placeholder','请输入PartnerKey');
                $('input[name="pass_key"]').attr('placeholder','请输入PaySignKey');

            }
            else{
                $('.control-label').eq(2).html('渠道商代码');
                $('.control-label').eq(3).html('渠道商密钥');
                $('input[name="payment_key"]').attr('placeholder','请输入渠道商代码');
                $('input[name="pass_key"]').attr('placeholder','请输入渠道商密钥');
            }

        }

    </script>
<?php

?>