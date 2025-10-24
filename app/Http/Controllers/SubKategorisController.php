<?php

namespace App\Http\Controllers;

use App\Models\subcategories;
use Illuminate\Http\Request;

class SubKategorisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $subcategories=subcategories::when($search, function ($query, $search){
            $query->where('id_subkategori', 'like', "%{$search}%")
                ->orWhere('nama_subkategori', 'like', "%{$search}%");
        })


            ->orderBy('id_subkategori', 'asc') // untuk pagination
            ->paginate(10)
            ->withQueryString();
        return view('subcategories.index',compact('subcategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subcategories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'nama_subkategori'=>'required|string|max:50',
        ]);

        subcategories::create($data);
        return redirect()->route('subcategories.index')
            ->with('success','sub categories berhasil di tambahkan');
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
    public function edit(subcategories $subcategories)
    {
        return view('subcategories.edit',compact('subcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, subcategories $subcategories)
    {
        $data = $request->validate([
            'nama_subkategori'=>'required|string|max:50'
        ]);
        $subcategories->update($data);
        return redirect()->route('subcategories.index')
            ->with('success','subcategories berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(subcategories $subcategories)
    {
        $subcategories->delete();
        return redirect()->route('subcategories.index')
            ->with('success','subcategories berhasil dihapus');
    }
}
