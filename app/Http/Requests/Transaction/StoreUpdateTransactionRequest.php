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
        return [
            'type' => ['required', 'string', 'min:3', 'max:255', Rule::in(TransactionTypeEnum::getAllValues())],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'min:3', 'max:10000'],
            'date' => ['nullable', 'date', 'date_format:Y-m-d H:i'],
            'actor_id' => ['required', 'numeric', 'exists:users,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        //TODO:: need to check for other users when they have the proper permission
        if (isAdmin()) {
            $this->merge(['actor_id' => auth()->user()?->id]);
        }
    }
}
