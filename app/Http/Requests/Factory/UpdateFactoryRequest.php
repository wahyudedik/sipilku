<?php

namespace App\Http\Requests\Factory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateFactoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'factory_type_id' => ['required', 'exists:factory_types,uuid'],
            'umkm_id' => ['nullable', 'exists:umkms,uuid'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'in:industri,umkm'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'business_license' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'delivery_price_per_km' => ['nullable', 'numeric', 'min:0'],
            'max_delivery_distance' => ['nullable', 'integer', 'min:1'],
            'capacity' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'factory_type_id.required' => 'Tipe pabrik wajib dipilih.',
            'factory_type_id.exists' => 'Tipe pabrik tidak valid.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori harus Industri atau UMKM.',
            'name.required' => 'Nama pabrik wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug from name
        if ($this->has('name')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}

