
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>中奖结果导出</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/floating-labels/">

    <!-- Bootstrap core CSS -->
    <link href="/frontend/web/shandong-renbao-hero/css/bootstrap.min.css" rel="stylesheet">
    <link href="/frontend/web/shandong-renbao-hero/css/bootstrap-datetimepicker.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/frontend/web/shandong-renbao-hero/css/floating-labels.css" rel="stylesheet">
    <script src="/frontend/web/shandong-renbao-hero/js/jquery-2.2.0.min.js"  ></script>
    <script src="/frontend/web/shandong-renbao-hero/js/bootstrap.min.js"  ></script>
    <script src="/frontend/web/shandong-renbao-hero/js/datetime.js"  ></script>
</head>

<body>
<form class="form-signin" action="download.html" method="get">


    <div class="form-label-group">
        <input type="text" id="begin" name="begin" class="form-control" placeholder="开始日期" required>
        <label for="begin">开始日期</label>
    </div>

    <div class="form-label-group">
        <input type="text" id="last" name="last" class="form-control" placeholder="结束日期" required>
        <label for="last">结束日期</label>
    </div>


    <button class="btn btn-lg btn-primary btn-block" type="submit">提交</button>

</form>
<script type="text/javascript">
    $(function () {
        $("#begin").datetimepicker({
            minView: "month",
            format: "yyyy-mm-dd"
        });
        $("#last").datetimepicker({
            format: "yyyy-mm-dd 23:59:59"
        });
    });


</script>
</body>
</html>
