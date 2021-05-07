<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5 0005
 * Time: 上午 10:35
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        观看码批量生成 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" >
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">生成数量</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="1000000000" id="generate_num" name="generate_num" class="form-control" placeholder="请输入要生成的数量">
            </div>
        </div>
        <div class="form-group xxz_amount">
            <label for="inputEmail3" class="col-sm-2 control-label">可观看次数</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="10000000000" id="views_num" name="views_num" value="14" class="form-control" placeholder="输入可观看次数">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">公司</label>
            <div class="col-sm-3">
                <select id="company_id" name="company_id"  placeholder="公司"  class="form-control">
                    <?php foreach ($company as $key => $val):?>
                        <option value="<?=$val['id']?>"><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="button" class="btn btn-default" onclick="Leadin()">批量生成</button>
            </div>
        </div>
    </form>
</div>

<div class="yinying" style="
position: fixed;width: 100%; height: 100%; left: 0;top: 0;background: #333;opacity: 0.6;z-index: 1000; display:none;"><span ></span>
</div>
<script type="text/javascript">

    function Leadin(){

        var generate_num = $("#generate_num").val(),
            views_num = $("#views_num").val(),
            company_id = $("#company_id").val();
        if(generate_num == '' ){
            alert('生成数量不能为空');
            return false;
        }

        if(generate_num>100000){
            alert('生成数量不能超过10万');
            return false;
        }

        $(".yinying").show();
        $.post('<?php echo Url::to(["calendarad/generate"]); ?>',{
            generate_num:generate_num,
            views_num:views_num,
            company_id: company_id
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
        $.post('<?php echo Url::to(["calendarad/batchcode"]); ?>',{
            key:data.xxzkey,
            num:num,
            xxzmaxnum:data.xxzmaxnum,
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
</script>
