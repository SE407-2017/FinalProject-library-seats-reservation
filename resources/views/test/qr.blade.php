<html>
<head>
    <title>generate seat qr</title>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script src="{{ url('/reserve/js/base64.js') }}"></script>
</head>
<body>
    <div>
        楼层: <input id="floor" />
    </div>
    <div>
        桌号: <input id="table" />
    </div>
    <div>
        座位: <input id="seat" />
    </div>
    <div><button onclick="generate()">生成</button></div>
    <div id="qrcode"></div>
</body>
<script>
    function generate() {
        var data = {floor: $("#qrcode").val(), table: $("#table").val(), seat: $("#seat").val()};
        $('#qrcode').qrcode('{{ url("/wechat/inSeat") }}/' + BASE64.encoder(JSON.stringify(data)));
    }

</script>
</html>