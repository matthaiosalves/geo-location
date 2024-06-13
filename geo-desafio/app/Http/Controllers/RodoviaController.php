<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Rodovia;

class RodoviaController extends Controller
{
    // public function index()
    // {
    //     $rodovias = Rodovia::all();
    //     return Inertia::render('Welcome', ['rodovia' => $rodovias]);
    // }

    public function index()
    {
        $rodovias = Rodovia::all();
        return response()->json($rodovias);
    }
}
