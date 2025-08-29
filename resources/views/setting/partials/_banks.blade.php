<div class="my-3">
    <a href="{{ route('banks.create') }}" class="btn btn-primary">Tambah Rekening</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nama Bank</th>
            <th>Nomor Rekening</th>
            <th>Atas Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($banks as $bank)
            <tr>
                <td>{{ $bank->nama_bank }}</td>
                <td>{{ $bank->nomor_rekening }}</td>
                <td>{{ $bank->nama_pemilik }}</td>
                <td>
                    <a href="{{ route('banks.edit', $bank->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form action="{{ route('banks.destroy', $bank->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus rekening ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
