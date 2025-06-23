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

    // Edit data energi
    public function edit($id)
    {
        $item = Energi::findOrFail($id);
        return view('energi.edit', compact('item'));
    }

    // Update data energi
    public function update(Request $request, $id)
    {
        $item = Energi::findOrFail($id);
        $item->update($request->only('kantor', 'bulan', 'tahun', 'listrik', 'air', 'bbm', 'kertas'));

        return redirect()->back()->with('success', '✅ Data berhasil diperbarui.');
    }

    // Hapus data energi
    public function destroy($id)
    {
        $item = Energi::findOrFail($id);
        $item->delete();
        return back()->with('success', '✅ Data berhasil dihapus.');
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
            'fileexcel' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('fileexcel')->getPathname();
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        for ($i = 1; $i < count($sheet); $i++) {
            $row = $sheet[$i];

            Energi::create([
                'kantor'  => $row[0],
                'bulan'   => $row[1],
                'tahun'   => $row[2],
                'listrik' => $row[3],
                'air'     => $row[4],
                'bbm'     => $row[5],
                'kertas'  => $row[6],
            ]);
        }

        return back()->with('success', '✅ Data dari Excel berhasil diimpor!');
    }

    // Halaman laporan energi - FIXED VERSION
    public function laporan(Request $request)
{
    $kantor = $request->kantor;
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $query = Energi::query();

    if ($kantor) $query->where('kantor', 'like', "%$kantor%");
    if ($bulan) $query->where('bulan', 'like', "%$bulan%");
    if ($tahun) $query->where('tahun', $tahun);

    $data = $query->orderByDesc('tahun')->orderByDesc('bulan')->get();

    $total = [
    'air' => 123,
    'listrik' => 456,
    'bbm' => 789,
    'kertas' => 321
];

    return view('admin.laporan', compact('data', 'total', 'kantor', 'bulan', 'tahun'));
}

    public function exportExcel()
    {
        return Excel::download(new \App\Exports\EnergiExport, 'laporan_energi.xlsx');
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

        $data = $query->orderByDesc('tahun')->orderByDesc('bulan')->get();

        $pdf = Pdf::loadView('admin.export_pdf', compact('data'))->setPaper('A4', 'landscape');

        return $pdf->download('laporan_energi.pdf');
    }

    public function exportChartToPDF(Request $request)
    {
        $kantor = $request->kantor;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query = Energi::query();
        if ($kantor) $query->where('kantor', 'like', "%$kantor%");
        if ($bulan) $query->where('bulan', 'like', "%$bulan%");
        if ($tahun) $query->where('tahun', $tahun);

        $data = $query->orderByDesc('tahun')->orderByDesc('bulan')->get();

        $total = [
            'air' => $data->sum('air'),
            'listrik' => $data->sum('listrik'),
            'bbm' => $data->sum('bbm'),
            'kertas' => $data->sum('kertas'),
        ];

        $html = view('admin.laporan', compact('data', 'kantor', 'bulan', 'tahun', 'total'))->render();

        $pdfPath = storage_path('app/public/laporan_chart.pdf');

        Browsershot::html($html)
            ->waitUntilNetworkIdle()
            ->format('A4')
            ->landscape()
            ->save($pdfPath);

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }
   
}