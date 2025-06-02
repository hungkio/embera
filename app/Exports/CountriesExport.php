<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Yajra\DataTables\Exports\DataTablesCollectionExport;


class CountriesExport extends DataTablesCollectionExport implements WithMapping
{
    public function headings(): array
    {
        return [
            'Tên TA',
            'Tên TV',
        ];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['name_vi'],
        ];
    }
}
