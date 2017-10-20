<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>查看预约</title>
    <style>
        table {
            margin: auto;
            padding: 10px;
            border: 1px solid black;
        }
        table::after {
            position: absolute;
            content: "已到末尾";
            font-size: smaller;
            font-style: italic;
            align-content: center;
            left: calc(50% - 25px);
            margin-top: 15px;
        }
        td, th {
            text-align: center;
            margin: auto;
            width: 14.25%;
        }
    </style>
</head>
<body>
<h2>你好，{{ $user_info['true_name'] }}。</h2>
<h3>我的预约：</h3>
<table class="table">
    <thead>
    <tr>
        <th>姓名</th>
        <th>预约时间</th>
        <th>楼层</th>
        <th>桌子</th>
        <th>座位</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_reservations as $reservation)
        <tr>
            <td>{{ $reservation->name }}</td>
            <td>{{ $reservation->arrive_at }}</td>
            <td>{{ $reservation->floor_id }}</td>
            <td>{{ $reservation->table_id }}</td>
            <td>{{ $reservation->seat_id }}</td>
            @if($reservation->is_arrived == 0 && $reservation->is_left == 1)
                <td>已经失效</td>
            @elseif($reservation->is_arrived == 0)
                <td>有效</td>
            @elseif($reservation->is_left == 1)
                <td>已经离开</td>
            @else
                <td>已经到达，尚未离开</td>
            @endif
            @if($reservation->is_arrived == 0 && $reservation->is_left == 0)
                <td><button onclick="alert('功能尚未实现，请稍后再试')">取消</button></td>
            @else
                <td></td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
