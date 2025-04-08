<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        $additional = [];
        if ($this->fullUrl() == route('api.v1.patient.register')) {
            $additional = [
                'birth_date' => 'required|date|date_format:Y-m-d',
                'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues())
            ];
        }

        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|regex:/^09\d{8}$/|unique:users,phone',
            'password' => 'required|min:8|confirmed',
            'gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
            ...$additional,
        ];
    }
}
