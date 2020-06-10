/**
 * Created by liuhanbin on 2016/4/23.
 */
/**
 * Created by Administrator on 2016/4/22.
 */
function addEvent(obj,type,fn){
    if(typeof obj.addEventListener != 'undefined'){
        //W3C浏览器执行这里
        obj.addEventListener(type,fn);
    }else{
        //假如obj.events不存在的话，那么就给它赋值为一个空对象
        if(!obj.events){
            obj.events = {}
        }
        //第一次执行事件
        if(!obj.events[type]){
            //创建一个存放事件的数组
            obj.events[type] = [];
            //把第一次事件放在数组的第一个位置
            if(obj['on' + type]){
                obj.events[type][0] = fn;
            }
        }else{
            //同一个注册函数进行屏蔽，不添加到计数器中
            if(addEvent.equal(obj.events[type],fn)){
                return false;
            }
        }
        obj.events[type][addEvent.ID++] = fn;
        //执行事件
        obj['on' + type] = addEvent.exec;
    }
}
addEvent.ID = 1;
//删除事件
function removeEvent(obj,type,fn){
    if(typeof obj.removeEventListener != 'undefined'){
        obj.removeEventListener(type,fn);
    }else{
        if(obj.events){
            for(var i in obj.events[type]){
                if(obj.events[type][i] == fn){
                    delete obj.events[type][i];
                }
            }
        }
    }
}

//同一个注册函数进行屏蔽
addEvent.equal = function (es, fn) {
    for(var i in es){
        if(es[i] == fn){
            return true;
        }
    }
    return false;
}

//执行事件处理函数
addEvent.exec = function (event) {
    var e = event || addEvent.fixEvent(window.event);
    var es = this.events[e.type];
    for(var i in es){
        es[i].call(this,e);
    }
}

//把IE常用的事件属性推进w3c中
addEvent.fixEvent = function (event) {
    event.preventDefault  = addEvent.fixEvent.preventDefault;
    event.stopPropagation = addEvent.fixEvent.stopPropagation;
    event.target = event.srcElement;
    return event;
}

//同步W3C的preventDefault;
addEvent.fixEvent.preventDefault = function () {
    this.returnValue = false;
}

//同步W3C的stopPropagation;
addEvent.fixEvent.stopPropagation = function () {
    this.cancelBubble = true;
}

//获取视口大小
function getInner(){
    if(typeof window.innerWidth != 'undefined'){
        return {
            width  : window.innerWidth,
            height : window.innerHeight
        }
    }else{
        if(document.compatMode == 'CSS1Compat'){
            return {
                width  : document.documentElement.clientWidth,
                height : document.documentElement.clientHeight
            }
        }else{
            return {
                width  : document.body.clientWidth,
                heigh  : document.body.clientHeight
            }
        }
    }
}

//获取滚动条高度
function getScrollTop(){
    return {
        top : document.documentElement.scrollTop || document.body.scrollTop,
        left: document.documentElement.scrollLeft || document.body.scrollLeft
    }
}

//获取元素style
function getStyle(element,attr){
    var value;
    if(typeof window.getComputedStyle != 'undefined'){
        value = window.getComputedStyle(element,null)[attr];
    }else{
        value = element.currentStyle[attr];
    }
    return value;
}

//跨浏览器获取innerText
function getInnerText(element){
    return (typeof element.textContent == 'string') ? element.textContent : element.innerText;
}

//跨浏览器设置innerText
function setInnerText(element,text){
    if(typeof element.textContent == 'string'){
        element.textContent = text;
    }else{
        element.innerText = text;
    }
}

//增加class类
function addClass(element,className){
    if(typeof element.classList == 'object'){
        element.classList.add(className);
    }else{
        var classString = element.className;
        if((new RegExp('\\s*'+ className +'\\s*','g')).test(classString)){
            return;
        }else{
            element.className = classString + ' ' + className;
        }
    }
}

//删除class类
function removeClass(element,className){
    if(typeof element.classList == 'object'){
        element.classList.remove(className);
    }else{
        var classString = element.className;
        if((new RegExp('\\s*'+ className +'\\s*','g')).test(classString)){
            element.className = classString.replace((new RegExp('\\s*'+ className,'g')),'');
        }
    }
}

//判断class是否存在
function hasClass(element,className){
    if(typeof element.classList == 'object'){
        element.classList.contains(className);
    }else{
        return !!element.className.match(new RegExp('(\\s*|^)' + className + '(\\s*|$)','gi'));
    }
}

//自动切换className，有时删除className，没有时添加className
function toggleClass(element,className){
    if(typeof element.classList == 'object'){
        element.classList.toggle(className);
    }else{
        var classString = element.className;
        if((new RegExp('\\s*' + className + '\\s*','g')).test(classString)){
            element.className = classString.replace((new RegExp('\\s*' + className,'g')),'');
        }else{
            element.className = classString + ' ' + className;
        }
    }
}

//获取自定义属性
function getDataValue(element,dataName){
    if(element.dataset != undefined){
        return element.dataset[dataName];
    }else{
        return element.getAttribute('data-' + dataName);
    }
}

//设置自定义属性
function setDataValue(element,dataName,dataValue){
    if(element.dataset != undefined){
        element.dataset[dataName] = dataValue;
    }else{
        element.setAttribute('data-' + dataName,dataValue);
    }
}

//outerHTML几乎都支持，firefox7以前版本不支持
if(Object.defineProperty == undefined && window.attachEvent == undefined){
    HTMLElement.prototype.__defineGetter__('outerHTML', function () {
        var attributes = this.attributes;
        var nodeName   = this.nodeName.toLowerCase();
        var innerHTML  = this.innerHTML;
        var HTML = '',text = '';
        for(var i = 0, len = attributes.length; i < len; i++){
            if(!attributes[i].specified){
                continue;
            }
            text += ' ' + attributes[i].nodeName + '=' + '"' + attributes[i].nodeValue + '"';
        }
        HTML = '<' + nodeName + text + '>' + innerHTML + '</' + nodeName + '>'
        return HTML;
    });
    HTMLElement.prototype.__defineSetter__('outerHTML', function (value) {
        var parentNode = this.parentNode;
        if((/^<([a-z])+>(\w*)<\/\1>$/).test(value)){
            var element = RegExp.$1;
            var textNode= RegExp.$2;
            element = document.createElement(element);
            element.appendChild(document.createTextNode(textNode));
            parentNode.replaceChild(element,this);
        }else{
            var textNode = document.createTextNode(value);
            parentNode.replaceChild(textNode,this);
        }
    })
}

//获取元素距离某个元素顶部的高度 相对高度
function getPosition(element,parentElement){
    //假如它的祖先元素里面有position定位的话
    var parent = element.parentNode;
    var positionTop = 0,positionLeft = 0;
    var offset = null;
    if(getStyle(parent,'position') == 'static'){
        parent.style.position = 'relative';
    }
    positionTop = element.offsetTop;
    positionLeft = element.offsetLeft;
    while(parent.parentNode != parentElement.parentNode){
        if(getStyle(parent.parentNode,'position') == 'static'){
            parent.parentNode.style.position = 'relative';
        }
        positionTop += parent.offsetTop;
        positionLeft += parent.offsetLeft;
        parent = parent.parentNode;
    }
    offset = {
        positionTop : positionTop,
        positionLeft : positionLeft
    }
    return offset;
}

//获取元素距离某个元素顶部的高度 绝对高度(相对于文档)
function getOffset(element){
    var getBoundClient = element.getBoundingClientRect();
    return {
        offsetTop : getBoundClient.top,
        offsetLeft: getBoundClient.left
    }
}

//插入规则
function insertRule(sheet,selectorText,cssText,position){
    if(sheet.insertRule){
        sheet.insertRule(selectorText + "{" + cssText + "}",position);
    }else if(sheet.addRule){
        sheet.addRule(selectorText, cssText ,position);
    }
}

//移除规则
function deleteRule(sheet,position){
    if(sheet.deleteRule){
        sheet.deleteRule(position);
    }else if(sheet.removeRule){
        sheet.removeRule(position)
    }
}

//获取元素的尺寸,如果有-webkit-box-sizing属性的话，那么它的值就是本身设置的值。
function getElementPix(element){
    var getBoundClient = element.getBoundingClientRect();
    return {
        height : getBoundClient.bottom - getBoundClient.top,
        width  : getBoundClient.right - getBoundClient.left
    }
}

//获取同辈所有元素
function siblings(element){
    var parentNode = element.parentNode,
        elementArray = [],
        elementNode = element;
    if(typeof parentNode.childElementCount != 'undefined'){
        while(element.previousElementSibling != null){
            elementArray.unshift(element.previousElementSibling);
            element = element.previousElementSibling;
        };
        while(elementNode.nextElementSibling != null){
            elementArray.push(elementNode.nextElementSibling);
            elementNode = elementNode.nextElementSibling;
        }
    }else{
        var childNodes = parentNode.childNodes;
        for(var i = 0; i < childNodes.length; i++){
            if(childNodes[i].nodeType != 1 || childNodes[i] == element){
                continue;
            }
            elementArray.push(childNodes[i]);
        }
    }
    return elementArray;
}

//获取同辈上一个兄弟元素节点
function prevElementSibling(element){
    var previousElement = null;
    if(typeof element.previousElementSibling != 'undefined'){
        previousElement = element.previousElementSibling;
    }else{
        try{
            while(element.previousSibling.nodeType != 1){
                element = element.previousSibling;
            }
            previousElement = element.previousSibling;
        }catch(e){
            //
        }
    }
    return previousElement;
}

//获取同辈下一个兄弟元素节点
function nextElementSibling(element){
    var nextElement = null;
    if(typeof element.nextElementSibling != 'undefined'){
        nextElement = element.nextElementSibling;
    }else{
        try{
            while(element.nextSibling.nodeType != 1){
                element = element.nextSibling;
            }
            nextElement = element.nextSibling;
        }catch(e){
            //
        }
    }
    return nextElement;
}

//兼容forEach
if(typeof Array.prototype.forEach != 'function'){
    Array.prototype.forEach = function (fn, context) {
        for(var k = 0, len = this.length; k < len; k++){
            if(typeof fn == 'function' && Object.prototype.hasOwnProperty.call(this,k)){
                fn.call(context, this[k], k, this);
            }
        }
    }
}

//兼容map
if(typeof Array.prototype.map != 'function'){
    Array.prototype.map = function (fn,context) {
        var arr = [];
        if(typeof fn == 'function'){
            for(var k = 0, len = this.length; k < len; k++){
                arr.push(fn.call(context, this[k], k, this)); //fn函数里面没有return返回的话，那么arr就全部是undefined元素
            }
        }
        return arr;
    }
}

//兼容filter
if(typeof Array.prototype.filter != 'function'){
    Array.prototype.filter = function (fn, context) {
        var arr = [];
        if(typeof fn == 'function'){
            for(var k = 0, len = this.length; k < len; k++){
                fn.call(context, this[k], k, this) && arr.push(this[k]);
            }
        }
        return arr;
    }
}

//兼容some
if(typeof Array.prototype.some != 'function'){
    Array.prototype.some = function (fn, context) {
        if(typeof fn == 'function'){
            for(var k = 0, len = this.length; k < len; k++){
                if(!!fn.call(context, this[k], k, this)){
                    return true;
                }
            }
        }
        return false;
    }
}

//兼容every
if(typeof Array.prototype.every != 'function'){
    Array.prototype.every = function (fn, context) {
        if(typeof fn == 'function'){
            for(var k = 0, len = this.length; k < len; k++){
                if(!fn.call(context, this[k], k, this)){
                   return false;
                }
            }
        }
        return true;
    }
}

//兼容indexOf
if(typeof Array.prototype.indexOf != 'function'){
    Array.prototype.indexOf = function (searchElement,fromIndex) {
        var fromIndex = fromIndex * 1 || 0,
            index     = -1;

        for(var k = 0, len = this.length; k < len; k++){
            if(k >= fromIndex && searchElement == this[k]){
                index = k;
                break;
            }
        }
        return index;
    }
}

//兼容lastIndexOf
if(typeof Array.prototype.lastIndexOf1 != 'function'){
    Array.prototype.lastIndexOf1 = function (searchElement,fromIndex) {
        var fromIndex = fromIndex * 1 || 0,
            index     = -1;

        for(var k = this.length; k > 0; k--){
            if(k <= fromIndex && searchElement == this[k]){
                index = k;
                break;
            }
        }
        return index;
    }
}

//兼容reduce
if(typeof Array.prototype.reduce != 'function'){
    Array.prototype.reduce = function (fn, initialValue) {
        var previous = initialValue, k = 0, len = this.length;
        if(typeof initialValue == 'undefined'){
            previous = this[0];
            k        = 1;
        }

        if(typeof fn == 'function'){
            for(k; k < len; k++){
                this.hasOwnProperty(k) && (previous = fn(previous, this[k], k, this));
            }
        }
        return previous;
    }
}

//兼容reduceRight
if(typeof Array.prototype.reduceRight != 'function'){
    Array.prototype.reduceRight = function (fn, initialValue) {
        var previous = initialValue, k = this.length, len = 0;
        if(typeof initialValue == 'undefined'){
            previous = this[k - 1];
            k = k - 2;
        }

        if(typeof fn == 'function'){
            for(k; k > len; k--){
                this.hasOwnProperty(k) && (previous = fn(previous, this[k], k, this));
            }
        }
        return previous;
    }
}

//选择文本并且焦点进入，适用于文本框
function selectText(range, startNum, endNum){
    if(typeof range.setSelectionRange != 'undefined'){ //设置文本选择范围
        //W3C
        range.setSelectionRange(startNum, endNum);
        range.focus();
    }else if(typeof range.createTextRange != 'undefined'){
        //IE
        var range = range.createTextRange(); //创造文本范围
        range.collapse(true);
        range.moveStart('character', startNum);
        range.moveEnd('character', endNum - startNum);
        range.select();
    }
}

//取得想要的文本，适用于文本框
function getSelectText(range){
    if(typeof range.selectionStart != 'undefined'){
        return range.value.substring(range.selectionStart, range.selectionEnd);
    }else if(document.selection){ //document.selection表示当前网页选中内容
        return document.selection.createRange().text;
    }
}

//requestAnimationFrame兼容,像素动画更自然
(function () {
    var lastTime = 0;
    var vendors = ['webkit','moz'];

    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x){
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelAnimationFrame  = window[vendors[x] + 'CancelAnimationFrame'] ||
            window[vendors[x] + 'CancelRequestAnimationFrame'];
    }
    if(!window.requestAnimationFrame){
        window.requestAnimationFrame = function (callback, element) {
            var currTime = (new Date()).getTime();
            var timeToCall = Math.max(0, 16.7 - (currTime - lastTime));
            var id = window.setTimeout(function () {
                callback(currTime + timeToCall);
            },timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        }
    }

    if(!window.cancelAnimationFrame){
        window.cancelAnimationFrame = function (id) {
            clearTimeout(id);
        }
    }
}());

//选择子元素
function findChildElement(parentNode, childNodeName, childSelector,index){
    var childrenNode = parentNode.getElementsByTagName(childNodeName);
    var elementArr = [];
    if(childSelector != undefined){
        for(var i = 0, len = childrenNode.length; i < len; i++){
                if(childrenNode[i].className == childSelector){
                    elementArr.push(childrenNode[i]);
                }
        }
        return elementArr[index];
    }else{
        for(var i = 0, len = childrenNode.length; i < len; i++){
                elementArr.push(childrenNode[i]);
        }
        return elementArr[index];
    }
}


var $Event = function (el) {
    return new _$Event(el);
}
var _$Event = function (el) {
    this.el = (el && el.nodeType == 1) ? el : document;
}
_$Event.prototype = {
    constructor : this,
    addEvent    : function (type, fn, capture) {
        var el = this.el;

        if(window.addEventListener){
            el.addEventListener(type, fn, capture);
            var ev = document.createEvent('HTMLEvents');
            ev.initEvent(type, capture || false, false);

            if( !el['ev' + type] ){
                el['ev' + type] = ev;
            }
        }else if(window.attachEvent){
            el.attachEvent('on' + type, fn);

            if(isNaN(el['cu' + type])){
                el['cu' + type] = 0;
            }

            var fnEv = function (event) {
                if(event.propertyName == 'cu' + type){
                    fn.call(el);
                }
            }
            el.attachEvent('onpropertychange', fnEv);

            if( !el['ev' + type] ){
                el['ev' + type] = [fnEv];
            }else{
                el['ev' + type].push(fnEv);
            }
        }
        return this;
    },
    fireEvent : function (type) {
        var el = this.el;

        if(typeof type === 'string'){
            if(document.dispatchEvent){
                if(el['ev' + type]){
                    el.dispatchEvent(el['ev' + type]);
                }
            }else if(document.attachEvent){
                el['cu' + type]++;
            }
        }
        return this;
    },
    removeEvent : function (type, fn, capture) {
        var el = this.el;

        if(window.removeEventListener){
            el.removeEventListener(type, fn, capture || false);
        }else if(document.attachEvent){
            el.detachEvent('on' + type, fn);
            var arrEv = el['ev' + type];
            if(arrEv instanceof Array){
                for(var i = 0; i < arrEv.length; i+=1){
                    el.detachEvent('onpropertychage',arrEv[i]);
                }
            }
        }
        return this;
    }
}
































