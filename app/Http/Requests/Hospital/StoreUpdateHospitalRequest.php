<?php

namespace App\Http\Requests\Hospital;

use App\Enums\HospitalStatusEnum;
use App\Models\Hospital;
use App\Rules\LanguageShape;
use App\Rules\NotInBlocked;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateHospitalRequest extends FormRequest
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
                'name'                    => ['required', 'json', new LanguageShape()],
                'status'                  => ['required', 'string', Rule::in(HospitalStatusEnum::getAllValues())],
                'phone_numbers'           => 'array|required',
                'phone_numbers.*'         => ['required', 'string', 'unique:phone_numbers,phone', 'min:1', 'max:255', new NotInBlocked()],
                'available_departments'   => 'array|required',
                'available_departments.*' => ['required', 'numeric', 'exists:available_departments,id'],
                "images"                  => 'array|nullable',
                'images.*'                => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'address'                 => 'array|required',
                'address.name'            => ['required', 'json', 'min:3', new LanguageShape()],
                'address.city_id'         => ['required', 'numeric', 'exists:cities,id'],
                'address.map_iframe'      => ['required', 'string']
            ];
        }

        return [
            'name'                    => ['nullable', 'json', new LanguageShape()],
            'status'                  => ['nullable', 'string', Rule::in(HospitalStatusEnum::getAllValues())],
            'phone_numbers'           => 'array|nullable',
            'phone_numbers.*'         => ['nullable', 'string', new UniquePhoneNumber(request()->route('hospital'), Hospital::class), 'regex:/^07\d{9}$/', new NotInBlocked()],
            'available_departments'   => 'array|nullable',
            'available_departments.*' => ['nullable', 'numeric', 'exists:available_departments,id'],
            "images"                  => 'array|nullable',
            'images.*'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'address'                 => 'array|nullable',
            'address.name'            => ['nullable', 'json', 'min:3', new LanguageShape()],
            'address.city_id'         => ['nullable', 'numeric', 'exists:cities,id'],
            'address.map_iframe'      => ['nullable', 'string']
        ];
    }

    public function attributes()
    {
        return [
            'phone_numbers.*'         => 'phone number',
            'available_departments.*' => 'available department',
            'images.*'                => 'image',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'address' => [
                ...$this->input('address'),
                'map_iframe' => strip_tags($this->input('address.map_iframe'), ['iframe'])
            ]
        ]);
    }
}
