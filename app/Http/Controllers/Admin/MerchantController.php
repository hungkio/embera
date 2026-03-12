<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\MerchantDataTable;
use App\Domain\Admin\Models\Admin;
use App\Http\Requests\Admin\MerchantStoreRequest;
use App\Http\Requests\Admin\MerchantUpdateRequest;
use App\Services\MerchantShareLogService;
use App\Models\Merchant;
use App\Services\MerchantEmailService;
use App\Services\ZaloService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Auth;
use Carbon\Carbon;

class MerchantController
{
    /**
     * Danh sách merchant
     */
    public function index(MerchantDataTable $dataTable)
    {
        return $dataTable->render('admin.merchants.index');
    }

    /**
     * Trang tạo merchant mới
     */
    public function create(): View
    {
        $employees = Admin::whereHas('roles', function (Builder $subQuery) {
            $subQuery->whereIn(config('permission.table_names.roles') . '.name', ['BD']);
        })->get();

        return view('admin.merchants.create', [
            'url' => route('admin.merchants.store'),
            'merchant' => new Merchant(),
            'employees' => $employees,
        ]);
    }

    /**
     * Lưu merchant mới
     */
    public function store(MerchantStoreRequest $request)
    {
        $data = $request->all();
        Merchant::create($data);

        return redirect()->route('admin.merchants.index')
            ->with('success', 'Thêm Merchant thành công');
    }

    public function createShareLog(
        Request                 $request,
        MerchantShareLogService $logService,
        MerchantEmailService    $emailService
    )
    {
        $request->validate([
            'merchant_ids' => 'required|array|min:1',
            'merchant_ids.*' => 'exists:merchants,id',
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $merchantIds = $request->input('merchant_ids');
        $createdCount = 0;

        foreach ($merchantIds as $id) {
            $merchant = Merchant::find($id);
            if (!$merchant) {
                \Log::warning("Merchant ID {$id} không tồn tại");
                continue;
            }

            \Log::info("Bắt đầu tạo log cho merchant {$id} - {$merchant->username}", [
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
            ]);

            $shops = $merchant->shops()->where('shops.is_deleted', 0)->get();
            \Log::info("Số shop của merchant {$id}: " . $shops->count());

            $data = $emailService->prepareData($merchant, $shops, $start, $end);

            \Log::info("Dữ liệu prepareData cho merchant {$id}", [
                'shop_data_count' => count($data['shop_data'] ?? []),
                'total_orders' => $data['tong_dong_hang'] ?? 'không có',
            ]);

            if (empty($data['shop_data'])) {
                \Log::warning("Merchant {$id} không có dữ liệu shop_data trong kỳ");
                continue;
            }

            $shareType = $emailService->detectType($shops);

            try {
                $logService->logShare($merchant, $data, $shareType, 'manual', 'sent');
                $createdCount++;
                \Log::info("Tạo log THÀNH CÔNG cho merchant {$id}");
            } catch (\Exception $e) {
                \Log::error("Tạo log THẤT BẠI merchant {$id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'created_count' => $createdCount,
            'message' => "Đã tạo $createdCount bản ghi log"
        ]);
    }

    /**
     * Trang chỉnh sửa merchant
     */
    public function edit(Merchant $merchant): View
    {
        $employees = Admin::whereHas('roles', function (Builder $subQuery) {
            $subQuery->whereIn(config('permission.table_names.roles') . '.name', ['BD']);
        })->get();

        return view('admin.merchants.edit', compact('merchant', 'employees'));
    }

    /**
     * Cập nhật merchant
     */
    public function update(MerchantUpdateRequest $request, Merchant $merchant)
    {
        $data = $request->all();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $merchant->update($data);

        flash()->success(__('Merchant ":model" đã được cập nhật!', ['model' => $merchant->username]));
        return redirect()->route('admin.merchants.index');
    }

    /**
     * Xóa merchant (soft delete)
     */
    public function destroy(Merchant $merchant)
    {
        $merchant->update(['is_deleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => __('Đã xóa Merchant thành công!'),
        ]);
    }

    /**
     * Xóa nhiều merchant cùng lúc
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('id', []);
        $deleted = 0;

        $merchants = Merchant::whereIn('id', $ids)->get();
        foreach ($merchants as $merchant) {
            if ($merchant->upload) {
                Storage::delete(public_path('uploads/merchants/' . $merchant->upload));
            }
            $merchant->update(['is_deleted' => 1]);
            $deleted++;
        }

        return response()->json([
            'status' => true,
            'message' => __('Đã xóa :count Merchant.', ['count' => $deleted]),
        ]);
    }

    /**
     * Gửi email chia sẻ doanh thu cho merchant
     */
    public function sendEmail(Request $request, MerchantEmailService $emailService)
    {
        $merchantIds = $request->input('ids', []);
        if (empty($merchantIds) || !is_array($merchantIds)) {
            return response()->json(['message' => 'Vui lòng chọn ít nhất một merchant để gửi mail.'], 422);
        }

        // ✅ validate date range
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $merchants = Merchant::with(['contract', 'shops'])->whereIn('id', $merchantIds)->get();

        $success = 0;
        $errors = [];

        foreach ($merchants as $merchant) {
            $hasEmail = !empty(trim($merchant->email));
            $hasShop = $merchant->shops->isNotEmpty();

            // ✅ dùng date range
            $data = $emailService->prepareData($merchant, $merchant->shops ?? [], $start, $end);
            // ✅ cần sửa sendMail nhận start/end (xem note bên dưới)
            $emailService->sendMail($merchant, $data, $start, $end);

            if ($hasEmail && $hasShop) {
                $success++;
            } else {
                $err = "Merchant ID {$merchant->id}: ";
                if (!$hasEmail) $err .= 'no email; ';
                if (!$hasShop) $err .= 'no shop; ';
                $errors[] = trim($err, '; ');
            }
        }

        if ($success > 0 && empty($errors)) {
            return response()->json(['message' => "Đã gửi mail thành công cho {$success} merchant."]);
        } elseif ($success > 0) {
            return response()->json([
                'message' => "Đã gửi mail thành công cho {$success} merchant, nhưng có lỗi với một số merchant.",
                'errors' => $errors
            ], 207);
        } else {
            return response()->json([
                'message' => 'Gửi mail thất bại.',
                'errors' => $errors
            ], 422);
        }
    }

    public function sendZaloContract(
        Request              $request,
        ZaloService          $zaloService,
        MerchantEmailService $emailService // <--- Inject thêm Service này
    )
    {
        try {
            $merchantIds = $request->input('ids', []);

            if (empty($merchantIds) || !is_array($merchantIds)) {
                return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một merchant.'], 422);
            }

            // Truyền cả $emailService sang bên ZaloService
            $results = $zaloService->sendZaloContract($merchantIds, $emailService);

            // Xử lý kết quả trả về
            $successCount = 0;
            $errors = [];

            foreach ($results as $merchantId => $res) {
                if ($res['success']) {
                    $successCount++;
                } else {
                    $name = Merchant::find($merchantId)->username ?? $merchantId;
                    $errors[] = "{$name}: " . $res['error'];
                }
            }

            if ($successCount > 0 && empty($errors)) {
                return response()->json([
                    'success' => true,
                    'message' => "Gửi Zalo thành công cho {$successCount} merchant."
                ]);
            } elseif ($successCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Gửi thành công {$successCount}, thất bại " . count($errors) . ".",
                    'errors' => $errors
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gửi thất bại toàn bộ.',
                    'errors' => $errors
                ], 422);
            }

        } catch (\Throwable $e) {
            \Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi tin nhắn Zalo và ghi log chia sẻ
     */
    public function sendZalo(
        Request                 $request,
        ZaloService             $zaloService,
        MerchantEmailService    $emailService,
        MerchantShareLogService $logService
    )
    {
        $merchantIds = $request->input('ids', []);
        if (empty($merchantIds) || !is_array($merchantIds)) {
            return response()->json(['message' => 'Vui lòng chọn ít nhất một merchant để gửi Zalo.'], 422);
        }

        // ✅ validate date range
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        // ✅ truyền date range xuống service (cần sửa signature sendToMerchants - xem note)
        $results = $zaloService->sendToMerchants(
            $merchantIds,
            'zalo.template_fixed',
            'zalo.template_percentage',
            $emailService,
            $start,
            $end
        );

        $successCount = 0;
        $errors = [];

        foreach ($results as $merchantId => $result) {
            $merchant = Merchant::find($merchantId);
            if (!$merchant) continue;

            $shops = $merchant->shops()
                ->where('shops.is_deleted', 0)  // hoặc false tùy kiểu dữ liệu
                ->get();

            // ✅ prepareData theo range để log đúng
            $data = $emailService->prepareData($merchant, $shops, $start, $end);
            $shareType = $emailService->detectType($shops);

            $status = $result['success'] ? 'sent' : 'failed';
            $logService->logShare($merchant, $data, $shareType, 'zalo', $status);

            if (!$result['success']) {
                $errors[] = "Merchant ID {$merchantId}: " . ($result['error'] ?? 'Unknown error');
            } else {
                $successCount++;
            }
        }

        if ($successCount > 0 && empty($errors)) {
            return response()->json(['message' => "Gửi Zalo thành công cho {$successCount} merchant."]);
        } elseif ($successCount > 0) {
            return response()->json([
                'message' => "Gửi Zalo thành công cho {$successCount} merchant, nhưng có lỗi với một số merchant.",
                'errors' => $errors
            ], 207);
        } else {
            return response()->json([
                'message' => 'Gửi Zalo thất bại.',
                'errors' => $errors
            ], 422);
        }
    }
}
