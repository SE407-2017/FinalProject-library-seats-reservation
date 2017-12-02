<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title>Failed</title>

    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">

    <link rel="stylesheet" href="https://cdn.bootcss.com/amazeui/2.7.2/css/amazeui.flat.min.css">




    <style>
        html, body {
            height: 100%;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
        }

        .id {
            font-size: 40px;
        }

        .name {
            font-size: 36px;
            font-family: 华文中宋, 微软雅黑;
        }

        .get {
            background: #1E5B94;
            color: #fff;
            text-align: center;
            padding: 100px 0;
        }

        .get-title {
            font-size: 200%;
            border: 2px solid #fff;
            padding: 20px;
            display: inline-block;
        }

        .get-btn {
            background: #fff;
        }

        .detail {
            background: #fff;
        }

        .detail-h2 {
            text-align: center;
            font-size: 150%;
            margin: 40px 0;
        }

        .detail-h3 {
            color: #1f8dd6;
        }

        .detail-p {
            color: #7f8c8d;
        }

        .detail-mb {
            margin-bottom: 30px;
        }

        .hope {
            background: #0bb59b;
            padding: 50px 0;
        }

        .hope-img {
            text-align: center;
        }

        .hope-hr {
            border-color: #149C88;
        }

        .hope-title {
            font-size: 140%;
        }

        .about {
            background: #fff;
            padding: 40px 0;
            color: #7f8c8d;
        }

        .about-color {
            color: #34495e;
        }

        .about-title {
            font-size: 180%;
            padding: 30px 0 50px 0;
            text-align: center;
        }

        .footer {
            width: 100%;
            height:150px;   /* footer的高度一定要是固定值*/
            position:absolute;
            bottom:0px;
            left:0px;
        }

        .footer p {
            color: #7f8c8d;
            margin: 0;
            padding: 15px 0;
            text-align: center;
            /* background: #2d3e50; */
        }

        .hitokoto_from {
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="get">
    <div class="am-g">
        <div class="am-u-lg-12">
            <h1 class="get-title">入座失败</h1>

            <p>{{ $msg }}</p>
        </div>
    </div>
</div>


<div class="footer">
    <div class="am-g am-container">
        <div class="am-u-lg-12">
            <p>{{ $hitokoto->hitokoto }}<br/>
                ——<span class="hitokoto_from">{{ $hitokoto->from }}</span></p>
        </div>
    </div>
</div>


</body>
</html>