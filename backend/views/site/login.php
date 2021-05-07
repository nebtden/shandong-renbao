<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php echo \Yii::$app->params['backendTitle']?></title>
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        html{
            height: 100%;
        }
        body{
            height: 100%;
            text-align: center;
        }
        div{
            margin: 0 auto;
        }
        .clearfix:after{
            display: block;
            height: 0;
            line-height: 0;
            visibility: hidden;
            content: '';
            clear: both;
        }
        .contain{
            width: 100%;
            height: 100%;
            background:-webkit-radial-gradient(center center,cover,#b7e3ff 0,#093281 90%);
            background:-moz-radial-gradient(center center,cover,#b7e3ff 0,#093281 90%);
            overflow: hidden;
        }
        .loginDiv{
            width: 742px;
            height: 359px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -webkit-border-image: url(<?php echo Yii::$app->params['baseUrl'];?>images/bg.png) 40 fill/40px stretch;
            -moz-border-image: url(<?php echo Yii::$app->params['baseUrl'];?>images/bg.png) 40 fill/40px stretch;
            text-align: left;
            margin-top: 250px;
        }
        @-moz-document url-prefix() {
            .loginDiv{
                padding: 40px;
            }
        }
        .loginC{
            width: 100%;
            height: 100%;
            background: #FFFFFF;
            overflow: hidden;
        }
        .userTitle{
            width: auto;
            height: 53px;
            background: url("<?php echo Yii::$app->params['baseUrl'];?>images/yhdl.png") 131px no-repeat;
            margin-top: 15px;
        }
        .formConfirm{
            width: auto;
            margin-left: 135px;
            margin-top: 15px;
        }
        .formLeft{}
        .formRight{
            margin-top: 20px;
            margin-left: 80px;
        }
        .formRight .but{
            width: 129px;
            height: 49px;
            background: url("<?php echo Yii::$app->params['baseUrl'];?>images/denglu.png") no-repeat;
            border: none;
        }
        .text{
            height: 30px;
            line-height: 30px;
            margin-top: 10px;
        }
        .text label{
            display: block;
            float: left;
            width: 70px;
            text-align: right;
            margin-right: 10px;
            color: #2578a4;
        }
        .text input{
            display: block;
            float: left;
            width: 200px;
            height: 30px;
            border: 1px solid #2578a4;
        }
        .text:nth-child(3) input{
            width: 100px;
        }
        .text:nth-child(3) span{
            background: red;
            display: block;
            float: left;
            margin-left: 10px;
        }
        .text:nth-child(3) span img{
            width: 90px;
            height: 32px;
            line-height: 32px;
            vertical-align: top;
        }
    </style>
    <!--[if lte IE 9]>
    <style>
        .contain{
            background: #093281;
        }
        .loginDiv{
            background: url("images/bg.png") no-repeat;
            overflow: hidden;
        }
        .loginC{
            width: 662px;
            height: 279px;
            margin-top: 40px;
        }
    </style>
    <![endif]-->
</head>
<body>
   
    <div class="contain">
    <form  action="" method="post">
        <div class="loginDiv">
                <div class="loginC">
                    <div class="userTitle"></div>
                    <div class="formConfirm clearfix">
                        <div class="formLeft">
                            <div class="text">
                                <label>用户名：</label>
                                
                                <input type="text" class="input" placeholder="请输入用户名" value="" name="username">
                            </div>
                            <div class="text">
                                <label>密&nbsp;码：</label>
                                
                                <input type="password" class="input" placeholder="请输入密码" value="" name="password">
                            </div>
                            <div class="text">
                                <label>验证码：</label>
                                <input type="text" class="input" placeholder="请输入验证码" value="" name="yzcode">
			                    <span><img  title="点击刷新" src="/backend/web/site/yzcode.html" align="absbottom" onclick="this.src='/backend/web/site/yzcode.html?'+Math.random();"></img></span>
                            </div>
                        </div>
                        <div class="formRight">
                            <button type="submit" class="but"></button>
                        </div>
                    </div>
                </div>
        </div>
      </form>
    </div>
   
</body>
</html>