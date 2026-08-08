<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Perpus</title>
    <style>
        @page {
            margin: 10px;
        }

        body {
            font-family: "Calibri", "Helvetica", sans-serif;
        }

        table.label-table {
            width: 100%;
            border-collapse: collapse;
        }

        td.label-cell {
            width: 33.33%;
            border: 1px solid #000;
            text-align: center;
            padding: 6px;
            vertical-align: middle;
        }

        .barcode-wrap {
            display: table;
            width: 100%;
        }

        .barcode-col {
            display: table-cell;
            width: 40px;
            vertical-align: middle;
            position: relative;
        }

        .barcode-outer {
            width: 40px;
            height: 150px;
            position: relative;
            overflow: hidden;
        }

        .barcode-block {
            width: 150px;
            height: 40px;
            position: absolute;
            top: 150px;
            left: 0;
            transform-origin: top left;
            transform: rotate(-90deg);
            text-align: center;
        }

        .barcode-block .code {
            line-height: 0;
        }

        .barcode-block .id-text {
            font-size: 9px;
            margin-top: 2px;
        }

        .info-col {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            font-size: 10px;
            padding-left: 10px;
        }

        .info-col .lib-name {
            font-size: 8px;
        }

        .info-col .lib-sub {
            font-size: 11px;
            font-weight: bold;
        }

        .info-col hr {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <table class="label-table">
        <tbody>
            @foreach ($bukus->chunk(3) as $row)
                <tr>
                    @foreach ($row as $data)
                        <td class="label-cell">
                            <div class="barcode-wrap">
                                <div class="barcode-col">
                                    <div class="barcode-outer">
                                        <div class="barcode-block">
                                            <div class="code">
                                                {!! $generator->getBarcode($prefix . $data->id_buku, $generator::TYPE_CODE_128, 1, 30) !!}
                                            </div>
                                            <div class="id-text">
                                                {{ $prefix }}{{ $data->id_buku }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-col">
                                    <span class="lib-name">PERPUSTAKAAN</span><br>
                                    <span class="lib-sub">{{ $namaPerpustakaan }}</span>
                                    <hr>
                                    {{ $data->klasifikasi_buku }}<br>
                                    {{ strtoupper(substr($data->pengarang_buku, 0, 3)) }}<br>
                                    {{ strtolower(substr($data->judul_buku, 0, 1)) }}
                                </div>
                            </div>
                        </td>
                    @endforeach
                    {{-- isi cell kosong kalau baris terakhir kurang dari 3 --}}
                    @for ($i = $row->count(); $i < 3; $i++)
                        <td class="label-cell"></td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>