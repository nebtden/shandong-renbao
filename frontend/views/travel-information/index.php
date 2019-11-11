<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 9:32
 */

?>

<div class="bm-number con-box">
    <div class="con6box">
        <h1>出游人信息填写</h1>
        <ul class="con7" id="comappend">
            <li class="inputName">
                <input type="text" class="con7input1" name="name">
                <span>出游人姓名:</span>
            </li>
            <li class="inputName">
                <input type="text" class="con7input1" name="sex">
                <span>出游人性别:</span>
            </li>
            <li class="inputName">
                <input type="text" class="con7input1" name="code">
                <span>身份证号码:</span>
            </li>
            <li class="inputName">
                <input type="text" class="con7input1 con7input2" name="mobile">
                <span>联系电话:</span>
            </li>
            <ul class="xuanze">
                <li>可选择出游日期:</li>
                <li>


                        <?php foreach ($dates as $date){ ?>
                            <label>
                                <input type="radio"   name="date" /><?php echo $date['date']  ?>
                            </label>
                            <br />

                        <?php } ?>

                </li>

            </ul>
            <input type="texarea" name="remark" placeholder="备注:"  class="con7input1 con7-text">



        </ul>
        <!--添加出游人  -->
        <div class="submitbtn">
            <a class="submit" href="javascript:;">提交</a>
        </div>
    </div>
    <div class="con7footer">
        <p>
            请在<b>59:59:60</b>内填写完并提交本页面信息否则名额
            将会释放，在填写完成并提交之前关闭了此页面，
            也不会为您锁定名额，如有需要需重新抢订名额
        </p>
    </div>

</div>
<style>


</style>

<script>
    $('.con7add').click(function(){
        // $("body").append($("#comappend>input").clone());
        var html = '<div id="comappend" class="con7 " >'+new Date().getTime()+'</div>';
        $(".con7").append($("#comappend").clone())

    })



    $('.submit').click(
        function () {
            var name = $("input[name='name']").val();
            var code = $("input[name='code']").val();
            var sex = $("input[name='sex']").val();
            var mobile = $("input[name='mobile']").val();
            var remark = $("input[name='remark']").val();
            var date = $("input[name='date']").val();

            var reg =/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/;
            // if(!reg.test(code)){
            //     // this.warnTips({txt:''});
            //     alert("请输入正确的身份证号码有误，请重填");
            //     return false;
            // }
            //
            // if(!(/^1[3456789]\d{9}$/.test(phone))){
            //     alert("手机号码有误，请重填");
            //     return false;
            // }

            $.post('add.html',{name:name,code:code,sex:sex,mobile:mobile,remark:remark,date:date},function (data) {
                if(data.status==1){
                    alert('添加成功！请继续添加');
                    window.location.reload();
                    // window.location.href = 'prize.html?id='+data.data.id;
                }else if(data.status==2){
                    window.location.href = 'submit.html?id='+data.data.id;
                }else{
                    alert(data.data.msg);
                    // window.location.href = 'prize.html?id='+data.data.id;
                }
            },'json');

        });
</script>


