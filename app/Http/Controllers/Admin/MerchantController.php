<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\MerchantDataTable;
use App\Domain\Admin\Models\Admin;
use App\Http\Requests\Admin\MerchantStoreRequest;
use App\Http\Requests\Admin\MerchantUpdateRequest;
use App\Models\Contract;
use App\Models\Email;
use App\Models\Merchant;
use App\Services\ContractEmailService;
use App\Services\MerchantEmailService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MerchantController
{
    public function index(MerchantDataTable $dataTable)
    {
        return $dataTable->render('admin.merchants.index');
    }

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

    public function store(MerchantStoreRequest $request)
    {
        $data = $request->all();
        Merchant::create($data);

        return redirect()->route('admin.merchants.index')->with('success', 'Thêm Merchant thành công');
    }

    public function edit(Merchant $merchant): View
    {
        $employees = Admin::whereHas('roles', function (Builder $subQuery) {
            $subQuery->whereIn(config('permission.table_names.roles') . '.name', ['BD']);
        })->get();

        return view('admin.merchants.edit', compact('merchant', 'employees'));
    }

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

    public function destroy(Merchant $merchant)
    {
        $merchant->update(['is_deleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => __('Đã xóa Merchant thành công!'),
        ]);
    }

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


    public function sendEmail(Request $request, ContractEmailService $emailService)
    {
        $merchantIds = $request->input('ids');

        if (empty($merchantIds) || !is_array($merchantIds)) {
            return response()->json(['message' => 'Vui lòng chọn ít nhất một merchant để gửi mail.'], 422);
        }

        $merchants = Merchant::with(['contract', 'shops'])->whereIn('id', $merchantIds)->get();

        foreach ($merchants as $merchant) {
            $contract = $merchant->contract;

            if (!$contract) {
                // Bỏ qua merchant nếu không có hợp đồng
                continue;
            }

            $shops = $merchant->shops ?? [];

            foreach ($shops as $shop) {
                $email = new Email([
                    'to' => $merchant->email,
                    'content' => json_encode([
                        'contract' => $contract,
                        'shop' => $shop,
                    ]),
                    'status' => 'pending',
                ]);

                $email->save();
                $emailService->send($email);
            }
        }

        return response()->json(['message' => 'Đã gửi mail thành công.']);
    }

}
