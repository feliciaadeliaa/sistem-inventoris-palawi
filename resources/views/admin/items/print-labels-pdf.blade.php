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

        table.grid td {
        width: 25%;
        text-align: center;
        vertical-align: top;
        padding: 2px;
    }

    .label {
        border: 1px solid #333;
        padding: 9px 1px;
        margin: 0 auto;
    }

    .logo-wrap {
        text-align: center;
        margin-bottom: 1px;
    }
    .logo-wrap img {
        width: 20px;
    }

    .qr-wrap {
        text-align: center;
        margin-bottom: 1px;
    }
    .qr-wrap img {
        width: 155px;
        max-width: 100%;
    }

    .nomor { font-size: 9px; font-weight: bold; margin-bottom: 2px; }
    .footer { font-size: 6.5px; margin-bottom: 5px; color: #333; line-height: 1.1; }
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
                                    <div class="footer">PT Perhutani Alam Wisata Risorsis</div>
                                </div>
                            </td>
                        @endforeach
                        {{-- Isi sel kosong kalau baris terakhir kurang dari 4 --}}
                        @for ($i = count($row); $i < 4; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
</body>
</html>