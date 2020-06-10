<?php 
use yii\helpers\Url;
?>

    <?php if(!$cartpros){?>
    <section class="MyOrderCont DetailsColor paddBot121">
    <h1 class="none_">购物车</h1>
    <div class="shoppingCar">
        <div class="CarTitle clearfix"><span>我的购物车</span></div>
    </div>
    <!-- 空购物车 -->
    <div class="emptyShopCar">
        <img src="/static/mobile/images/konggouwuche.png">
        <p>篮子里空空如也~赶快去选购吧~</p>
    </div>
    </section>
    <?php }else{?>
    <section class="MyOrderCont DetailsColor paddBot121">
    <h1 class="none_">购物车</h1>
    <div class="shoppingCar">
        <div class="CarTitle clearfix"><span>我的购物车</span></div>
        <div class="CarCont">
        <?php foreach($cartpros as $k=>$v){?>
            <dl class="dlDiv linebot boxSizing webkitbox clearfix">
                <dt class="dt1" pid=<?php echo $k?>></dt>
                <dd class="dd1"><img src=<?php echo $v['pic']?>></dd>
                <dd class="dd2">
                    <div class="p1"><?php echo $v['proname']?></div>
                    <div class="p3 webkitbox boxSizing">
                        <span class="sp" style="font-size:16px;"><b>积分</b><?php echo $v['price']*10?></span>
                        <span class="i"></span>
                        <div class="em webkitbox" pid=<?php echo $k?>>
                            <i class="i1"></i>
                            <i class="i2"><?php echo $v['proNum']?></i>
                            <i class="i3"></i>
                        </div>
                    </div>
                </dd>
            </dl>
            <?php }?>
        </div>
    </div>
</section>
 
<div class="shopMessDiv webkitbox boxSizing">
    <div class="allselc webkitbox boxSizing" id="selectAll">
        <span class="sp1"></span>
        <em>全选</em>
    </div>
    <div class="del webkitbox boxSizing" id="delSelected">
        <span class="sp1"></span>
        <em>删除</em>
    </div>
    <div class="allMoney">
        <span>积分:<em><i id="moneyNum">0</i></em></span>
    </div>
    <div class="goJS"><a onclick="jieshuan();">结算</a></div>
</div>
<?php }?>
<?php echo $this->context->renderPartial('../layouts/mall_footer');?>

<script src="/static/mobile/js/jquery-1.10.1.js"></script>
<script src="/static/mobile/js/MeTool.js"></script>
<script src="/static/mobile/js/alert.js"></script>
<script type="text/javascript">
    function shoPPing() {
        var selectAll       = document.querySelector( '#selectAll' ),
                delSelected = document.querySelector( '#delSelected' ),
                CarCont     = document.querySelector( '.CarCont' ),
                moneyNum    = document.querySelector( '#moneyNum' ),
                iTwoElement = CarCont.querySelectorAll('.i2'),
                danCount = 0,
                objClass    = {
                    Selected : 'dt2',
                    noSelect : 'dt1',
                    spanSelc : 'sp2',
                    spanNose : 'sp1',
                    spanprice : 'sp',
                    span : 'span',
                    iNum : 'i2',
                    i : 'i'
                },
                singObj = {},
                dtEle = null,
                selectAllChildSpan = Array.prototype.filter.call(selectAll.childNodes, function (v, i, a) {
                    return a[i].nodeName.toLocaleLowerCase() == 'span';
                })[0],
                allMoney = 0;

        function CarContTouch(e) {
            var dt = this.getElementsByTagName( 'dt' ),flag = true;

            if( e.target.nodeType == 1 && e.target.nodeName.toLowerCase() == 'dt' ){
                var     parentDL       = e.target.parentNode,
                        spanSpElement  = findChildElement( parentDL,objClass.span, objClass.spanprice, 0 ),
                        iElement       = findChildElement( parentDL, objClass.i, objClass.iNum, 0 ),
                        price          = parseFloat( spanSpElement.innerText.substring( 2 ) ),
                        shopNum        = parseInt( iElement.innerText ),
                        singleAllPrice = price * shopNum;

                if( e.target.className == objClass.noSelect ){
                    e.target.className = objClass.Selected;
                    allMoney += singleAllPrice;
                }else if( e.target.className == objClass.Selected ){
                    e.target.className = objClass.noSelect;
                    allMoney -= singleAllPrice;
                }
                moneyNum.innerHTML = allMoney.toFixed(0);
            }
            for( var i = 0, len = dt.length; i < len; i++ ){

                if( dt[i].className == objClass.noSelect ){
                    selectAllChildSpan.className = objClass.spanNose;
                    flag = false;
                }
            }
            if( flag ){
                selectAllChildSpan.className = objClass.spanSelc;
            }
        }

        function selectAllTouch() {
            var dt = CarCont.getElementsByTagName( 'dt' );

            if( selectAllChildSpan.className == objClass.spanNose ){
                for( var i = 0, len = dt.length; i < len; i++ ){
                    if( dt[i].className == objClass.Selected ){
                        continue;
                    }
                    var     parentDL       = dt[i].parentNode,
                            spanSpElement  = findChildElement( parentDL,objClass.span, objClass.spanprice, 0 ),
                            iElement       = findChildElement( parentDL, objClass.i, objClass.iNum, 0 ),
                            price          = parseFloat( spanSpElement.innerText.substring( 2 ) ),
                            shopNum        = parseInt( iElement.innerText ),
                            singleAllPrice = price * shopNum;
                    allMoney += singleAllPrice;
                    moneyNum.innerHTML = allMoney.toFixed( 0 );
                    dt[i].className = objClass.Selected;
                }
                selectAllChildSpan.className = objClass.spanSelc;
            }else if( selectAllChildSpan.className == objClass.spanSelc ){

                for( var i = 0, len = dt.length; i < len; i++ ){
                    dt[i].className = objClass.noSelect;
                }
                selectAllChildSpan.className = objClass.spanNose;
                allMoney = 0;
                moneyNum.innerHTML = '0.00';
            }
        }

        function delSelectedTouch() {
            var dt = CarCont.getElementsByTagName('dt'), dtlen = dt.length, arr = [];
            var proArr=[];
            for( var i = 0, len = dt.length; i < len; i++ ){
                if( dt[i].className == objClass.Selected ){
                    arr.push(dt[i]); proArr.push($(dt[i]).attr('pid'));
                }
            }
            $.post("<?php echo Url::toRoute('cartdel');?>",{proArr:proArr},function(data){
            	arr.forEach(function (v, i, a) {
                    a[i].parentNode.remove();
                });
               })
            arr.length == dtlen ? selectAllChildSpan.className = objClass.spanNose : '';
            allMoney = 0;
            moneyNum.innerHTML = '0.00';
        }

        function iOneTouch(e) {
            e.preventDefault();
            var ordeNum = $(this).next();
            var textNum = ordeNum.text(),num
            var parent  = this.parentNode.parentNode;
            var singleZJ= parseFloat( findChildElement(parent, objClass.span, objClass.spanprice, 0).innerText.substring(2) );
            var dt = findChildElement( parent.parentNode.parentNode, 'dt', undefined, 0 );
            if( textNum <= 1 ){
                return false;
            }
            num = Number( textNum );
            num--;
            var pid=$(this).parent().parent().parent().siblings('dt').attr('pid');
            $.post("<?php echo Url::toRoute('operation');?>",{pid:pid,num:num},function(data){
            	ordeNum.text( data );
               })
            if( dt.className != objClass.Selected ){
                return;
            }
            allMoney = allMoney - singleZJ;
            moneyNum.innerHTML = allMoney.toFixed(0)
        }

        function iTwoTouch(e) {
            e.preventDefault();
            var ordeNum = $(this).prev();
            var textNum = ordeNum.text(),num;
            var parent  = this.parentNode.parentNode;
            var singleZJ= parseFloat( findChildElement( parent, objClass.span, objClass.spanprice, 0 ).innerText.substring(2) );
            var dt = findChildElement( parent.parentNode.parentNode, 'dt', undefined, 0 );
            num = Number( textNum );
            num++;
            var pid=$(this).parent().parent().parent().siblings('dt').attr('pid');
            $.post("<?php echo Url::toRoute('operation');?>",{pid:pid,num:num},function(data){
            	ordeNum.text( data );
               })
            if( dt.className != objClass.Selected ){
                return;
            }
            allMoney = allMoney + singleZJ;
            moneyNum.innerHTML = allMoney.toFixed(0)
        }

        //焦点进入
        Array.prototype.forEach.call(iTwoElement, function (v, i, a) {
            addEvent(a[i], 'focus', function(){
                this.scrollIntoView();
                danCount = $(this).text();
                singObj.parent = this.parentNode.parentNode;
                singObj.singleZJ = parseFloat( findChildElement( singObj.parent, objClass.span, objClass.spanprice, 0 ).innerText.substring(2) );
            });

            addEvent(a[i], 'input', function (e) {
                
                if(!(/^\d*$/g).test($(this).text())){

                	
                    this.innerHTML = $(this).text().replace(/\D/g, '');
                    
                    $(this).tanchu('请输入数字');
                    return;
                }
                var changenum=$(this).text();
                var changepid=$(this).parent().parent().parent().siblings('dt').attr('pid');
                $.post("<?php echo Url::toRoute('operation');?>",{pid:changepid,num:changenum})
                var dlParent = null, parent = this.parentNode;
                while(parent.nodeName.toLowerCase()!= 'dl' && parent.className != 'dlDiv'){
                    parent = parent.parentNode;
                }
                dlParent = parent;
                dtEle = dlParent.querySelector('dt');
                if(dtEle.className == objClass.noSelect){
                    return;
                }
                if(dtEle.className == objClass.Selected){
                    var textNum = Number($(this).text());

                    if(textNum < danCount){
                        allMoney = allMoney - (danCount - textNum) * singObj.singleZJ;
                        danCount = textNum || 0;
                    }else if(textNum > danCount){
                        allMoney += singObj.singleZJ * (textNum - danCount);
                        danCount = textNum;
                    }
                    moneyNum.innerHTML = allMoney.toFixed(0)
                }
            });

            addEvent(a[i], 'blur', function () {
                if((/^\s*$/g).test($(this).text())){
                    $(this).text(1);

                    if(dtEle.className == objClass.Selected){
                        allMoney += singObj.singleZJ * 1;
                        moneyNum.innerHTML = allMoney.toFixed(0)
                    }
                }
            })
        })

        addEvent( CarCont, 'touchstart', CarContTouch );
        addEvent( selectAll, 'touchstart', selectAllTouch );
        addEvent( delSelected, 'click', delSelectedTouch );

        $('.CarCont').on( 'touchstart','i.i1', iOneTouch );
        $('.CarCont').on( 'touchstart', 'i.i3', iTwoTouch );
    }

    addEvent( document, 'DOMContentLoaded', shoPPing );


</script>
<script>
         $('.shopFooter a').eq(2).css('background-image','url(/static/mobile/images/a3.png)').css('color','#ff5500');  
</script>
<script>
          function jieshuan(){
                var pidstr='';
                $('.CarCont').find('dt').each(function(){
                     if($(this).hasClass("dt2")){
                    	 pidstr+=$(this).attr('pid')+',';
                         }
                    });
            //    pidstr=pidstr.substring(0,pidstr.Length-1);
                
                location.href='<?php echo Url::toRoute('cartbuy');?>'+'?pidstr='+pidstr;        
              } 
</script>

</html>