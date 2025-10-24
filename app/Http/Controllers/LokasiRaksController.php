<?php

namespace App\Http\Controllers;

use App\Models\rackslocation;
use Illuminate\Http\Request;

class LokasiRaksController extends Controller
{
    public function index(Request $request)
    {
     $search=$request->input('search');

        $rackslocation = rackslocation::when($search, function ($query, $search) {
            $query->where('lantai', 'like', "%{$search}%")
                ->orWhere('ruang', 'like', "%{$search}%")
                ->orWhere('sisi', 'like', "%{$search}%");
        })

        ->orderBy('id_lokasi', 'asc') // untuk pagination
        ->paginate(10)
        ->withQueryString();
        return view('rackslocation.index', compact('rackslocation'));
    }

    public function create()
    {
        return view('rackslocation.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lantai' => 'required|string|max:50',
            'ruang'  => 'required|string|max:50',
            'sisi'   => 'nullable|string|max:50',
        ]);

        rackslocation::create($data);

        return redirect()->route('rackslocation.index')
            ->with('success', 'Lokasi rak berhasil ditambahkan.');
    }

    public function show(rackslocation $rackslocation)
    {
        return view('rackslocation.show', compact('rackslocation'));
    }

    public function edit(rackslocation $rackslocation)
    {
        return view('rackslocation.edit', compact('rackslocation'));
    }

    public function update(Request $request, rackslocation $rackslocation)
    {
        $data = $request->validate([
            'lantai' => 'required|string|max:50',
            'ruang'  => 'required|string|max:50',
            'sisi'   => 'nullable|string|max:50',
        ]);

        $rackslocation->update($data);

        return redirect()->route('rackslocation.index')
            ->with('success', 'Lokasi rak berhasil diperbarui.');
    }

    public function destroy(rackslocation $rackslocation)
    {
        $rackslocation->delete();

        return redirect()->route('rackslocation.index')
            ->with('success', 'Lokasi rak berhasil dihapus.');
    }
}
