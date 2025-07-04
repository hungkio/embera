<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\ContractDataTable;
use App\Imports\ContractImport;
use App\Models\Contract;
use App\Http\Requests\Admin\ContractStoreRequest;
use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Models\Shop;
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

    public function printContract($id, ContractEmailService $emailService)
    {
        $contract = Contract::with('shops.merchant')->findOrFail($id);

        $firstShopWithMerchant = $contract->shops->firstWhere('merchant');

        if (!$firstShopWithMerchant) {
            return redirect()->back()->with('error', 'Không thể in vì thiếu thông tin cửa hàng hoặc thương nhân.');
        }

        $data = $emailService->prepareData($contract);

        $templatePath = storage_path('app/templates/hd_xac_nhan_doanh_thu.docx');
        $processor = new TemplateProcessor($templatePath);

        foreach ($data as $key => $value) {
            $processor->setValue($key, $value);
        }

        $fileName = 'Hop_Dong_' . $contract->contract_number . '_' . now()->format('Ymd_His') . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);
        $processor->saveAs($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function printMultipleContracts(Request $request, ContractEmailService $emailService)
    {
        $ids = explode(',', $request->query('ids'));
        $contracts = Contract::with('shops.merchant')->whereIn('id', $ids)->get();

        if ($contracts->isEmpty()) {
            return redirect()->back()->with('error', 'Không có hợp đồng nào được chọn.');
        }

        $zip = new \ZipArchive();
        $zipFileName = 'Hop_Dong_Multiple_' . now()->format('Ymd_His') . '.zip';
        $tempZipPath = storage_path('app/temp/' . $zipFileName);

        if ($zip->open($tempZipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($contracts as $contract) {
                $firstShopWithMerchant = $contract->shops->firstWhere('merchant');
                if (!$firstShopWithMerchant) {
                    continue;
                }

                $data = $emailService->prepareData($contract);
                $templatePath = storage_path('app/templates/hd_xac_nhan_doanh_thu.docx');
                $processor = new TemplateProcessor($templatePath);

                foreach ($data as $key => $value) {
                    $processor->setValue($key, $value);
                }

                $fileName = 'Hop_Dong_' . $contract->contract_number . '_' . now()->format('Ymd_His') . '.docx';
                $tempPath = storage_path('app/temp/' . $fileName);
                $processor->saveAs($tempPath);
                $zip->addFile($tempPath, $fileName);
            }

            $zip->close();

            foreach ($contracts as $contract) {
                $fileName = 'Hop_Dong_' . $contract->contract_number . '_' . now()->format('Ymd_His') . '.docx';
                $tempPath = storage_path('app/temp/' . $fileName);
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }

            return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Lỗi khi tạo file ZIP.');
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
