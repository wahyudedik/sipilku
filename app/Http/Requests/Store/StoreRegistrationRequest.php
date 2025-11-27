<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Any authenticated user can register a store
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'business_license' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'location' => ['required', 'array'],
            'location.name' => ['required', 'string', 'max:255'],
            'location.address' => ['required', 'string', 'max:500'],
            'location.city' => ['required', 'string', 'max:255'],
            'location.province' => ['required', 'string', 'max:255'],
            'location.postal_code' => ['nullable', 'string', 'max:10'],
            'location.country' => ['nullable', 'string', 'max:255'],
            'location.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'location.longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama toko wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'location.name.required' => 'Nama lokasi wajib diisi.',
            'location.address.required' => 'Alamat wajib diisi.',
            'location.city.required' => 'Kota wajib diisi.',
            'location.province.required' => 'Provinsi wajib diisi.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
            'banner.image' => 'Banner harus berupa gambar.',
            'banner.max' => 'Ukuran banner maksimal 5MB.',
            'documents.*.file' => 'Dokumen harus berupa file.',
            'documents.*.max' => 'Ukuran dokumen maksimal 5MB.',
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
