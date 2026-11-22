<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithTitle, WithStrictNullComparison
{
    private array $rows;

    public function __construct(private readonly string $title, iterable $rows)
    {
        $this->rows = collect($rows)->map(fn ($row) => (array) $row)->values()->all();
    }

    public static function download(string $title, iterable $rows): BinaryFileResponse
    {
        return Excel::download(new self($title, $rows), Str::slug($title) . '-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function array(): array
    {
        return array_map(fn (array $row) => array_values($row), $this->rows);
    }

    public function headings(): array
    {
        return array_map(fn (string $key) => Str::headline($key), array_keys($this->rows[0] ?? []));
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');

        return [1 => ['font' => ['bold' => true]]];
    }

    public function title(): string
    {
        return Str::limit($this->title, 31, '');
    }
}
