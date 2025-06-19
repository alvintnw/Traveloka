<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transportasi;
use Illuminate\Http\Request;

class TransportasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::orderBy('name')->get();
        $transportasi = Transportasi::with('category')->orderBy('kode')->orderBy('name')->get();
        return view('server.transportasi.index', compact('category', 'transportasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = Category::orderBy('name')->get();
        return view('server.transportasi.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kode' => 'required|string|max:10',
            'jumlah' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id'
        ]);

        Transportasi::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'kode' => strtoupper($request->kode),
                'jumlah' => $request->jumlah,
                'category_id' => $request->category_id,
            ]
        );

        return redirect()->route('transportasi.index')
            ->with('success', $request->id ? 'Success Update Transportasi!' : 'Success Add Transportasi!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transportasi = Transportasi::with('category')->findOrFail($id);
        return view('server.transportasi.show', compact('transportasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::orderBy('name')->get();
        $transportasi = Transportasi::findOrFail($id);
        return view('server.transportasi.edit', compact('category', 'transportasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kode' => 'required|string|max:10',
            'jumlah' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id'
        ]);

        $transportasi = Transportasi::findOrFail($id);
        $transportasi->update([
            'name' => $request->name,
            'kode' => strtoupper($request->kode),
            'jumlah' => $request->jumlah,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('transportasi.index')->with('success', 'Success Update Transportasi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transportasi = Transportasi::find($id);
        if ($transportasi) {
            $transportasi->delete();
            return redirect()->back()->with('success', 'Success Delete Transportasi!');
        } else {
            return redirect()->back()->with('error', 'Transportasi not found!');
        }
    }
}
