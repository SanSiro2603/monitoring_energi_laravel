<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Energi;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data energi terbaru
        $terbaru = Energi::latest('created_at')->first();

        return view('dashboard.index', compact('terbaru'));
    }
}
