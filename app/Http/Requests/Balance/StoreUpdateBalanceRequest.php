<?php

namespace App\Http\Requests\Balance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateBalanceRequest extends FormRequest
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
                'balanceable_type' => ['required', 'string', 'min:3', 'max:255'],
                'balanceable_id' => ['required', 'numeric'],
                'balance' => ['required', 'numeric'],
            ];
        }

        return [
            'balanceable_type' => ['nullable', 'string', 'min:3', 'max:255'],
            'balanceable_id' => ['nullable', 'numeric'],
            'balance' => ['nullable', 'numeric'],
        ];
    }
}
