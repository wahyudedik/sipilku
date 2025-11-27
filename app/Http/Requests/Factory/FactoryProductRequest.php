<?php

namespace App\Http\Requests\Factory;

use Illuminate\Foundation\Http\FormRequest;

class FactoryProductRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sku' => ['nullable', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:100'],
            'product_category' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'unit' => ['required', 'string', 'max:50'],
            'available_units' => ['nullable', 'array'],
            'available_units.*' => ['string', 'max:50'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'min_order' => ['nullable', 'integer', 'min:1'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'specifications' => ['nullable', 'array'],
            'quality_grade' => ['nullable', 'array'],
            'quality_grade.grade' => ['nullable', 'string', 'max:100'],
            'quality_grade.value' => ['nullable', 'string', 'max:100'],
            'quality_grade.description' => ['nullable', 'string', 'max:500'],
            'is_available' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'discount_price.lt' => 'Harga diskon harus lebih kecil dari harga normal.',
            'unit.required' => 'Unit produk wajib diisi.',
            'images.max' => 'Maksimal 10 gambar.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.max' => 'Ukuran gambar maksimal 5MB.',
        ];
    }
}

