<?php
use yii\helpers\Url;
?>
<input type="button" id="btn" value="选择图片">
此为公众号的垃圾分类显示的测试版本，请知悉

<div id="pic" style="width: 50%;height: 50%;">

    <div id="text" ></div>
</div>
<div id="result">

</div>

<script src="http://www.yunche168.com/frontend/web/cloudcar/js/jquery-2.1.4.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: 'wx790a5eeaad8aa436', // 必填，公众号的唯一标识
        timestamp: <?php echo $alxg_sign['timestamp'] ?> , // 必填，生成签名的时间戳
        nonceStr: '<?php echo $alxg_sign['noncestr'] ?>', // 必填，生成签名的随机串
        signature: '<?php echo $alxg_sign['signature'] ?>',// 必填，签名，见附录1
        jsApiList: [
            "chooseImage",
            "uploadImage",
            "downloadImage",
            'checkJsApi',
            'chooseWXPay',
            'checkJsApi',
            'openLocation',//使用微信内置地图查看地理位置接口
            'getLocation' //获取地理位置接口
        ]
    });
    $(function(){
        $("#btn").click(function(){
            var images = {
                localId: [],
                serverId: []
            };
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    // alert(111);
                    images.localId = res.localIds;
                    // alert('已选择 ' + res.localIds.length + ' 张图片');

                    if (images.localId.length == 0) {
                        alert('请先使用 chooseImage 接口选择图片');
                        return;
                    }
                    var i = 0, length = images.localId.length;
                    images.serverId = [];
                     // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    function upload() {
                        //图片上传
                        wx.uploadImage({
                            localId: images.localId[i],
                            success: function(res) {
                                // alert(333);
                                i++;
                                images.serverId.push(res.serverId);
                                // alert(444);
                                //图片上传完成之后，进行图片的下载，图片上传完成之后会返回一个在腾讯服务器的存放的图片的ID--->serverId
                                wx.downloadImage({
                                    serverId: res.serverId, // 需要下载的图片的服务器端ID，由uploadImage接口获得
                                    isShowProgressTips: 1, // 默认为1，显示进度提示
                                    success: function (res) {
                                        // alert(555);
                                        var localId = res.localId; // 返回图片下载后的本地ID
                                        // alert(6);
                                        //通过下载的本地的ID获取的图片的base64数据，通过对数据的转换进行图片的保存
                                        wx.getLocalImgData({
                                            localId: localId, // 图片的localID
                                            success: function (res) {
                                                var localData = res.localData; // localData是图片的base64数据，可以用img标签显示
                                                 $("#pic").append("<img src='data:image/jpg;base64,"+localData+"'>");
                                                //通过ajax来将base64数据转换成图片保存在本地

                                                $.post('/baidu.php',{ localData: localData},function(res){
                                                    // alert('日志上传成功');
                                                    // alert(res);
                                                    // var  res = JSON.parse(res);
                                                    // alert(res);


                                                    var  result = res.result;
                                                    // alert('result');
                                                    // alert(result);

                                                    var item = result[0];
                                                    // console.log(item);
                                                    // alert('item.keyword');
                                                    // alert();
                                                    $.ajax({
                                                        type:"GET",
                                                        url:"http://api.tianapi.com/txapi/lajifenlei/?&key=eebf833f1af449396c616f2d6fca780b&word="+item.keyword,
                                                        dataType:"json",
                                                        success:function(data){
                                                            // alert(22);
                                                            // 0为可回收、1为有害、2为厨余(湿)、3为其他(干)
                                                            if(data["newslist"][0]["type"]==0){
                                                                var type='可回收垃圾'
                                                            }
                                                            if(data["newslist"][0]["type"]==1){
                                                                var type='有害垃圾'
                                                            }
                                                            if(data["newslist"][0]["type"]==2){
                                                                var type='湿垃圾'
                                                            }
                                                            if(data["newslist"][0]["type"]==3){
                                                                var type='干垃圾'
                                                            }
                                                            alert('此最有可能为'+item.keyword+',分类为'+type);
                                                            var txapi="<ul>";
                                                            for(var i=0; i<10; i++)
                                                            {
                                                                txapi+="<li>"+data["newslist"][i]["explain"]+"</li>";
                                                                txapi+="<li>"+data["newslist"][i]["contain"]+"</li>";
                                                                txapi+="<li>"+data["newslist"][i]["tip"]+"</li>";
                                                                // alert(data["newslist"][i]["title"]);
                                                            }
                                                            txapi+="</ul>";
                                                            $('#result').html(txapi);


                                                        }
                                                    },"json");
                                                    },"json"
                                                );

                                                // $.ajax({
                                                //     url: "/baidu.php",
                                                //     type: "post",
                                                //     async: "false",
                                                //     dataType: "json",
                                                //     data: {
                                                //         localData: localData,
                                                //     },
                                                //     success: function (res) {
                                                //         alert('日志上传成功');
                                                //         var data = res.data;
                                                //
                                                //         // alert(mydata);
                                                //         var  result = data.result;
                                                //             var item = result[0];
                                                //            console.log(item);
                                                //            alert('item.keyword');
                                                //            alert(item.keyword);
                                                //          console.log(item.keyword);
                                                //     },
                                                //     error: function (XMLHttpRequest, textStatus, errorThrown) {
                                                //         alert(errorThrown);
                                                //     },
                                                // });

                                            }
                                        });
                                    }
                                });
                                if (i < length) {
                                    upload();
                                }
                            },
                            fail: function(res) {
                                alert(JSON.stringify(res));
                            }
                        });
                    }
                    upload();
                },
                error:function (res) {
                    alert(res);
                }

            });
        });
    });

</script>
 
