<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $service = $this->route('service');
        
        // Handle slug-based route model binding
        if ($service && !($service instanceof \App\Models\Service)) {
            $service = \App\Models\Service::where('slug', $service)->first();
        }
        
        return $this->user()->isSeller() && $service && $this->user()->id === $service->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $serviceParam = $this->route('service');
        $service = $serviceParam instanceof \App\Models\Service 
            ? $serviceParam 
            : \App\Models\Service::where('slug', $serviceParam)->first();

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('services')->ignore($service->id)],
            'description' => ['required', 'string', 'min:50'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'package_prices' => ['required', 'array', 'min:1'],
            'package_prices.*.name' => ['required', 'string', 'max:255'],
            'package_prices.*.price' => ['required', 'numeric', 'min:0'],
            'package_prices.*.description' => ['nullable', 'string', 'max:1000'],
            'preview_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'gallery_images' => ['nullable', 'array', 'max:10'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'portfolio' => ['nullable', 'array', 'max:20'],
            'portfolio.*.title' => ['required_with:portfolio', 'string', 'max:255'],
            'portfolio.*.description' => ['nullable', 'string', 'max:1000'],
            'portfolio.*.image' => ['required_with:portfolio', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul jasa wajib diisi.',
            'description.required' => 'Deskripsi jasa wajib diisi.',
            'description.min' => 'Deskripsi minimal 50 karakter.',
            'package_prices.required' => 'Minimal harus ada 1 paket harga.',
            'package_prices.*.name.required' => 'Nama paket wajib diisi.',
            'package_prices.*.price.required' => 'Harga paket wajib diisi.',
            'package_prices.*.price.min' => 'Harga paket tidak boleh negatif.',
            'preview_image.image' => 'File harus berupa gambar.',
            'preview_image.max' => 'Ukuran gambar preview maksimal 2MB.',
        ];
    }
}
