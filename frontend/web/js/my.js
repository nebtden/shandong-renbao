function uploadImg(fid,tid){
	$('#'+fid).diyUpload({
		url:'/backend/web/server/fileupload.php',
		success:function( data ) {
			console.info( data );
            $('input[name="'+tid+'"]').val(data.result);
            $('input[name="'+tid+'"]').next('img').remove();
            $('#'+fid).hide();

		},
		error:function( err ) {
			console.info( err );
		}
	});
}

function uploadImg_r2(fid,tid,w,h){
    $('#'+fid).diyUpload({
        url:'/backend/web/server/fileupload.php?w='+w+'&h='+h,
        success:function( data ) {
            if(data.error)
            {
             alert(data.error.message)   ;
                history.go(0);
            }
            else
            {

                console.info( data );
                $val= $('input[name="'+tid+'"]').val();
                if($val){
                    $('input[name="'+tid+'"]').val($val + '|'+data.result);
                }else{
                    $('input[name="'+tid+'"]').val(data.result);
                }
              //  $('input[name="'+tid+'"]').next('img').remove();
               //  $('#'+fid).hide();
            }


        },
        error:function( err ) {
           // console.info( err );
           //alert(err);
        }
    });
}

function uploadImg_dan(fid,tid,w,h){
    $('#'+fid).diyUpload({
        url:'/backend/web/server/fileupload.php?w='+w+'&h='+h,
        success:function( data ) {
            if(data.error)
            {
             alert(data.error.message)   ;
                history.go(0);
            }
            else
            {
                console.info( data );
                $('input[name="'+tid+'"]').val(data.result);
              //  $('input[name="'+tid+'"]').next('img').remove();
               //  $('#'+fid).hide();
            }

        },
        error:function( err ) {
           // console.info( err );
           //alert(err);
        }
    });
}


//$('#hadpic').diyUpload({
//	url:'/backend/web/server/fileupload.php',
//	success:function( data ) {
//		console.info( data );
//		$("#hadimg").val(data.result);
//	},
//	error:function( err ) {
//		console.info( err );
//	}
//});
//
//$('#picList').diyUpload({
//	url:'/backend/web/server/fileupload.php',
//	success:function( data ) {
//		console.info( data );
//		$val=$("#hadimgs").val();
//		if($val){
//			$("#hadimgs").val($val + '|'+data.result);
//		}else{
//			$("#hadimgs").val(data.result);
//		}
//	},
//	error:function( err ) {
//		console.info( err );
//	}
//});
//
//$('#notepicList').diyUpload({
//	url:'/backend/web/server/fileupload.php',
//	success:function( data ) {
//		console.info( data );
//		$val=$("#notehadimgs").val();
//		if($val){
//			$("#notehadimgs").val($val + ','+data.result);
//		}else{
//			$("#notehadimgs").val(data.result);
//		}
//	},
//	error:function( err ) {
//		console.info( err );
//	}
//});


function submits(obj){
	
	$node = $(obj).parents('form');
	$node.submit();
}

function bensubmits(obj){
	
	$node = $(obj).parents('form');
	$node.attr('action', '/order/index');
	$node.submit();
}

function writesubmits(obj){
	
	$node = $(obj).parents('form');
	$node.attr('action', '/order/index/type/2');
	$node.submit();
}

function writenewssubmits(obj){
	
	$node = $(obj).parents('form');
	$node.attr('action', '/backend/web/news/newsprint.html');
	$node.submit();
}

function writeordersubmits(obj){
	
	$node = $(obj).parents('form');
	$node.attr('action', '/backend/web/order/orderprint.html');
	$node.submit();
}

function writethirdordersubmits(obj){
	
	$node = $(obj).parents('form');
	$node.attr('action', '/backend/web/thorder/orderprint.html');
	$node.submit();
}

function writecountlist(obj){
	
	$node = $(obj).parents('form');
	$node.attr('action', '/backend/web/thorder/count_list_print.html');
	$node.submit();
}

$("#allCheck").click(function () {
    if ($(this).prop("checked") == true) {
        $(".tableCheck").prop("checked", true);
    } else {
        $(".tableCheck").prop("checked", false);
    }
});

$("#test").click(function(){
//	Alertify.dialog.confirm("确认删除？", function () {
//		Alertify.dialog.alert("确认！",function(){});
//    }, function () {
//    	Alertify.dialog.alert("取消！",function(){});
//    });
	
	Alertify.dialog.prompt("Message", function () {
		Alertify.dialog.alert("确认！",function(){});
   	}, function () {
    	Alertify.dialog.alert("取消！",function(){});
    },"Default Value");
});


$('.uptr').on('click',function(){
    var id=$(this).attr('id');
    var url='/autoreply/simplereply/id/'+id;
    $.get(url,function(data){
        $(".am-u-sm-12-n").html(data);
    });

});

//更改提示默认样式
$(function(){
    function check_lhb(i,tip){
        if(i.validity.patternMismatch === true){
            i.setCustomValidity(tip);
        }else{
            i.setCustomValidity('');
        }
    }
    $('.am-btn').click(function(){
        $('input[type=text]').each(function(index,element){
            if($(element).attr('required') !== ''){
               //$(element).attr('required','');
            }
        })
    });

    /*$('input[name=name][class *= am-text-primary]').attr({
        'required' : ''
    });
    $('input[name=sort][class=am-input-sm]').attr({
        "required" : ''
    });*/
});


