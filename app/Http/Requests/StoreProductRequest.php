<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSeller();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['required', 'string', 'min:50'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'preview_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'gallery_images' => ['nullable', 'array', 'max:5'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'file' => ['required', 'file', 'mimes:zip,rar,7z,pdf,doc,docx,xls,xlsx,ppt,pptx,dwg,skp,rvt', 'max:102400'], // 100MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'description.min' => 'Deskripsi minimal 50 karakter.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'discount_price.lt' => 'Harga diskon harus lebih kecil dari harga normal.',
            'preview_image.required' => 'Gambar preview wajib diisi.',
            'preview_image.image' => 'File harus berupa gambar.',
            'preview_image.max' => 'Ukuran gambar preview maksimal 2MB.',
            'file.required' => 'File produk wajib diupload.',
            'file.max' => 'Ukuran file maksimal 100MB.',
        ];
    }
}
