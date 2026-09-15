<html>
<head>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; }

    .page {
        page-break-after: always;
    }
    .page:last-child {
        page-break-after: auto;
    }

    table.grid {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table.grid td {
        width: 33.33%;
        text-align: center;
        vertical-align: top;
        padding: 6px;
    }

    .label {
        border: 1px solid #333;
        padding: 10px;
        margin: 0 auto;
    }

    .logo-wrap {
        text-align: center;
        margin-bottom: 6px;
    }
    .logo-wrap img {
        width: 28px;
    }

    .qr-wrap {
        text-align: center;
        margin-bottom: 6px;
    }
    .qr-wrap img {
        width: 130px;
        max-width: 100%;
    }

    .nomor { font-size: 11px; font-weight: bold; }
    .footer { font-size: 8px; margin-top: 4px; color: #333; }
</style>
</head>
<body>
    @foreach ($pages as $rows)
        <div class="page">
            <table class="grid">
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $data)
                            <td>
                                <div class="label">
                                    <div class="logo-wrap">
                                        <img src="{{ public_path('images/logo/logo-palawi.png') }}">
                                    </div>
                                    <div class="qr-wrap">
                                        <img src="{{ $data['qrData'] }}">
                                    </div>
                                    <div class="nomor">{{ $data['item']->nomor_aktiva_tetap ?? '-' }}</div>
                                    <div class="footer">Asset milik PT Perhutani Alam Wisata Risorsis</div>
                                </div>
                            </td>
                        @endforeach
                        {{-- Isi sel kosong kalau baris terakhir kurang dari 3 --}}
                        @for ($i = count($row); $i < 3; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
</body>
</html>