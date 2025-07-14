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
            $data[] = ['', '', '', '', '', '', '', '', '', '', ''];
        }

        // Header (baris ke-8)
        $data[] = [
            'Kantor', 'Bulan', 'Tahun', 'Listrik', 'Daya Listrik',
            'Air', 'BBM', 'Jenis BBM', 'Kertas', 'Tanggal Input', 'Penginput'
        ];

        // Ambil data
        $energiData = Energi::with('user')->select(
            'kantor', 'bulan', 'tahun', 'listrik', 'daya_listrik', 'air',
            'bbm', 'jenis_bbm', 'kertas', 'created_at', 'user_id'
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
                $energi->created_at->format('d-m-Y H:i:s'),
                optional($energi->user)->name ?? '-',
            ];
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, 'B' => 10, 'C' => 10,
            'D' => 12, 'E' => 14, 'F' => 10,
            'G' => 10, 'H' => 14, 'I' => 10,
            'J' => 20, 'K' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = Energi::count();
        $headerRow = 8;
        $dataStartRow = 9;
        $lastDataRow = $dataStartRow + $dataCount - 1;

        for ($i = 1; $i <= 7; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(30);
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // Header style
        $sheet->getStyle("A{$headerRow}:K{$headerRow}")->applyFromArray([
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

        if ($dataCount > 0) {
            $sheet->getStyle("A{$dataStartRow}:K{$lastDataRow}")->applyFromArray([
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

            $alignments = [
                'A' => Alignment::HORIZONTAL_LEFT,
                'B' => Alignment::HORIZONTAL_LEFT,
                'C' => Alignment::HORIZONTAL_CENTER,
                'D' => Alignment::HORIZONTAL_RIGHT,
                'E' => Alignment::HORIZONTAL_RIGHT,
                'F' => Alignment::HORIZONTAL_RIGHT,
                'G' => Alignment::HORIZONTAL_RIGHT,
                'H' => Alignment::HORIZONTAL_CENTER,
                'I' => Alignment::HORIZONTAL_RIGHT,
                'J' => Alignment::HORIZONTAL_CENTER,
                'K' => Alignment::HORIZONTAL_LEFT,
            ];

            foreach ($alignments as $col => $align) {
                $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastDataRow}")
                      ->getAlignment()->setHorizontal($align);
            }

            $sheet->getStyle("A{$dataStartRow}:K{$lastDataRow}")->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('FFFFFF');
        }

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
