<?php

namespace App\Support;

use Illuminate\Http\Request;

trait RedirectHelper
{
    protected function redirect(Request $request)
    {
        $submitType = $request->input('submit_type', 'back');
        if ($submitType == 'submit_and_create') {
            return redirect()->action([self::class, 'create']);
        }
        if ($submitType == 'submit_and_back') {
            return redirect()->action([self::class, 'index']);
        }

        return redirect()->back();
    }
}
