<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Laporan Keuangan</h3>
    <p>Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') }} s.d. {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->tipe == 'Pemasukan' ? rupiah($item->jumlah) : '-' }}</td>
                <td>{{ $item->tipe == 'Pengeluaran' ? rupiah($item->jumlah) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>{{ rupiah($totalPemasukan) }}</th>
                <th>{{ rupiah($totalPengeluaran) }}</th>
            </tr>
            <tr>
                <th colspan="3">Profit</th>
                <th colspan="2">{{ rupiah($profit) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
