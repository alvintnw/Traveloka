<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Models\Station; // Import Station model
use Illuminate\Http\Request;

class TrainController extends Controller
{
    public function index()
    {
        $trains = Train::all();
        return view('trains.index', compact('trains'));
    }

    public function create()
    {
        $stations = Station::all();
        return view('trains.create', compact('stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'origin_station_id' => 'required|exists:stations,id',
            'destination_station_id' => 'required|exists:stations,id|different:origin_station_id',
            'train_name' => 'required|string|max:255',
            'train_number' => 'required|string|max:255|unique:trains,train_number',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:0',
        ]);

        Train::create($request->all());

        return redirect()->route('trains.index')->with('success', 'Train created successfully.');
    }

    public function show(Train $train)
    {
        return view('trains.show', compact('train'));
    }

    public function edit(Train $train)
    {
        $stations = Station::all();
        return view('trains.edit', compact('train', 'stations'));
    }

    public function update(Request $request, Train $train)
    {
        $request->validate([
            'origin_station_id' => 'required|exists:stations,id',
            'destination_station_id' => 'required|exists:stations,id|different:origin_station_id',
            'train_name' => 'required|string|max:255',
            'train_number' => 'required|string|max:255|unique:trains,train_number,' . $train->id,
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:0',
        ]);

        $train->update($request->all());

        return redirect()->route('trains.index')->with('success', 'Train updated successfully.');
    }

    public function destroy(Train $train)
    {
        $train->delete();
        return redirect()->route('trains.index')->with('success', 'Train deleted successfully.');
    }
}