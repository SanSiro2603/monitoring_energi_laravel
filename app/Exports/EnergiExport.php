<?php

namespace App\Exports;

use App\Models\Energi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle; // Ditambahkan: Untuk nama sheet
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Auth; // Ditambahkan: Untuk otorisasi filter

class EnergiExport implements FromArray, WithStyles, WithDrawings, WithColumnWidths, WithTitle
{
    protected $kantor;
    protected $bulan;
    protected $tahun;
    protected $filteredData; // Akan menyimpan hasil query yang difilter

    public function __construct($kantor = null, $bulan = null, $tahun = null)
    {
        $this->kantor = $kantor;
        $this->bulan = $bulan;
        $this->tahun = $tahun;

        // Lakukan query data di sini atau di method array()
        $this->filteredData = $this->queryEnergiData();
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

        // Tambahan: Filter berdasarkan user_id atau kantor jika role bukan super_user
        // Pastikan Anda memanggil Auth::user() di sini jika diperlukan,
        // atau jika data user tidak selalu tersedia di constructor export class,
        // Anda bisa meneruskannya dari controller.
        $user = Auth::user(); // Asumsi user selalu ada saat ekspor
        if ($user && $user->role === 'divisi_user') {
            // Contoh: Jika divisi user hanya boleh melihat data kantornya
            // Anda perlu memastikan kolom 'kantor' ada di tabel users
            // $query->where('kantor', $user->kantor);
        }
        // ... untuk user_umum jika perlu

        return $query->orderByDesc('tahun')
                     ->orderByDesc('bulan')
                     ->get();
    }

    public function array(): array
    {
        $data = [];

        // Informasi umum di bagian atas
        $data[] = ['Laporan Konsumsi Energi Bank Lampung']; // Judul Utama
        $data[] = ['']; // Baris kosong
        $data[] = ['Tanggal Cetak:', date('d-m-Y H:i:s')]; // Tanggal Cetak
        $data[] = ['']; // Baris kosong

        // Informasi Filter
        $data[] = ['Filter Aktif:'];
        $data[] = ['Kantor:', $this->kantor ?: 'Semua Kantor'];
        $data[] = ['Bulan:', $this->bulan ?: 'Semua Bulan'];
        $data[] = ['Tahun:', $this->tahun ?: 'Semua Tahun'];
        $data[] = ['']; // Baris kosong
        $data[] = ['']; // Baris kosong

        // Jika tidak ada data ditemukan
        if ($this->filteredData->isEmpty()) {
            $data[] = ['Tidak ada data konsumsi energi yang ditemukan untuk filter yang dipilih.'];
            return $data; // Hentikan di sini, jangan tambahkan header tabel atau data
        }

        // Header Tabel (Setelah baris-baris info)
        $data[] = [
            'No', 'Kantor', 'Bulan', 'Tahun', 'Listrik (kWh)', 'Daya Listrik (VA)',
            'Air (m³)', 'BBM (liter)', 'Jenis BBM', 'Tanggal Input', 'Penginput'
        ];

        // Data dari database
        foreach ($this->filteredData as $index => $energi) {
            $data[] = [
                $index + 1, // Nomor urut
                $energi->kantor,
                $energi->bulan,
                $energi->tahun,
                $energi->listrik,
                $energi->daya_listrik,
                $energi->air,
                $energi->bbm,
                $energi->jenis_bbm,
                $energi->created_at->format('d-m-Y H:i:s'),
                optional($energi->user)->name ?? '-',
            ];
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,  // No
            'B' => 20, // Kantor
            'C' => 12, // Bulan
            'D' => 8,  // Tahun
            'E' => 15, // Listrik (kWh)
            'F' => 18, // Daya Listrik (VA)
            'G' => 12, // Air (m³)
            'H' => 12, // BBM (liter)
            'I' => 18, // Jenis BBM
            'J' => 20, // Tanggal Input
            'K' => 25, // Penginput
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $startRowHeaderTable = 1;
        $endRowHeaderTable = 8; // Karena ada 8 baris sebelum header tabel data

        // Style untuk Judul Utama "Laporan Konsumsi Energi Bank Lampung"
        $sheet->mergeCells('A1:K1'); // Merge cell untuk judul utama
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'name' => 'Arial',
                'color' => ['rgb' => '2E7D32'], // Hijau gelap
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Style untuk Tanggal Cetak
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->mergeCells('A3:B3'); // Merge cell untuk label Tanggal Cetak
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Sesuaikan alignment jika perlu
        $sheet->getStyle('C3')->getFont()->setBold(true);


        // Style untuk informasi Filter Aktif
        $sheet->mergeCells('A5:K5'); // Merge cell "Filter Aktif:"
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1B5E20']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9'] // Latar belakang hijau muda
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C8E6C9']]
            ]
        ]);

        // Style untuk setiap baris filter
        for ($i = 6; $i <= 8; $i++) {
            $sheet->getStyle("A{$i}")->getFont()->setBold(true);
            $sheet->getStyle("A{$i}:K{$i}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E9'] // Latar belakang hijau muda
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C8E6C9']]
                ]
            ]);
        }
        $sheet->getStyle('B6:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Header kolom data
        $headerRow = 10; // Baris ke-10 adalah header tabel data
        $sheet->getStyle("A{$headerRow}:K{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Arial',
                'color' => ['rgb' => 'FFFFFF'] // Teks putih
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'] // Hijau tua
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1B5E20'] // Border sedikit lebih gelap
                ]
            ]
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);


        $dataStartRow = 11; // Data dimulai dari baris ke-11
        $dataCount = $this->filteredData->count();
        $lastDataRow = $dataStartRow + $dataCount - 1;

        if ($dataCount > 0) {
            // Gaya untuk sel data
            $sheet->getStyle("A{$dataStartRow}:K{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'] // Border abu-abu muda
                    ]
                ],
                'font' => [
                    'name' => 'Arial',
                    'size' => 10
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP
                ]
            ]);

            // Alignment spesifik untuk kolom data
            $alignments = [
                'A' => Alignment::HORIZONTAL_CENTER, // No
                'B' => Alignment::HORIZONTAL_LEFT,    // Kantor
                'C' => Alignment::HORIZONTAL_LEFT,    // Bulan
                'D' => Alignment::HORIZONTAL_CENTER,  // Tahun
                'E' => Alignment::HORIZONTAL_RIGHT,   // Listrik (kWh)
                'F' => Alignment::HORIZONTAL_RIGHT,   // Daya Listrik (VA)
                'G' => Alignment::HORIZONTAL_RIGHT,   // Air (m³)
                'H' => Alignment::HORIZONTAL_RIGHT,   // BBM (liter)
                'I' => Alignment::HORIZONTAL_LEFT,    // Jenis BBM
                'J' => Alignment::HORIZONTAL_CENTER,  // Tanggal Input
                'K' => Alignment::HORIZONTAL_LEFT,    // Penginput
            ];

            foreach ($alignments as $col => $align) {
                $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastDataRow}")
                      ->getAlignment()->setHorizontal($align);
            }

            // Atur format angka untuk kolom Listrik, Daya Listrik, Air, BBM, Kertas
            $numberColumns = ['E', 'F', 'G', 'H']; // Listrik, Daya Listrik, Air, BBM
            foreach ($numberColumns as $col) {
                $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastDataRow}")
                      ->getNumberFormat()
                      ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Format angka dengan pemisah ribuan
            }
            // Untuk Kertas (I) jika juga angka dan ingin format sama
            $sheet->getStyle("I{$dataStartRow}:I{$lastDataRow}")
                  ->getNumberFormat()
                  ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


            // Warna latar belakang baris bergantian (opsional)
            // for ($row = $dataStartRow; $row <= $lastDataRow; $row++) {
            //     if ($row % 2 == 0) {
            //         $sheet->getStyle("A{$row}:K{$row}")->getFill()
            //               ->setFillType(Fill::FILL_SOLID)
            //               ->getStartColor()->setRGB('F5F5F5'); // Abu-abu sangat muda
            //     }
            // }

        } else {
            // Style jika tidak ada data
            $sheet->mergeCells('A' . $dataStartRow . ':K' . $dataStartRow);
            $sheet->getStyle('A' . $dataStartRow)->applyFromArray([
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '888888'],
                    'size' => 11
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F9F9F9']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ]);
            $sheet->getRowDimension($dataStartRow)->setRowHeight(30);
        }

        $sheet->freezePane("A{$headerRow}"); // Membekukan baris header

        return $sheet;
    }

    public function drawings()
    {
        $logoPath = public_path('assets/img/banklpg.png');

        if (!file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo Bank Lampung');
        $drawing->setDescription('Logo Bank Lampung');
        $drawing->setPath($logoPath);
        $drawing->setHeight(70); // Ukuran logo disesuaikan (lebih kecil dari PDF)
        $drawing->setCoordinates('B2'); // Koordinat ditempatkan di dekat judul
        $drawing->setOffsetX(0);
        $drawing->setOffsetY(0);

        return [$drawing];
    }

    public function title(): string
    {
        return 'Laporan Energi';
    }
}