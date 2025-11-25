<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCalculationRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:rab,volume_material,struktur,pondasi,estimasi_waktu,overhead_profit'],
            'title' => ['nullable', 'string', 'max:255'],
            'inputs' => ['required', 'array'],
            'results' => ['required', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Tipe kalkulator wajib diisi.',
            'type.in' => 'Tipe kalkulator tidak valid.',
            'inputs.required' => 'Input data wajib diisi.',
            'results.required' => 'Hasil perhitungan wajib diisi.',
        ];
    }
}
