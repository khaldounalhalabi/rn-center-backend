<?php

namespace App\Http\Requests\Review;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (request()->method() == "PUT") {
            $review = Review::find(request()->route('review'));
            if (!$review?->canUpdate()) {
                return false;
            }
        }

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
                'clinic_id'   => ['required', 'numeric', 'exists:clinics,id'],
                'customer_id' => ['required', 'numeric', 'exists:customers,id'],
                'rate'        => ['required', 'numeric', 'min:0', 'max:5', 'integer'],
                'review'      => ['nullable', 'string', 'min:3', 'max:255'],
            ];
        }

        return [
            'clinic_id'   => ['nullable', 'numeric', 'exists:clinics,id'],
            'customer_id' => ['nullable', 'numeric', 'exists:customers,id'],
            'rate'        => ['nullable', 'numeric', 'min:0', 'max:5', 'integer'],
            'review'      => ['nullable', 'string', 'min:3', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (auth()?->user()?->isCustomer()) {
            $this->merge([
                'customer_id' => auth()?->user()?->customer?->id
            ]);
        }
    }
}
