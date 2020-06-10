<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7 0007
 * Time: 下午 3:28
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd" style="height: 100%;overflow: hidden">
    <div class="TranslateDiv webkitbox">
        <div class="zhengwenDiv">
            <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot">
                <div class="renzhengTopTitle afterFour">
                    提现
                </div>
            </div>
            <div class="tixianTellYou boxSizing">
                <p>1、您可提现金额为：<i><img src="/frontend/web/images/income.png" ><?php echo $shopinfo['amount'];?></i></p>
                <p>2、提现周期为48小时。</p>
            </div>
            <div class="shopPageCont NoPaddLR NoPaddTop NoPaddBot NoMarginTop">
                <ul class="renzhengMessUl boxSizing">
                    <li class="webkitbox afterFour">
                        <label>提现金额</label>
                        <input type="number" id="xxz_amount" placeholder="请输入提现金额">
                    </li>
                    <li class="webkitbox afterFour">
                        <label>提现密码</label>
                        <input type="password" id="xxz_password" placeholder="请输入提现密码">
                    </li>
                </ul>
            </div>
            <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot">
                <div class="renzhengTopTitle afterFour changeSKMess webkitbox">
                    <span>收款信息</span>
                    <a href="#" id="modify">修改</a>
                </div>
            </div>
            <div class="shopPageCont NoPaddLR NoPaddTop NoPaddBot">
                <ul class="renzhengMessUl boxSizing labelSameWd">
                    <li class="webkitbox afterFour">
                        <label>收款人</label>
                        <em id="xxz_name"><?php echo $payeeinfo['payee_name'];?></em>
                    </li>
                    <li class="webkitbox afterFour">
                        <label>收款帐号</label>
                        <em id="xxz_account"><?php echo $payeeinfo['payee_account'];?></em>
                    </li>
                    <li class="webkitbox afterFour">
                        <label>开户行</label>
                        <em id="xxz_bank"><?php echo $payeeinfo['payee_bank'];?></em>
                    </li>
                </ul>
            </div>
            <div class="duihuanBot boxSizing">
                <a href="#" onclick="withdrawal();" id="withdrawal">提现</a>
            </div>
            <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot">
                <div class="renzhengTopTitle afterFour">
                    提现记录
                </div>
            </div>
            <div class="shopPageCont NoPaddLR NoPaddTop NoPaddBot">
                <ul class="tixianJLCont tixianMore" id="ongoing">
                    <?php foreach ($log as $val){?>
                        <li class="boxSizing afterFour">
                            <div class="left">
                                <p class="p1">提现订单（订单号：<?php echo $val['withdrawals_no']?>）</p>
                                <p class="p2"><?php echo date("Y-m-d H:i:s",$val['apply_time']);?></p>
                            </div>
                            <div class="right">
                                +<?php echo $val['amount']?>
                                <?php if($val['status']==2){?>
                                    <span>提现成功</span>
                                <?php }?>
                            </div>
                        </li>
                    <?php }?>
                </ul>
            </div>
        </div>
        <div class="tanchuDiv">
            <div class="close"></div>
            <div class="modifyTelTitle boxSizing">
                修改收款账户
            </div>
            <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot NoPaddTop">
                <ul class="renzhengMessUl boxSizing">
                    <li class="webkitbox afterFour">
                        <label>账户</label>
                        <input type="text" id="payee_name" placeholder="请输入账户所有人姓名" value="<?php echo $payeeinfo['payee_name'];?>">
                    </li>
                    <li class="webkitbox afterFour">
                        <label>账号</label>
                        <input type="text" id="payee_account" placeholder="请输入收款账户卡号" value="<?php echo $payeeinfo['payee_account'];?>">
                    </li>
                    <li class="webkitbox afterFour">
                        <label>开户行</label>
                        <input type="text" id="payee_bank" placeholder="请填银行名称或‘支付宝’" value="<?php echo $payeeinfo['payee_bank'];?>">
                    </li>
                    <li class="webkitbox afterFour">
                        <label>账户类型</label>
                        <div class= "webkitbox">
                            <input style="width: 20px" id="r3" type="radio" value="2" <?php if($payeeinfo['type']=='2')echo 'checked'?>  name="payee_account_type"/>
                            <span style="margin-left: 10px">银行</span>
                            <input style="width: 20px; margin-left: 30px" id="r4" type="radio" value="1" <?php if($payeeinfo['type']=='1')echo 'checked'?>  name="payee_account_type"/>
                            <span style="margin-left: 10px">支付宝</span>
                        </div>
                    </li>

                    <li class="webkitbox afterFour">
                        <label>手机号</label>
                        <input type="tel" name="mobile" placeholder="请输入本机号" >
                    </li>
                    <li class="webkitbox afterFour">
                        <label>短信验证码</label>
                        <input type="text" id="yzcode" placeholder="请输入短信验证码">
                        <span class="yzbotton">获取验证码</span>
                    </li>
                </ul>
            </div>
            <div class="duihuanBot boxSizing">
                <span id="baocun">保存</span>
            </div>
        </div>
    </div>
</section>

<?php $this->beginBlock('script');?>
    <script type="text/javascript">
        $(function () {
            var scrollTop = 0;
            $('#modify').on('click', function () {
                var  shopstatus='<?php echo $shopinfo['shop_status'];?>';
                if(shopstatus != 2){
                    alert('您的商户还没通过审核不可添加账号');
                    return false;
                }
                show();
            });
            $('.close').on('click', function () {
                hide();
            });
//            $('#baocun').on('click', function () {
//                hide();
//            });
            function show(e){
                scrollTop = (document.body || document.documentElement).scrollTop;
                if(e){
                    e.preventDefault();
                }
                $('.TranslateDiv').addClass('keep').css({
                    '-webkit-transform' : 'translate3d(-' + window.innerWidth + 'px,0,0)',
                })
            }
            function hide(){
                $('.TranslateDiv').css({
                    '-webkit-transform' : 'translate3d(0px,0,0)',
                })

            }

          /////////////////////////////

            var testMobile = function(m){
                var reg = /^1[0-9]{10}$/;
                return reg.test(m);
            }
            var getYzcode = function(){
                var mobile = $("input[name=mobile]").val();
                if(!testMobile(mobile)){
                    alert('请输入正确的手机号码！');
                    return false;
                }
                $.post('<?php echo Url::to(["car/smscode"])?>',{mobile:mobile},function(json){
                    if(json.status != 1){
                        alert('验证码获取失败，请重新获取');
                    }
                });
                return true;
            }
            $('.yzbotton').click(function () {
                if($(this).data('yzm') == undefined){
                    if(!getYzcode()){
                        return false;
                    }
                    var _this = this;
                    var num   = 59;
                    $(this).css('background','#bfbfbf').html('<i>60</i>' + 's后重新获取').data('yzm',true);
                    var setTimer = setInterval(function () {
                        $(_this).find('i').text(num--);
                        if(num < 0){
                            num = 59;
                            $(_this).removeAttr('style').removeData('yzm').html('获取验证码');
                            clearInterval(setTimer);
                        }
                    },1000);
                }else{
                    return false;
                }
            });

            var is_sub=false;
            $('#baocun').on('click', function () {
                if(is_sub){
                    alert('数据提交中请稍后');
                    return false;
                }
                var opt1 = $("#payee_name").val();
                var opt2 = $("#payee_account").val();
                var opt3 = $("#payee_bank").val();
                var opt4 = $("#yzcode").val();
                var opt5 = $("input[name=mobile]").val();
                var opt6 = '<?php echo $shopinfo['id'];?>';
                var opt7= $("input[name=payee_account_type]:checked").val();
                if(!testMobile(opt5)){
                    alert('请输入正确的手机号码！');
                    return false;
                }
                if(opt4=='' || opt4.length != 6){
                    alert('请输入正确的验证码');
                    return false;
                }
                if(opt1==''){
                    alert('账户不能为空');
                    return false;
                }
                if(opt1.length>20){
                    alert('账户不能太长');
                    return false;
                }
                if(opt2==''){
                    alert('账号不能为空');
                    return false;
                }
                if(opt2.length > 25){
                    alert('账号不能太长');
                    return false;
                }
                if(opt3==''){
                    alert('开户不能为空');
                    return false;
                }
                if(opt3.length>50){
                    alert('开户行不能太长');
                    return false;
                }

                is_sub=true;
                $.post('<?php echo Url::to(["car/cardup"]);?>',{
                    payee_name:opt1,
                    payee_account:opt2,
                    payee_bank:opt3,
                    yzcode:opt4,
                    mobile:opt5,
                    shop_id:opt6,
                    payee_account_type:opt7
                },function(json){
                    is_sub=false;
                    if(json.status == 1){
                        var arr=json.data;
                        $('#xxz_name').html(arr['payee_name']);
                        $('#xxz_account').html(arr['payee_account']);
                        $('#xxz_bank').html(arr['payee_bank']);
                        hide();
                    }else{
                        alert(json.msg);
                    }
                });
            });

        });

        /////////////////////////////////////////////////////////////////////////

        function  withdrawal() {
            var amount=$('#xxz_amount').val();
            var password=$('#xxz_password').val();
            if(amount == ''){
                alert('请填写提现金额');
                return false;
            }

            if(amount > 99999999 || amount==0 ){
                alert('请填写正确的金额');
                return false;
            }
            if(password == ''){
                alert('请填写提现密码');
                return false;
            }

            var url = "<?php echo Url::to(['car/apply_withdrawal']);?>";

            $('#withdrawal').removeAttr('onclick');
            $.post(url,{amount:amount,password:password},function(json){

                if(json.status == 1){
                    alert(json.msg);
                    window.location.href= "<?php echo Url::to(['car/shop_core']);?>";
                }else{
                    alert(json.msg);
                }
                $('#withdrawal').attr('onclick','withdrawal();');
            });
        }

/////////////////////////////////////////////////////////////////////////////////////////
        var pagenum=2;
        $(document).ready(function () { //本人习惯这样写了TranslateDiv
            $('.zhengwenDiv').scroll(function () {
                //$(window).scrollTop()这个方法是当前滚动条滚动的距离
                //$(window).height()获取当前窗体的高度
                //$(document).height()获取当前文档的高度
               // var bot = $('.footMenu').height(); //bot是底部距离的高度
                if (($('.zhengwenDiv').scrollTop()) >= ($('.zhengwenDiv').get(0).scrollHeight - $('.zhengwenDiv').height())) {
                    //当底部基本距离+滚动的高度〉=文档的高度-窗体的高度时；
                    //我们需要去异步加载数据了
                    pagelist();
                }
            });
        });

        function pagelist(){
            var html='';
            $.post('ajaxpage.html',{pagenum:pagenum,type:1},function(s){
                if(s.msg==0){
                    alert('没有提现记录');return false;
                }else {
                    $.each(s.data,function(key,val){
                        html+='<li class="boxSizing afterFour">';
                        html+='<div class="left">';
                        html+='<p class="p1">提现订单（订单号：'+val['withdrawals_no']+'）</p>';
                        html+='<p class="p2">'+val['apply_time']+'</p>';
                        html+='</div>';
                        html+='<div class="right">';
                        html+='+'+val['amount'];
                        if(val['status']==2){
                            html+='<span>提现成功</span>';
                        }
                        html+='</div>';
                        html+='</li>';
                    });
                    $("#ongoing").append(html);
                    pagenum++;
                }

            });
        };

    </script>


<?php $this->endBlock('script');?>
