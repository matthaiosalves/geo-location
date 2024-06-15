<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Uf;

class UfController extends Controller
{
    public function index()
    {
        $ufs = Uf::all();
        return Inertia::render('Dashboard', ['ufs' => $ufs]);
    }
}
