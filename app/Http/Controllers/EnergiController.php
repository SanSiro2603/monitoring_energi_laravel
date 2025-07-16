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
        $query = Energi::with('user'); // Load relasi user

        // Filter parameters
        $cari_kantor = $request->input('cari_kantor');
        $cari_bulan = $request->input('cari_bulan');
        $cari_tahun = $request->input('cari_tahun');
        $cari_email = $request->input('cari_email');
        $cari_nama = $request->input('cari_nama');

        // Apply filters
        if ($cari_kantor) {
            $query->where('kantor', 'like', "%{$cari_kantor}%");
        }
        if ($cari_bulan) {
            $query->where('bulan', 'like', "%{$cari_bulan}%");
        }
        if ($cari_tahun) {
            $query->where('tahun', $cari_tahun);
        }
        if ($cari_email) {
            $query->whereHas('user', function($q) use ($cari_email) {
                $q->where('email', 'like', "%{$cari_email}%");
            });
        }
        if ($cari_nama) {
            $query->whereHas('user', function($q) use ($cari_nama) {
                $q->where('name', 'like', "%{$cari_nama}%");
            });
        }

        // Order by created_at desc, then by year and month
        $data = $query->orderByDesc('created_at')
                      ->orderByDesc('tahun')
                      ->orderByDesc('bulan')
                      ->paginate(10)
                      ->withQueryString(); // Preserve query string for pagination links

        return view('energi.index', compact('data', 'cari_kantor', 'cari_bulan', 'cari_tahun', 'cari_email', 'cari_nama'));
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
     * Updated untuk mendukung multiple rows dan format BBM terpisah.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kantor' => 'required|array|min:1',
            'kantor.*' => 'required|string|max:255',
            'bulan' => 'required|array|min:1',
            'bulan.*' => 'required|string|max:50',
            'tahun' => 'required|array|min:1',
            'tahun.*' => 'required|numeric|integer|min:1900|max:' . (date('Y') + 5),
            'listrik' => 'required|array|min:1',
            'listrik.*' => 'required|numeric|min:0',
            'daya_listrik' => 'nullable|array',
            'daya_listrik.*' => 'nullable|numeric|min:0',
            'air' => 'required|array|min:1',
            'air.*' => 'required|numeric|min:0',
            'kertas' => 'required|array|min:1',
            'kertas.*' => 'required|numeric|min:0',
            'pertalite' => 'nullable|array',
            'pertalite.*' => 'nullable|numeric|min:0',
            'pertamax' => 'nullable|array',
            'pertamax.*' => 'nullable|numeric|min:0',
            'solar' => 'nullable|array',
            'solar.*' => 'nullable|numeric|min:0',
            'dexlite' => 'nullable|array',
            'dexlite.*' => 'nullable|numeric|min:0',
            'pertamina_dex' => 'nullable|array',
            'pertamina_dex.*' => 'nullable|numeric|min:0',
        ]);

        try {
            $savedCount = 0;
            $totalRows = count($request->kantor);

            for ($i = 0; $i < $totalRows; $i++) {
                // Hitung total BBM
                $totalBBM = ($request->pertalite[$i] ?? 0) + 
                           ($request->pertamax[$i] ?? 0) + 
                           ($request->solar[$i] ?? 0) + 
                           ($request->dexlite[$i] ?? 0) + 
                           ($request->pertamina_dex[$i] ?? 0);

                // Buat array jenis BBM yang digunakan
                $jenis_bbm = [];
                if (($request->pertalite[$i] ?? 0) > 0) $jenis_bbm[] = 'Pertalite';
                if (($request->pertamax[$i] ?? 0) > 0) $jenis_bbm[] = 'Pertamax';
                if (($request->solar[$i] ?? 0) > 0) $jenis_bbm[] = 'Solar';
                if (($request->dexlite[$i] ?? 0) > 0) $jenis_bbm[] = 'Dexlite';
                if (($request->pertamina_dex[$i] ?? 0) > 0) $jenis_bbm[] = 'Pertamina Dex';

                Energi::create([
                    'kantor' => $request->kantor[$i],
                    'bulan' => $request->bulan[$i],
                    'tahun' => $request->tahun[$i],
                    'listrik' => $request->listrik[$i],
                    'daya_listrik' => $request->daya_listrik[$i] ?? null,
                    'air' => $request->air[$i],
                    'pertalite' => $request->pertalite[$i] ?? 0,
                    'pertamax' => $request->pertamax[$i] ?? 0,
                    'solar' => $request->solar[$i] ?? 0,
                    'dexlite' => $request->dexlite[$i] ?? 0,
                    'pertamina_dex' => $request->pertamina_dex[$i] ?? 0,
                    'bbm' => $totalBBM, // Total BBM untuk kompatibilitas
                    'jenis_bbm' => implode(', ', $jenis_bbm), // String jenis BBM
                    'kertas' => $request->kertas[$i],
                    'user_id' => auth()->id(),
                ]);

                $savedCount++;
            }

            $user = Auth::user();
            $redirectRoute = match($user->role) {
                'super_user' => 'admin.energi.index',
                'divisi_user' => 'divisi.energi.index',
                'user_umum' => 'umum.energi.index',
                default => 'dashboard',
            };

            return redirect()->route($redirectRoute)->with('success', "✅ Berhasil menambahkan {$savedCount} data energi!");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal menambahkan data: ' . $e->getMessage())->withInput();
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
     * Updated untuk mendukung format BBM terpisah.
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
                'kertas' => 'required|numeric|min:0',
                'pertalite' => 'nullable|numeric|min:0',
                'pertamax' => 'nullable|numeric|min:0',
                'solar' => 'nullable|numeric|min:0',
                'dexlite' => 'nullable|numeric|min:0',
                'pertamina_dex' => 'nullable|numeric|min:0',
            ]);

            // Hitung total BBM
            $totalBBM = ($validated['pertalite'] ?? 0) + 
                       ($validated['pertamax'] ?? 0) + 
                       ($validated['solar'] ?? 0) + 
                       ($validated['dexlite'] ?? 0) + 
                       ($validated['pertamina_dex'] ?? 0);

            // Buat array jenis BBM yang digunakan
            $jenis_bbm = [];
            if (($validated['pertalite'] ?? 0) > 0) $jenis_bbm[] = 'Pertalite';
            if (($validated['pertamax'] ?? 0) > 0) $jenis_bbm[] = 'Pertamax';
            if (($validated['solar'] ?? 0) > 0) $jenis_bbm[] = 'Solar';
            if (($validated['dexlite'] ?? 0) > 0) $jenis_bbm[] = 'Dexlite';
            if (($validated['pertamina_dex'] ?? 0) > 0) $jenis_bbm[] = 'Pertamina Dex';

            $validated['bbm'] = $totalBBM;
            $validated['jenis_bbm'] = implode(', ', $jenis_bbm);

            $item->update($validated);

            // Redirect based on user role
            $user = Auth::user();
            $redirectRoute = match($user->role) {
                'super_user' => 'admin.energi.index',
                'divisi_user' => 'divisi.energi.index',
                'user_umum' => 'umum.energi.index',
                default => 'dashboard',
            };

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
     *
     * @return \Illuminate\View\View
     */
    public function summary()
    {
        $data = Energi::with('user')->latest()->get();
        return view('energi.summary', compact('data'));
    }

    /**
     * Mengimpor data energi dari file Excel.
     * Updated untuk mendukung format BBM terpisah.
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
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

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

                // Basic validation for critical columns
                // Format: Kantor | Bulan | Tahun | PERTALITE | PERTAMAX | SOLAR | DEXLITE | PERTAMINA DEX | Listrik | Daya Listrik | Air | Kertas
                if (empty($row['A']) || empty($row['B']) || empty($row['C'])) {
                    $errors[] = "Baris " . ($i) . ": Data 'Kantor', 'Bulan', atau 'Tahun' tidak boleh kosong.";
                    continue;
                }

                try {
                    $pertalite = (float)($row['D'] ?? 0);
                    $pertamax = (float)($row['E'] ?? 0);
                    $solar = (float)($row['F'] ?? 0);
                    $dexlite = (float)($row['G'] ?? 0);
                    $pertamina_dex = (float)($row['H'] ?? 0);
                    
                    // Hitung total BBM
                    $totalBBM = $pertalite + $pertamax + $solar + $dexlite + $pertamina_dex;
                    
                    // Buat array jenis BBM yang digunakan
                    $jenis_bbm = [];
                    if ($pertalite > 0) $jenis_bbm[] = 'Pertalite';
                    if ($pertamax > 0) $jenis_bbm[] = 'Pertamax';
                    if ($solar > 0) $jenis_bbm[] = 'Solar';
                    if ($dexlite > 0) $jenis_bbm[] = 'Dexlite';
                    if ($pertamina_dex > 0) $jenis_bbm[] = 'Pertamina Dex';

                    Energi::updateOrCreate(
                        [
                            'kantor' => $row['A'],
                            'bulan' => $row['B'],
                            'tahun' => (int)$row['C'],
                            'user_id' => auth()->id(),
                        ],
                        [
                            'pertalite' => $pertalite,
                            'pertamax' => $pertamax,
                            'solar' => $solar,
                            'dexlite' => $dexlite,
                            'pertamina_dex' => $pertamina_dex,
                            'listrik' => (float)($row['I'] ?? 0),
                            'daya_listrik' => !empty($row['J']) ? (float)$row['J'] : null,
                            'air' => (float)($row['K'] ?? 0),
                            'kertas' => (float)($row['L'] ?? 0),
                            'bbm' => $totalBBM,
                            'jenis_bbm' => implode(', ', $jenis_bbm),
                        ]
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i) . ": " . $e->getMessage();
                }
            }

            $message = "✅ Berhasil mengimpor $imported data energi.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " baris dengan kesalahan. Contoh: " . implode(', ', array_slice($errors, 0, 3));
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_energi.xlsx"',
        ];

        // Buat spreadsheet sederhana dengan header
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'Kantor');
        $sheet->setCellValue('B1', 'Bulan');
        $sheet->setCellValue('C1', 'Tahun');
        $sheet->setCellValue('D1', 'PERTALITE');
        $sheet->setCellValue('E1', 'PERTAMAX');
        $sheet->setCellValue('F1', 'SOLAR');
        $sheet->setCellValue('G1', 'DEXLITE');
        $sheet->setCellValue('H1', 'PERTAMINA DEX');
        $sheet->setCellValue('I1', 'Listrik (kWh)');
        $sheet->setCellValue('J1', 'Daya Listrik (VA)');
        $sheet->setCellValue('K1', 'Air (m3)');
        $sheet->setCellValue('L1', 'Kertas (rim)');

        // Contoh data
        $sheet->setCellValue('A2', 'Kantor Pusat');
        $sheet->setCellValue('B2', 'Januari');
        $sheet->setCellValue('C2', '2025');
        $sheet->setCellValue('D2', '100');
        $sheet->setCellValue('E2', '50');
        $sheet->setCellValue('F2', '0');
        $sheet->setCellValue('G2', '0');
        $sheet->setCellValue('H2', '0');
        $sheet->setCellValue('I2', '1000');
        $sheet->setCellValue('J2', '1300');
        $sheet->setCellValue('K2', '50');
        $sheet->setCellValue('L2', '10');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, $headers);
    }

    /**
     * Menampilkan halaman laporan konsumsi energi dengan filter dinamis dan total.
     * Updated untuk mendukung format BBM terpisah.
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

        // Calculate totals from $dataAll with BBM breakdown
        $totalListrik = $dataAll->sum('listrik');
        $totalAir = $dataAll->sum('air');
        $totalKertas = $dataAll->sum('kertas');
        
        // Calculate total BBM by summing all BBM types
        $totalBBM = $dataAll->sum('pertalite') + 
                    $dataAll->sum('pertamax') + 
                    $dataAll->sum('solar') + 
                    $dataAll->sum('dexlite') + 
                    $dataAll->sum('pertamina_dex');

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

        // Determine view based on user role
        $user = Auth::user();
        $viewName = match($user->role) {
            'super_user' => 'admin.laporan',
            'divisi_user' => 'admin.laporan',
            'user_umum' => 'admin.laporan',
            default => 'laporan',
        };

        return view($viewName, compact(
            'data', 'dataAll', 'kantor', 'bulan', 'tahun',
            'uniqueKantor', 'uniqueBulan', 'uniqueTahun',
            'totalListrik', 'totalAir', 'totalBBM', 'totalKertas'
        ));
    }

    /**
     * Return JSON data for laporan (used for AJAX/chart updates).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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

        // Calculate totals for PDF
        $totalListrik = $data->sum('listrik');
        $totalAir = $data->sum('air');
        $totalKertas = $data->sum('kertas');
        $totalBBM = $data->sum('pertalite') + 
                    $data->sum('pertamax') + 
                    $data->sum('solar') + 
                    $data->sum('dexlite') + 
                    $data->sum('pertamina_dex');

        $filename = 'laporan_energi';
        if ($kantor) $filename .= "_$kantor";
        if ($bulan) $filename .= "_$bulan";
        if ($tahun) $filename .= "_$tahun";
        $filename .= ".pdf";

        // Determine PDF view based on user role
        $user = Auth::user();
        $pdfView = match($user->role) {
            'super_user' => 'admin.export_pdf',
            'divisi_user' => 'admin.export_pdf',
            'user_umum' => 'admin.export_pdf',
            default => 'export_pdf',
        };

        $pdf = Pdf::loadView($pdfView, compact(
            'data', 'kantor', 'bulan', 'tahun',
            'totalListrik', 'totalAir', 'totalBBM', 'totalKertas'
        ))->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Export chart laporan ke PDF menggunakan Spatie\Browsershot.
     * Updated untuk mendukung format BBM terpisah.
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

            // Pass dataAll for charts
            $dataAll = $data;

            // Calculate totals with BBM breakdown
            $totalListrik = $data->sum('listrik');
            $totalAir = $data->sum('air');
            $totalKertas = $data->sum('kertas');
            $totalBBM = $data->sum('pertalite') + 
                        $data->sum('pertamax') + 
                        $data->sum('solar') + 
                        $data->sum('dexlite') + 
                        $data->sum('pertamina_dex');

            // Fetch unique data for dropdowns
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

            // Determine view based on user role
            $user = Auth::user();
            $viewName = match($user->role) {
                'super_user' => 'admin.laporan',
                'divisi_user' => 'admin.laporan',
                'user_umum' => 'admin.laporan',
                default => 'laporan',
            };

            // Render the view that contains the chart.js canvas
            $html = view($viewName, compact(
                'data', 'dataAll', 'kantor', 'bulan', 'tahun',
                'uniqueKantor', 'uniqueBulan', 'uniqueTahun',
                'totalListrik', 'totalAir', 'totalBBM', 'totalKertas'
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