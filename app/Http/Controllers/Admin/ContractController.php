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
            [$date_from, $date_to] = explode(' - ', $dateRange);
        } else {
            $date_from = now()->startOfMonth()->toDateString();
            $date_to = now()->endOfMonth()->toDateString();
        }

        $request->merge([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        $statusList = Contract::distinct()->pluck('status')->filter()->sort()->toArray();
        $emailList = Contract::distinct()->pluck('email')->filter()->sort()->toArray();
        $titleList = Contract::distinct()->pluck('title')->filter()->sort()->toArray();

        $query = Contract::query();
        $query->whereBetween('sign_date', [
            Carbon::parse($date_from)->startOfDay(),
            Carbon::parse($date_to)->endOfDay()
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('email')) {
            $query->where('email', $request->email);
        }

        if ($request->filled('title')) {
            $query->where('title', $request->title);
        }

        $contracts = $query->get();
        $downloadCount = $contracts->sum('download_count');

        $byStatus = $contracts->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => $status,
                'count' => $group->count(),
                'downloads' => $group->sum('download_count'),
            ];
        })->values();

        $byDate = $contracts->groupBy(fn($c) => Carbon::parse($c->sign_date)->format('Y-m-d'))
            ->map(fn($group, $date) => [
                'date' => $date,
                'count' => $group->count(),
            ])->sortKeys()->values();

        return $dataTable->with([
            'filters' => $request->only(['status', 'email', 'title', 'date_from', 'date_to', 'show_deleted' => $request->get('show_deleted', 'no')]),
        ])->render('admin.contracts.index', [
            'statusList' => $statusList,
            'emailList' => $emailList,
            'titleList' => $titleList,
            'downloadCount' => $downloadCount,
            'byStatus' => $byStatus,
            'byDate' => $byDate,
            'filters' => $request->only(['status', 'email', 'title', 'date_from', 'date_to', 'show_deleted' => $request->get('show_deleted', 'no')]),
        ]);
    }

    public function create(): View
    {

        $shops = Shop::with('merchant')->get();
        return view('admin.contracts.create', compact( 'shops'));
    }

    public function store(ContractStoreRequest $request)
    {
        $data = $request->all();

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
            $uploadPath = $file->store('contracts', 'public'); // Lưu vào storage/app/public/contracts
            $data['upload'] = $uploadPath; // Lưu đường dẫn vào DB
        }

        $contract = Contract::create($data);

        if ($request->hasFile('upload')) {
            $contract->addMedia($request->file('upload'))->toMediaCollection('contract');
        }
        return redirect()->route('admin.contracts.index')->with('success', 'Hợp đồng đã được lưu thành công');
    }

    public function edit(Contract $contract): View
    {
        $shops = Shop::with('merchant')->get();
        return view('admin.contracts.edit', compact('contract', 'shops'));
    }

    public function update(ContractUpdateRequest $request, Contract $contract)
    {
        $data = $request->except('upload');

        // Tự động tính expired_time theo số tháng
        $signDate = Carbon::parse($data['sign_date']);
        $expiredDate = Carbon::parse($data['expired_date']);
        $data['expired_time'] = $signDate->diffInMonths($expiredDate) . ' tháng';
        // Upload file qua Storage
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $uploadPath = $file->store('contracts', 'public'); // Lưu vào storage/app/public/contracts
            $data['upload'] = $uploadPath; // Lưu đường dẫn vào DB
        }

        $contract->update($data);

        flash()->success(__('Hợp đồng ":model" đã được cập nhật!', ['model' => $contract->contract_number]));
        return redirect()->route('admin.contracts.index');
    }

    public function destroy(Contract $contract)
    {
        if ($contract->upload) {
            Storage::delete($contract->upload);
        }
        $contract->update(['is_deleted' => 1]); // Soft delete by setting is_deleted to 1

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
            $contract->update(['is_deleted' => 1]); // Soft delete
            $deleted++;
        }

        return response()->json([
            'status' => true,
            'message' => __('Đã xoá :count hợp đồng.', ['count' => $deleted]),
        ]);
    }

    public function sendEmail($id, ContractEmailService $emailService)
    {
        $contract = Contract::with('shop.merchant')->findOrFail($id);

        if (!$contract->shop || !$contract->shop->merchant) {
            return back()->with('error', 'Không thể gửi email vì thiếu thông tin.');
        }

        $emailService->sendContract($contract);

        return redirect()->back()->with('success', 'Đã gửi email cho: ' . $contract->shop->merchant->email);
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
        $contract = Contract::with('shop.merchant')->findOrFail($id);

        if (!$contract->shop || !$contract->shop->merchant) {
            return redirect()->back()->with('error', 'Không thể in vì thiếu thông tin cửa hàng hoặc thương nhân.');
        }

        $data = $emailService->prepareData($contract);

        // Tạo file Word từ template
        $templatePath = storage_path('app/templates/hd_xac_nhan_doanh_thu.docx');
        $processor = new TemplateProcessor($templatePath);

        foreach ($data as $key => $value) {
            $processor->setValue($key, $value);
        }

        $fileName = 'Hop_Dong_' . $contract->contract_number . '_' . now()->format('Ymd_His') . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);
        $processor->saveAs($tempPath);

        // Tải file về
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function printMultipleContracts(Request $request, ContractEmailService $emailService)
    {
        $ids = explode(',', $request->query('ids'));
        $contracts = Contract::with('shop.merchant')->whereIn('id', $ids)->get();

        if ($contracts->isEmpty()) {
            return redirect()->back()->with('error', 'Không có hợp đồng nào được chọn.');
        }

        $zip = new \ZipArchive();
        $zipFileName = 'Hop_Dong_Multiple_' . now()->format('Ymd_His') . '.zip';
        $tempZipPath = storage_path('app/temp/' . $zipFileName);

        if ($zip->open($tempZipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($contracts as $contract) {
                if (!$contract->shop || !$contract->shop->merchant) {
                    continue; // Bỏ qua nếu thiếu thông tin
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

            // Xóa các file tạm
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
