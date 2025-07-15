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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EnergiController extends Controller
{
    /**
     * Menampilkan daftar data energi dengan fitur pencarian dan pagination.
     * Digunakan untuk halaman CRUD/Manajemen Data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Energi::query();

        $kantor = $request->input('kantor');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Apply filters
        if ($kantor) {
            $query->where('kantor', 'like', "%{$kantor}%");
        }
        if ($bulan) {
            $query->where('bulan', 'like', "%{$bulan}%");
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        // Order by year and month, then paginate
        $data = $query->orderByDesc('tahun')
                      ->orderByDesc('bulan')
                      ->paginate(10)
                      ->withQueryString(); // Preserve query string for pagination links

        return view('energi.index', compact('data', 'kantor', 'bulan', 'tahun'));
    }

    /**
     * Menampilkan form untuk membuat data energi baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('energi.input');
    }

    /**
     * Menyimpan data energi baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'kantor' => 'required|string|max:255',
        'bulan' => 'required|string|max:50',
        'tahun' => 'required|numeric|integer|min:1900|max:' . (date('Y') + 5),
        'listrik' => 'required|numeric|min:0',
        'daya_listrik' => 'nullable|numeric|min:0',
        'air' => 'required|numeric|min:0',
        'kertas' => 'required|numeric|min:0',
        'jenis_bbm' => 'required|array|min:1',
        'jenis_bbm.*' => 'required|string|max:100',
        'jumlah_bbm' => 'required|array|min:1',
        'jumlah_bbm.*' => 'required|numeric|min:0',
    ]);

    try {
        $totalBBM = array_sum($request->jumlah_bbm);

        Energi::create([
            'kantor' => $request->kantor,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'listrik' => $request->listrik,
            'daya_listrik' => $request->daya_listrik,
            'air' => $request->air,
            'bbm' => $totalBBM,
            'jenis_bbm' => json_encode($request->jenis_bbm),
            'kertas' => $request->kertas,
            'user_id' => auth()->id(),
        ]);

        $user = Auth::user();
        $redirectRoute = match($user->role) {
            'super_user' => 'admin.energi.index',
            'divisi_user' => 'divisi.energi.index',
            'user_umum' => 'umum.energi.index',
            default => 'dashboard',
        };

        return redirect()->route($redirectRoute)->with('success', '✅ Data energi berhasil ditambahkan!');
    } catch (\Exception $e) {
        return back()->with('error', '❌ Gagal menambahkan data: ' . $e->getMessage());
    }
}


    /**
     * Menampilkan form untuk mengedit data energi.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $item = Energi::findOrFail($id);
            return view('energi.edit', compact('item'));
        } catch (\Exception $e) {
            return back()->with('error', '❌ Data tidak ditemukan.');
        }
    }

    /**
     * Memperbarui data energi di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $item = Energi::findOrFail($id);
            $validated = $request->validate([
                'kantor' => 'required|string|max:255',
                'bulan' => 'required|string|max:50',
                'tahun' => 'required|numeric|integer|min:1900|max:' . (date('Y') + 5),
                'listrik' => 'required|numeric|min:0',
                'daya_listrik' => 'nullable|numeric|min:0',
                'air' => 'required|numeric|min:0',
                'bbm' => 'required|numeric|min:0',
                'jenis_bbm' => 'required|string|max:100',
                'kertas' => 'required|numeric|min:0',
            ]);

            $item->update($validated);

            // Redirect based on user role to ensure correct return path
            $user = Auth::user();
            $redirectRoute = 'dashboard'; // Default redirect to dashboard
            if ($user) {
                if ($user->role === 'super_user') {
                    $redirectRoute = 'admin.energi.index';
                } elseif ($user->role === 'divisi_user') {
                    // Jika divisi_user diizinkan mengedit, arahkan ke indeks mereka
                    $redirectRoute = 'divisi.energi.index';
                } elseif ($user->role === 'user_umum') {
                    // Jika user_umum diizinkan mengedit, arahkan ke indeks mereka
                    $redirectRoute = 'umum.energi.index';
                }
            }
            return redirect()->route($redirectRoute)->with('success', '✅ Data berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data energi dari database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $item = Energi::findOrFail($id);
            $item->delete();
            return back()->with('success', '✅ Data berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan ringkasan data energi.
     * Jika ini hanya halaman tampilan semua data, bisa jadi redundan dengan index jika tidak ada logika agregasi khusus.
     * Saya biarkan sesuai aslinya, Anda bisa menghapus jika tidak diperlukan.
     *
     * @return \Illuminate\View\View
     */
    public function summary()
    {
        $data = Energi::latest()->get(); // Mengambil semua data terbaru
        return view('energi.summary', compact('data'));
    }

    /**
     * Mengimpor data energi dari file Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'fileexcel' => 'required|file|mimes:xlsx,xls,csv|max:2048' // Max 2MB
        ], [
            'fileexcel.required' => 'File Excel harus diunggah.',
            'fileexcel.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'fileexcel.max' => 'Ukuran file tidak boleh melebihi 2MB.'
        ]);

        try {
            $file = $request->file('fileexcel');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // Get all values, including formulas and formatted values

            $imported = 0;
            $errors = [];

            // Skip header row
            $headerSkipped = false;
            foreach ($sheet as $i => $row) {
                if (!$headerSkipped) {
                    $headerSkipped = true;
                    continue; // Skip the first row (header)
                }

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Trim all string values in the row
                $row = array_map(function($value) {
                    return is_string($value) ? trim($value) : $value;
                }, $row);

                // Basic validation for critical columns (A:Kantor, B:Bulan, C:Tahun)
                if (empty($row['A']) || empty($row['B']) || empty($row['C'])) {
                    $errors[] = "Baris " . ($i) . ": Data 'Kantor', 'Bulan', atau 'Tahun' tidak boleh kosong.";
                    continue;
                }

                try {
                    Energi::updateOrCreate(
                        [
                            'kantor' => $row['A'],
                            'bulan' => $row['B'],
                            'tahun' => (int)$row['C'],
                        ],
                        [
                            'listrik' => (float)($row['D'] ?? 0),
                            'daya_listrik' => !empty($row['E']) ? (float)$row['E'] : null,
                            'air' => (float)($row['F'] ?? 0),
                            'bbm' => (float)($row['G'] ?? 0),
                            'jenis_bbm' => $row['H'] ?? 'Pertalite', // Default value if empty
                            'kertas' => (float)($row['I'] ?? 0),
                        ]
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i) . ": " . $e->getMessage();
                }
            }

            $message = "✅ Berhasil mengimpor $imported data energi.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " baris dengan kesalahan. Contoh: " . implode(', ', array_slice($errors, 0, 5));
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh template Excel untuk impor data energi.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header for the template
            $headers = [
                'Kantor', 'Bulan', 'Tahun', 'Listrik (kWh)', 'Daya Listrik (VA)',
                'Air (m³)', 'BBM (liter)', 'Jenis BBM', 'Kertas (rim)'
            ];
            $sheet->fromArray($headers, null, 'A1');

            // Example data
            $exampleData = [
                ['Kantor Pusat', 'Januari', 2025, 1500, 1300, 150, 200, 'Pertalite', 50],
                ['Kantor Cabang A', 'Februari', 2025, 800, 900, 80, 100, 'Pertamax', 25],
                ['Kantor Cabang B', 'Maret', 2025, 600, 700, 60, 80, 'Solar', 20]
            ];
            $sheet->fromArray($exampleData, null, 'A2');

            // Set header row to bold
            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);

            // Auto-size columns
            foreach (range('A', $sheet->getHighestColumn()) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_import_energi.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'template');

            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal membuat template Excel: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman laporan konsumsi energi dengan filter dinamis dan total.
     * Juga menyediakan data unik untuk dropdown filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function laporan(Request $request)
    {
        $kantor = $request->input('kantor');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $query = Energi::query();

        // Apply filters for the main data
        if ($kantor) {
            $query->where('kantor', $kantor);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        // Get all data matching the filters (without pagination) for total calculations and charts
        $dataAll = (clone $query)->orderByDesc('tahun')->orderByDesc('bulan')->get();

        // Get paginated data for the table display
        $data = $query->orderByDesc('tahun')
                      ->orderByDesc('bulan')
                      ->paginate(10)
                      ->withQueryString();

        // Calculate totals from $dataAll
        $total = [
            'air' => $dataAll->sum('air'),
            'listrik' => $dataAll->sum('listrik'),
            'daya_listrik' => $dataAll->sum('daya_listrik'),
            'bbm' => $dataAll->sum('bbm'),
            'kertas' => $dataAll->sum('kertas')
        ];

        // --- Fetch unique data for dynamic filter dropdowns ---
        $uniqueKantor = Energi::distinct()->pluck('kantor')->filter()->sort()->values()->all();

        $monthOrder = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $uniqueBulan = Energi::distinct()->pluck('bulan')
            ->filter()
            ->sort(function ($a, $b) use ($monthOrder) {
                return array_search($a, $monthOrder) - array_search($b, $monthOrder);
            })
            ->values()
            ->all();

        $uniqueTahun = Energi::distinct()->pluck('tahun')->filter()->sortDesc()->values()->all();
        // --- End unique data for dynamic filter dropdowns ---

        return view('admin.laporan', compact(
            'data', 'dataAll', 'total', 'kantor', 'bulan', 'tahun',
            'uniqueKantor', 'uniqueBulan', 'uniqueTahun'
        ));
    }

    // Metode laporanJson belum ada di EnergiController yang Anda berikan, saya tambahkan di sini
    public function laporanJson(Request $request)
    {
        $kantor = $request->input('kantor');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $query = Energi::query();

        if ($kantor) {
            $query->where('kantor', $kantor);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $data = $query->orderByDesc('tahun')
                      ->orderByDesc('bulan')
                      ->get();

        return response()->json($data);
    }


    /**
     * Export laporan energi ke format Excel.
     * Uses Maatwebsite\Excel for exporting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $kantor = $request->input('kantor');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $filename = 'laporan_energi';
        if ($kantor) $filename .= "_$kantor";
        if ($bulan) $filename .= "_$bulan";
        if ($tahun) $filename .= "_$tahun";
        $filename .= ".xlsx";

        return Excel::download(new EnergiExport($kantor, $bulan, $tahun), $filename);
    }

    /**
     * Export laporan energi ke format PDF.
     * Uses Barryvdh\DomPDF for rendering PDF from a Blade view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $kantor = $request->input('kantor');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $query = Energi::query();

        if ($kantor) {
            $query->where('kantor', $kantor);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $data = $query->orderByDesc('tahun')
                      ->orderByDesc('bulan')
                      ->get();

        $filename = 'laporan_energi';
        if ($kantor) $filename .= "_$kantor";
        if ($bulan) $filename .= "_$bulan";
        if ($tahun) $filename .= "_$tahun";
        $filename .= ".pdf";

        $pdf = Pdf::loadView('admin.export_pdf', compact('data', 'kantor', 'bulan', 'tahun'))
                  ->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Export chart laporan ke PDF menggunakan Spatie\Browsershot.
     * Membutuhkan PhantomJS atau Google Chrome Headless terinstal di server.
     * Pastikan path ke binary Node, NPM, dan Chrome benar di server Anda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportChartToPDF(Request $request)
    {
        try {
            $kantor = $request->input('kantor');
            $bulan = $request->input('bulan');
            $tahun = $request->input('tahun');

            $query = Energi::query();
            if ($kantor) $query->where('kantor', $kantor);
            if ($bulan) $query->where('bulan', $bulan);
            if ($tahun) $query->where('tahun', $tahun);

            $data = $query->orderByDesc('tahun')
                          ->orderByDesc('bulan')
                          ->get();

            // Calculate totals (same logic as in laporan method)
            $total = [
                'air' => $data->sum('air'),
                'listrik' => $data->sum('listrik'),
                'daya_listrik' => $data->sum('daya_listrik'),
                'bbm' => $data->sum('bbm'),
                'kertas' => $data->sum('kertas'),
            ];

            // Fetch unique data for dropdowns (same logic as in laporan method)
            $uniqueKantor = Energi::distinct()->pluck('kantor')->filter()->sort()->values()->all();
            $monthOrder = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            $uniqueBulan = Energi::distinct()->pluck('bulan')
                ->filter()
                ->sort(function ($a, $b) use ($monthOrder) {
                    return array_search($a, $monthOrder) - array_search($b, $monthOrder);
                })
                ->values()
                ->all();
            $uniqueTahun = Energi::distinct()->pluck('tahun')->filter()->sortDesc()->values()->all();

            // Render the view that contains the chart.js canvas.
            // It's crucial that this view (admin.laporan) is capable of rendering the chart
            // dynamically based on the passed data. Browsershot will take a screenshot of this rendered HTML.
            $html = view('admin.laporan', compact(
                'data', 'kantor', 'bulan', 'tahun', 'total',
                'uniqueKantor', 'uniqueBulan', 'uniqueTahun'
            ))->render();

            $pdfPath = storage_path('app/public/laporan_chart_' . time() . '.pdf');

            Browsershot::html($html)
                // !!! PENTING: SESUAIKAN PATH BERIKUT DENGAN INSTALASI DI SERVER ANDA !!!
                ->setNodeBinary('/usr/bin/node') // Contoh path: /usr/local/bin/node atau /opt/nodejs/bin/node
                ->setNpmBinary('/usr/bin/npm')   // Contoh path: /usr/local/bin/npm atau /opt/nodejs/bin/npm
                ->setChromePath('/usr/bin/google-chrome') // Contoh path: /usr/bin/google-chrome atau /usr/bin/chromium-browser
                // Pastikan environment server Anda memiliki Google Chrome/Chromium Headless terinstal dan dapat diakses.

                ->showBackground() // Important for charts and background colors to show
                ->waitUntilNetworkIdle() // Wait until network activity is low (helps with dynamic content like charts)
                ->setDelay(3000) // Add a delay to ensure Chart.js has fully rendered
                ->format('A4')
                ->landscape()
                ->save($pdfPath);

            return response()->download($pdfPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Error exporting chart to PDF: ' . $e->getMessage());
            return back()->with('error', '❌ Gagal mengekspor chart ke PDF: ' . $e->getMessage() . '. Pastikan Chrome/Chromium Headless terinstal dan path di konfigurasi Browsershot sudah benar.');
        }
    }
}