<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\MBTransactionDataTable;
use App\DataTables\OrderDataTable;
use App\Models\MBTransaction;
use App\Models\Order;
use App\Models\TblOrder;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrderImport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\DataTables\Export\OrderExportHandler;

class OrderController
{
    use AuthorizesRequests;

    private const ACCOUNTING_PAYMENT_CHANNELS = ['balance', 'mbpay'];
    private const EXCLUDED_ACCOUNTING_ORDER_AMOUNTS = [240000, 364000];

    public function index(OrderDataTable $dataTable, Request $request)
    {
        $dateRange = $request->get('date_range');
        if ($dateRange && str_contains($dateRange, ' - ')) {
            list($date_from, $date_to) = explode(' - ', $dateRange);
        } else {
            $date_from = now()->startOfMonth()->toDateString();
            $date_to = now()->endOfMonth()->toDateString();
        }

        $request->merge([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        // Danh sách dropdown filter mới
        $accountingOrders = $this->applyAccountingOrderScope(Order::query());
        $employeeList = (clone $accountingOrders)->distinct()->pluck('employee_name')->filter()->sort()->toArray();
        $shopList = (clone $accountingOrders)->distinct()->pluck('rental_shop')->filter()->sort()->toArray();
        $shopType = (clone $accountingOrders)->distinct()->pluck('rental_shop_type')->filter()->sort()->toArray();
        $merchantList = (clone $accountingOrders)->distinct()->pluck('merchant_name')->filter()->sort()->toArray();
        $regionList = (clone $accountingOrders)->distinct()->pluck('region')->filter()->sort()->toArray();
        $cityList = (clone $accountingOrders)->distinct()->pluck('city')->filter()->sort()->toArray();
        $areaList = (clone $accountingOrders)->distinct()->pluck('area')->filter()->sort()->toArray();
        $paymentChannelList = (clone $accountingOrders)->distinct()->pluck('payment_channels')->filter()->sort()->toArray();

        $query = $this->applyAccountingOrderScope(Order::query())
            ->whereBetween('orders.payment_time', [
                Carbon::parse($date_from)->startOfDay(),
                Carbon::parse($date_to)->endOfDay(),
            ])->leftJoin('shops', 'shops.shop_name', '=', 'orders.rental_shop');

        if ($request->filled('staff')) {
            $query->where('employee_name', $request->staff);
        }

        if ($request->filled('shop_name')) {
            $query->whereIn('rental_shop', $request->shop_name);
        }

        if ($request->filled('shop_type')) {
            $query->where('rental_shop_type', $request->shop_type);
        }

        if ($request->filled('region')) {
            $query->where('orders.region', $request->region);
        }
        if ($request->filled('city')) {
            $query->where('orders.city', $request->city);
        }
        if ($request->filled('area')) {
            $query->where('orders.area', $request->area);
        }
        if ($request->filled('payment_channel')) {
            $query->where('orders.payment_channels', $request->payment_channel);
        }
        if ($request->filled('merchant_name')) {
            $query->whereIn('orders.merchant_name', array_filter((array) $request->merchant_name));
        }
        if ($request->order_amount) {
            if ($request->order_amount == 1) {
                $query->where('order_amount', '>', 0);
            }

            if ($request->order_amount == 2) {
                $query->where('order_amount', '<=', 0);
            }
        }

        $orders = (clone $query)->select('orders.*', 'shops.share_rate_type', 'shops.share_rate')
            ->orderByDesc('orders.payment_time')->get();
        $totalRevenue = $orders->sum('order_amount');

        $byShop = $orders->groupBy('rental_shop')->map(function ($group) {
            $shop = $group->first()->rental_shop;
            $address = $group->first()->rental_shop_address;
            $merchant_share_rate = $group->first()->share_rate;
            $merchant_share_rate_type = $group->first()->share_rate_type;
            $revenue = $group->sum('order_amount');

            if ($merchant_share_rate_type === 'fixed') {
                $sharing = number_format((float)$merchant_share_rate, 0) . 'đ';
                $sharing_revenue = $merchant_share_rate * $group->count();
            } else {
                $sharing = (number_format((float)$merchant_share_rate, 0) ?? 0) . '%';
                $sharing_revenue = $merchant_share_rate / 100 * $revenue;
            }

            return [
                'shop' => $shop,
                'revenue' => $revenue,
                'number_of_order' => $group->count() ?? 0,
                'address' => $address,
                'sharing_percent' => $sharing,
                'sharing_revenue' => $sharing_revenue,
            ];
        })
        ->sortByDesc('revenue')
        ->values();

        $byEmployee = $orders->groupBy('employee_name')->map(function ($group) {
            return [
                'employee' => $group->first()->employee_name,
                'revenue' => $group->sum('order_amount'),
            ];
        })->values();

        $byDate = $orders->groupBy(fn($o) => Carbon::parse($o->payment_time)->format('Y-m-d'))
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                    'revenue' => $group->sum('order_amount'),
                ];
            })->sortKeys()->values();

        return $dataTable->with([
            'filters' => $request->only(['employee_name', 'rental_shop', 'merchant_name', 'date_from', 'date_to']),
        ])->render('admin.orders.index', [
            'staffList' => $employeeList,
            'shopTypeList' => $shopType,
            'shopNameList' => $shopList,
            'merchantList' => $merchantList,
            'totalRevenue' => $totalRevenue,
            'regionList' => $regionList,
            'areaList' => $areaList,
            'cityList' => $cityList,
            'byShop' => $byShop,
            'byStaff' => $byEmployee,
            'byDate' => $byDate,
            'paymentChannelList' => $paymentChannelList,
            'filters' => $request->only([
                'staff', 'shop_type', 'shop_name', 'region', 'city', 'payment_channel', 'date_from', 'date_to'
            ]),
        ]);
    }

    private function applyAccountingOrderScope($query)
    {
        return $query
            ->whereNotNull('orders.payment_time')
            ->where('orders.order_amount', '>', 0)
            ->whereNotIn('orders.order_amount', self::EXCLUDED_ACCOUNTING_ORDER_AMOUNTS)
            ->whereIn('orders.payment_channels', self::ACCOUNTING_PAYMENT_CHANNELS);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new OrderImport, $request->file('import_file'));
            return back()->with('success', 'Import thành công!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Import thất bại: ' . $e->getMessage());
        }
    }

    public function importMBTransaction(Request $request)
    {
        $request->validate([
            'input_file_in' => 'required|file|mimes:xlsx,xls,csv',
            'input_file_out' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $inFile = $request->file('input_file_in');
        $outFile = $request->file('input_file_out');

        $incoming = Excel::toCollection(null, $inFile)[0]->skip(7);
        $outgoing = Excel::toCollection(null, $outFile)[0]->skip(7);

        $incomingData = collect();
        foreach ($incoming->slice(1) as $row) {
            $amount_in = (float)$row[8];
            if (!$amount_in) {
                continue;
            }
            $incomingData->push([
                'date_in' => $this->parseDate($row[3] ?? ''),
                'amount_in' => (float)$row[8],
                'ft_code_in' => trim($row[17] ?? ''),
            ]);
        }

        $outgoingData = collect();
        foreach ($outgoing->slice(1) as $row) {
            preg_match('/FT(\d+)\s*(\d+)/', $row[12], $matches);
            if (!$matches) {
                continue;
            }
            $code_ref = 'FT' . $matches[1] . $matches[2];
            if (!$code_ref) {
                continue;
            }
            $outgoingData->push([
                'code_ref' => trim($code_ref), // Mã giao dịch gốc
                'amount_out' => (float)$row[7],
                'date_out' => $this->parseDate($row[3] ?? ''),
                'ft_code_out' => trim($row[17] ?? ''),
            ]);
        }

        foreach ($incomingData as $in) {
            $match = $outgoingData->firstWhere('code_ref', $in['ft_code_in']);
            MBTransaction::updateOrCreate(
                ['ft_code_in' => $in['ft_code_in']],
                [
                    'date_in' => $in['date_in'],
                    'amount_in' => $in['amount_in'] * 1000,
                    'date_out' => $match['date_out'] ?? null,
                    'ft_code_out' => $match['ft_code_out'] ?? null,
                    'amount_out' => ($match['amount_out'] ?? 0) * 1000,
                    'revenue' => ($in['amount_in'] - ($match['amount_out'] ?? 0)) * 1000,
                ]
            );
        }

        return back()->with('success', 'Import dữ liệu thành công!');
    }

    public function importMBTransactionNew(Request $request)
    {
        $request->validate([
            'input_file_in' => 'required|file|mimes:xlsx,xls,csv',
            'input_file_out' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $inFile = $request->file('input_file_in');
        $outFile = $request->file('input_file_out');

        // Đọc toàn bộ sheet
        $incomingSheet = Excel::toCollection(null, $inFile)[0];
        $outgoingSheet = Excel::toCollection(null, $outFile)[0];

        // Bỏ 6 dòng đầu (thông tin), dòng 7 mới là header -> data từ dòng 8
        $incoming = $incomingSheet->slice(7)->filter(fn($row) => !empty($row[2]));
        $outgoing = $outgoingSheet->slice(7)->filter(fn($row) => !empty($row[3]) || !empty($row[9]));

        $incomingData = $incoming->map(function ($row) {
            return [
                'code' => trim($row[2] ?? ''),
                'date_in' => $this->parseDate($row[1] ?? ''),
                'amount_in' => (float)str_replace(',', '', (string)($row[4] ?? 0)),
                'ft_code_in' => trim($row[11] ?? ''),
            ];
        })->filter(fn($in) => $in['code'] && $in['ft_code_in']);

        $outgoingData = $outgoing->map(function ($row) {
            return [
                'code_ref' => trim($row[3] ?? ''),
                'amount_out' => (float)str_replace(',', '', (string)($row[5] ?? 0)),
                'date_out' => $this->parseDate($row[1] ?? ''),
                'ft_code_out' => trim($row[8] ?? ''),
                'ft_code_in' => trim($row[9] ?? ''),
            ];
        });

        foreach ($incomingData as $in) {
            // Ưu tiên match theo FT gốc
            $match = $outgoingData->firstWhere('ft_code_in', $in['ft_code_in']);

            // Nếu không khớp, fallback theo mã GD
            if (!$match) {
                $match = $outgoingData->firstWhere('code_ref', $in['code']);
            }

            MBTransaction::updateOrCreate(
                ['code_in' => $in['code']],
                [
                    'code_in' => $in['code'],
                    'date_in' => $in['date_in'],
                    'ft_code_in' => $in['ft_code_in'],
                    'amount_in' => $in['amount_in'],

                    'code_out' => $match['code_ref'] ?? null,
                    'date_out' => $match['date_out'] ?? null,
                    'ft_code_out' => $match['ft_code_out'] ?? null,
                    'amount_out' => $match['amount_out'] ?? 0,

                    'revenue' => $in['amount_in'] - ($match['amount_out'] ?? 0),
                ]
            );
        }

        return back()->with('success', 'Import dữ liệu thành công!');
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            return Carbon::createFromFormat('d/m/Y H:i:s', $value);
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d/m/Y', $value);
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function mergeTransaction(MBTransactionDataTable $dataTable, Request $request)
    {
        $dateRange = $request->get('date_range');
        if ($dateRange && str_contains($dateRange, ' - ')) {
            list($date_from, $date_to) = explode(' - ', $dateRange);
        } else {
            $date_from = now()->startOfMonth()->toDateString();
            $date_to = now()->endOfMonth()->toDateString();
        }

        $request->merge([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        return $dataTable->render('admin.orders.compare');
    }

    public function compare(Request $request)
    {
        $request->validate([
            'date_range' => 'required|string',
            'order_code' => 'nullable|string'
        ]);

        list($date_from, $date_to) = explode(' - ', $request->date_range);
        $from = Carbon::parse($date_from)->startOfDay();
        $to = Carbon::parse($date_to)->endOfDay();

        $orderCode = trim($request->get('order_code') ?? '');

        $mbTransactions = MBTransaction::query()
            ->when($orderCode, fn($q) => $q->where('ft_code_in', $orderCode))
            ->whereBetween('date_in', [$from, $to])
            ->get();

        if ($mbTransactions->isEmpty()) {
            return response()->json([
                'data' => [],
            ]);
        }

        $orders = TblOrder::leftJoin('tbl_transactions as t', 't.order_code', '=', 'tbl_orders.code')
            ->select([
                'tbl_orders.amount', 'tbl_orders.refund_amount', 'tbl_orders.rental_time',
                \DB::raw("MAX(CASE WHEN t.tx_type = 'receive' THEN t.provider_tx_id END) AS ft_in"),
                \DB::raw("MAX(CASE WHEN t.tx_type = 'refund' THEN t.provider_tx_id END) AS ft_out"),
            ])
            ->when($orderCode, fn($q) => $q->where('provider_tx_id', $orderCode))
            ->whereBetween('tbl_orders.created_at', [$from, $to])
            ->groupBy(['tbl_orders.amount', 'tbl_orders.refund_amount', 'tbl_orders.rental_time'])
            ->get();


        $orderMap = $orders->keyBy('ft_in');

// dd($orderMap);
        $report = $mbTransactions->map(function ($mb) use ($orderMap) {
            $order = $orderMap->get($mb->ft_code_in);
            if (!$order) {
                return [
                    'code' => $mb->ft_code_in,
                    'matched' => false,
                    'reason' => 'Không tìm thấy đơn hàng',
                    'revenue' => $mb->revenue,
                    'order_amount' => null,
                    'payment_time' => null,
                    'date_in' => formatDate($mb->date_in),
                    'ft_in' => $mb->ft_code_in,
                    'ft_out' => $mb->ft_code_out,
                ];
            }

            $orderAmount = (int)($order->amount - $order->refund_amount);
            if ((int)$orderAmount !== (int)$mb->revenue) {
                return [
                    'code' => $mb->ft_code_in,
                    'matched' => false,
                    'reason' => 'Lệch số tiền',
                    'revenue' => $mb->revenue,
                    'order_amount' => $orderAmount,
                    'payment_time' => $order->rental_time ? formatDate($order->rental_time) : '',
                    'date_in' => formatDate($mb->date_in),
                    'ft_in' => $mb->ft_code_in,
                    'ft_out' => $mb->ft_code_out,
                ];
            }

            return [
                'code' => $mb->code_in,
                'matched' => true,
                'reason' => 'Khớp',
                'revenue' => $mb->revenue,
                'order_amount' => $orderAmount,
                'payment_time' => $order->rental_time ? formatDate($order->rental_time) : '',
                'date_in' => formatDate($mb->date_in),
                'ft_in' => $mb->ft_code_in,
                'ft_out' => $mb->ft_code_out,
            ];
        });

        // Đơn hàng không tìm thấy ở MB
        $matchedMB = $mbTransactions->pluck('code_in')->all();
        $extraOrders = $orders->filter(fn($order) => !in_array($order->payment_id, $matchedMB))
            ->map(function ($order) {
                return [
                    'code' => $order->payment_id,
                    'matched' => false,
                    'reason' => 'Không tìm thấy giao dịch MB',
                    'amount_in' => null,
                    'order_amount' => $orderAmount,
                    'payment_time' => $order->rental_time ? formatDate($order->rental_time) : '',
                    'date_in' => null,
                    'ft_in' => null,
                    'ft_out' => null,
                ];
            });

        $finalReport = $report->merge($extraOrders)->values();

        return response()->json([
            'data' => $finalReport,
        ]);
    }

    public function exportFull(OrderDataTable $dataTable)
    {
        // Allow very large exports to run without PHP timeout.
        set_time_limit(0);

        $export = $dataTable->buildExcelFileFull();

        $fileName = 'Orders_Full_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download($export, $fileName);
    }

    public function sendEmailExport(Request $request, OrderDataTable $dataTable)
    {
        $request->validate([
            'email'   => 'required|email',
            'title'   => 'required|string',
            'content' => 'nullable|string',
        ]);

        // ✅ lấy query giống hệt DataTable export
        $query = $dataTable->getExportQuery();
        $data = $query->get();

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $date = ($date_from == $date_to) ? $date_from : null;

        // ✅ tránh lỗi Excel khi không có data
        if ($data->isEmpty()) {
            $data = collect([
                ['message' => 'Không có dữ liệu']
            ]);
        }

        $fileName = 'Orders_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = 'exports/' . $fileName;

        // tạo folder nếu chưa có
        Storage::disk('local')->makeDirectory('exports');

        // ✅ export đúng chuẩn
        Excel::store(
            new OrderExportHandler($data, $date, $date_from, $date_to),
            $filePath,
            'local'
        );

        $fullPath = Storage::disk('local')->path($filePath);

        // check file tồn tại
        if (!file_exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'File chưa được tạo: ' . $fullPath
            ]);
        }

        // gửi mail
        Mail::send([], [], function ($message) use ($request, $fullPath, $fileName) {
            $message->to($request->email)
                ->subject($request->title)
                ->attach($fullPath, [
                    'as' => $fileName,
                ]);

            if ($request->filled('content')) {
                $message->setBody($request->content, 'text/html');
            }

            if ($request->hasFile('original_data') && $request->file('original_data')->isValid()) {
                $uploadedFile = $request->file('original_data');
                $message->attach($uploadedFile->getRealPath(), [
                    'as' => $uploadedFile->getClientOriginalName(),
                    'mime' => $uploadedFile->getMimeType(),
                ]);
            }
        });

        // xoá file sau khi gửi
        Storage::disk('local')->delete($filePath);

        return response()->json([
            'success' => true,
            'message' => 'Gửi email thành công!'
        ]);
    }
}
