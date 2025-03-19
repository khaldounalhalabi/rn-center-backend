<?php

namespace App\Http\Requests\SystemOffer;

use App\Enums\OfferTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateSystemOfferRequest extends FormRequest
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'title' => ['required', 'string', 'min:3', 'max:255'],
                'description' => ['nullable', 'string'],
                'type' => ['required', 'string', 'min:3', 'max:255', Rule::in(OfferTypeEnum::getAllValues())],
                'amount' => ['required', 'numeric', 'min:1'],
                'allowed_uses' => ['required', 'numeric', 'min:1'],
                'allow_reuse' => ['required', 'boolean'],
                'image' => ['required', 'image', 'max:5000'],
                'clinics' => ['array', 'required', 'min:1'],
                'clinics.*' => ['required', 'numeric', 'exists:clinics,id'],
                'from' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                'to' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            ];
        }

        return [
            'title' => ['nullable', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'min:3', 'max:255', Rule::in(OfferTypeEnum::getAllValues())],
            'amount' => ['nullable', 'numeric', 'min:1'],
            'allowed_uses' => ['nullable', 'numeric', 'min:1'],
            'allow_reuse' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5000'],
            'clinics' => ['array', 'nullable', 'min:1'],
            'clinics.*' => ['required_with:clinics', 'numeric', 'exists:clinics,id'],
            'from' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'to' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
