<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\MerchantDataTable;
use App\Domain\Admin\Models\Admin;
use App\Http\Requests\Admin\MerchantStoreRequest;
use App\Http\Requests\Admin\MerchantUpdateRequest;
use App\Models\Merchant;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        $data = $request->validated();
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/merchants'), $filename);
            $data['upload'] = $filename;
        }
        $merchant = Merchant::create($data);

        // Clear the temporary password from session after use
        $plainPassword = session()->get('temp_password');
        session()->forget('temp_password');

        return redirect()->route('admin.merchants.index')->with('success', 'Thêm Merchant thành công')
            ->with('plain_password', $plainPassword);
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
        $data = $request->validated();
        if ($request->hasFile('upload')) {
            if ($merchant->upload) {
                Storage::delete(public_path('uploads/merchants/' . $merchant->upload));
            }
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/merchants'), $filename);
            $data['upload'] = $filename;
        }
        $merchant->update($data);

        flash()->success(__('Merchant ":model" đã được cập nhật!', ['model' => $merchant->username]));
        return redirect()->route('admin.merchants.index');
    }

    public function destroy(Merchant $merchant)
    {
        if ($merchant->upload) {
            Storage::delete(public_path('uploads/merchants/' . $merchant->upload));
        }
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

    public function changeStatus(Merchant $merchant, Request $request)
    {
        $merchant->update(['status' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => __('Trạng thái đã được cập nhật.'),
        ]);
    }

    public function bulkStatus(Request $request)
    {
        $ids = $request->input('id', []);
        $status = $request->input('status');

        $merchants = Merchant::whereIn('id', $ids)->get();
        foreach ($merchants as $merchant) {
            $merchant->update(['status' => $status]);
        }

        return response()->json([
            'status' => true,
            'message' => __('Đã cập nhật trạng thái cho :count Merchant.', ['count' => $merchants->count()]),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls',
        ]);

        return back()->with('success', 'Import thành công!');
    }

    public function export()
    {
        return redirect()->route('admin.merchants.index')->with('success', 'Xuất file thành công!');
    }

}
