<?php

namespace App\Http\Requests\v1\Media;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddCustomerAttachmentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'attachment' => 'file|mimes:jpeg,png,jpg,pdf,webp,zip,rar,word,txt|max:25000',
            'customer_id' => ['numeric', 'exists:customers,id'],
        ];
    }
}
