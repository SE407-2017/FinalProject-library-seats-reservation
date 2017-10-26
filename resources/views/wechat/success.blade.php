<!DOCTYPE html>
<html>
<head>
    <title>Success</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
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

    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">Success.<br/></div>

        <div class="id">{{ $JaccountID }}</div>
        <div class="name">{{ $JaccountUserName }}</div>
    </div>
</div>
</body>
</html>