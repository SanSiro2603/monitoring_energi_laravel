<?php

namespace App\Exports;

use App\Models\Energi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Auth;

class EnergiExport implements FromArray, WithStyles, WithDrawings, WithColumnWidths, WithTitle, WithEvents
{
    protected $kantor;
    protected $bulan;
    protected $tahun;
    protected $filteredData;
    protected $totalData;

    public function __construct($kantor = null, $bulan = null, $tahun = null)
    {
        $this->kantor = $kantor;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->filteredData = $this->queryEnergiData();
        $this->calculateTotals();
    }

    protected function queryEnergiData()
    {
        $query = Energi::with('user');

        // Apply filters
        if ($this->kantor) {
            $query->where('kantor', $this->kantor);
        }
        if ($this->bulan) {
            $query->where('bulan', $this->bulan);
        }
        if ($this->tahun) {
            $query->where('tahun', $this->tahun);
        }

        // Role-based filtering
        $user = Auth::user();
        if ($user) {
            switch ($user->role) {
                case 'divisi_user':
                    // Filter berdasarkan kantor user jika ada
                    if (isset($user->kantor)) {
                        $query->where('kantor', $user->kantor);
                    }
                    break;
                case 'user_umum':
                    // Filter khusus untuk user_umum jika diperlukan
                    break;
                // super_user dapat melihat semua data
            }
        }

        return $query->orderBy('tahun', 'desc')
                     ->orderBy('bulan', 'asc')
                     ->get();
    }

    protected function calculateTotals()
    {
        $this->totalData = [
            'listrik' => $this->filteredData->sum('listrik'),
            'air' => $this->filteredData->sum('air'),
            'pertalite' => $this->filteredData->sum('pertalite'),
            'pertamax' => $this->filteredData->sum('pertamax'),
            'solar' => $this->filteredData->sum('solar'),
            'dexlite' => $this->filteredData->sum('dexlite'),
            'pertamina_dex' => $this->filteredData->sum('pertamina_dex'),
            'kertas' => $this->filteredData->sum('kertas'),
            'total_bbm' => $this->filteredData->sum('pertalite') + 
                          $this->filteredData->sum('pertamax') + 
                          $this->filteredData->sum('solar') + 
                          $this->filteredData->sum('dexlite') + 
                          $this->filteredData->sum('pertamina_dex'),
        ];
    }

    public function array(): array
    {
        $data = [];

        // Header Information
        $data[] = ['LAPORAN KONSUMSI ENERGI'];
        $data[] = ['BANK LAMPUNG'];
        $data[] = [''];
        
        // Export Information
        $data[] = ['Tanggal Export:', date('d F Y H:i:s')];
        $data[] = ['Diexport oleh:', Auth::user()->name ?? 'System'];
        $data[] = [''];

        // Filter Information
        $data[] = ['FILTER YANG DITERAPKAN:'];
        $data[] = ['Kantor:', $this->kantor ?: 'Semua Kantor'];
        $data[] = ['Bulan:', $this->bulan ?: 'Semua Bulan'];
        $data[] = ['Tahun:', $this->tahun ?: 'Semua Tahun'];
        $data[] = [''];

        // Summary Information
        $data[] = ['RINGKASAN DATA:'];
        $data[] = ['Total Data:', $this->filteredData->count() . ' record'];
        $data[] = ['Total Listrik:', number_format($this->totalData['listrik'], 2, ',', '.') . ' kWh'];
        $data[] = ['Total Air:', number_format($this->totalData['air'], 2, ',', '.') . ' m³'];
        $data[] = ['Total BBM:', number_format($this->totalData['total_bbm'], 2, ',', '.') . ' Liter'];
        $data[] = ['Total Kertas:', number_format($this->totalData['kertas'], 2, ',', '.') . ' Rim'];
        $data[] = [''];
        $data[] = [''];

        // Check if data exists
        if ($this->filteredData->isEmpty()) {
            $data[] = ['Tidak ada data konsumsi energi yang ditemukan untuk filter yang dipilih.'];
            return $data;
        }

        // Table Headers
        $data[] = [
            'No', 
            'Kantor', 
            'Bulan', 
            'Tahun', 
            'PERTALITE (L)', 
            'PERTAMAX (L)', 
            'SOLAR (L)', 
            'DEXLITE (L)', 
            'PERTAMINA DEX (L)',
            'Listrik (kWh)', 
            'Daya Listrik (VA)',
            'Air (m³)', 
            'Kertas (Rim)',
            'Tanggal Input',
            'Penginput'
        ];

        // Data Rows
        foreach ($this->filteredData as $index => $energi) {
            $data[] = [
                $index + 1,
                $energi->kantor,
                $energi->bulan,
                $energi->tahun,
                $energi->pertalite ?? 0,
                $energi->pertamax ?? 0,
                $energi->solar ?? 0,
                $energi->dexlite ?? 0,
                $energi->pertamina_dex ?? 0,
                $energi->listrik,
                $energi->daya_listrik ?? '-',
                $energi->air,
                $energi->kertas,
                $energi->created_at->format('d-m-Y H:i:s'),
                optional($energi->user)->name ?? '-',
            ];
        }

        // Footer Total
        $data[] = [''];
        $data[] = [
            '', 
            'TOTAL', 
            '', 
            '', 
            number_format($this->totalData['pertalite'], 2, ',', '.'),
            number_format($this->totalData['pertamax'], 2, ',', '.'),
            number_format($this->totalData['solar'], 2, ',', '.'),
            number_format($this->totalData['dexlite'], 2, ',', '.'),
            number_format($this->totalData['pertamina_dex'], 2, ',', '.'),
            number_format($this->totalData['listrik'], 2, ',', '.'),
            '',
            number_format($this->totalData['air'], 2, ',', '.'),
            number_format($this->totalData['kertas'], 2, ',', '.'),
            '',
            ''
        ];

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Kantor
            'C' => 12,  // Bulan
            'D' => 8,   // Tahun
            'E' => 12,  // PERTALITE
            'F' => 12,  // PERTAMAX
            'G' => 12,  // SOLAR
            'H' => 12,  // DEXLITE
            'I' => 15,  // PERTAMINA DEX
            'J' => 12,  // Listrik
            'K' => 15,  // Daya Listrik
            'L' => 10,  // Air
            'M' => 10,  // Kertas
            'N' => 18,  // Tanggal Input
            'O' => 20,  // Penginput
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Main Title Styling
        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'name' => 'Arial',
                'color' => ['rgb' => '1B5E20']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Export Info Styling
        $sheet->getStyle('A4:A5')->getFont()->setBold(true);
        $sheet->getStyle('B4:B5')->getFont()->setItalic(true);

        // Filter Section Styling
        $sheet->mergeCells('A7:O7');
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // Filter rows styling
        for ($i = 8; $i <= 10; $i++) {
            $sheet->getStyle("A{$i}")->getFont()->setBold(true);
            $sheet->getStyle("A{$i}:B{$i}")->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('F5F5F5');
        }

        // Summary Section Styling
        $sheet->mergeCells('A12:O12');
        $sheet->getStyle('A12')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // Summary rows styling
        for ($i = 13; $i <= 17; $i++) {
            $sheet->getStyle("A{$i}")->getFont()->setBold(true);
            $sheet->getStyle("B{$i}")->applyFromArray([
                'font' => [
                    'color' => ['rgb' => '1976D2']
                ]
            ]);
        }

        // Find header row (should be row 20 based on the structure)
        $headerRow = 20;
        
        // Header Table Styling
        $sheet->getStyle("A{$headerRow}:O{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1B5E20']
                ]
            ]
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(30);

        // Data Rows Styling
        $dataStartRow = $headerRow + 1;
        $lastDataRow = $highestRow - 2; // Minus 2 for empty row and total row

        if ($this->filteredData->count() > 0) {
            // Apply borders to all data cells
            $sheet->getStyle("A{$dataStartRow}:O{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E0E0E0']
                    ]
                ],
                'font' => [
                    'size' => 10
                ]
            ]);

            // Number formatting for numeric columns
            $numericColumns = ['E', 'F', 'G', 'H', 'I', 'J', 'L', 'M'];
            foreach ($numericColumns as $col) {
                $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastDataRow}")
                      ->getNumberFormat()
                      ->setFormatCode('#,##0.00');
            }

            // Alignment for specific columns
            $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$dataStartRow}:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$dataStartRow}:M{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("N{$dataStartRow}:N{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra striping
            for ($row = $dataStartRow; $row <= $lastDataRow; $row++) {
                if (($row - $dataStartRow) % 2 == 1) {
                    $sheet->getStyle("A{$row}:O{$row}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setRGB('FAFAFA');
                }
            }
        }

        // Total Row Styling
        $totalRow = $highestRow;
        $sheet->getStyle("A{$totalRow}:O{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM
                ]
            ]
        ]);

        // Freeze panes at header row
        $sheet->freezePane("A" . ($headerRow + 1));

        return $sheet;
    }

    public function drawings()
    {
        $drawings = [];
        $logoPath = public_path('assets/img/banklpg.png');

        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo Bank Lampung');
            $drawing->setDescription('Logo Bank Lampung');
            $drawing->setPath($logoPath);
            $drawing->setHeight(60);
            $drawing->setCoordinates('N1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawings[] = $drawing;
        }

        return $drawings;
    }

    public function title(): string
    {
        $title = 'Laporan Energi';
        if ($this->tahun) {
            $title .= ' ' . $this->tahun;
        }
        if ($this->bulan) {
            $title .= ' ' . $this->bulan;
        }
        return $title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set print settings
                $sheet->getPageSetup()
                      ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                      ->setFitToWidth(1)
                      ->setFitToHeight(0);
                
                // Set margins
                $sheet->getPageMargins()
                      ->setTop(0.75)
                      ->setRight(0.25)
                      ->setBottom(0.75)
                      ->setLeft(0.25);
            },
        ];
    }
}