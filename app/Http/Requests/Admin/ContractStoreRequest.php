<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContractStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_number'   => 'required|string|unique:contracts,contract_number',
            'sign_date'         => 'required|date',
            'expired_date'      => 'required|date|after_or_equal:sign_date',
            'status'            => 'nullable|string|in:pending,active,disabled',
            'expired_time'      => 'nullable|string',
            'bank_info'         => 'nullable|string',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string',
            'admin_id'          => 'nullable|exists:admins,id',
            'title'             => 'nullable|string',
            'ceo_sign'          => 'nullable|string',
            'location'          => 'nullable|string',
            'note'              => 'nullable|string',
            'upload'            => 'nullable|file|mimes:pdf',
            'download_count'    => 'nullable|integer|min:0',
        ];
    }
}
