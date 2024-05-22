<?php

namespace App\Http\Requests\Subscription;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateSubscriptionRequest extends FormRequest
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
                'name' => ['required', 'string', 'min:3', 'max:255', 'unique:subscriptions,name'],
                'description' => ['nullable', 'string'],
                'period' => ['required', 'numeric', function (string $attribute, mixed $value, Closure $fail) {
                    if ($value == 0) {
                        $fail($attribute . ' must not be 0.');
                    }

                    if ($value < -1) {
                        $fail($attribute . ' must not be less than -1.');
                    }
                }],
                'allow_period' => ['nullable', 'numeric', 'min:0'],
                'cost' => ['required', 'numeric', 'min:0'],
            ];
        }

        $subscriptionId = $this->route('subscription');
        return [
            'name' => ['nullable', 'string', 'min:3', 'max:255', 'unique:subscriptions,name,' . $subscriptionId],
            'description' => ['nullable', 'string'],
            'period' => ['nullable', 'numeric'],
            'allow_period' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
