<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ShopsAllExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Tên shop',       // A
            'Địa chỉ',        // B
            'Có hợp đồng?',   // C
            'Số hợp đồng',    // D
            'Đối tác',        // E
            'Contract ID',    // F
        ];
    }

    public function map($row): array
    {
        $hasContract = !empty($row->contract_id);

        return [
            $row->shop_name,
            $row->address,
            $hasContract ? 'Có' : 'Không',
            $row->contract_number ?? '',
            $row->merchant_username ?? '',
            $row->contract_id ?? '',
        ];
    }

    /**
     * 🎨 Style & width
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                /** =========================
                 * 1️⃣ TĂNG ĐỘ RỘNG CỘT ABDE
                 * ========================= */
                $sheet->getColumnDimension('A')->setWidth(32); // Tên shop
                $sheet->getColumnDimension('B')->setWidth(45); // Địa chỉ
                $sheet->getColumnDimension('D')->setWidth(26); // Số hợp đồng
                $sheet->getColumnDimension('E')->setWidth(24); // Đối tác

                /** =========================
                 * 2️⃣ TÔ MÀU DÒNG HEADER
                 * ========================= */
                $highestCol = $sheet->getHighestColumn();

                $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0D6EFD'], // xanh Bootstrap
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                ]);
            },
        ];
    }
}
