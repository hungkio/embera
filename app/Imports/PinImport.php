<?php

namespace App\Imports;

use App\Models\Pin;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PinImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pin([
            'imei' => $row['imei'],
            'serial_number' => $row['serial_number'],
        ]);
    }
}
