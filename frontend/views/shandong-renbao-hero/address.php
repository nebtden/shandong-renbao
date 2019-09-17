<div class="hero-bg">
    <h1 class="hide">猜英雄 得大奖</h1>

    <div class="bug-site">
        <input type="hidden" class="rewards_id" value="<?= $rewards_id ?>">
        <span>
                <input type="text" class="name" placeholder="收件人">
                <i></i> 
            </span>
        <span>
                <input type="text" class="mobile" placeholder="中奖手机号码">
                <i class="input-i2" ></i>
            </span>

        <div class="control-group">
            <label class="control-label" for="location_p">详细地址</label>
            <div class="controls">
                <select name="location_p" id="location_p"></select>
                <select name="location_c" id="location_c"></select>
                <select name="location_a" id="location_a"></select>
            </div>
        </div>
        <textarea name="text" id="messagesbox"  class="messagesbox" placeholder="详细地址：如道路、门牌号、小区、楼栋号、单元室等 "></textarea>
        <a href="javascript:;" class="affirm">
            确认
        </a>
    </div>
</div>
<script src="/frontend/web/shandong-renbao-hero/js/region_select.js"></script>
<script type="text/javascript">
    new PCAS('location_p', 'location_c', 'location_a', ' ', '', '');

    $(function () {
        $('.affirm').click(function(){
                var rewards_id = $('.rewards_id').val();
                var mobile = $('.mobile').val();
                var name = $('.name').val();
                var p = $('#location_p').val();
                var c = $('#location_c').val();
                var a = $('#location_a').val();
                var address = $('.messagesbox').val();
                if(!mobile || !name || !p || !c || !a || !address){
                    alert('请补充资料完全再提交');
                }
            $.post('address-save.html',{mobile:mobile,name:name,address:address},function (data) {
                // console.log();
                if(data.status==0){
                    alert(data.msg);

                }else{
                    window.location.href = 'remind.html?id='+data.data.id;

                }
            },'json');


        });
    });

</script>
