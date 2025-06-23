<?php

namespace App\Exports;

use App\Models\Energi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EnergiExport implements FromView
{
    public function view(): View
    {
        return view('exports.energi', [
            'data' => Energi::all()
        ]);
    }
}

