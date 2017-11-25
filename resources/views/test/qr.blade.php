<html>
<head>
    <title>generate seat qr</title>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script src="{{ url('/reserve/js/base64.js') }}"></script>
</head>
<body>
	<div class="row" style="padding-left: 10px;">
		<br>
		<div style="font-size: 2.0em; font-weight: 200; font-family: 微软雅黑; padding-left: 20px;">图书馆预约系统</div>
		<br>
	</div>
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
						data-target="#My-navbar-collapse">
					<span class="sr-only">切换导航</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">相关链接</a>
			</div>
			<div class="collapse navbar-collapse" id="My-navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="https://library.shinko.love">系统主页</a></li>
					<li><a href="http://www.lib.sjtu.edu.cn/">上海交大图书馆</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div style="font-size: 1.1em; font-weight: 300; font-family: 微软雅黑; padding-left: 50px;"><br>
			<h4>欢迎来到图书馆预约系统</h4>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请输入目标座位的楼层号，桌号和座位号以生成二维码。<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请扫描生成的二维码以确认到达或离开。
			<br>
	</div>
	<br><br>
	<div class="input-group" style="font-family: 华文中宋, 微软雅黑; font-size: large; width: 240px; padding-left: 20px;">
		<span class="input-group-addon" id="sizing-addon1">楼层号</span>
		<input id="floor" type="text" class="form-control" placeholder="Floor" aria-describedby="sizing-addon1">
	</div>
	<br>
	<div class="input-group" style="font-family: 华文中宋, 微软雅黑; font-size: large; width: 240px; padding-left: 20px;">
		<span class="input-group-addon" id="sizing-addon1">桌子号</span>
		<input id="table" type="text" class="form-control" placeholder="Table" aria-describedby="sizing-addon2">
	</div>
	<br>
	<div class="input-group" style="font-family: 华文中宋, 微软雅黑; font-size: large; width: 240px; padding-left: 20px;">
		<span class="input-group-addon" id="sizing-addon1">座位号</span>
		<input id="seat" type="text" class="form-control" placeholder="Seat" aria-describedby="sizing-addon3">
	</div>
	<br>
    <div style="font-family: 华文中宋, 微软雅黑; font-size: large; padding-left: 100px;"><button onclick="generate()">生成</button></div>
    <div id="qrcode"></div>
</body>
<script>
    function generate() {
        var data = {floor: Number($("#floor").val()), table: Number($("#table").val()), seat: Number($("#seat").val())};
        $('#qrcode').val();
        $('#qrcode').qrcode('{{ url("/wechat/inSeat") }}/' + BASE64.encoder(JSON.stringify(data)));
    }

</script>
</html>
