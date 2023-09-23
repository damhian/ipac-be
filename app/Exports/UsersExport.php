<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersExport implements FromCollection, WithColumnFormatting, WithStyles, WithEvents, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {

        // Return the data for export
        return collect($this->data);
    }

    public function columnFormats(): array
    {
        return [
            'G' => '0',
            'M' => '0',
            'U' => '0',
        ];
    }

    public function headings(): array
    {
        // Get the headers from the first row of the data
        return $this->data[0];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $highestDataRow = $event->sheet->getDelegate()->getHighestDataRow();

                // Set background color for the header row
                $event->sheet->getDelegate()->getStyle('A1:' . $event->sheet->getDelegate()->getHighestColumn() . '1')
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00B050');
        
                // // Set horizontal alignment to the right for columns 'E2' to 'P2'
                // $event->sheet->getDelegate()->getStyle('E2:P' . $highestDataRow)
                //     ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
