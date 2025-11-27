<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFactoryTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $factoryType = $this->route('factory_type');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('factory_types')->ignore($factoryType->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:1024'],
            'default_units' => ['nullable', 'array'],
            'default_units.*' => ['string', 'max:50'],
            'specifications_template' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama tipe pabrik wajib diisi.',
            'name.unique' => 'Nama tipe pabrik sudah digunakan.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'icon.image' => 'File icon harus berupa gambar.',
            'icon.max' => 'Ukuran icon maksimal 1MB.',
        ];
    }
}
