<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\ContractDataTable;
use App\Models\Contract;
use App\Http\Requests\Admin\ContractStoreRequest;
use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Imports\ContractImport;
use App\Exports\ContractExportHandler;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
        return view('admin.contracts.create');
    }

    public function store(ContractStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/contracts'), $filename);
            $data['upload'] = $filename;
        }

        Contract::create($data);

        return redirect()->route('admin.contracts.index')->with('success', 'Thêm hợp đồng thành công');
    }

    public function edit(Contract $contract): View
    {
        return view('admin.contracts.edit', compact('contract'));
    }

    public function update(ContractUpdateRequest $request, Contract $contract)
    {
        $contract->update($request->validated());

        if ($request->hasFile('upload')) {
            if ($contract->upload) {
                Storage::delete($contract->upload);
            }
            $path = $request->file('upload')->store('contracts');
            $contract->upload = $path;
            $contract->save();
        }

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

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new ContractImport, $request->file('import_file'));
            return back()->with('success', 'Import thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import thất bại: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new ContractExportHandler, 'Contracts_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function changeStatus(Contract $contract, Request $request)
    {
        $contract->update(['status' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => __('Trạng thái đã được cập nhật.'),
        ]);
    }

    public function bulkStatus(Request $request)
    {
        $contracts = Contract::whereIn('id', $request->id)->get();
        foreach ($contracts as $contract) {
            $contract->update(['status' => $request->status]);
        }

        return response()->json([
            'status' => true,
            'message' => __('Đã cập nhật trạng thái cho :count hợp đồng.', ['count' => $contracts->count()]),
        ]);
    }
}
