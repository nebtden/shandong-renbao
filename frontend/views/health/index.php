<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>全民战疫  邀你闯关</title>
		<link rel="stylesheet" type="text/css" href="/static/health/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="/static/health/css/animate.min.css" />
		<link rel="stylesheet" type="text/css" href="/static/health/css/index.css" />
	</head>

	<body>
  <div style="display:none"><img src='/static/health/img/1-1.jpg' /></div>
    <div id="player">
			<audio class="media-audio" src="/static/health/js/yinyue.mp3" loop id="uploadMusic"></audio>
			<img src="/static/health/img/Music.png" class="musicLogo off"/>
		</div>
		<div class="index">
			<div class="box One">
				<div class="title animated bounce"><img src="/static/health/img/1-2.png"></div>
				<div class="button animated pulse"><img src="/static/health/img/1-3.png"></div>
			</div>
			<div class="box Two">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInLeft">
					<img>
					<div class="A" onclick="answer('1', 'A')"></div>
					<div class="B" onclick="answer('1', 'B')"></div>
					<div class="C" onclick="answer('1', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
			</div>
			<div class="box Three">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInRight">
					<img>
					<div class="A" onclick="answer('2', 'A')"></div>
					<div class="B" onclick="answer('2', 'B')"></div>
					<div class="C" onclick="answer('2', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
			</div>
			<div class="box Four">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInLeft">
					<img>
					<div class="A" onclick="answer('3', 'A')"></div>
					<div class="B" onclick="answer('3', 'B')"></div>
					<div class="C" onclick="answer('3', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
			</div>
			<div class="box Five">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInRight">
					<img>
					<div class="A" onclick="answer('4', 'A')"></div>
					<div class="B" onclick="answer('4', 'B')"></div>
					<div class="C" onclick="answer('4', 'C')"></div>
				</div>
			</div>
			<div class="box Six">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInLeft">
					<img>
					<div class="A" onclick="answer('5', 'A')"></div>
					<div class="B" onclick="answer('5', 'B')"></div>
					<div class="C" onclick="answer('5', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
				<div class="cao"><img src="/static/health/img/11-5.png"></div>
			</div>
			<div class="box Seven">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInRight">
					<img>
					<div class="A" onclick="answer('6', 'A')"></div>
					<div class="B" onclick="answer('6', 'B')"></div>
					<div class="C" onclick="answer('6', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
			</div>
			<div class="box Eight">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInLeft">
					<img>
					<div class="A" onclick="answer('7', 'A')"></div>
					<div class="B" onclick="answer('7', 'B')"></div>
					<div class="C" onclick="answer('7', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
				<div class="cao"><img src="/static/health/img/11-5.png"></div>
			</div>
			<div class="box Nine">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInRight">
					<img>
					<div class="A" onclick="answer('8', 'A')"></div>
					<div class="B" onclick="answer('8', 'B')"></div>
					<div class="C" onclick="answer('8', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
				<div class="cao"><img src="/static/health/img/11-5.png"></div>
			</div>
			<div class="box Ten">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInLeft">
					<img>
					<div class="A" onclick="answer('9', 'A')"></div>
					<div class="B" onclick="answer('9', 'B')"></div>
					<div class="C" onclick="answer('9', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
				<div class="cao"><img src="/static/health/img/11-5.png"></div>
			</div>
			<div class="box Eleven">
				<div class="brand animated swing"><img></div>
				<div class="option animated bounceInRight">
					<img>
					<div class="A" onclick="answer('10', 'A')"></div>
					<div class="B" onclick="answer('10', 'B')"></div>
					<div class="C" onclick="answer('10', 'C')"></div>
				</div>
				<div class="img animated pulse"><img></div>
				<div class="cao"><img src="/static/health/img/11-5.png"></div>
			</div>
			<div class="box Twelve">
				<div class="title animated bounce"><img src="/static/health/img/12-2.png"></div>
			</div>
			<div class="box Thirteen">
				<div class="title animated bounce"><img src="/static/health/img/13-2.png"></div>
				<div class="img animated bounce"><img src="/static/health/img/13-3.png"></div>
				<div class="button animated pulse"><img src="/static/health/img/13-4.png"></div>
			</div>
			<div class="box Fourteen">
				<img class="img1" >
				<img class="img2 animated bounceIn" >
				<img class="img3" >
  				<div class="img4">
  					<img>
  					<input type="text" name="name" id="name"/>
            <input type="text" name="gender" id="gender">
  					<input type="text" name="phone" id="phone"/>
  					<input type="text" name="address" id="address"/>
  				</div>
				<div class="button"><img ></div>
			</div>
			<div class="box Fifteen">
				<img class="img1 animated bounceIn" >
				<img class="img3 animated bounceIn" >
				<img class="img2 animated bounceInLeft" >
				<img class="img4 animated bounceInRight" >
			</div>
		</div>
		<script src="/static/health/js/fontSize_rem.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/health/js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
		<script>             
			var pageIndex = 0
			function showPage(index) {
				$('.box').hide()
				var brandSrc = '/static/health/img/' + (index + 1) +'-4.png'
				var optionSrc = '/static/health/img/' + (index + 1) +'-3.png'
				var imgSrc = '/static/health/img/' + (index + 1) +'-2.png'
				$('.box').eq(index).find('.brand img').attr("src", brandSrc);
				$('.box').eq(index).find('.option img').attr("src", optionSrc);
				$('.box').eq(index).find('.img img').attr("src", imgSrc);
				$('.box').eq(index).show()
			}
			var result =  function(type) {
				$('.box').hide()
				if(type == '1'){           
					$('.Twelve').show()
					setTimeout(function(){
						pageIndex++
						showPage(pageIndex)
					},1000)
				}else if(type == '2'){   
					$('.Thirteen').show()
				}else if(type == '3'){
					$('.Fourteen').show()
		    }
			}
			function answer(key, value) {
				switch (key) {
					case '1':
						if (value == 'C') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '2':
						if (value == 'C') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '3':
						if (value == 'B') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '4':
						if (value == 'C') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '5':
						if (value == 'B') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '6':
						if (value == 'C') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '7':
						if (value == 'B') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '8':
						if (value == 'C') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '9':
						if (value == 'B') {
							result('1')
						} else {
							result('2')
						}
						break;
					case '10':
						if (value == 'C') {
							result('3')
						} else {
							result('2')
						}
						break;
					default:
						break;
				}
			}
			$(function() {
        var uploadMusic = $("#uploadMusic")[0]; /*jquery对象转换成js对象*/
				var musicNum = 0
				$("#player").bind("click",function () {
					if (uploadMusic.paused){
						$('.musicLogo').removeClass('off').addClass('on');
						uploadMusic.play();
					}else {
						$('.musicLogo').removeClass('on').addClass('off')
						uploadMusic.pause();
					}
				});
				
				// document.body.addEventListener('mousedown', function(){
				// vdo.muted = false;
				// }, false);
				
				// var e = document.createEvent("MouseEvents");
				// e.initEvent("click", true, true);
				// document.getElementById("player").dispatchEvent(e);

				$('.One .button').click(function() {
					$('.musicLogo').removeClass('off').addClass('on');
					uploadMusic.play();
					pageIndex = 1
					showPage(pageIndex)
				})
				$('.One .button').click(function() {
					pageIndex = 1
					showPage(pageIndex)
				})
				$('.Twelve .button').click(function() {
					pageIndex++
					showPage(pageIndex)
				})
				$('.Thirteen .button').click(function() {
					showPage(pageIndex)
				})
				$('.Fourteen .button').click(function(){
          $.ajax({
            type: 'POST',
            url: "http://health.yunche168.com/db.php",
            data:{                       
              name:$("#name").val(), 
              gender:$("#gender").val(),
              phone:$("#phone").val(),
              address:$("#address").val(),
            },
            success: function(data){
            data = JSON.parse(data);
            if(data.code==200){
              alert(data.message);
              $('.box').hide()
              $('.Fifteen').show()
    					$('.Fifteen .img1').attr("src", '/static/health/img/15-2.png');
    					$('.Fifteen .img2').attr("src", '/static/health/img/15-3.png');
    					$('.Fifteen .img3').attr("src", '/static/health/img/15-4.png');
    					$('.Fifteen .img4').attr("src", '/static/health/img/15-5.png');
            }
            if(data.code==500){
             alert(data.message);
            }
            },
          })
				})
				setTimeout(function(){
					$('.Fourteen .img1').attr("src", '/static/health/img/14-2.png');
				},5000)
				setTimeout(function(){
					$('.Fourteen .img2').attr("src", '/static/health/img/14-3.png');
				},10000)
				setTimeout(function(){
					$('.Fourteen .img3').attr("src", '/static/health/img/14-4.png');
				},15000)
				setTimeout(function(){
					$('.Fourteen .img4 img').attr("src", '/static/health/img/14-5.png');
					$('.Fourteen .button img').attr("src", '/static/health/img/14-6.png');
				},20000)
			})
		</script>
  <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
  <script type="text/javascript">
      $(function(){
          var s_title = '全民战疫 邀你闯关';
          var s_desc = '（测试版）趣味答题，通关就送奖品，还不来试试!';
          var s_imgUrl = 'http://www.yunche168.com/static/health/img/1-1.jpg';
          var s_link = 'http://www.yunche168.com/frontend/web/health/index.html';
          wx.config({
              debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
              appId: '<?php echo $alxg_sign['appId']; ?>', // 必填，公众号的唯一标识
              timestamp: <?php echo $alxg_sign['timestamp'] ?> , // 必填，生成签名的时间戳
              nonceStr: '<?php echo $alxg_sign['noncestr'] ?>', // 必填，生成签名的随机串
              signature: '<?php echo $alxg_sign['signature'] ?>',// 必填，签名，见附录1
              jsApiList: [
                  'onMenuShareTimeline',
                  'onMenuShareAppMessage'
              ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
          });
          wx.ready(function () {
              wx.onMenuShareTimeline({
                  title: s_title, // 分享标题
                  imgUrl: s_imgUrl, // 分享图标
                  link: s_link,
                  success: function () {
                  },
                  cancel: function () {
                  }
              });
              wx.onMenuShareAppMessage({
                  title: s_title, // 分享标题
                  desc: s_desc, // 分享描述
                  link: s_link,
                  imgUrl: s_imgUrl, // 分享图标
                  type: '', // 分享类型,music、video或link，不填默认为link
                  dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                  success: function () {
                  },
                  cancel: function () {
                  }
              });
          });
      });
  </script>
	</body>

</html>
