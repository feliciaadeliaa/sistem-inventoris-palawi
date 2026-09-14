<html>
<head>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; text-align: center; }
    .label {
        width: 220px;
        padding: 12px;
        border: 1px solid #333;
        margin: 8px auto;
        overflow: hidden;
    }
    .logo-wrap {
        width: 100%;
        text-align: center;
        margin-bottom: 8px;
    }
    .logo-wrap img {
        width: 32px;
    }
    .qr-wrap {
        width: 100%;
        text-align: center;
        margin-bottom: 8px;
    }
    .qr-wrap img {
        width: 180px;
        max-width: 100%;
    }
    .nomor { font-size: 12px; font-weight: bold; }
    .footer { font-size: 9px; margin-top: 4px; color: #333; }
</style>
</head>
<body>
    <div class="label">
        <div class="logo-wrap">
            <img src="{{ public_path('images/logo/logo-palawi.png') }}">
        </div>
        <div class="qr-wrap">
            <img src="{{ $qrData }}">
        </div>
        <div class="nomor">{{ $item->nomor_asset_tetap ?? '-' }}</div>
        <div class="footer">Asset milik PT Perhutani Alam Wisata Risorsis</div>
    </div>
</body>
</html>