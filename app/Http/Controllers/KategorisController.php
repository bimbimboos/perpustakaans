<?php

namespace App\Http\Controllers;

use App\Models\categories;
use Illuminate\Http\Request;

class KategorisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories=categories::when($search, function ($query, $search) {
            $query->where('id_kategori', 'like', "%{$search}%")
                ->orWhere('nama_kategori', 'like', "%{$search}%");
        })

            ->orderBy('id_kategori', 'asc') // untuk pagination
            ->paginate(10)
            ->withQueryString();
        return view('categories.index', compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'nama_kategori'=>'required|string|max:50',
        ]);

        categories::create($data);
        return redirect()->route('categories.index')
            ->with('success','categories berhasil di tambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(categories $categories)
    {
        return view('categories.edit',compact('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, categories $categories)
    {
        $data = $request->validate([
            'nama_kategori'=>'required|string|max:50'
        ]);
        $categories->update($data);
        return redirect()->route('categories.index')
            ->with('success','categories berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(categories $categories)
    {
        $categories->delete();
        return redirect()->route('categories.index')
            ->with('success','kategori berhasil dihapus');
    }
}
