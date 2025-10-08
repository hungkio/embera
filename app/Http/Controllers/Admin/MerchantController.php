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

        $merchants = Merchant::with(['contract', 'shops'])->whereIn('id', $merchantIds)->get();

        $success = 0;
        $errors = [];

        foreach ($merchants as $merchant) {
            $hasEmail = !empty(trim($merchant->email));
            $hasShop = $merchant->shops->isNotEmpty();

            $data = $emailService->prepareData($merchant, $merchant->shops ?? []);
            $emailService->sendMail($merchant, $data);

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


    /**
     * Gửi tin nhắn Zalo và ghi log chia sẻ
     */
     public function sendZalo(
            Request $request,
            ZaloService $zaloService,
            MerchantEmailService $emailService,
            MerchantShareLogService $logService // <-- Inject Service vào đây!
        )
        {
            $merchantIds = $request->input('ids', []);
            if (empty($merchantIds) || !is_array($merchantIds)) {
                return response()->json(['message' => 'Vui lòng chọn ít nhất một merchant để gửi Zalo.'], 422);
            }

            // Gửi Zalo đến danh sách merchant
            $results = $zaloService->sendToMerchants(
                $merchantIds,
                'zalo.template_fixed',
                'zalo.template_percentage',
                $emailService
            );

            $successCount = 0;
            $errors = [];

            foreach ($results as $merchantId => $result) {
                $merchant = Merchant::find($merchantId);
                if (!$merchant) continue;

                $shops = $merchant->shops()->where('shops.is_deleted', false)->get();
                $data = $emailService->prepareData($merchant, $shops);
                $shareType = $emailService->detectType($shops);

                // Trạng thái: sent hoặc error message
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
