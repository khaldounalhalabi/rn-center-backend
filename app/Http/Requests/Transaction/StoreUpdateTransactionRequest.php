<?php

namespace App\Http\Requests\Transaction;

use App\Enums\TransactionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateTransactionRequest extends FormRequest
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
                'type' => ['required', 'string', 'min:3', 'max:255', Rule::in(TransactionTypeEnum::getAllValues())],
                'amount' => ['required', 'numeric', 'min:0'],
                'description' => ['nullable', 'string', 'min:3', 'max:10000'],
                'date' => ['nullable', 'date', 'date_format:Y-m-d H:i'],
            ];
        }

        return [
            'type' => ['nullable', 'string', 'min:3', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'min:3', 'max:10000'],
            'date' => ['nullable', 'date', 'date_format:Y-m-d H:i'],
        ];
    }
}
