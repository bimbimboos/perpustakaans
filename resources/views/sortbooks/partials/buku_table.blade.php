<table class="table table-striped table-bordered">
    <thead class="table-dark">
    <tr>
        <th>No</th>
        <th>Judul Buku</th>
        <th>Aksi</th>
    </tr>
    </thead>
    <tbody>
    @forelse($books as $index => $book)
        <tr>
            <td>{{ ($books->currentPage() - 1) * $books->perPage() + $loop->iteration }}</td>
            <td>{{ $book->judul }}</td>
            <td>
                <button class="btn btn-sm btn-primary pilih-buku"
                        data-id="{{ $book->id_buku }}"
                        data-judul="{{ $book->judul }}">
                    Pilih
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center">Tidak ada buku ditemukan.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<!-- Pagination links -->
<div class="d-flex justify-content-center">
    {{ $books->links('pagination::bootstrap-5') }}
</div>
