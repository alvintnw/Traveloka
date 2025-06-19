<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use App\Models\Transportasi;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transportasi = Transportasi::orderBy('kode')->orderBy('name')->get();
        $rute = Rute::with('transportasi.category')->orderBy('created_at', 'desc')->get();
        return view('server.rute.index', compact('rute', 'transportasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $transportasi = Transportasi::orderBy('kode')->orderBy('name')->get();
        return view('server.rute.create', compact('transportasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tujuan' => 'required|string|max:255',
            'start' => 'required|string|max:255',
            'end' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'jam' => 'required',
            'transportasi_id' => 'required|exists:transportasis,id'
        ]);

        Rute::updateOrCreate(
            ['id' => $request->id],
            [
                'tujuan' => $request->tujuan,
                'start' => $request->start,
                'end' => $request->end,
                'harga' => $request->harga,
                'jam' => $request->jam,
                'transportasi_id' => $request->transportasi_id,
            ]
        );

        return redirect()->route('rute.index')
            ->with('success', $request->id ? 'Success Update Rute!' : 'Success Add Rute!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rute = Rute::with('transportasi.category')->findOrFail($id);
        return view('server.rute.show', compact('rute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rute = Rute::findOrFail($id);
        $transportasi = Transportasi::orderBy('kode')->orderBy('name')->get();
        return view('server.rute.edit', compact('rute', 'transportasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tujuan' => 'required|string|max:255',
            'start' => 'required|string|max:255',
            'end' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'jam' => 'required',
            'transportasi_id' => 'required|exists:transportasis,id'
        ]);

        $rute = Rute::findOrFail($id);
        $rute->update([
            'tujuan' => $request->tujuan,
            'start' => $request->start,
            'end' => $request->end,
            'harga' => $request->harga,
            'jam' => $request->jam,
            'transportasi_id' => $request->transportasi_id,
        ]);

        return redirect()->route('rute.index')->with('success', 'Success Update Rute!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rute = Rute::find($id);
        if ($rute) {
            $rute->delete();
            return redirect()->back()->with('success', 'Success Delete Rute!');
        } else {
            return redirect()->back()->with('error', 'Rute not found!');
        }
    }
}
