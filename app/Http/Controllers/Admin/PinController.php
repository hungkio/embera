<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PinDataTable;
use App\Http\Requests\Admin\PinStoreRequest;
use App\Http\Requests\Admin\PinUpdateRequest;
use App\Imports\PinImport;
use App\Models\Pin;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class PinController
{
    public function index(PinDataTable $dataTable, Request $request)
    {
        return $dataTable->render('admin.pins.index');
    }

    public function create(): View
    {
        return view('admin.pins.create');
    }

    public function store(PinStoreRequest $request)
    {
        try {
            Pin::create($request->validated());

            flash()->success(__('Pin đã được tạo thành công'));
            return redirect()->route('admin.pins.index');
        } catch (\Exception $e) {
            Log::error('Pin Store Error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi tạo pin: ' . $e->getMessage());
        }
    }

    public function show(Pin $pin): View
    {
        return view('admin.pins.show', compact('pin'));
    }

    public function edit(Pin $pin): View
    {
        return view('admin.pins.edit', compact('pin'));
    }

    public function update(PinUpdateRequest $request, Pin $pin)
    {
        try {
            $pin->update($request->validated());

            flash()->success(__('Pin đã được cập nhật thành công'));
            return redirect()->route('admin.pins.index');
        } catch (\Exception $e) {
            Log::error('Pin Update Error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi cập nhật pin: ' . $e->getMessage());
        }
    }

    public function destroy(Pin $pin)
    {
        $pin->update(['is_deleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => __('Đã xóa pin thành công'),
        ]);
    }

    public function importForm(): View
    {
        return view('admin.pins.import');
    }

    public function import(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                throw new \Exception('Không tìm thấy file để import.');
            }

            Excel::import(new PinImport, $request->file('file'));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Đã import danh sách Pins! Các bản ghi trùng lặp đã được cập nhật.'),
                ]);
            }
            flash()->success(__('Đã import danh sách Pins! Các bản ghi trùng lặp đã được cập nhật.'));
            return redirect()->route('admin.pins.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in pin import', ['errors' => $e->errors()]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->errors()[array_key_first($e->errors())]),
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Pin Import Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
