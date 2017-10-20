{{--Created by PhpStorm.--}}
{{--User: hebin--}}
{{--Date: 2017-09-30--}}
{{--Time: 15:58--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>个人中心</title>
</head>
<body>
<h2>你好，{{ $user_info['true_name'] }}。</h2>

@if(App\Reservations::where('jaccount', $user_info['jaccount'])->first() != NULL)
    <a href="{{  url('/reserve/detail') }}">查看预约详情</a>
@endif
</body>
</html>
