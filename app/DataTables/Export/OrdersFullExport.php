<?php

namespace App\DataTables\Export;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class OrdersFullExport extends DefaultValueBinder implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithCustomValueBinder
{
    use Exportable;

    public function __construct(
        protected Builder $query,
        protected array   $columns
    )
    {
    }

    public function query()
    {
        return $this->query->select($this->columns);
    }

    public function headings(): array
    {
        return $this->columns;
    }

    public function map($row): array
    {
        $data = $row->toArray();

        return array_map(function ($col) use ($data) {
            $value = $data[$col] ?? null;

            // created_at, updated_at
            if (in_array($col, ['created_at', 'updated_at']) && $value) {
                return ExcelDate::PHPToExcel(
                    Carbon::parse($value)->setTimezone('Asia/Ho_Chi_Minh')
                );
            }

            return $value;
        }, $this->columns);
    }


    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT, // payment_id
            'AN' => 'yyyy-mm-dd hh:mm:ss', // created_at
            'AO' => 'yyyy-mm-dd hh:mm:ss', // updated_at
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'C' && $value !== null) {
            $cell->setValueExplicit((string)$value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
