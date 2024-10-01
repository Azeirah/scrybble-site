<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemarkableDocumentShareRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'feedback' => ['nullable', 'string'],
            'sync_id' => ['required', 'exists:sync,id'],
            'developer_access_consent_granted' => ['boolean'],
            'open_access_consent_granted' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
