<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Energi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Exports\EnergiExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EnergiController extends Controller
{
    // Tampilkan semua data energi
    public function index()
    {
        $data = Energi::latest()->get();
        return view('energi.index', compact('data'));
    }

    // Form input data energi
    public function create()
    {
        return view('energi.input');
    }

    // Simpan data energi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kantor' => 'required|string',
            'bulan' => 'required|string',
            'tahun' => 'required|numeric',
            'listrik' => 'required|numeric',
            'air' => 'required|numeric',
            'bbm' => 'required|numeric',
            'jenis_bbm' => 'required|string',
            'kertas' => 'required|numeric',
        ]);

        Energi::create($validated);

        return redirect()->route('energi.index')->with('success', '✅ Data energi berhasil ditambahkan!');
    }

    // Edit data energi
    public function edit($id)
    {
        $item = Energi::findOrFail($id);
        return view('energi.edit', compact('item'));
    }

    // Update data energi
    public function update(Request $request, $id)
    {
        try {
            $item = Energi::findOrFail($id);
            $validated = $request->validate([
                'kantor' => 'required|string',
                'bulan' => 'required|string',
                'tahun' => 'required|numeric',
                'listrik' => 'required|numeric',
                'air' => 'required|numeric',
                'bbm' => 'required|numeric',
                'jenis_bbm' => 'required|string',
                'kertas' => 'required|numeric',
            ]);
            $item->update($validated);

            return redirect()->back()->with('success', '✅ Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal memperbarui: '.$e->getMessage());
        }
    }

    // Hapus data energi
    public function destroy($id)
    {
        try {
            $item = Energi::findOrFail($id);
            $item->delete();
            return back()->with('success', '✅ Data berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal menghapus: '.$e->getMessage());
        }
    }

    // Ringkasan energi (untuk user umum)
    public function summary()
    {
        $data = Energi::latest()->get();
        return view('energi.summary', compact('data'));
    }

    // Import data dari Excel
    public function import(Request $request)
    {
        $request->validate([
            'fileexcel' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            $file = $request->file('fileexcel');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $imported = 0;
            foreach ($sheet as $i => $row) {
                if ($i === 0 || empty($row[0])) continue; // Skip header dan baris kosong

                Energi::updateOrCreate(
                    [
                        'kantor' => $row[0],
                        'bulan' => $row[1],
                        'tahun' => $row[2]
                    ],
                    [
                        'listrik' => $row[3],
                        'air' => $row[4],
                        'bbm' => $row[5],
                        'kertas' => $row[6]
                    ]
                );
                $imported++;
            }

            return back()->with('success', "✅ $imported data berhasil diimpor/diperbarui!");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal mengimpor: '.$e->getMessage());
        }
    }

    // Halaman laporan energi
    public function laporan(Request $request)
    {
        $kantor = $request->kantor;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query = Energi::query();

        if ($kantor) $query->where('kantor', 'like', "%$kantor%");
        if ($bulan) $query->where('bulan', 'like', "%$bulan%");
        if ($tahun) $query->where('tahun', $tahun);

        $data = $query->orderByDesc('tahun')
                     ->orderByDesc('bulan')
                     ->get();

        $total = [
            'air' => $data->sum('air'),
            'listrik' => $data->sum('listrik'),
            'bbm' => $data->sum('bbm'),
            'kertas' => $data->sum('kertas')
        ];

        return view('admin.laporan', compact('data', 'total', 'kantor', 'bulan', 'tahun'));
    }

public function export()
{
    return Excel::download(new EnergiExport, 'laporan_energi.xlsx');
}

    public function exportPdf(Request $request)
    {
        $kantor = $request->kantor;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query = Energi::query();

        if ($kantor) $query->where('kantor', 'like', "%$kantor%");
        if ($bulan) $query->where('bulan', 'like', "%$bulan%");
        if ($tahun) $query->where('tahun', $tahun);

        $data = $query->orderByDesc('tahun')
                     ->orderByDesc('bulan')
                     ->get();

        $pdf = Pdf::loadView('admin.export_pdf', compact('data'))
                 ->setPaper('A4', 'landscape');

        return $pdf->download('laporan_energi.pdf');
    }

    public function exportChartToPDF(Request $request)
    {
        try {
            $kantor = $request->kantor;
            $bulan = $request->bulan;
            $tahun = $request->tahun;

            $query = Energi::query();
            if ($kantor) $query->where('kantor', 'like', "%$kantor%");
            if ($bulan) $query->where('bulan', 'like', "%$bulan%");
            if ($tahun) $query->where('tahun', $tahun);

            $data = $query->orderByDesc('tahun')
                         ->orderByDesc('bulan')
                         ->get();

            $total = [
                'air' => $data->sum('air'),
                'listrik' => $data->sum('listrik'),
                'bbm' => $data->sum('bbm'),
                'kertas' => $data->sum('kertas'),
            ];

            $html = view('admin.laporan', compact('data', 'kantor', 'bulan', 'tahun', 'total'))->render();
            $pdfPath = storage_path('app/public/laporan_chart_'.time().'.pdf');

            Browsershot::html($html)
                ->waitUntilNetworkIdle()
                ->setDelay(1000)
                ->format('A4')
                ->landscape()
                ->save($pdfPath);

            return response()->download($pdfPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal mengekspor chart: '.$e->getMessage());
        }
    }
}