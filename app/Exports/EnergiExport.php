<?php

namespace App\Exports;

use App\Models\Energi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
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

        // Baris kosong untuk logo (1-7)
        for ($i = 1; $i <= 7; $i++) {
            $data[] = ['', '', '', '', '', '', '', '', ''];
        }

        // Header (baris ke-8)
        $data[] = ['Kantor', 'Bulan', 'Tahun', 'Listrik', 'Daya Listrik', 'Air', 'BBM', 'Jenis BBM', 'Kertas'];

        // Data dari database
        $energiData = Energi::select(
            'kantor', 'bulan', 'tahun',
            'listrik', 'daya_listrik', 'air',
            'bbm', 'jenis_bbm', 'kertas'
        )->get();

        foreach ($energiData as $energi) {
            $data[] = [
                $energi->kantor,
                $energi->bulan,
                $energi->tahun,
                $energi->listrik,
                $energi->daya_listrik,
                $energi->air,
                $energi->bbm,
                $energi->jenis_bbm,
                $energi->kertas,
            ];
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Kantor
            'B' => 10, // Bulan
            'C' => 10, // Tahun
            'D' => 12, // Listrik
            'E' => 14, // Daya Listrik
            'F' => 10, // Air
            'G' => 10, // BBM
            'H' => 14, // Jenis BBM
            'I' => 10, // Kertas
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = Energi::count();
        $headerRow = 8;
        $dataStartRow = 9;
        $lastDataRow = $dataStartRow + $dataCount - 1;

        // Tinggi baris
        for ($i = 1; $i <= 7; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(30);
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // Header style
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->applyFromArray([
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
                'startColor' => ['rgb' => 'BDD7EE']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '3772FF']
                ]
            ]
        ]);

        // Style data jika ada
        if ($dataCount > 0) {
            $sheet->getStyle("A{$dataStartRow}:I{$lastDataRow}")->applyFromArray([
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

            // Alignment per kolom
            $alignments = [
                'A' => Alignment::HORIZONTAL_LEFT,   // Kantor
                'B' => Alignment::HORIZONTAL_LEFT,   // Bulan
                'C' => Alignment::HORIZONTAL_CENTER, // Tahun
                'D' => Alignment::HORIZONTAL_RIGHT,  // Listrik
                'E' => Alignment::HORIZONTAL_RIGHT,  // Daya Listrik
                'F' => Alignment::HORIZONTAL_RIGHT,  // Air
                'G' => Alignment::HORIZONTAL_RIGHT,  // BBM
                'H' => Alignment::HORIZONTAL_CENTER, // Jenis BBM
                'I' => Alignment::HORIZONTAL_RIGHT,  // Kertas
            ];

            foreach ($alignments as $col => $align) {
                $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastDataRow}")
                      ->getAlignment()->setHorizontal($align);
            }

            // Putihkan background data
            $sheet->getStyle("A{$dataStartRow}:I{$lastDataRow}")->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('FFFFFF');
        }

        // Freeze header
        $sheet->freezePane("A{$dataStartRow}");

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
        $drawing->setHeight(140);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);

        return [$drawing];
    }
}
