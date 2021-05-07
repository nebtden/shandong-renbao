<?php
use \yii\helpers\Url;
?>
<style type="text/css">
.fcontent {
	background: url('../images/phone.png') no-repeat;
	width: 322px;
	height: 659px;
}

}
.footerMenu {
	width: 279px;
	height: 39px;
	position: fixed;
	left: 22px;
	top: 538px;
	border-top: 1px solid #e3e3e3;
}

.footerMenu .jianpan {
	width: 50px;
	height: 39px;
	border-right: 1px solid #e3e3e3;
	-webkit-box-sizing: border-box;
}

.footerMenu .jianpan img {
	width: 49px;
	height: 39px;
}

.footerMenu .ulMenu {
	width: -webkit-calc(100% -   50px);
}

.footerMenu .ulMenu li {
	width: 33.333333%;
	line-height: 39px;
	text-align: center;
	font-size: 16px;
	-webkit-box-sizing: border-box;
	border-right: 1px solid #e3e3e3;
	position: relative;
}

.footerMenu .ulMenu li .TCdiv {
	position: absolute;
	left: -1px;
	bottom: 39px;
	width: -webkit-calc(100% +   2px);
	border: 1px solid #e3e3e3;
	border-bottom: none;
	padding-right: 1px;
	-webkit-box-sizing: border-box;
	display: none;
}

.footerMenu .ulMenu li .TCdiv p {
	border-bottom: 1px solid #e3e3e3;
}

.footerMenu .ulMenu li .TCdiv p:last-child {
	border-bottom: none;
}

.footerMenu .ulMenu li .TCdiv p a {
	line-height: 45px;
	color: #3f3f3f;
	text-decoration: none;
}

.cor {
	background: #f3f3f3;
}

.footerMenu .ulMenu li:last-child {
	border-right: none;
}

.footerMenu .ulMenu li.cur {
	background-image: url("../images/youxiasj.png");
	background-position: -webkit-calc(100% -   5px) 25px;
	background-repeat: no-repeat;
	background-size: 8px 9px;
}
</style>
<div class="page-header am-fl am-cf">
	<h4>
		自定义菜单 <small>&nbsp;&nbsp;/列表信息</small>
	</h4>
</div>
<div style="width: 100%;">
	<div style="width: 70%; float: left;">
		<form method="post"
			action="<?php echo  Url::to(['admin/menulist']); ?>">
			<table class="table table-bordered" id="ttt">
				<thead>
					<tr>
						<th width="20%">导航名称</th>
						<th width="40%">链接</th>
						<th width="20%">排序</th>
						<th width="20%">操作</th>
					</tr>
				</thead>
            <?php
												$i = 1;
												foreach ( $menus [0] as $s => $v ) {
													?>
                <tbody>
					<tr>
						<td><input name='mainMenu[<?php echo $i;?>]' type='text'
							class="mainmenu" placeholder='主菜单' id="<?php echo $s+1; ?>"
							value='<?php echo $v['menu'];?>' size='15'></td>
						<td><?php if(!$menus[$v['id']]){?>
                            <select name='mainType[<?php echo $i;?>]'>
								<option value='1' <?php if($v['type']<2)echo 'selected';?>>图文信息</option>
								<option value='2' <?php if($v['type']==2)echo 'selected';?>>链接</option>
						</select> <input name='mainCon[<?php echo $i;?>]'
							style='width: 300px;' type='text'
							value='<?php echo $v['content'];?>'>
                        <?php }?>
                    </td>
						<td><?php if(!$menus[$v['id']]){?>
                            <input name='mainOrder[<?php echo $i;?>]'
							type='text' placeholder='排序' value='<?php echo $v['sort'];?>'
							size='6'>
                        <?php }?>
                    </td>
						<td><div class='am-btn-toolbar'>
								<div class='am-btn-group am-btn-group-xs caozuobtn'>
									<a class='am-btn am-btn-primary am-btn-xs '
										style='margin-right: 10px;'
										onclick='add(this,<?php echo $i;?>)'> <span
										class='am-icon-plus'></span> 添加
									</a> <a class='am-btn am-btn-default am-btn-xs am-text-danger '
										style='margin-right: 10px;' onclick='delMain(this)' data-align="<?php echo $v['id']?>"> <span
										class='am-icon-trash-o'></span> 删除
									</a>
								</div>
							</div></td>
					</tr>
                <?php
													if ($menus [$v ['id']]) {
														?>
                   <?php
														
foreach ( $menus [$v ['id']] as $k => $val ) {
															?>
                        <tr class="s<?php echo $i; ?>">
						<td><div style='margin-left: 60px;'>
								<input id="<?php echo $k+1; ?>" class="sunmenu"
									name='sunMenu[<?php echo $i;?>][]' type='text'
									placeholder='子菜单' value='<?php echo $val['menu'];?>' size='15'>
							</div></td>
						<td><select name='sunType[<?php echo $i;?>][]'>
								<option value='1' <?php if($val['type']<2)echo 'selected';?>>图文信息</option>
								<option value='2' <?php if($val['type']==2)echo 'selected';?>>链接</option>
						</select> <input name='sunCon[<?php echo $i;?>][]'
							style='width: 300px;' type='text'
							value='<?php echo $val['content'];?>'></td>
						<td><input name='sunOrder[<?php echo $i;?>][]' type='text'
							placeholder='排序' value='<?php echo $val['sort'];?>' size='6'></td>
						<td><div class='am-btn-toolbar'>
								<div class='am-btn-group am-btn-group-xs caozuobtn'>
									<a class='am-btn am-btn-default am-btn-xs am-text-danger'
										style='margin-left: 65px;' onclick='del(this)'> <span
										class='am-icon-trash-o'></span> 删除
									</a>
								</div>
							</div></td>
					</tr>
                    <?php
														}
													}
													?>
                </tbody>
                <?php $i++;}?>
        </table>
			<div style="margin: 50px 60px 100px 300px;">
				<div class="am-btn-group am-btn-group-xs caozuobtn"
					style="margin-right: 200px;">
					<a class="am-btn am-btn-success" style="margin-left: 10px;"
						onclick="addMain()"> <span class="am-icon-plus"></span> 增加主菜单
					</a>
				</div>

				<div class="am-btn-group am-btn-group-xs caozuobtn">
					<button class="am-btn am-btn-primary" style="margin-right: 10px;"
						id="submit">
						<span class="am-icon-pencil-square-o"></span> 生成
					</button>
				</div>
			</div>
		</form>
    <?php
				
if ($result ['msg']) {
					if ($result ['msg'] == 'ok') {
						echo "菜单发布成功，请不要重复操作!";
					} else {
						echo "网络通讯失败，请稍后重试！";
					}
				}
				?>
</div>
	<div style="width: 26%; float: right;">
		<h4 style="font-weight: bold">预览</h4>
		<div style="clear: both;"></div>
		<div class="fcontent">
			<div class="footerMenu webkitbox"
				style="width: 271px; margin: 0px auto; padding-top: 537px;">
				<div class="jianpan">
					<img src="../images/jianpan.png">
				</div>
				<ul class="ulMenu webkitbox">
				</ul>
			</div>
		</div>

	</div>
	<script type="text/javascript">
    var i=<?php echo $i;?>;
    if(!i)i=1;
    function addMain(){
        if(i>3){alert("主菜单最多只能创建3个！");return false;}
        $("#ttt").append("<tbody><tr><td><input name='mainMenu["+i+"]' id='"+i+"' class='mainmenu' type='text' placeholder='主菜单' value='' size='15'></td><td><select name='mainType["+i+"]'><option value ='1'>图文信息</option><option value ='2'>链接</option></select> <input name='mainCon["+i+"]'style='width:300px;' type='text'></td><td><input name='mainOrder["+i+"]' type='text' placeholder='排序' value='' size='6'></td><td><div class='am-btn-toolbar'><div class='am-btn-group am-btn-group-xs caozuobtn'><a class='am-btn am-btn-primary am-btn-xs ' style='margin-right:10px;' onclick='add(this,"+i+")'><span class='am-icon-plus'></span> 添加</a><a class='am-btn am-btn-default am-btn-xs am-text-danger ' style='margin-right:10px;' onclick='delMain(this)'><span class='am-icon-trash-o'></span> 删除</a></div></div></td></tr></tbody>");
        i++;
    }

    $("#submit").bind("click",function(){
        $("tbody").each(function(index,obj){});
    });
    var j=1;
    function add(obj,num){
        if($(obj).parents("tbody").children("tr").length>5){alert("子菜单最多只能创建5个！");return false;};
        $(obj).parents("tbody").append("<tr class='s"+num+"'><td><div style='margin-left: 60px;'><input   id='"+j+"'  class='sunmenu' name='sunMenu["+num+"][]' type='text' placeholder='子菜单' value='' size='15'></div></td><td><select name='sunType["+num+"][]'><option value ='1'>图文信息</option><option value ='2'>链接</option></select><input name='sunCon["+num+"][]' style='width:300px;' type='text' ></td><td><input name='sunOrder["+num+"][]' type='text' placeholder='排序' value='' size='6'></td><td><div class='am-btn-toolbar'><div class='am-btn-group am-btn-group-xs caozuobtn'><a class='am-btn am-btn-default am-btn-xs am-text-danger' style='margin-left:65px;' onclick='del(this)'><span class='am-icon-trash-o'></span> 删除</a></div></div></td></tr>");
        $(obj).parents("td").prev().empty();
        $(obj).parents("td").prev().prev().empty();
        j++;
    }
    function del(obj){
        $(obj).parents("tr").remove();
    }
    function delMain(obj){

        var url = "<?php echo Url::to(['admin/delmain']);?>";
        var opt1=$(obj).attr('data-align');

        $(obj).removeAttr('onclick');

        $.post(url,{id:opt1},function(json){
            if(json.status == 1){
                $(obj).parents("tbody").remove();
            }else{
                alert(json.msg);
            }
            $(obj).attr('onclick','delMain(this);');
        });
    }

    ///
    $(function(){
        var array1=[],array2=[];
        var ulMenu = $('.ulMenu');
        function  menulist(){
        	ulMenu.html('');
            array1.forEach(function (v, i, a) {
                if(array2[i] != undefined && array2[i].length >= 1){
                    var li = $('<li>' + v + '</li>');
                    var div = $('<div class="TCdiv"></div>');

                    array2[i].forEach(function (v, i, a) {
                        $('<p><a href="#">' + v + '</a></p>').appendTo(div);
                    });
                    
                    div.appendTo(li);
                    li.appendTo(ulMenu).css('width',(100 / a.length) + '%').addClass('cur');
                }else{
                    $('<li>' + v + '</li>').appendTo(ulMenu).css('width',(100 / a.length) + '%');
                }
            });
        }
        function initMenu(){
        	var x=0;
        	array1=[],array2=[];
        	$('.mainmenu').each(function(s){
                x++;
                array1.push($(this).val());
                var son=$('.s'+x);
                var array3=[];
                son.find('.sunmenu').each(function(){
                    array3.push($(this).val());
                });
                array2.push(array3);
            });
        	menulist();
        }
        
        initMenu();
        
        $('#ttt').on('mouseout','.mainmenu,.sunmenu',function(s){
        	initMenu();
        });
        
            ulMenu.on('mouseover','li',function (e) {
                var TCdiv = $(this).children('.TCdiv');
                e.stopPropagation();
                $(this).addClass('cor');

                if(TCdiv.is(':hidden')){
                    $(this).children('.TCdiv').show().end().siblings().children('.TCdiv').hide();
                    TCdiv.on('click', function (e) {e.stopPropagation();})
                        .children('p')
                        .on('click', function () {$(this).addClass('cor');})
                        .on('mouseout',function(){$(this).removeClass('cor');});
                    TCdiv = null;
                }else{
                    $(this).children('.TCdiv').hide();
                }
            }).on('mouseout','li',function(){
                $(this).removeClass('cor');
            });;

            $(document).on('click',function(){
                $('.TCdiv').hide();
            });
    });
</script>