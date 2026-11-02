<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelBuilder
{
    protected Spreadsheet $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->removeSheetByIndex(0);
    }

    /**
     * Tambah sheet laporan dengan header INVENTA.
     */
    public function addSheet(
        string $title,
        array $headers,
        array $rows,
        ?string $catatan = null
    ): static {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle(substr($title, 0, 31));

        $jumlahKolom = max(count($headers), 1);
        $kolomTerakhir = Coordinate::stringFromColumnIndex($jumlahKolom);

        // Header utama INVENTA.
        $sheet->setCellValue('A1', 'INVENTA');
        $sheet->mergeCells("A1:{$kolomTerakhir}1");

        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(18)
            ->getColor()->setRGB('7C3AED');

        $sheet->getStyle('A1')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getRowDimension(1)->setRowHeight(30);

        // Judul laporan.
        $sheet->setCellValue('A2', strtoupper($title));
        $sheet->mergeCells("A2:{$kolomTerakhir}2");

        $sheet->getStyle('A2')->getFont()
            ->setBold(true)
            ->setSize(13);

        // Waktu unduh.
        if ($catatan) {
            $sheet->setCellValue('A3', $catatan);
            $sheet->mergeCells("A3:{$kolomTerakhir}3");

            $sheet->getStyle('A3')->getFont()
                ->setItalic(true)
                ->setSize(10);

            $sheet->getStyle('A3')->getFont()
                ->getColor()->setRGB('64748B');
        }

        // Header tabel.
        $barisHeader = 5;

        foreach ($headers as $i => $judul) {
            $kolom = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$kolom}{$barisHeader}", $judul);
        }

        $rangeHeader = "A{$barisHeader}:{$kolomTerakhir}{$barisHeader}";

        $sheet->getStyle($rangeHeader)->getFont()
            ->setBold(true)
            ->getColor()->setRGB('FFFFFF');

        $sheet->getStyle($rangeHeader)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('7C3AED');

        $sheet->getStyle($rangeHeader)->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getRowDimension($barisHeader)->setRowHeight(24);

        // Data laporan.
        $r = $barisHeader + 1;

        foreach ($rows as $dataRow) {
            foreach (array_values($dataRow) as $i => $nilai) {
                $kolom = Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue("{$kolom}{$r}", $nilai);
            }

            $r++;
        }

        // Lebar kolom otomatis.
        for ($i = 1; $i <= $jumlahKolom; $i++) {
            $kolom = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($kolom)->setAutoSize(true);
        }

        // Bekukan header tabel.
        $sheet->freezePane('A' . ($barisHeader + 1));

        if ($this->spreadsheet->getSheetCount() === 1) {
            $this->spreadsheet->setActiveSheetIndex(0);
        }

        return $this;
    }

    /**
     * Tambah sheet ringkasan dashboard.
     */
    public function addSummarySheet(
        string $title,
        string $judulBesar,
        array $ringkasan
    ): static {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle(substr($title, 0, 31));

        $sheet->setCellValue('A1', $judulBesar);
        $sheet->mergeCells('A1:B1');

        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(14);

        $sheet->getStyle('A1')->getFont()
            ->getColor()->setRGB('7C3AED');

        $sheet->setCellValue(
            'A2',
            'Diunduh pada: ' . now()->translatedFormat('d F Y H:i')
        );

        $sheet->mergeCells('A2:B2');

        $sheet->getStyle('A2')->getFont()
            ->setItalic(true)
            ->setSize(10);

        $sheet->getStyle('A2')->getFont()
            ->getColor()->setRGB('64748B');

        $r = 4;

        foreach ($ringkasan as $label => $nilai) {
            $sheet->setCellValue("A{$r}", $label);
            $sheet->setCellValue("B{$r}", $nilai);

            $sheet->getStyle("A{$r}")->getFont()->setBold(true);

            $r++;
        }

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(20);

        if ($this->spreadsheet->getSheetCount() === 1) {
            $this->spreadsheet->setActiveSheetIndex(0);
        }

        return $this;
    }

    /**
     * Unduh file Excel.
     */
    public function download(string $filename): StreamedResponse
    {
        $writer = new Xlsx($this->spreadsheet);

        if (! str_ends_with($filename, '.xlsx')) {
            $filename .= '.xlsx';
        }

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}
