<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ShopDataTable;
use App\DataTables\ShopRevenueDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShopStoreRequest;
use App\Http\Requests\Admin\ShopUpdateRequest;
use App\Models\Contract;
use App\Models\Shop;
use App\Services\BBNTExportService;
use App\Services\BBNTService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShopsAllExport;

class ShopController extends Controller
{
    protected $bbntService;

    public function __construct(BBNTService $bbntService)
    {
        $this->bbntService = $bbntService;
    }

    public function index(ShopDataTable $dataTable)
    {
        return $dataTable->render('admin.shops.index');
    }

    public function revenue(ShopRevenueDataTable $dataTable)
    {
        return $dataTable->render('admin.shops.shop-revenue');
    }

    public function create()
    {
        $contracts = Contract::with('merchant')
            ->where('is_deleted', false)
            ->get()
            ->filter(fn($c) => $c->merchant)
            ->mapWithKeys(function ($contract) {
                return [$contract->id => "{$contract->contract_number} - {$contract->merchant->username}"];
            });

        return view('admin.shops.create', [
            'url' => route('admin.shops.store'),
            'method' => 'POST',
            'shop' => new Shop(),
            'contracts' => $contracts,
        ]);
    }

    public function store(ShopStoreRequest $request)
    {
        $data = $request->validated();
        $data = $this->parseRegionInfo($data);

        if ($request->filled('device_json')) {
            try {
                $decoded = json_decode($request->input('device_json'), true);
                $data['device_json'] = is_array($decoded) ? $decoded : null;
            } catch (\Exception $e) {
                $data['device_json'] = null;
            }
        } else {
            $data['device_json'] = null;
        }

        Shop::create($data);
        return redirect()->route('admin.shops.index')->with('success', 'Đã thêm shop thành công!');
    }

    public function edit(Shop $shop)
    {
        $contracts = Contract::with('merchant')
            ->where('is_deleted', false)
            ->get()
            ->filter(fn($c) => $c->merchant)
            ->mapWithKeys(function ($contract) {
                return [$contract->id => "{$contract->contract_number} - {$contract->merchant->username}"];
            });

        return view('admin.shops.edit', [
            'url' => route('admin.shops.update', $shop),
            'method' => 'PUT',
            'shop' => $shop,
            'contracts' => $contracts,
        ]);
    }

    public function update(ShopUpdateRequest $request, Shop $shop)
    {
        $data = $request->validated();
        $data = $this->parseRegionInfo($data);

        if ($request->filled('device_json')) {
            try {
                $decoded = json_decode($request->input('device_json'), true);
                $data['device_json'] = is_array($decoded) ? $decoded : null;
            } catch (\Exception $e) {
                $data['device_json'] = null;
            }
        } else {
            $data['device_json'] = null;
        }

        $shop->update($data);
        return redirect()->route('admin.shops.index')->with('success', 'Đã cập nhật shop!');
    }

    public function destroy(Shop $shop)
    {
        $shop->update(['is_deleted' => true]);

        return response()->json(['success' => true, 'message' => 'Đã xóa shop thành công!']);
    }

    private function parseRegionInfo(array $data): array
    {
        if (isset($data['shop_name']) && preg_match('/\((.*?)\)/', $data['shop_name'], $matches)) {
            $parts = explode('-', $matches[1]);
            $data['region'] = $parts[0] ?? null;
            $data['city'] = $parts[1] ?? null;
            $data['area'] = $parts[2] ?? null;
        }
        return $data;
    }

    public function bbntPreview(Shop $shop)
    {
        $contract = $shop->contract;
        $deviceSummary = $this->bbntService->parseDeviceJson($shop->device_json);
        $productSummary = $this->bbntService->parseProductJson($shop->product_json);

        return view('admin.shops.bbnt.create', [
            'shop' => $shop,
            'contract' => $contract,
            'deviceSummary' => $deviceSummary,
            'productSummary' => $productSummary,
            'url' => route('admin.shops.bbnt.update', $shop),
            'method' => 'PUT',
        ]);
    }

    public function bbntUpdate(Request $request, Shop $shop)
    {
        try {
            $request->validate([
                'product_json' => 'required|json',
                'bbnt_file' => 'nullable|file|mimes:pdf,docx|max:10240', // 10MB
            ]);

            $productJson = $request->input('product_json');
            $decoded = json_decode($productJson, true);

            if (!is_array($decoded) || empty($decoded['products'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu sản phẩm không hợp lệ.',
                ], 422);
            }

            $updateData = [
                'product_json' => $decoded,
            ];

            if ($request->hasFile('bbnt_file')) {
                if ($shop->bbnt_file && \Storage::disk('public')->exists($shop->bbnt_file)) {
                    \Storage::disk('public')->delete($shop->bbnt_file);
                }
                $path = $request->file('bbnt_file')->store('bbnt', 'public');
                $updateData['bbnt_file'] = $path;
            }

            $shop->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu BBNT thành công.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->errors()[array_key_first($e->errors())]),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('BBNT Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu dữ liệu: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bbntDownload(Shop $shop, BBNTExportService $service)
    {
        $path = $service->generateBBNTDocx($shop);
        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function toggleBind(Request $request, Shop $shop)
    {
        $shop->update(['is_bound' => $request->input('is_bound', 0)]);

        return response()->json([
            'success' => true,
            'message' => $shop->is_bound ? 'Đã bind thiết bị.' : 'Đã bỏ bind.'
        ]);
    }

    public function exportAll()
    {
        $rows = Shop::query()
            ->where('shops.is_deleted', 0)
            ->leftJoin('contracts', 'contracts.id', '=', 'shops.contract_id')
            ->leftJoin('merchants', 'merchants.id', '=', 'contracts.merchant_id')
            ->select([
                'shops.shop_name',
                'shops.address',
                'shops.contract_id',
                'contracts.contract_number',
                'merchants.username as merchant_username',
            ])
            ->orderBy('shops.shop_name')
            ->get();

        return Excel::download(
            new ShopsAllExport($rows),
            'shops_all_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
