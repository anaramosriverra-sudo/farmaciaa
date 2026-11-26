<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Hoja de Excel del reporte de ventas (librería maatwebsite/excel).
// Cada interfaz (Concern) agrega una característica a la hoja.
class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle
{
    // Recibe las ventas ya filtradas por ReportController
    public function __construct(private Collection $sales)
    {
    }

    // FromCollection: las filas de la hoja
    public function collection(): Enumerable
    {
        return $this->sales;
    }

    // WithHeadings: la primera fila con los títulos de las columnas
    public function headings(): array
    {
        return ['N.°', 'Fecha', 'Cliente', 'DNI', 'Vendedor', 'Op. gravada (S/)', 'IGV (S/)', 'Total (S/)'];
    }

    // WithMapping: qué dato va en cada columna, por cada venta
    public function map(mixed $sale): array
    {
        return [
            str_pad($sale->id, 6, '0', STR_PAD_LEFT),
            $sale->date->format('d/m/Y H:i'),
            $sale->customer->name,
            $sale->customer->dni,
            $sale->user->name,
            (float) $sale->total - (float) $sale->igv,
            (float) $sale->igv,
            (float) $sale->total,
        ];
    }

    // WithColumnFormatting: los montos con dos decimales
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'G' => NumberFormat::FORMAT_NUMBER_00,
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    // WithStyles: la fila de títulos en negrita
    public function styles(Worksheet $sheet): ?array
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    // WithTitle: nombre de la pestaña de la hoja
    public function title(): string
    {
        return 'Ventas';
    }
}
