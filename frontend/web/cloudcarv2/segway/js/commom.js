//首页公用弹窗函数 div拼接
function runCommomLayer(){
    var html = '';
    html += '<div class="commom-popup-outside big-popup-outside">'+
                '<div class="commom-popup">'+
                    '<div class="title">请先选择洗车券<i class="icon-error"></i></div>'+
                    '<div class="content">'+
                        '<ul class="card-popup-ul">'+
                            '<li>'+
                                '<div class="title">'+
                                    '<span>单次洗车服务·A</span>'+
                                    '<i>￥30.00</i>'+
                                '</div>'+
                                '<div class="middle">服务码：hcuhr9439u405 </div>'+
                                '<div class="down">'+
                                    '<time>有效期：2018.05.01-2018.05.31</time>'+
                                    '<a href="javascript:;" class="btn">选择</a>'+
                                '</div>'+
                            '</li>'+
                            '<li>'+
                                '<div class="title">'+
                                    '<span>单次洗车服务·B</span>'+
                                    '<i>￥30.00</i>'+
                                '</div>'+
                                '<div class="middle">服务码：hcuhr9439u405 </div>'+
                                '<div class="down">'+
                                    '<time>有效期：2018.05.01-2018.05.31</time>'+
                                    '<a href="javascript:;" class="btn">选择</a>'+
                                '</div>'+
                            '</li>'+
                            '<li>'+
                                '<div class="title">'+
                                    '<span>单次洗车服务·C</span>'+
                                    '<i>￥30.00</i>'+
                                '</div>'+
                                '<div class="middle">服务码：hcuhr9439u405 </div>'+
                                '<div class="down">'+
                                    '<time>有效期：2018.05.01-2018.05.31</time>'+
                                    '<a href="javascript:;" class="btn">选择</a>'+
                                '</div>'+
                            '</li>'+
                        '</ul>'+
                    '</div>'+    
                '</div>'+
            '</div>';
    $('body').prepend(html);       
} 
//提示小弹窗
function runSmallLayer(){
    var html = '';
    html += '<div class="commom-popup-outside  small-popup-outside"  >'+
                '<div class="commom-popup">'+
                    '<div class="title title-nobg"><i class="icon-error"></i></div>'+
                    '<div class="content">'+
                        '<div class="up">您还没有可用服务码，<br>是否现在激活服务码？</div>'+
                        '<div class="commom-submit need-submit">'+
                            '<a class="btn-block btn-primary small-popup-btn" href="javascript:;" >好&nbsp;&nbsp;的</a>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';
    $('body').prepend(html);
}
//弹窗 出示服务码
function showServiceCode(){
    var html = '';
    html += '<div class="commom-popup-outside  big-popup-outside"  >'+
                '<div class="commom-popup">'+
                    '<div class="title">典典洗车服务·C<i class="icon-error"></i></div>'+
                    '<div class="content">'+
                        '<div class="up">'+
                            '<span>服务码：hcuhr9439u405 </span>'+
                            '<time>有效期：2018.05.01-2018.05.31</time>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';
    $('body').prepend(html);
}
