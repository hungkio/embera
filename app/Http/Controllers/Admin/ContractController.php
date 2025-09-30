<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\ContractDataTable;
use App\Domain\Admin\Models\Admin;
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
                [$date_from, $date_to] = explode(' - ', $dateRange);
                $request->merge([
                    'date_from' => $date_from,
                    'date_to'   => $date_to,
                ]);
            }

            // ✅ Hợp đồng sắp hết hạn trong 30 ngày
            $expiringContracts = Contract::with('admin')
                ->active()
                ->whereBetween('expired_date', [now(), now()->addDays(30)])
                ->get();

            $expiringByAdmin = $expiringContracts
                ->groupBy('admin_id')
                ->map(function ($group) {
                    return [
                        'admin_name'     => optional($group->first()->admin)->full_name ?? 'Unknown',
                        'expiring_count' => $group->count(),
                    ];
                });

            $totalExpiring = $expiringByAdmin->sum('expiring_count');

            $expiringByAdmin = $expiringByAdmin->map(function ($row) use ($totalExpiring) {
                $row['percent'] = $totalExpiring > 0
                    ? round(($row['expiring_count'] / $totalExpiring) * 100, 2)
                    : 0;
                return $row;
            })->values();

            return $dataTable->with([
                'filters' => $request->only(['date_from', 'date_to']),
            ])->render('admin.contracts.index', [
                'filters'        => $request->only(['date_from', 'date_to']),
                'expiringByAdmin'=> $expiringByAdmin,
                'totalExpiring'  => $totalExpiring,
            ]);
    }

    public function create(): View
    {
        $shops = Shop::with('merchant')->get();
        $merchants = \App\Models\Merchant::pluck('username', 'id');

        return view('admin.contracts.create', compact('shops', 'merchants'));
    }

    public function store(ContractStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $data['merchant_id'] = $request->input('merchant_id');

            $adminId = auth()->id();

            $signDate = Carbon::parse($data['sign_date']);
            $expiredDate = Carbon::parse($data['expired_date']);
            $data['expired_time'] = $signDate->diffInMonths($expiredDate) . ' tháng';
            $data['city'] = $request->input('city');
            $data['admin_id'] = $adminId;
            $data['contract_number'] = $data['contract_number'] ?? $this->generateUniqueContractNumber();
            $data['status'] = $data['status'] ?? 'pending';

            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $uploadPath = $file->store('contracts', 'public');
                $data['upload'] = $uploadPath;
            }

            $contract = Contract::create($data);

            if ($request->filled('shop_ids')) {
                Shop::whereIn('id', $request->input('shop_ids'))->update(['contract_id' => $contract->id]);
            }

            if ($request->hasFile('upload')) {
                $contract->addMedia($request->file('upload'))->toMediaCollection('contract');
            }

            \Log::info('Contract saved successfully', ['contract_id' => $contract->id]);

            flash()->success(__('Hợp đồng đã được lưu thành công'));

            return redirect()->route('admin.contracts.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in contract store', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Contract Store Error: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return back()->with('error', 'Lỗi khi lưu dữ liệu: ' . $e->getMessage());
        }
    }

    public function edit(Contract $contract): View
    {
        $shops = Shop::with('merchant')->get();
        $merchants = \App\Models\Merchant::pluck('username', 'id');

        return view('admin.contracts.edit', compact('contract', 'shops', 'merchants'));
    }

    public function update(ContractUpdateRequest $request, Contract $contract)
    {
        try {
            $data = $request->except(['upload', 'merchant_id']);

            $signDate = Carbon::parse($data['sign_date']);
            $expiredDate = Carbon::parse($data['expired_date']);
            $data['expired_time'] = $signDate->diffInMonths($expiredDate) . ' tháng';
            $data['city'] = $request->input('city');
            if ($request->hasFile('upload')) {
                if ($contract->upload) {
                    Storage::disk('public')->delete($contract->upload);
                }
                $file = $request->file('upload');
                $uploadPath = $file->store('contracts', 'public');
                $data['upload'] = $uploadPath;
            }
            if (empty($contract->contract_number)) {
                $data['contract_number'] = $this->generateUniqueContractNumber();
            }
            $contract->update($data);

            if ($request->filled('shop_ids')) {
                Shop::whereIn('id', $request->input('shop_ids'))->update(['contract_id' => $contract->id]);
            }

            \Log::info('Contract updated successfully', ['contract_id' => $contract->id]);

            if ($request->ajax()) {
                $redirect = $request->input('redirect_type') ?? route('admin.contracts.index');
                return response()->json([
                    'success' => true,
                    'message' => __('Hợp đồng ":model" đã được cập nhật!', ['model' => $contract->contract_number]),
                    'redirect' => $redirect,
                ]);
            }

            flash()->success(__('Hợp đồng ":model" đã được cập nhật!', ['model' => $contract->contract_number]));
            return redirect()->route('admin.contracts.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in contract update', ['errors' => $e->errors()]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->errors()[array_key_first($e->errors())]),
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Contract Update Error: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi cập nhật dữ liệu: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Lỗi khi cập nhật dữ liệu: ' . $e->getMessage());
        }
    }

    public function destroy(Contract $contract)
    {
        if ($contract->upload) {
            Storage::disk('public')->delete($contract->upload);
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
                Storage::disk('public')->delete($contract->upload);
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
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi email vì thiếu thông tin.',
            ], 422);
        }

        $emailService->sendContract($contract);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi email cho: ' . $firstShopWithMerchant->merchant->email,
        ]);
    }

    private function generateUniqueContractNumber(): string
    {
        $maxNumber = DB::table('contracts')
            ->whereRaw("contract_number REGEXP '^[0-9]{5}$'")
            ->selectRaw("MAX(CAST(contract_number AS UNSIGNED)) as max_number")
            ->value('max_number');

        $next = ($maxNumber ?? 0) + 1;

        return str_pad((string)$next, 5, '0', STR_PAD_LEFT);
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
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi in hợp đồng: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new ContractImport, $request->file);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Đã import danh sách HĐ!'),
                ]);
            }
            flash()->success(__('Đã import danh sách HĐ!'));
        } catch (\Exception $exception) {
            \Log::error('Contract Import Error: ' . $exception->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 500);
            }
            flash()->error($exception->getMessage());
        }

        return back();
    }

    public function show(Contract $contract)
    {
        $merchants = Admin::whereHas('roles', fn($q) =>
            $q->where('name', 'BD')
        )
            ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name, id")
            ->pluck('full_name', 'id');

        return view('admin.contracts.show', compact('contract', 'merchants'));
    }
}
