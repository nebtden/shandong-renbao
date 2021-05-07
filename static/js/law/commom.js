//公用弹窗函数调用

//公用弹窗函数 div拼接
function runCommomLayer(data){
    var html = '';
    html += '<div class="commom-popup-outside big-popup-outside" >'
    html += '<div class="commom-popup">'
    html += '<div class="title">'+data.title+'<i class="icon-error"></i></div>'
    html += ' <div class="content">'
    html += '<div class="tip">·以下类别请直接咨询·</div>'
    html += '<ul class="travel-ul">';
    //console.log(data)
    $.each(data.data,function(i,val){
        html+='<li>'
        html+='<a href="javascript:;" data-type-pid="'+i+'">'+val+'</a>'
        html+='</li>'

    })
    html += '</ul>'
    html += '<div class="commom-submit hot-line-consult">'
    html += '<a class="btn-block btn-primary  big-popup-btn" href="javascript:;" ><i class="icon-lvjiabao-rexian-"></i>热线咨询</a>'
    html += '</div>'
    html += '</div>'
    html += '</div>'
    html += '</div>';
    $('body').prepend(html);       
} 
//拨打电话弹窗
function runCallPhoneLayer(){
    var html = '';
    html += '<div class="commom-popup-outside small-popup-outside call-phone-outside" >'+
                '<div class="commom-popup">'+
                    '<div class="title title-nobg"><i class="icon-error"></i></div>'+
                    '<div class="content">'+
                        '<div class="connect-tip">您的需求已提交，请注意接听律师回电</div>'+
                        '<div class="commom-submit">'+
                            '<a class="btn-block btn-primary small-popup-btn" href="javascript:;" >取&nbsp;&nbsp;消</a>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';
    $('body').prepend(html);
}
//系统繁忙
function runSystembusy(){
    var html = '';
    html += '<div class="commom-popup-outside busy-popup-outside call-phone-outside" >'+
        '<div class="commom-popup">'+
        '<div class="title title-nobg"><i class="icon-error"></i></div>'+
        '<div class="content">'+
        '<div class="connect-tip">系统繁忙</div>'+
        '<div class="commom-submit">'+
        '<a class="btn-block btn-primary small-popup-btn" href="javascript:;" >取&nbsp;&nbsp;消</a>'+
        '</div>'+
        '</div>'+
        '</div>'+
        '</div>';
    $('body').prepend(html);
}