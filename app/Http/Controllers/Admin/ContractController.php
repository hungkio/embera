<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\ContractDataTable;
use App\Imports\ContractImport;
use App\Models\Contract;
use App\Http\Requests\Admin\ContractStoreRequest;
use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Models\Shop;
use App\Services\PrintContractToWord;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ContractEmailService;
use Illuminate\Support\Facades\DB;

class ContractController
{
    public function index(ContractDataTable $dataTable, Request $request)
    {
        $dateRange = $request->get('date_range');
        if ($dateRange && str_contains($dateRange, ' - ')) {
            list($date_from, $date_to) = explode(' - ', $dateRange);
            $request->merge([
                'date_from' => $date_from,
                'date_to' => $date_to,
            ]);
        }

        return $dataTable->with([
            'filters' => $request->only(['date_from', 'date_to']),
        ])->render('admin.contracts.index', [
            'filters' => $request->only(['date_from', 'date_to']),
        ]);
    }

    public function create(): View
    {
        $shops = Shop::with('merchant')->get();
        $merchants = \App\Models\Merchant::pluck('username', 'id'); // ← Add this line

        return view('admin.contracts.create', compact('shops', 'merchants')); // ← Pass both
    }


    public function store(ContractStoreRequest $request)
    {
        $data = $request->validated();
        $data['merchant_id'] = $request->input('merchant_id');

        // Lấy admin_id từ người dùng hiện tại
        $adminId = auth()->id();

        // Tính expired_time tự động
        $signDate = Carbon::parse($data['sign_date']);
        $expiredDate = Carbon::parse($data['expired_date']);
        $data['expired_time'] = $signDate->diffInMonths($expiredDate) . ' tháng';

        // Tạo bản ghi mới với admin_id
        $data['admin_id'] = $adminId;
        $data['contract_number'] = $data['contract_number'] ?? $this->generateUniqueContractNumber();
        $data['status'] = $data['status'] ?? 'pending';

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $uploadPath = $file->store('contracts', 'public');
            $data['upload'] = $uploadPath;
        }

        $contract = Contract::create($data);

        // Gán nhiều cửa hàng cho hợp đồng
        if ($request->filled('shop_ids')) {
            Shop::whereIn('id', $request->input('shop_ids'))->update(['contract_id' => $contract->id]);
        }

        if ($request->hasFile('upload')) {
            $contract->addMedia($request->file('upload'))->toMediaCollection('contract');
        }

        return redirect()->route('admin.contracts.index')->with('success', 'Hợp đồng đã được lưu thành công');
    }

    public function edit(Contract $contract): View
    {
        $shops = Shop::with('merchant')->get();

        $merchants = \App\Models\Merchant::pluck('username', 'id');

        return view('admin.contracts.edit', compact('contract', 'shops', 'merchants'));
    }

    public function update(ContractUpdateRequest $request, Contract $contract)
    {
        $data = $request->except(['upload', 'merchant_id']);

        // Tự động tính expired_time
        $signDate = Carbon::parse($data['sign_date']);
        $expiredDate = Carbon::parse($data['expired_date']);
        $data['expired_time'] = $signDate->diffInMonths($expiredDate) . ' tháng';

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $uploadPath = $file->store('contracts', 'public');
            $data['upload'] = $uploadPath;
        }

        $contract->update($data);

        // Gỡ liên kết cũ và gán lại shop mới
        Shop::where('contract_id', $contract->id)->update(['contract_id' => null]);

        if ($request->filled('shop_ids')) {
            Shop::whereIn('id', $request->input('shop_ids'))->update(['contract_id' => $contract->id]);
        }

        flash()->success(__('Hợp đồng ":model" đã được cập nhật!', ['model' => $contract->contract_number]));
        return redirect()->route('admin.contracts.index');
    }

    public function destroy(Contract $contract)
    {
        if ($contract->upload) {
            Storage::delete($contract->upload);
        }
        $contract->update(['is_deleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => __('Đã xoá hợp đồng thành công!'),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('id', []);
        $deleted = 0;

        $contracts = Contract::whereIn('id', $ids)->get();

        foreach ($contracts as $contract) {
            if ($contract->upload) {
                Storage::delete($contract->upload);
            }
            $contract->update(['is_deleted' => 1]);
            $deleted++;
        }

        return response()->json([
            'status' => true,
            'message' => __('Đã xoá :count hợp đồng.', ['count' => $deleted]),
        ]);
    }

    public function sendEmail($id, ContractEmailService $emailService)
    {
        $contract = Contract::with('shops.merchant')->findOrFail($id);

        $firstShopWithMerchant = $contract->shops->firstWhere('merchant');

        if (!$firstShopWithMerchant) {
            return back()->with('error', 'Không thể gửi email vì thiếu thông tin.');
        }

        $emailService->sendContract($contract);

        return redirect()->back()->with('success', 'Đã gửi email cho: ' . $firstShopWithMerchant->merchant->email);
    }

    private function generateUniqueContractNumber(): string
    {
        $currentYear = now()->year;

        $maxNumber = Contract::whereYear('created_at', $currentYear)
            ->max(DB::raw('CAST(contract_number AS UNSIGNED)'));

        $nextNumber = $maxNumber ? $maxNumber + 1 : 1;

        return (string)$nextNumber;
    }

    public function printContract($contract, PrintContractToWord $printService)
    {
        \Log::info('Starting printContract for ID: ' . $contract);
        $contract = Contract::with('shops')->findOrFail($contract);

        try {
            \Log::info('Attempting to generate and download Word file for contract ID: ' . $contract->id);
            return $printService->printContractToWord($contract);
        } catch (\Exception $e) {
            \Log::error('Exception in printContract: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Lỗi khi in hợp đồng: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new ContractImport, $request->file);
            flash()->success(__('Đã import danh sách HĐ!'));
        } catch (\Exception $exception) {
            flash()->error($exception->getMessage());
        }

        return back();
    }
}
