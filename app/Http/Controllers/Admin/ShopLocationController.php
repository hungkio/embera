<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShopLocationImportService;
use Illuminate\Http\Request;

class ShopLocationController extends Controller
{
    public function importForm()
    {
        return view('admin.shop-locations.import');
    }

    public function import(Request $request, ShopLocationImportService $service)
    {
        $request->validate([
            'json_file' => 'required|file|mimes:json'
        ]);

        $json = json_decode(
            file_get_contents($request->file('json_file')->getRealPath()),
            true
        );

        if (!$json) {
            return back()->withErrors(['json_file' => 'File JSON không hợp lệ']);
        }

        $service->import($json);

        return redirect()->back()->with('imported', true);
    }
}
