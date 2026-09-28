<?php

namespace App\Http\Controllers;

use App\Models\Mesa;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::with('juego')->get();

        return view('mesas.index', compact('mesas'));
    }
}