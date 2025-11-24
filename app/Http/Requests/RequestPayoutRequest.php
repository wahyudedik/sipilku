<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestPayoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isSeller();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:50000'],
            'method' => ['required', 'string', Rule::in(['bank_transfer', 'e_wallet'])],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'bank_name' => ['required_if:method,bank_transfer', 'nullable', 'string', 'max:255'],
            'e_wallet_type' => ['required_if:method,e_wallet', 'nullable', 'string', Rule::in(['gopay', 'ovo', 'dana', 'linkaja', 'shopeepay'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah penarikan wajib diisi.',
            'amount.numeric' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Minimum penarikan adalah Rp 50.000.',
            'method.required' => 'Metode penarikan wajib dipilih.',
            'method.in' => 'Metode penarikan tidak valid.',
            'account_name.required' => 'Nama akun wajib diisi.',
            'account_number.required' => 'Nomor akun wajib diisi.',
            'bank_name.required_if' => 'Nama bank wajib diisi untuk transfer bank.',
            'e_wallet_type.required_if' => 'Jenis e-wallet wajib dipilih.',
        ];
    }
}
