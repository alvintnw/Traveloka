<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ini akan menjadi halaman beranda mirip Traveloka
        return view('home');
    }
}