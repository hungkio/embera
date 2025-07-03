<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ShopDataTable;
use App\Http\Requests\Admin\ShopStoreRequest;
use App\Http\Requests\Admin\ShopUpdateRequest;
use App\Models\Contract;
use App\Models\Merchant;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends \App\Http\Controllers\Controller
{
    public function index(ShopDataTable $dataTable)
    {
        return $dataTable->render('admin.shops.index');
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
            'contracts' => $contracts, // <- Phải có dòng này
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
        $contracts = Contract::with('merchant')->get()
            ->mapWithKeys(function ($contract) {
                $merchantName = $contract->merchant->username ?? 'Không có merchant';
                return [$contract->id => "{$contract->contract_number} - {$merchantName}"];
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
            $data['city']   = $parts[1] ?? null;
            $data['area']   = $parts[2] ?? null;
        }
        return $data;
    }
}
