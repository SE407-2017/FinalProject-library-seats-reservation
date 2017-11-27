<html>
<head>
    <title>generate seat qr</title>
    <script src="{{ url('reserve/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script src="{{ url('/reserve/js/base64.js') }}"></script>
</head>
<body>
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
