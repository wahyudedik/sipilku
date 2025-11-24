<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receiver_id' => ['required', 'exists:users,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'message' => ['required_without:attachments', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z,jpg,jpeg,png,gif,webp,dwg,skp,rvt'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'receiver_id.required' => 'Penerima pesan wajib dipilih.',
            'receiver_id.exists' => 'Penerima pesan tidak ditemukan.',
            'message.required_without' => 'Pesan atau file attachment wajib diisi.',
            'message.max' => 'Pesan maksimal 5000 karakter.',
            'attachments.max' => 'Maksimal 5 file attachment.',
            'attachments.*.file' => 'File harus berupa file yang valid.',
            'attachments.*.max' => 'Ukuran file maksimal 10MB.',
            'attachments.*.mimes' => 'Format file tidak didukung.',
        ];
    }
}

