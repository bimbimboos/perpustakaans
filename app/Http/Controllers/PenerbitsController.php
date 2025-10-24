<?php

namespace App\Http\Controllers;

use App\Models\publisher;
use Illuminate\Http\Request;

class PenerbitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $publisher = publisher::when($search, function ($query, $search) {
            $query->where('id_penerbit', 'like', "%{$search}%")
                ->orWhere('nama_penerbit', 'like', "%{$search}%");
        })
            ->orderBy('id_penerbit', 'asc') // Ganti 'id_item' dengan kolom yang sesuai, misalnya 'id_penerbit'
            ->paginate(10)
            ->withQueryString();

        return view('publisher.index', compact('publisher'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('publisher.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_penerbit' => 'required|string|max:50',
            'alamat'  => 'required|string|max:100',
            'no_telepon'   => 'nullable|string|max:50',
            'email'       => 'nullable|string|max:50',
        ]);

        publisher::create($data);
        return redirect()->route('publisher.index')
            ->with('success','data publisher berhasil di tambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(publisher $publisher)
    {
        return view('publisher.show',compact('publisher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(publisher $publisher)
    {
        return view('publisher.edit',compact('publisher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, publisher $publisher)
    {
        $data = $request->validate([
            'nama_penerbit' => 'required|string|max:50',
            'alamat'  => 'required|string|max:100',
            'no_telepon'   => 'nullable|string|max:50',
            'email'       => 'nullable|string|max:50',
        ]);

        $publisher->update($data);
        return redirect()->route('publisher.index')
            ->with('success','data publisher berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(publisher $publisher)
    {
        $publisher->delete();
        return redirect()->route('publisher.index')
            ->with('success','data penerbit berhasil di hapus');
    }
}
