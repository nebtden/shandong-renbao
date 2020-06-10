<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7 0007
 * Time: 下午 4:24
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd">
    <div class="shopPageCont NoMarginTop NoPaddLR NoPaddBot">
        <div class="renzhengTopTitle afterFour">
            账号管理
        </div>
    </div>
    <div class="shopPageCont NoPaddLR NoPaddBot">
        <div class="renzhengTopTitle nobg afterFour changeSKMess webkitbox">
            <span>主账号</span>
            <?php if(!empty($shopinfo)){?>
                <a href="#" id="modify" style="width: 100px">添加子帐号</a>
            <?php }?>
        </div>
        <ul class="zhanghaoList">
            <li class="boxSizing webkitbox">
                <span>微信昵称</span>
                <em><?php  echo $user['nickname']?></em>
            </li>
            <li class="boxSizing webkitbox">
                <span>手机</span>
                <em><?php echo $shopinfo['mobile']?></em>
            </li>

        </ul>
    </div>
    <?php foreach ($ch_fans as $k=>$v){?>
        <div class="shopPageCont CountZZHNum NoPaddLR NoPaddBot">
            <div class="renzhengTopTitle nobg afterFour changeSKMess webkitbox">
                <span>授权子账号</span>
                <a href="#" data-uid="<?php echo $v['id']?>" data-realname="<?php echo $v['realname']?>" data-telphone="<?php echo $v['telphone']?>" class="list<?php echo $k;?>">编辑</a>
                <a href="#" data-uid="<?php echo $v['id']?>"  class="delData<?php echo $k;?>" style="background: #b2b2b2;margin-left: 10px">删除</a>
            </div>
            <ul class="zhanghaoList">
                <li class="boxSizing webkitbox">
                    <span>微信昵称</span>
                    <em ><?php echo $v['nickname']?></em>
                </li>
                <li class="boxSizing webkitbox">
                    <span>真实姓名</span>
                    <em><?php echo $v['realname']?></em>
                </li>
                <li class="boxSizing webkitbox">
                    <span>手机</span>
                    <em><?php echo $v['telphone']?></em>
                </li>

            </ul>
        </div>
    <?php }?>
</section>

<!-- 弹出层 -->
<div class="erweiCounts">
    <div class="close AccountsClose" id="erweiClose"></div>
    <div class="addErWeiDiv boxSizing">
        <img src="<?php echo $code;?>"/>
    </div>
</div>
<div class="AddSubAccounts">
    <div class="close AccountsClose" id="modifyClose"></div>
    <ul class="AccountsUl boxSizing">
        <li class="webkitbox">
            <label>姓名</label>
            <div class="afterFour">
                <input type="text" data-uid="" id="ch_realname"  placeholder="请输入子账号真实姓名">
            </div>
        </li>
        <li class="webkitbox">
            <label>手机</label>
            <div class="afterFour">
                <input type="tel" id="ch_telphone" placeholder="请输入子账号手机号码">
            </div>
        </li>
    </ul>
    <div class="AccountsBot" >保存</div>
</div>
<div class="DelSubAccounts">
    <div class="topMess boxSizing">
        是否确定删除子账号
        <p class="username">摇一树桃花</p>
    </div>
    <div class="botAnniu boxSizing webkitbox afterFour">
        <span id="cancle">取消</span>
        <em id="sure"  data-uid="" onclick="del_chaccount(this)">确定</em>
    </div>
</div>
<div class="zzLevel"></div>
<?php $this->beginBlock('script');?>
<script type="text/javascript">
    $(function () {
        var scrollTop = 0, winHeight = window.innerHeight;
        function show(obj,text,uid){
            scrollTop = $(window).scrollTop();
            $('section').css({
                height : winHeight + 'px',
                'overflow' : 'hidden'
            });
            $('.zzLevel').css({
                height : winHeight + 'px',
            }).show();
            $(obj).css({
                top : (winHeight - $(obj).innerHeight()) / 2,
                display : 'block'
            });
            if(text){
                $('.DelSubAccounts').find('.username').text(text);
                $('#sure').attr('data-uid',uid);
            }
        }
        function hide(obj){
            $('.zzLevel').hide();
            $(obj).css({
                display : 'none'
            }).attr('data','');
            $('section').removeAttr('style')
        }
        function del(){
            var deldataFlag = $('.DelSubAccounts').attr('data');
            $('.' + deldataFlag).parents('.shopPageCont').remove();
            hide('.DelSubAccounts')
        }
        function bianji(){
            var dataFlag = $('.AddSubAccounts').attr('data');
            var input    = $('.AccountsUl').find('input');
            var flag     = true;
            input.each(function () {
                if(/^\s*$/.test($(this).val())){
                    $().tanchu('请填写完整');
                    flag = false;
                    return false;
                }
            });
            if(flag){
                var input = $('.AddSubAccounts').find('input');
                var em = $('.' + dataFlag).parents('.renzhengTopTitle').next().find('em').not(':first');
                var realname=$('#ch_realname').val();
                var telphone=$('#ch_telphone').val();
                if(realname.length==0 || realname.length>20){
                    alert('请输入合法的真实姓名');
                    return false;
                }
                if(!/^1[0-9]\d{9}$/.test(telphone)){
                    alert('请输入正确的联系人手机号码');
                    return false;
                }
               $('.' + dataFlag).attr('data-realname',realname) ;
               $('.' + dataFlag).attr('data-telphone',telphone);

                 update_chaccount();
                em.each(function (i) {
                        $(this).text(input.eq(i).val())
                });
                hide('.AddSubAccounts');
            }
        }
        //显示编辑框
        $('#modify').on('click', function () {
            var status='<?php echo $shopinfo['shop_status'];?>';
            if(status != '2'){
                alert('您的商铺还未通过审核不可添加子账号');
                return false;
            }
            show('.erweiCounts');
        });
        $('#erweiClose').on('click', function () {
            hide('.erweiCounts');
        });
        //关闭编辑框
        $('#modifyClose').on('click', function () {
            hide('.AddSubAccounts');
        });
        //取消确认框
        $('#cancle').on('click', function () {
            hide('.DelSubAccounts');
        })
        //关闭修改编辑
        $('.AccountsBot').on('click', function () {
            bianji();
        })
        //关闭确认框
//        $('#sure').on('click', function () {
//            del();
//        })
        //显示修改框
        $('.contentFull').on('click','[class^="list"]', function () {

            $('.AddSubAccounts').attr('data',$(this).attr('class'));
            $('.AddSubAccounts #ch_realname').attr('data-uid',$(this).attr('data-uid'));
            $('#ch_realname').val($(this).attr('data-realname'));
            $('#ch_telphone').val($(this).attr('data-telphone'));
            show('.AddSubAccounts');
        })
        //显示确认框
        $('.contentFull').on('click','[class^="delData"]', function () {
            $('.DelSubAccounts').attr('data',$(this).attr('class'));
            show('.DelSubAccounts',$(this).parents('.renzhengTopTitle').next().find('li').first().children('em').text(),$(this).attr('data-uid'))

        })
    })
 //////////////////////////////////////////////////////
    var is_sub=false;
    function  update_chaccount() {
        if(is_sub){
            alert('数据提交中请稍后');
            return false;
        }
        var opt1 = $("#ch_realname").val();
        var opt2 = $("#ch_telphone").val();
        var opt3 = $("#ch_realname").attr('data-uid');

        if(opt1.length==0 || opt1.length>20){
            alert('请输入合法的真实姓名');
            return false;
        }
        if(!/^1[0-9]\d{9}$/.test(opt2)){
            alert('请输入正确的联系人手机号码');
            return false;
        }
        var url = "<?php echo Url::to(['car/update_chaccount']);?>";
        is_sub=true;
        $.post(url,{realname:opt1,telphone:opt2,uid:opt3},function(json){
            is_sub=false;
            if(json.status == 1){

                return true;
            }else{
                alert(json.msg);
                return false;
            }
        });
    }
    function  del_chaccount(_this) {
        var opt1 = $(_this).attr('data-uid');
        var url = "<?php echo Url::to(['car/del_chaccount']);?>";
        $.post(url,{uid:opt1},function(json){
            if(json.status == 1){
                alert(json.msg);
                var deldataFlag = $('.DelSubAccounts').attr('data');
                $('.' + deldataFlag).parents('.shopPageCont').remove();
                $('.zzLevel').hide();
                $('.DelSubAccounts').css({
                    display : 'none'
                }).attr('data','');
                $('section').removeAttr('style')
            }else{
                alert(json.msg);
            }
        });
    }


</script>
<?php $this->endBlock('script');?>