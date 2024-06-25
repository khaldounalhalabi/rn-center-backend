<?php

namespace App\Http\Requests\Offer;

use App\Enums\OfferTypeEnum;
use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateOfferRequest extends FormRequest
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
                'title'     => ['required', 'min:3', 'max:255', 'json', new LanguageShape()],
                'value'     => ['required', 'numeric', 'min:0'],
                'note'      => ['nullable', 'json', new LanguageShape()],
                'start_at'  => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                'end_at'    => ['required', 'date', 'date_format:Y-m-d', 'after:start_at'],
                'type'      => ['required', 'string', 'min:3', 'max:255', Rule::in(OfferTypeEnum::getAllValues())],
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
                'image'     => ['required', 'image', 'max:5000'],
            ];
        }

        return [
            'title'    => ['nullable', 'min:3', 'max:255', 'json', new LanguageShape()],
            'value'    => ['nullable', 'numeric', 'min:0'],
            'note'     => ['nullable', 'json', new LanguageShape()],
            'start_at' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_at'   => ['nullable', 'date', 'date_format:Y-m-d', 'after:start_at'],
            'type'     => ['nullable', 'string', 'min:3', 'max:255', Rule::in(OfferTypeEnum::getAllValues())],
            'image'    => ['nullable', 'image', 'max:5000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (auth()->user()?->isClinic()) {
            $this->merge([
                'clinic_id' => auth()->user()?->getClinicId()
            ]);
        }
    }
}
