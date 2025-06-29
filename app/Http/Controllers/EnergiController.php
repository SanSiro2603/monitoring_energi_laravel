<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Energi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
    public function index(Request $request)
    {
        $query = Energi::query();

        // ✅ Perbaiki parameter sesuai dengan form
        if ($request->cari_kantor) {
            $query->where('kantor', 'like', "%{$request->cari_kantor}%");
        }
        if ($request->cari_bulan) {
            $query->where('bulan', 'like', "%{$request->cari_bulan}%");
        }
        if ($request->cari_tahun) {
            $query->where('tahun', $request->cari_tahun);
        }

        $data = $query->orderByDesc('tahun')
                      ->orderByDesc('bulan')
                      ->paginate(10)
                      ->withQueryString();
        return view('energi.index', compact('data'));
    }

    public function create()
    {
        return view('energi.input');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kantor' => 'required|string',
            'bulan' => 'required|string',
            'tahun' => 'required|numeric',
            'listrik' => 'required|numeric',
            'daya_listrik' => 'nullable|numeric',
            'air' => 'required|numeric',
            'bbm' => 'required|numeric',
            'jenis_bbm' => 'required|string',
            'kertas' => 'required|numeric',
        ]);

        Energi::create($validated);

        // Redirect berdasarkan role user
        $user = auth()->user();
        $redirectRoute = $user->role === 'super_user' ? 'admin.energi.index' : 'divisi.energi.index';
        
        return redirect()->route($redirectRoute)->with('success', '✅ Data energi berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = Energi::findOrFail($id);
        return view('energi.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        try {
            $item = Energi::findOrFail($id);
            $validated = $request->validate([
                'kantor' => 'required|string',
                'bulan' => 'required|string',
                'tahun' => 'required|numeric',
                'listrik' => 'required|numeric',
                'daya_listrik' => 'nullable|numeric',
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

    public function summary()
    {
        $data = Energi::latest()->get();
        return view('energi.summary', compact('data'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'fileexcel' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $file = $request->file('fileexcel');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $imported = 0;
            $errors = [];

            foreach ($sheet as $i => $row) {
                if ($i === 0 || empty($row[0])) continue; // Skip header atau baris kosong

                // Validasi data sebelum insert
                if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                    $errors[] = "Baris " . ($i + 1) . ": Data kantor, bulan, atau tahun kosong";
                    continue;
                }

                try {
                    Energi::updateOrCreate(
                        [
                            'kantor' => trim($row[0]),
                            'bulan' => trim($row[1]),
                            'tahun' => (int)$row[2],
                        ],
                        [
                            'listrik' => (float)($row[3] ?? 0),
                            'daya_listrik' => !empty($row[4]) ? (float)$row[4] : null,
                            'air' => (float)($row[5] ?? 0),
                            'bbm' => (float)($row[6] ?? 0),
                            'jenis_bbm' => trim($row[7] ?? 'Pertalite'),
                            'kertas' => (float)($row[8] ?? 0),
                        ]
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            $message = "✅ Berhasil mengimpor $imported data energi.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " error: " . implode(', ', array_slice($errors, 0, 3));
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal mengimpor: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $headers = [
                'A1' => 'Kantor',
                'B1' => 'Bulan', 
                'C1' => 'Tahun',
                'D1' => 'Listrik (kWh)',
                'E1' => 'Daya Listrik (VA)',
                'F1' => 'Air (m³)',
                'G1' => 'BBM (liter)',
                'H1' => 'Jenis BBM',
                'I1' => 'Kertas (rim)'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            // Contoh data
            $exampleData = [
                ['Kantor Pusat', 'Januari', 2025, 1500, 1300, 150, 200, 'Pertalite', 50],
                ['Kantor Cabang A', 'Januari', 2025, 800, 900, 80, 100, 'Pertamax', 25],
                ['Kantor Cabang B', 'Januari', 2025, 600, 700, 60, 80, 'Solar', 20]
            ];

            $row = 2;
            foreach ($exampleData as $data) {
                $col = 'A';
                foreach ($data as $value) {
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'I') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_import_energi.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'template');
            
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal membuat template: ' . $e->getMessage());
        }
    }

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
            'daya_listrik' => $data->sum('daya_listrik'),
            'bbm' => $data->sum('bbm'),
            'kertas' => $data->sum('kertas')
        ];

        return view('admin.laporan', compact('data', 'total', 'kantor', 'bulan', 'tahun'));
    }

    public function exportExcel()
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
                'daya_listrik' => $data->sum('daya_listrik'),
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