<?php

namespace App\Exports;

use App\Models\Energi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EnergiExport implements FromArray, WithStyles, WithDrawings, WithColumnWidths
{
    public function array(): array
    {
        $data = [];
        
        // Baris kosong untuk logo (baris 1-7)
        for ($i = 1; $i <= 7; $i++) {
            $data[] = ['', '', '', '', ''];
        }
        
        // Header tabel (baris 8)
        $data[] = ['Kantor', 'Bulan', 'Tahun', 'Listrik', 'Air'];
        
        // Data dari database
        $energiData = Energi::select('kantor', 'bulan', 'tahun', 'listrik', 'air')->get();
        
        foreach ($energiData as $energi) {
            $data[] = [
                $energi->kantor,
                $energi->bulan,
                $energi->tahun,
                $energi->listrik,
                $energi->air
            ];
        }
        
        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Kantor
            'B' => 12,  // Bulan  
            'C' => 8,   // Tahun
            'D' => 12,  // Listrik
            'E' => 10,  // Air
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = Energi::count();
        $headerRow = 8;
        $dataStartRow = 9;
        $lastDataRow = $dataStartRow + $dataCount - 1;
        
        // Atur tinggi baris
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(30);
        $sheet->getRowDimension('5')->setRowHeight(30);
        $sheet->getRowDimension('6')->setRowHeight(30);
        $sheet->getRowDimension('7')->setRowHeight(15);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);
        
        // Style untuk header (baris 8)
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri',
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'BDD7EE'] // Biru muda
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '3772FF']
                ]
            ]
        ]);

        // Style untuk data jika ada
        if ($dataCount > 0) {
            // Border untuk semua data
            $sheet->getStyle("A{$dataStartRow}:E{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'font' => [
                    'name' => 'Calibri',
                    'size' => 11
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            // Alignment untuk setiap kolom
            $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Kantor - kiri
                
            $sheet->getStyle("B{$dataStartRow}:B{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Bulan - kiri
                
            $sheet->getStyle("C{$dataStartRow}:C{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tahun - tengah
                
            $sheet->getStyle("D{$dataStartRow}:D{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);  // Listrik - kanan
                
            $sheet->getStyle("E{$dataStartRow}:E{$lastDataRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);  // Air - kanan

            // Background putih untuk data
            $sheet->getStyle("A{$dataStartRow}:E{$lastDataRow}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFFFFF');
        }

        // Freeze panes di header
        $sheet->freezePane("A{$dataStartRow}");
        
        return $sheet;
    }

    public function drawings()
    {
        $logoPath = public_path('assets/img/banklpg.png');
        
        // Cek apakah file logo ada
        if (!file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo Bank Lampung');
        $drawing->setDescription('Logo Bank Lampung');
        $drawing->setPath($logoPath);
        $drawing->setHeight(140);  // Tinggi logo
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);  // Offset horizontal
        $drawing->setOffsetY(10);  // Offset vertikal
        
        return [$drawing];
    }
}