<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo \Yii::$app->params['backendTitle']?></title>
    <link href="/backend/web/css/bootstrap5.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-3">
        <form method="post" action="">

            <div class="form-group">
                <label>用户名：</label>
                <input type="text" class="input" placeholder="请输入用户名" value="" name="username">
            </div>
            <div class="form-group">
                <label>密&nbsp;码：</label>

                <input type="password" class="input" placeholder="请输入密码" value="" name="password">
            </div>
            <div class="form-group">
                <label>验证码：</label>
                <input type="text" class="input" placeholder="请输入验证码" value="" name="yzcode">
                <span><img  title="点击刷新" src="/backend/web/site/yzcode.html" align="absbottom" onclick="this.src='/backend/web/site/yzcode.html?'+Math.random();"></img></span>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
        </form>
    </div>

</body>
</html>