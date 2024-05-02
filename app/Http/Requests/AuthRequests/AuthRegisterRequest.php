<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\GenderEnum;
use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class AuthRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        //customer register
        return [
            'first_name' => 'required|string|max:255|min:3',
            'middle_name' => 'required|string|max:255|min:3',
            'last_name' => 'required|string|max:255|min:3',
            'phone_number' => 'array|required',
            'phone_number.*' => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/'],
            'email' => 'required|email|unique:users,email|min:3|max:255',
            'password' => 'required|min:8|confirmed|max:255',
            'fcm_token' => 'nullable|string|min:3|max:1000',
            'gender' => 'required|string|' . Rule::in(GenderEnum::getAllValues()),
            'image' => 'image|max:50000|mimes:jpg,png|nullable',
            'birth_date' => 'required|date|date_format:Y-m-d',
            'address' => 'required|array',
            'address.name' => ['required', 'string', new LanguageShape()],
            'address.city_id' => ['required', 'exists:cities,id', 'integer'],
            'address.lat' => ['nullable', 'string', 'required_without:address.map_iframe'],
            'address.lng' => ['nullable', 'string', 'required_without:address.map_iframe'],
            'address.map_iframe' => ['nullable', 'string', 'required_without:address.lat', 'required_without:address.lng']
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
