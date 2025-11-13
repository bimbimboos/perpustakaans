<?php

    namespace App\Http\Controllers;

    use App\Models\books;
    use App\Models\categories;
    use App\Models\subcategories;
    use App\Models\publisher;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class BukusController extends Controller
    {
        /**
         * Display a listing of the resource.
         */
        public function index(Request $request)
        {
            $q = books::with(['publisher', 'categories', 'subcategories'])
                ->withCount(['sortbooks as jumlah_tata' => function ($query) {
                    $query->select(\DB::raw('COALESCE(SUM(jumlah), 0)'));
                }]);

            if ($s = $request->get('search')) {
                $q->where(function ($x) use ($s) {
                    $x->where('judul', 'like', "%$s%")
                        ->orWhere('pengarang', 'like', "%$s%")
                        ->orWhere('tahun_terbit', 'like', "%$s%")
                        ->orWhere('isbn', 'like', "%$s%");
                })
                    ->orWhereHas('publisher', function ($rel) use ($s) {
                        $rel->where('nama_penerbit', 'like', "%$s%");
                    })
                    ->orWhereHas('categories', function ($rel) use ($s) {
                        $rel->where('nama_kategori', 'like', "%$s%");
                    })
                    ->orWhereHas('subcategories', function ($rel) use ($s) {
                        $rel->where('nama_subkategori', 'like', "%$s%");
                    });
            }

            $book = $q->orderBy('id_buku', 'asc')
                ->paginate(10)
                ->withQueryString();

            // data dropdown untuk modal tambah buku
            $publisher = publisher::all();
            $categories = categories::all();
            $subcategories = subcategories::all();

            return view('books.index', compact('book', 'publisher', 'categories', 'subcategories'));
        }


        /**
         * Show the form for creating a new resource.
         */
        public function create()
        {
            if (Auth::user()->role === 'konsumen') {
                // Kalau konsumen, tolak akses
                abort(403, 'Unauthorized');
                // atau bisa juga redirect:
                // return redirect()->route('books.index')->with('error', 'Konsumen tidak bisa menambah buku.');
            } else {
                // Kalau admin/petugas, ambil data dropdown
                $publisher = publisher::all();
                $categories = categories::all();
                $subcategories = subcategories::all();

                return view('books.create', compact('publisher', 'categories', 'subcategories'));
            }
        }


        /**
         * Store a newly created resource in storage.
         */
        public function store(Request $request)
        {
            $data = $request->validate([
                'judul' => 'required|string|max:255',
                'id_penerbit' => 'nullable|integer|exists:publisher,id_penerbit',
                'pengarang' => 'nullable|string|max:255',
                'tahun_terbit' => 'nullable|integer',
                'id_kategori' => 'nullable|integer|exists:categories,id_kategori',
                'id_subkategori' => 'nullable|integer|exists:subcategories,id_subkategori',
                'isbn' => 'nullable|string|max:100',
                'barcode' => 'nullable|string|max:100',
                'jumlah' => 'required|integer|min:0',
            ]);

            books::create($data);

            return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
        }

        /**
         * Show the form for editing the specified resource.
         */
        public function edit(string $id_item)
        {
            $book = books::findOrFail($id_item);
            $publisher = publisher::all();
            $categories = categories::all();
            $subcategories = subcategories::all();

            return view('books.edit', compact('book', 'publisher', 'categories', 'subcategories'));
        }

        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, string $id_item)
        {
            $book = books::findOrFail($id_item);

            $data = $request->validate([
                'judul' => 'required|string|max:255',
                'id_penerbit' => 'nullable|integer|exists:publisher,id_penerbit',
                'pengarang' => 'nullable|string|max:255',
                'tahun_terbit' => 'nullable|integer',
                'id_kategori' => 'nullable|integer|exists:categories,id_kategori',
                'id_subkategori' => 'nullable|integer|exists:subcategories,id_subkategori',
                'isbn' => 'nullable|string|max:100',
                'barcode' => 'nullable|string|max:100',
                'jumlah' => 'required|integer|min:0',
            ]);

            $book->update($data);

            return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui.');
        }

        /**
         * Remove the specified resource from storage.
         */
        public function destroy(string $id_item)
        {
            books::findOrFail($id_item)->delete();

            return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
        }

        public function getForSelection(Request $request)
        {
            $search = $request->query('search');

            $books = books::when($search, function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%");
            })->paginate(10);

            // Return partial view sebagai string HTML
            return view('sortbooks.partials.buku_table', compact('books'))->render();
        }

        public function show(string $id_buku)
        {
            // Ambil data buku dengan relasi publisher, categories, subcategories, dan items
            $book = books::with(['publisher', 'categories', 'subcategories', 'bookitems'])
                ->withCount(['sortbooks as jumlah_tata' => function ($query) {
                    $query->select(\DB::raw('COALESCE(SUM(jumlah), 0)'));
                }])
                ->findOrFail($id_buku);

            // Data dropdown untuk modal edit buku
            $publisher = publisher::all();
            $categories = categories::all();
            $subcategories = subcategories::all();

            return view('books.show', compact('book', 'publisher', 'categories', 'subcategories'));
        }
    }
