<style>
    label{
        width: 100%;
        display: block;
        margin-top:0.5rem ;
    }
    .con{
        margin-left: 0.2rem;

    }
    button{
        width: 2rem;
        height:0.5rem;
    margin-top: 0.5rem}
</style>
<label>
    当前实际参与人数：<input type="text" readonly value="<?php echo $total; ?>"/></label>
<label>当前展示参与人数：<input type="text" readonly value="<?php echo $showTotal; ?>"/></label>
<label class="con"> 基础参与人数:<input id="hero_base_count" type="text" value="<?php echo $hero_base_count; ?>"/></label>
<label class="con">  参与人数倍数:<input id="here_multiple_count" type="text" value="<?php echo $here_multiple_count; ?>"/>
</label>
<button>确认修改</button>
<script>
    $('button').on('click', function() {
        $.post('set-total.html', {hero_base_count: $("#hero_base_count").val(), here_multiple_count: $("#here_multiple_count").val()}, function() {
            alert('修改成功');
        })
    })
</script>