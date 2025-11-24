<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopUpBalanceRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:10000', 'max:10000000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah top-up wajib diisi.',
            'amount.numeric' => 'Jumlah top-up harus berupa angka.',
            'amount.min' => 'Minimum top-up adalah Rp 10.000.',
            'amount.max' => 'Maximum top-up adalah Rp 10.000.000.',
        ];
    }
}
