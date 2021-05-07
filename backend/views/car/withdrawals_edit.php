<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14 0014
 * Time: 下午 4:06
 */
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>提现记录 <small>&nbsp;/&nbsp;提现记录详情</small></h4>
</div>
<table class="table table-bordered" style="margin-top:10px;">
    <thead>
    <tr>
        <th colspan="2" align="center" style="text-align: center;">提现记录详情</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td width="50%">提现记录编号：<?=$info['id']?></td>
        <td width="50%">提现人微信昵称：<?=$info['nickname']?></td>
    </tr>
    <tr>
        <td width="50%">收款人姓名：<?=$info['payee']?></td>
        <td width="50%">提现单号：<?=$info['withdrawals_no']?></td>
    </tr>
    <tr>
        <td width="50%">提现金额：<?=$info['amount']?></td>
        <td width="50%">提现账号：<?=$info['account']?></td>
    </tr>
    <tr>
        <td width="50%">提现账号开户行：<?=$info['account_bank']?></td>
        <td width="50%">提现申请时间：<?=$info['apply_time']?></td>
    </tr>
    <tr>
        <td width="50%">确认打款时间：<?=$info['playmoney_time']?></td>
        <td width="50%">
            提现状态：<?=$info['status']?>
            <?php if($info['status']=='已申请'){?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button" onclick="play_money(this)" id="refund" class="btn btn-info" data-id="<?=$info['id']?>"> 确认打款</button>
            <?php }?>
        </td>
    </tr>
    <tr>
        <td width="50%">收款商户名称：<?=$info['shop_name']?></td>
        <td width="50%"></td>
    </tr>
    </tbody>
</table>
<script>
   //确认打款操作
    function play_money(_this){
        if(!confirm('您确定已经打款了？')) return false;
        var id = $(_this).data('id');
        var url = '<?php echo Url::to(['car/play_money']);?>';
        $(_this).removeAttr('onclick');

        $.post(url,{id:id},function(json){

            if(json.status == 1){
                window.location.reload();
            }else{
                alert(json.msg);
            }
            $(_this).attr('onclick','play_money(this);');
        });
    };

</script>