<?php

namespace App\Http\Controllers;

use App\Models\racks;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\books;

class RaksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $racks = racks::when($search, function ($query, $search) {
            $query->where('id_rak', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%")
                ->orWhere('id_lokasi', 'like', "%{$search}%")
                ->orWhereHas('categories', function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', "%{$search}%");
                });
        })
            ->with('rackslocation', 'categories')  // Load relasi untuk view
            ->withSum('sortbooks', 'jumlah')      // Hitung relasi untuk tampilan kapasitas
            ->orderBy('id_rak', 'asc') // untuk pagination
            ->paginate(10)
            ->withQueryString();

            return view('racks.index',compact('racks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('racks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'nullable|string|max:100',
            'nama'    => 'required|string|max:255',
            'kolom'   => 'nullable|string|max:50',
            'baris'   => 'nullable|string|max:50',
            'kapasitas'=> 'nullable|integer',
            'id_lokasi' => 'nullable|integer',
            'id_kategori' => 'nullable|integer',
        ]);

        racks::create($data);

        return redirect()->route('rackscks.index')->with('success', 'Rak berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */

    public function show($id_rak, Request $request)
    {
        // Ambil data rak (dengan relasi lokasi)
        $rack = racks::with(['rackslocation'])->findOrFail($id_rak);

        // Ambil parameter dari URL
        $kategori = $request->query('category'); // ?kategori=...
        $search   = $request->query('search');   // ?search=...

        // Query buku yang punya penataan di rak ini (dasar)
        $bookQuery = books::query()
            // eager load penataan yang hanya untuk rak ini,
            // supaya nanti bisa hitung total jumlah tanpa N+1 queries
            ->with(['sortbooks' => function($q) use ($id_rak) {
                $q->where('id_rak', $id_rak);
            }])
            // pastikan buku memang ada penataan di rak ini
            ->whereHas('sortbooks', function($q) use ($id_rak) {
                $q->where('id_rak', $id_rak);
            });

        // Jika ada parameter kategori, batasi juga ke kategori itu
        if ($kategori) {
            $bookQuery->where('id_kategori', $kategori);
        }

        // Jika ada pencarian judul
        if ($search) {
            $bookQuery->where('judul', 'like', "%{$search}%");
        }

        // Pagination (ubah angka 10 sesuai kebutuhan)
        $paginator = $bookQuery->orderBy('judul')->paginate(10)->withQueryString();

        // Mapping: ubah collection jadi item yg berisi 'book' + 'total_jumlah'
        $mapped = $paginator->getCollection()->map(function($book) {
            return (object)[
                'book' => $book,
                // total jumlah penataan di rak (karena kita eager load, ini tidak n+1)
                'total_jumlah' => $book->sortbooks->sum('jumlah')
            ];
        });

        // Buat kembali LengthAwarePaginator supaya links() tetap bekerja
        $booksInRak = new LengthAwarePaginator(
            $mapped,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(), // biar querystring (kategori/search) tetap ada di link
            ]
        );

        // Kirim ke view dengan nama variabel 'bukusInRak' (konsisten, lowercase)
        return view('racks.show', compact('rack', 'booksInRak'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id_rak)
    {
        $rackslocation = \App\Models\rackslocation::all();
        $categories = \App\Models\categories::all();
        $rack= racks::findOrFail($id_rak);
        return view ('racks.edit',compact('rack'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_rak)
    {
        $rack = racks::findOrFail($id_rak);

        $data = $request->validate([
            'barcode' => 'nullable|string|max:100',
            'nama'    => 'required|string|max:255',
            'kolom'   => 'nullable|string|max:50',
            'baris'   => 'nullable|string|max:50',
            'kapasitas'=> 'nullable|integer',
            'id_lokasi' => 'nullable|integer',
            'id_kategori' => 'nullable|integer',
        ]);

        $rack->update($data);

        return redirect()->route('racks.index')->with('success', 'Rak berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id_rak)
    {
        $rack = racks::findOrFail($id_rak);
        $rack->delete();

        return redirect()->route('racks.index')->with('success', 'Rak berhasil dihapus.');
    }
}
