var all = fn('box')
// console.log(all);
var screen = all.children[0];
var arr = all.children[1];
var ul = screen.children[0];
var ol = screen.children[1];
var left = arr.children[0];
var right = arr.children[1];
var imagesWidth = screen.offsetWidth;
// console.log(imagesWidth);
// 先获取需要使用的元素
for (var i = 0; i < ul.children.length; i++) {
    var li = document.createElement('li');
    ol.appendChild(li);
    li.innerText = i + 1;
    li.onclick = links;
    li.setAttribute('index', i);
    if (i === 0) {
        li.className = 'current'
    }
    // 遍历循环给ol里面添加li然后给li注册事件并且添加自定义属性，并且设置对应的12345并且给第一个高亮
}
// var dex = ol.children[0];
function links() {
    /* dex.className = ''
    this.className = 'current';
    dex = this; */
    // 这是老师说的方法，使用于tab切换还不错 但是当控制条件有两个的时候就会出现bug 循环清零最为合理
    for (var i = 0; i < ol.children.length; i++) {
        ol.children[i].className = ''
    }
    this.className = 'current';

    index = parseInt(this.getAttribute('index'))
    animate(ul, -index * imagesWidth)
}
// 克隆第一张图片放在最后
var li = document.createElement('li');
ul.appendChild(li);
var img = document.createElement('img');
li.appendChild(img);
img.src = ul.children[0].children[0].src;
// console.log(img.src);

// 鼠标移入的时候显示
all.onmouseenter = function () {

    arr.style.display = 'block';
    clearInterval(Timeout);
}
// 鼠标移出的隐藏
all.onmouseleave = function () {
    arr.style.display = 'none';
    /* if (Timeout) {
        clearInterval(Timeout);
        Timeout = null;
    } */
    Timeout = setInterval(function () {
        if (index < ul.children.length - 2) {
            index++;
            ol.children[index].click();
        } else if (index == ul.children.length - 2) {
            animate(ul, -(ul.children.length - 1) * imagesWidth);
            ol.children[ol.children.length - 1].className = '';
            ol.children[0].className = 'current';
            index++;
            // console.log(1);
        } else if (index == ul.children.length - 1) {
            // console.log(2);
            ul.style.left = 0 + 'px';
            // 瞬间过度到第一张图 因为是同一张图片路径 切换的时候没有变化
            ol.children[0].className = '';
            // 索引值重新赋值，开始新的循环
            index = 1;
            ol.children[index].click();
        }
    }, 1500)
}



// 右边的按钮
var index = 0;
right.onclick = function () {

    if (index < ul.children.length - 2) {
        index++;
        ol.children[index].click();
        // 对应的索引值来对应ol里面的按钮 通过按钮的点击事件来达到图片滚动和按钮同步的效果
    } else if (index == ul.children.length - 2) {
        animate(ul, -(ul.children.length - 1) * imagesWidth);
        ol.children[ol.children.length - 1].className = '';
        ol.children[0].className = 'current';
        index++;
        // 因为下面没有对应的按钮了 这个时候就要手动设置动画跳转到最后一张，然后取消下面按钮的最后一个高亮，并且要让下面按钮第一个高亮 

    } else if (index == ul.children.length - 1) {
        ul.style.left = 0 + 'px';
        ol.children[0].className = '';
        index = 1;
        ol.children[index].click();
        // 从第六张瞬间跳到第一张，并且重新赋值index 对应的按钮2的过度动画
    }
}
left.onclick = function () {
    if (index > 0) {
        index--
        ol.children[index].click();
    } else if (index === 0) {
        // animate(ul,-(ul.children.length-1)*imagesWidth);
        ul.style.left = -(ul.children.length - 1) * imagesWidth + 'px';
        index = ol.children[ol.children.length - 1];
        ol.children[index].click();
        // ol.children[0].className = '';
    }
}

var Timeout = setInterval(function () {
    if (index < ul.children.length - 2) {
        index++;
        ol.children[index].click();
    } else if (index == ul.children.length - 2) {
        animate(ul, -(ul.children.length - 1) * imagesWidth);
        ol.children[ol.children.length - 1].className = '';
        ol.children[0].className = 'current';
        index++;
        console.log(1);
    } else if (index === ul.children.length - 1) {
        console.log(2);
        ul.style.left = 0 + 'px';
        ol.children[0].className = '';
        index = 1;
        ol.children[index].click();
    }
}, 3000)
