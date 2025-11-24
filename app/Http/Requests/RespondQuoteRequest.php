<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespondQuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $quoteRequest = $this->route('quoteRequest');
        return $this->user()->isSeller() && 
               $quoteRequest && 
               $quoteRequest->service->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quoted_price' => ['required', 'numeric', 'min:0'],
            'quote_message' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quoted_price.required' => 'Harga quote wajib diisi.',
            'quoted_price.min' => 'Harga quote tidak boleh negatif.',
            'quote_message.required' => 'Pesan quote wajib diisi.',
            'quote_message.min' => 'Pesan quote minimal 20 karakter.',
            'quote_message.max' => 'Pesan quote maksimal 2000 karakter.',
        ];
    }
}
